<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    // ─── Halaman login ────────────────────────────────────────────────────
    // GET /login
    public function index()
    {
        // Jika sudah login, redirect langsung ke dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login');
    }

    // ─── Proses login ─────────────────────────────────────────────────────
    // POST /login/attempt
    public function attempt()
    {
        $rules = [
            'username'    => 'required',
            'password' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username atau password tidak valid.',
            ]);
        }

        $username    = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $model = new UserModel();
        $user  = $model->attemptLogin($username, $password);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username atau password salah.',
            ]);
        }

        // Simpan data sesi
        session()->set([
            'is_logged_in' => true,
            'user_id'      => $user['id'],
            'user_name'    => $user['nama'],
            'user_username'   => $user['username'],
            'user_role'    => $user['role'],
        ]);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Login berhasil.',
            'redirect' => base_url('dashboard'),
            'user'     => [
                'nama'  => $user['nama'],
                'role'  => $user['role'],
                'username' => $user['username'],
            ],
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────
    // GET /logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
