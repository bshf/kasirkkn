<?php

// app/Config/Filters.php
// Daftarkan AuthFilter ke dalam konfigurasi filter CI4

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /**
     * Daftar alias filter yang tersedia.
     * Tambahkan 'auth' => AuthFilter di sini.
     */
    public array $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'invalidchars'  => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,

        // ── Custom filter ──
        'auth' => \App\Filters\AuthFilter::class,
    ];

    /**
     * Filter yang berjalan sebelum SEMUA request.
     * Kosongkan jika tidak ada filter global.
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => ['except' => ['login/attempt', 'transaction/struk/*']],
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}
