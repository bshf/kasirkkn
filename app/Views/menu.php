<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page active" id="page-catalogue">
    <div class="section-header">
        <div>
            <h2>Katalog</h2>
        </div>
        <button class="btn-accent" data-bs-toggle="modal" data-bs-target="#addProductModal" onclick="prepareAddModal()">
            <i class="fa-solid fa-plus me-1"></i>Add Product
        </button>
    </div>

    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
        <input class="cf-input" style="max-width:220px" type="text" placeholder="🔍  Search products…" id="productSearch" />
    </div>

    <div class="row g-3" id="productGrid"></div>

    <!-- Tambahkan modal AddProductModal di sini khusus halaman catalogue -->

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-box me-2" style="color:var(--accent)"></i>Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="crudForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" id="prodId" name="id">
                            <div class="col-6">
                                <label class="cf-label">Nama Menu</label>
                                <input class="cf-input" id="newProdName" type="text" name="nama" placeholder="e.g. Chikuwa" />
                            </div>
                            <div class="col-6">
                                <label class="cf-label">Kategori</label>
                                <select class="cf-input" id="newProdCat" name="kategori">
                                    <option value="makanan">Makanan</option>
                                    <option value="minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="cf-label">Harga (Rp)</label>
                                <input class="cf-input" id="newProdPrice" type="text" placeholder="2.000" name="harga" required />
                            </div>
                            <div class="col-12">
                                <label class="cf-label">Gambar Produk</label>
                                <input type="hidden" name="old_image_url" id="oldImageUrl">
                                <div id="currentImageWrapper" class="mb-2 d-none">
                                    <p class="cf-label" style="font-size: 0.7rem; color: var(--muted);">Gambar saat ini:</p>
                                    <div style="width: 100px; height: 100px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
                                        <img id="currentProductImg" src="" alt="Current Image" style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                </div>
                                <input class="cf-input" id="newProdImage" name="food_image" type="file" accept="image/*" />
                                <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah/menambahkan gambar.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-accent" id="btnSaveProduct"><i class="fa-solid fa-plus me-1"></i>Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Ambil data asli dari database yang dilempar dari MenuController::index
    // Jika data kosong, fallback ke array kosong
    let products = <?= json_encode($menus ?? []) ?>;

    // Jalankan render pertama kali saat halaman dimuat
    $(document).ready(function() {
        renderProducts();

        // Gabungkan submit form ke handler jQuery agar lebih aman daripada onclick biasa
        $('#crudForm').on('submit', function(e) {
            e.preventDefault();
            saveProduct();
        });
        $('#newProdPrice').on('input', function() {
            // 1. Ambil nilai input, hapus semua karakter non-angka
            let value = $(this).val().replace(/[^0-9]/g, '');

            // 2. Jika kosong, biarkan kosong
            if (value === '') {
                $(this).val('');
                return;
            }

            // 3. Format angka dengan pemisah ribuan titik (.)
            let formattedValue = parseInt(value).toLocaleString('id-ID');

            // 4. Masukkan kembali hasil format ke dalam input
            $(this).val(formattedValue);
        });
    });

    // Menampilkan daftar produk ke dalam Grid HTML
    function renderProducts(filter = '') {
        const grid = document.getElementById('productGrid');
        if (!grid) return;

        const list = products.filter(p => p.nama.toLowerCase().includes(filter.toLowerCase()));

        if (list.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted py-5">No products found.</div>';
            return;
        }

        grid.innerHTML = list.map(p => {
            // Tentukan sumber gambar: jika ada di DB pasang full URL, jika tidak gunakan placeholder
            const imgSrc = p.image_url ? '<?= base_url("uploads/menu/") ?>/' + p.image_url : 'https://placehold.co/150x150?text=No+Image';

            return `
            <div class="col-6 col-md-4 col-xl-3" id="product-card-${p.id}">
                <div class="product-card">
                    <div class="prod-img-wrapper" style="width:100%; height:150px; overflow:hidden; background:var(--surface);">
                        <img src="${imgSrc}" alt="${p.nama}" style="width:100%; height:100%; object-fit:cover; object-position:center;" loading="lazy" />
                    </div>
                    <div class="product-body">
                        <div class="product-name">${p.nama}</div>
                        <div class="product-cat" style="text-transform: capitalize;">${p.kategori}</div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-end">
                            <div>
                                <div class="product-price">${fmt(p.harga)}</div>
                            </div>
                            <div class="product-actions" style="display:flex; gap:6px;">
                                <button class="btn-sm-icon" title="Edit" onclick="editProduct(${p.id})"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn-sm-icon danger" style="color:var(--danger); border-color:rgba(240,82,82,0.2);" title="Delete" onclick="deleteProduct(${p.id})"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // Event listener untuk pencarian produk secara realtime
    $('#productSearch').on('input', function() {
        renderProducts($(this).val());
    });

    // Fungsi untuk mengambil data terbaru dari database via AJAX tanpa reload halaman
    function fetchProducts() {
        $.ajax({
            url: '<?= base_url("menu/get_all_json") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // 1. Perbarui array lokal dengan data terbaru dari database
                products = data;

                // 2. Render ulang grid produk secara realtime menggunakan data terbaru
                // Serta pertahankan kata kunci pencarian yang sedang diketik jika ada
                const currentSearch = $('#productSearch').val() || '';
                renderProducts(currentSearch);
            },
            error: function(xhr) {
                console.error('Gagal mengambil data terbaru:', xhr.responseText);
            }
        });
    }

    // Mengondisikan Modal untuk mode TAMBAH BARU
    function prepareAddModal() {
        $('#crudForm')[0].reset();
        $('#prodId').val('');
        $('#oldImageUrl').val('');

        $('#currentImageWrapper').addClass('d-none');
        $('#currentProductImg').attr('src', '');
        $('#modalTitle').html('<i class="fa-solid fa-box me-2" style="color:var(--accent)"></i>Add New Product');
        $('#btnSaveProduct').html('<i class="fa-solid fa-plus me-1"></i>Save Product');
    }

    // Mengondisikan Modal untuk mode EDIT/UPDATE DATA
    function editProduct(id) {
        // Cari data item dari array lokal berdasarkan ID
        const item = products.find(p => p.id == id);
        if (!item) return;

        // Isi form modal dengan data lama yang siap diedit
        $('#prodId').val(item.id);
        $('#oldImageUrl').val(item.image_url || ''); // Menerima parameter old_image_url di Controller
        $('#newProdName').val(item.nama);
        $('#newProdCat').val(item.kategori);
        let formattedPrice = parseInt(item.harga).toLocaleString('id-ID');
        $('#newProdPrice').val(formattedPrice);
        $('#newProdImage').val(''); // Reset input file pilihan baru
        if (item.image_url) {
            // Gabungkan dengan base_url folder upload Anda
            const imgSrc = '<?= base_url("uploads/menu/") ?>/' + item.image_url;
            $('#currentProductImg').attr('src', imgSrc);
            $('#currentImageWrapper').removeClass('d-none'); // Munculkan div gambar
        } else {
            // Jika di DB tidak ada gambar, sembunyikan div pratinjau
            $('#currentImageWrapper').addClass('d-none');
            $('#currentProductImg').attr('src', '');
        }
        // Ubah teks judul modal dan teks tombol simpan
        $('#modalTitle').html('<i class="fa-solid fa-pen-to-square me-2" style="color:var(--accent)"></i>Edit Product');
        $('#btnSaveProduct').html('<i class="fa-solid fa-floppy-disk me-1"></i>Update Product');

        // Munculkan modal secara terprogram
        const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
        modal.show();
    }

    // Proses Pengiriman Data AJAX Terpadu (Insert & Update)
    function saveProduct() {
        const name = $('#newProdName').val().trim();
        const priceRaw = $('#newProdPrice').val().replace(/[^0-9]/g, '');
        const price = parseInt(priceRaw) || 0;

        if (!name || !price) {
            toast('Please fill in name and price.');
            return;
        }

        // Membuat objek FormData otomatis mendeteksi input name="nama", name="kategori", name="harga", name="id", name="old_image_url", dan name="food_image"
        let formData = new FormData($('#crudForm')[0]);
        formData.set('harga', price);
        $.ajax({
            url: '<?= base_url("menu/save") ?>', // Mengarah ke MenuController::save
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                $('#btnSaveProduct').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Tutup Bootstrap Modal
                    const modalEl = document.getElementById('addProductModal');
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    toast(response.message);

                    // ── PERBAIKAN: Reset form dan ambil data terbaru tanpa reload halaman ──
                    $('#crudForm')[0].reset();
                    fetchProducts();
                } else {
                    toast('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                toast('Failed to save product. Server error.');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#btnSaveProduct').prop('disabled', false).html('<i class="fa-solid fa-plus me-1"></i>Save Product');
            }
        });
    }

    // Fungsi Hapus Produk via AJAX Terhubung ke Controller delete()
    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '<?= base_url("menu/delete") ?>/' + id,
                type: 'DELETE', // Atau POST sesuai kecocokan routing sistem Anda
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toast(response.message);

                        fetchProducts();
                    } else {
                        toast('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    toast('Failed to delete item.');
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>