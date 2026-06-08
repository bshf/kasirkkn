<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiTable extends Migration
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
            'tanggal' => [
                'type'           => 'DATE',
                'null'           => false,
            ],
            'nama' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true, // Diizinkan null jika di kasir tidak mengisi nama pelanggan
            ],
            'level' => [
                'type'           => 'INT',
                'constraint'     => '2',
                'null'           => true,
            ],
            'payment_via' => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'null'           => false, // e.g. Cash, QRIS, Transfer
            ],
            'total' => [
                'type'           => 'FLOAT',
                'unsigned'       => true,
                'default'        => 0,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Menjadikan id sebagai Primary Key
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}
