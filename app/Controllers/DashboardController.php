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
}