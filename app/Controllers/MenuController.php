<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MenuModel;

class MenuController extends BaseController
{
    protected $menuModel;

    public function __construct()
    {
        // Inisialisasi model menu
        $this->menuModel = new MenuModel();
    }

    // Tampilkan halaman utama views/menu.php dengan membawa data awal
    public function index()
    {
        $data = [
            'activeMenu' => 'catalogue',
            'pageTitle'  => 'Katalog Menu',
            'title'      => 'CashFlow — Menu',
            'menus'      => $this->menuModel->findAll()
        ];
        return view('menu', $data);
    }

    public function get_all_json()
    {
        // Ambil data terbaru dari database
        $menus = $this->menuModel->findAll();

        // Kembalikan langsung dalam bentuk JSON murni
        return $this->response->setJSON($menus);
    }

    // Proses Tambah & Update Menu via AJAX
    public function save()
    {
        // 1. Ambil data input teks biasa
        $id           = $this->request->getPost('id');
        $nama         = $this->request->getPost('nama');
        $harga        = $this->request->getPost('harga');
        $kategori     = $this->request->getPost('kategori');
        $old_image_url = $this->request->getPost('old_image_url'); // file lama jika proses edit


        if (empty($nama) || empty($harga)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama dan Harga wajib diisi.']);
        }

        // 2. Siapkan penampung nama file gambar (default menggunakan file lama jika ada)
        $db_image_name = $old_image_url ?: null;

        // 3. Ambil file gambar dari request
        $imageFile = $this->request->getFile('food_image');

        // Cek apakah user mengupload file baru dan filenya valid
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {

            // Validasi Rule Ekstensi & Ukuran (Maksimal 2MB)
            $validationRule = [
                'food_image' => [
                    'label' => 'Image File',
                    'rules' => [
                        'uploaded[food_image]',
                        'is_image[food_image]',
                        'mime_in[food_image,image/jpg,image/jpeg,image/png]',
                        'max_size[food_image,2048]',
                    ],
                ],
            ];

            if (!$this->validate($validationRule)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validasi gagal: Pastikan file berupa gambar (JPG/PNG) dan max 2MB.'
                ]);
            }

            // Generate nama unik acak untuk file gambar baru
            $newImageName = $imageFile->getRandomName();

            /**
             * Pindahkan file ke folder: ROOT/public/uploads/menu/
             * Kita gunakan ROOTPATH . 'public/uploads/menu' agar tersimpan di direktori publik 
             * sehingga bisa dipanggil dengan fungsi base_url() oleh browser.
             */
            $imageFile->move(ROOTPATH . 'public/uploads/menu', $newImageName);

            // Hapus file foto lama di folder jika prosesnya adalah UPDATE/EDIT
            if (!empty($id) && !empty($old_image_url)) {
                $oldFilePath = ROOTPATH . 'public/uploads/menu/' . $old_image_url;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Menghapus file fisik lama
                }
            }

            // Set nama file baru untuk disimpan ke kolom 'image_url' di database
            $db_image_name = $newImageName;
        }

        // 4. Susun array data untuk database
        $dataSave = [
            'nama'      => $nama,
            'harga'     => $harga,
            'kategori'  => $kategori,
            'image_url' => $db_image_name
        ];

        // Inisialisasi model
        $menuModel = new \App\Models\MenuModel();

        // var_dump($id, $db_image_name);
        // die;
        if (empty($id)) {
            // INSERT DATA BARU
            $menuModel->insert($dataSave);
            $insertedId = $menuModel->getInsertID();

            return $this->response->setJSON([
                'status'    => 'success',
                'action'    => 'insert',
                'id'        => $insertedId,
                'image_url' => $db_image_name ? base_url('uploads/menu/' . $db_image_name) : null,
                'raw_name'  => $db_image_name, // simpan untuk data-attribute tombol edit
                'message'   => 'Menu item added successfully!'
            ]);
        } else {
            // UPDATE DATA LAMA
            $menuModel->update($id, $dataSave);

            return $this->response->setJSON([
                'status'    => 'success',
                'action'    => 'update',
                'id'        => $id,
                'image_url' => $db_image_name ? base_url('uploads/menu/' . $db_image_name) : null,
                'raw_name'  => $db_image_name,
                'message'   => 'Menu item updated successfully!'
            ]);
        }
    }

    // Proses Hapus Menu via AJAX
    public function delete($id)
    {
        $menuModel = new \App\Models\MenuModel();
        $menu = $menuModel->find($id);

        if ($menu) {
            // Hapus file gambar jika ada di folder storage
            if (!empty($menu['image_url'])) {
                $filePath = ROOTPATH . 'public/uploads/menu/' . $menu['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $menuModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Item deleted.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Not found.'], 404);
    }
}
