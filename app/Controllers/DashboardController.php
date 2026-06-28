<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Tempat Anda memproses logika analitik statistik dashboard nantinya
        $data = [
            'activeMenu' => 'dashboard',
            'pageTitle'  => 'Dashboard Overview',
            'title'      => 'CashFlow — Overview Dashboard'
        ];

        return view('dashboard', $data);
    }

    public function get_stats()
    {
        $db    = \Config\Database::connect();
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Revenue & order count hari ini
        $todayRow = $db->table('transaksi')
            ->selectSum('total', 'revenue')
            ->selectCount('id', 'orders')
            ->where('DATE(tanggal)', $today)
            ->get()->getRowArray();

        // Revenue & order count kemarin (untuk perbandingan %)
        $yestRow = $db->table('transaksi')
            ->selectSum('total', 'revenue')
            ->selectCount('id', 'orders')
            ->where('DATE(tanggal)', $yesterday)
            ->get()->getRowArray();

        $revToday = (float)($todayRow['revenue']  ?? 0);
        $revYest  = (float)($yestRow['revenue']   ?? 0);
        $ordToday = (int)  ($todayRow['orders']   ?? 0);
        $ordYest  = (int)  ($yestRow['orders']    ?? 0);

        // Hitung persentase perubahan revenue; hindari pembagian 0
        $revDiff = $revYest > 0
            ? round((($revToday - $revYest) / $revYest) * 100, 1)
            : ($revToday > 0 ? 100.0 : 0.0);

        $ordDiff = $ordToday - $ordYest;

        // Total produk aktif
        $totalProducts = $db->table('menu')->countAllResults();

        $terlaris = $db->table('transaksi_detail td')
            ->select('menu.nama as nama_menu, SUM(td.qty) as total_qty')
            ->join('transaksi t', 't.id = td.transaksi_id')
            ->join('menu', 'menu.id = td.menu_id', 'left')
            ->where('DATE(t.tanggal)', date('Y-m-d'))
            ->groupBy('menu.nama')
            ->orderBy('total_qty', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'revenue_today'   => $revToday,
            'revenue_diff'    => $revDiff,       // % (bisa negatif)
            'orders_today'    => $ordToday,
            'orders_diff'     => $ordDiff,       // selisih jumlah
            'total_products'  => $totalProducts,
            'terlaris' => $terlaris,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /dashboard/get_weekly_chart
    // Mengembalikan: total revenue per hari untuk 7 hari terakhir
    // ─────────────────────────────────────────────────────────────────────
    public function get_weekly_chart()
    {
        $db   = \Config\Database::connect();
        $days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date  = date('Y-m-d', strtotime("-$i days"));
            $label = date('D', strtotime($date)); // Mon, Tue, …

            $row = $db->table('transaksi')
                ->selectSum('total', 'total')
                ->where('DATE(tanggal)', $date)
                ->get()->getRowArray();

            $days[] = [
                'day'   => $label,
                'date'  => $date,
                'total' => (float)($row['total'] ?? 0),
            ];
        }

        return $this->response->setJSON($days);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /dashboard/get_payment_breakdown
    // Mengembalikan: persentase cash vs qris hari ini + top 3 kategori menu
    // ─────────────────────────────────────────────────────────────────────
    public function get_payment_breakdown()
    {
        $db    = \Config\Database::connect();
        $today = date('Y-m-d');
        $month = date('m');

        // Hitung per metode pembayaran
        $rows = $db->table('transaksi')
            ->select('payment_via, COUNT(id) as jumlah')
            ->where('MONTH(tanggal)', $month)
            ->groupBy('payment_via')
            ->get()->getResultArray();

        $total = array_sum(array_column($rows, 'jumlah'));
        $breakdown = [];
        foreach ($rows as $r) {
            $key = strtolower($r['payment_via']); // 'cash' | 'qris'
            $breakdown[$key] = [
                'count' => (int)$r['jumlah'],
                'pct'   => $total > 0 ? round(($r['jumlah'] / $total) * 100) : 0,
            ];
        }

        // Top 3 kategori menu berdasarkan revenue hari ini
        // Asumsi tabel: menu(id, nama, kategori, harga), transaksidetail(transaksi_id, menu_id, qty)
        $topCats = $db->table('transaksi_detail td')
            ->select('m.kategori, SUM(m.harga * td.qty) as total')
            ->join('menu m',       'm.id = td.menu_id',       'left')
            ->join('transaksi t',  't.id = td.transaksi_id',  'left')
            ->where('DATE(t.tanggal)', $today)
            ->groupBy('m.kategori')
            ->orderBy('total', 'DESC')
            ->limit(3)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'cash'           => $breakdown['cash']  ?? ['count' => 0, 'pct' => 0],
            'qris'           => $breakdown['qris']  ?? ['count' => 0, 'pct' => 0],
            'top_categories' => $topCats,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /dashboard/get_recent_transactions
    // Mengembalikan: 5 transaksi terbaru hari ini
    // ─────────────────────────────────────────────────────────────────────
    public function get_recent_transactions()
    {
        $db    = \Config\Database::connect();
        $today = date('Y-m-d');

        $list = $db->table('transaksi')
            ->select('id, tanggal, nama, level, total, payment_via')
            ->where('DATE(tanggal)', $today)
            ->orderBy('tanggal', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return $this->response->setJSON($list);
    }
}
