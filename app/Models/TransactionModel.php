<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = ['tanggal', 'nama', 'level', 'payment_via', 'total', 'bayar'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDataExport($bulan)
    {
        $db = \Config\Database::connect();
        return $db->table('transaksi t')
            ->select('t.tanggal, menu.nama as item, td.qty, menu.harga, (td.qty * menu.harga) as total')
            ->join('transaksi_detail td', 'td.transaksi_id = t.id')
            ->join('menu', 'menu.id = td.menu_id', 'left')
            ->where('DATE_FORMAT(t.tanggal, "%Y-%m")', $bulan)
            ->orderBy('t.tanggal', 'ASC')
            ->get()
            ->getResultArray();
    }
}
