<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\TransactionModel;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionController extends BaseController
{
    protected $transaksiModel;

    public function __construct()
    {
        // Inisialisasi model menu
        $this->transaksiModel = new TransactionModel();
    }

    public function index()
    {
        $data = [
            'activeMenu' => 'transaction',
            'pageTitle'  => 'Transaksi',
            'title'      => 'CashFlow — Transaksi',
            'menus'      => (new MenuModel())->findAll()
        ];

        return view('transaction', $data);
    }

    public function get_all_json()
    {
        $tanggalFilter = $this->request->getGet('tanggal');
        // Ambil data terbaru dari database
        $transactions = $this->transaksiModel
            ->select("transaksi.*, DATE_FORMAT(transaksi.tanggal, '%d-%m-%Y') as tanggal")
            ->orderBy('tanggal', 'DESC')
            ->where('tanggal', $tanggalFilter)
            ->get()
            ->getResultArray();

        // Kembalikan langsung dalam bentuk JSON murni
        return $this->response->setJSON($transactions);
    }
    public function save()
    {
        $json = $this->request->getJSON(true);
        if (empty($json) || empty($json['items'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data transaksi atau keranjang kosong.',
                'token'   => csrf_hash()
            ]);
        }
        if (!isset($json['bayar']) || $json['bayar'] === '' || $json['bayar'] < 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nominal pembayaran wajib diisi dan tidak boleh minus.',
                'token'   => csrf_hash()
            ]);
        }

        if ($json['bayar'] < $json['total']) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Uang yang dibayarkan kurang dari total tagihan.',
                'token'   => csrf_hash()
            ]);
        }
        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            $dataTransaksi = [
                'tanggal'     => date('Y-m-d'),
                'nama'        => $json['nama'] ?? 'Umum',
                'payment_via' => $json['payment_via'],
                'total'       => $json['total'],
                'bayar'       => $json['bayar'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $db->table('transaksi')->insert($dataTransaksi);

            $transaksiId = $db->insertID();

            foreach ($json['items'] as $item) {
                $dataDetail = [
                    'transaksi_id' => $transaksiId,
                    'menu_id'      => $item['menu_id'],
                    'qty'          => $item['qty']
                ];

                $db->table('transaksi_detail')->insert($dataDetail);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'       => 'success',
                'transaksi_id' => $transaksiId,
                'message'      => 'Transaksi berhasil disimpan!',
                'token'        => csrf_hash()
            ]);
        } catch (\Exception $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }
    public function get_detail_json($id)
    {
        // JOIN transaksidetail dengan menu untuk dapat nama, harga, image_url
        $db = \Config\Database::connect();
        $details = $db->table('transaksi_detail td')
            ->select('td.menu_id, td.qty, m.nama, m.harga, m.image_url')
            ->join('menu m', 'm.id = td.menu_id', 'left')
            ->where('td.transaksi_id', $id)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($details);
    }
    public function delete($id = null)
    {
        // Pengecekan jika ID kosong
        if (empty($id)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID transaksi tidak valid.',
                'token'   => csrf_hash()
            ]);
        }

        $transactionModel = new TransactionModel();
        $deleted = $transactionModel->delete($id);

        if ($deleted) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Transaksi berhasil dihapus/dibatalkan.',
                'token'   => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus transaksi dari database.',
                'token'   => csrf_hash()
            ]);
        }
    }

    public function struk($id)
    {
        $model = new TransactionModel();
        $transaksi = $model->find($id);

        if (!$transaksi) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ]);
        }
        $db = \Config\Database::connect();
        $items = $db->table('transaksi_detail td')
            ->select('td.menu_id, td.qty, m.nama, m.harga, m.image_url')
            ->join('menu m', 'm.id = td.menu_id', 'left')
            ->where('td.transaksi_id', $id)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success'   => true,
            'toko'      => [
                'nama'    => 'Dimsum Gendhis',
                'alamat'  => 'Jl. Cinta Karya, Sari Rejo, Medan Polonia',
            ],
            'tanggal'      => date('d-m-Y H:i:s', strtotime($transaksi['created_at'])),
            'items'        => array_map(function ($item) {
                return [
                    'nama'  => $item['nama'],
                    'qty'   => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['qty'] * $item['harga'],
                ];
            }, $items),
            'total'     => $transaksi['total'],
            'bayar'     => $transaksi['bayar'],
            'kembalian' => $transaksi['bayar'] - $transaksi['total'],
        ]);
    }
    public function export()
    {
        $bulan = $this->request->getGet('bulan'); // format: YYYY-MM

        if (!$bulan) {
            return redirect()->back()->with('error', 'Bulan harus dipilih.');
        }

        $model = new TransactionModel();
        $data  = $model->getDataExport($bulan);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Transaksi');

        // Header
        $headers = ['Tanggal', 'Item', 'Qty', 'Harga', 'Total'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9D9D9');
        $sheet->getStyle('A1:E1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Isi data
        $row = 2;
        $grandTotal = 0;

        foreach ($data as $d) {
            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($d['tanggal'])));
            $sheet->setCellValue('B' . $row, $d['item']);
            $sheet->setCellValue('C' . $row, $d['qty']);
            $sheet->setCellValue('D' . $row, $d['harga']);
            $sheet->setCellValue('E' . $row, $d['total']);

            $grandTotal += $d['total'];
            $row++;
        }

        // Format angka rupiah pada kolom Harga & Total
        $sheet->getStyle('D2:E' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Baris Grand Total
        $sheet->setCellValue('D' . $row, 'Grand Total');
        $sheet->setCellValue('E' . $row, $grandTotal);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto width kolom
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nama file sesuai bulan dipilih
        $namaBulan = date('F-Y', strtotime($bulan . '-01'));
        $filename  = 'Laporan-Transaksi-' . $namaBulan . '.xlsx';

        // Output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
