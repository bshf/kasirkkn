<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom-kolom yang diizinkan untuk diisi/dimanipulasi data-nya
    protected $allowedFields    = ['name', 'price', 'category', 'image_url'];

    // Otomatis mencatat created_at dan updated_at saat data berubah
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
