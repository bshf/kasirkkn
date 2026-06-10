<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\TransactionModel;
use Config\Database;

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
        // Ambil data terbaru dari database
        $transactions = $this->transaksiModel
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();

        // Kembalikan langsung dalam bentuk JSON murni
        return $this->response->setJSON($transactions);
    }
    public function save()
    {
        // 1. Tangkap payload kiriman JSON dari AJAX
        $json = $this->request->getJSON(true); // true mengubahnya menjadi bentuk Array Assoc PHP

        if (empty($json) || empty($json['items'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data transaksi atau keranjang kosong.']);
        }

        // 2. Koneksikan ke database builder
        $db = Database::connect();

        // Mulai transaksi database (Mencegah data parsial jika di tengah jalan terjadi error)
        $db->transStart();

        // 3. Insert ke tabel 'transaksi' utama
        $dataTransaksi = [
            'tanggal'     => date('Y-m-d'),
            'nama'        => $json['nama'],
            'level'       => $json['level'],
            'payment_via' => $json['payment_via'],
            'total'       => $json['total'],
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $db->table('transaksi')->insert($dataTransaksi);

        // Ambil ID utama yang baru saja ter-generate otomatis oleh database
        $transaksiId = $db->insertID();

        // 4. Looping untuk insert massal ke tabel 'transaksi_detail'
        foreach ($json['items'] as $item) {
            $dataDetail = [
                'transaksi_id' => $transaksiId,
                'menu_id'      => $item['menu_id'],
                'qty'          => $item['qty']
            ];

            $db->table('transaksi_detail')->insert($dataDetail);
        }

        // Selesaikan instruksi transaksi database
        $db->transComplete();

        // 5. Berikan respon balik ke AJAX status akhir eksekusi database
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan transaksi ke database.'
            ]);
        }

        return $this->response->setJSON([
            'status'       => 'success',
            'transaksi_id' => $transaksiId,
            'message'      => 'Transaksi berhasil disimpan!'
        ]);
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
}
