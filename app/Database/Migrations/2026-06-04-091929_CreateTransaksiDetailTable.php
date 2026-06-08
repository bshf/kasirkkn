<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaksi_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'menu_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true, 
            ],
            'qty' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'default'        => 1,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary Key tabel detail

        // Menambahkan Relasi Foreign Key ke tabel transaksi utama
        $this->forge->addForeignKey('transaksi_id', 'transaksi', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('transaksi_detail');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi_detail');
    }
}
