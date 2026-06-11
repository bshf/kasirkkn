<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama', 'username','email', 'password', 'role', 'created_at'];

    // Cari user berdasarkan username, verifikasi password MD5
    public function attemptLogin(string $username, string $password): array|false
    {
        $user = $this->where('username', $username)->first();

        if (!$user) {
            return false;
        }

        if ($user['password'] !== md5($password)) {
            return false;
        }

        return $user;
    }
}
