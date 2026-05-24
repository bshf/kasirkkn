<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Item Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h2 class="h4 font-weight-bold text-dark mb-1">Food Catalog Management</h2>
            <p class="text-muted small mb-0">Manage menu listings, availability status, and baseline pricing configurations.</p>
        </div>
        <button class="btn btn-primary btn-sm px-3 py-2 mt-2 mt-sm-0 rounded-lg font-weight-bold shadow-sm" id="btn-add-food">
            <i class="fas fa-plus mr-1"></i> Add New Food
        </button>
    </div>

    <div class="row" id="food-list-container">
        <?php if (!empty($menus) && is_array($menus)): ?>
            <?php foreach ($menus as $menu): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4 food-card-item" id="food-card-<?= $menu['id'] ?>">
                    <div class="card h-100 border-light shadow-sm rounded-lg overflow-hidden bg-white">
                        <div style="height: 160px; background-color: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <?php if (!empty($menu['image_url'])): ?>
                                <img src="<?= esc($menu['image_url']) ?>" alt="<?= esc($menu['nama']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas <?= $menu['kategori'] == 'minuman' ? 'fa-glass-martini-alt' : 'fa-hamburger' ?> fa-3x text-muted opacity-50"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column">
                            <h5 class="card-title font-weight-bold text-dark mb-1 food-name" style="font-size: 1rem;"><?= esc($menu['nama']) ?></h5>
                            <p class="card-text text-primary font-weight-bold mb-3 food-price">$<?= number_format($menu['harga'], 2) ?></p>
                            <div class="mt-auto d-flex">
                                <button class="btn btn-sm btn-outline-secondary btn-block mr-1 btn-edit-food"
                                    data-id="<?= $menu['id'] ?>"
                                    data-kategori="<?= $menu['kategori'] ?>"
                                    data-image="<?= esc($menu['image_url']) ?>">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger m-0 btn-delete-food" data-id="<?= $menu['id'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5" id="empty-message">
                <p class="text-muted">No items available in the food catalog.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<div class="modal fade" id="foodModal" tabindex="-1" role="dialog" aria-labelledby="foodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0 p-3">
                <h5 class="modal-title font-weight-bold text-dark" id="foodModalLabel">Add New Food</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="foodForm" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" id="food-id">

                    <div class="form-group mb-3">
                        <label for="food-image" class="small font-weight-bold text-muted text-uppercase">Upload Food Picture</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 text-muted"><i class="fas fa-image"></i></span>
                            </div>
                            <input type="file" class="form-control border-left-0" id="food-image" name="food_image" accept="image/*">
                        </div>
                        <small class="text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>

                        <input type="hidden" id="old-image-url" name="old_image_url">
                    </div>

                    <div class="form-group mb-3">
                        <label for="food-name-input" class="small font-weight-bold text-muted text-uppercase">Nama</label>
                        <input type="text" class="form-control" name="nama" id="food-name-input" placeholder="e.g. Premium Beef Burger" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="food-price-input" class="small font-weight-bold text-muted text-uppercase">Harga (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 text-muted">Rp</span>

                                <input type="number" step="0.01" name="harga" class="form-control border-left-0" id="food-price-input" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="kategori-input" class="small font-weight-bold text-muted text-uppercase">Kategori</label>
                        <select class="form-control" name="kategori">
                            <option value="makanan">MAKANAN</option>
                            <option value="minuman">MINUMAN</option>
                        </select>
                    </div>
                    <div class="modal-footer bg-light border-top-0 p-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-lg" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 rounded-lg font-weight-bold shadow-sm" id="btn-save-food">Save Changes</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {

        // Tombol Add Klik
        $('#btn-add-food').on('click', function() {
            $('#foodForm')[0].reset();
            $('#food-id').val('');
            $('#old-image-url').val(''); // Kosongkan pelacak foto lama
            $('#foodModalLabel').text('Add New Food');
            $('#btn-save-food').text('Add to Catalog');
            $('#foodModal').modal('show');
        });

        // Tombol Edit Klik
        $(document).on('click', '.btn-edit-food', function() {
            var card = $(this).closest('.food-card-item');
            var foodId = $(this).data('id');
            var category = $(this).data('category');
            var rawImageName = $(this).data('image'); // Nama file mentah database
            var currentName = card.find('.food-name').text();
            var currentPrice = card.find('.food-price').text().replace('$', '').trim();

            $('#food-id').val(foodId);
            $('#food-name-input').val(currentName);
            $('#food-price-input').val(currentPrice);
            $('#kategori-input').val(category);
            $('#old-image-url').val(rawImageName); // Set penampung nama file lama untuk backend
            $('#food-image').val(''); // Reset input file pencarian (selalu kosong saat awal edit)

            $('#foodModalLabel').text('Edit Food Item');
            $('#btn-save-food').text('Save Changes');
            $('#foodModal').modal('show');
        });

        // Submit Form (Koneksi ke backend query insert/update database Anda di sini)
        $('#foodForm').on('submit', function(e) {
            e.preventDefault();

            // Bungkus seluruh element form ke dalam objek FormData otomatis
            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('menu/save') ?>',
                type: 'POST',
                data: formData, // Mengirimkan objek FormData berisi file biner gambar
                dataType: 'json',
                contentType: false, // WAJIB: mematikan set tipe konten bawaan jQuery
                processData: false, // WAJIB: melarang jQuery mengubah data menjadi query string
                success: function(response) {
                    if (response.status === 'success') {
                        $('#empty-message').remove();

                        // Bentuk tag gambar baru berdasarkan respon link dari server
                        var imgTag = response.image_url ?
                            `<img src="${response.image_url}" alt="${formData.get('name')}" style="width: 100%; height: 100%; object-fit: cover;">` :
                            `<i class="fas ${formData.get('category') === 'minuman' ? 'fa-glass-martini-alt' : 'fa-hamburger'} fa-3x text-muted opacity-50"></i>`;

                        if (response.action === 'insert') {
                            // Render Card Baru
                            var newCardHtml = `
                            <div class="col-6 col-md-4 col-lg-3 mb-4 food-card-item" id="food-card-${response.id}">
                                <div class="card h-100 border-light shadow-sm rounded-lg overflow-hidden bg-white">
                                    <div class="card-img-wrapper" style="height: 160px; background-color: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        ${imgTag}
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h5 class="card-title font-weight-bold text-dark mb-1 food-name" style="font-size: 1rem;">${formData.get('name')}</h5>
                                        <p class="card-text text-primary font-weight-bold mb-3 food-price">$${parseFloat(formData.get('price')).toFixed(2)}</p>
                                        <div class="mt-auto d-flex">
                                            <button class="btn btn-sm btn-outline-secondary btn-block mr-1 btn-edit-food" data-id="${response.id}" data-category="${formData.get('category')}" data-image="${response.raw_name}">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger m-0 btn-delete-food" data-id="${response.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                            $('#food-list-container').append(newCardHtml);
                        } else {
                            // Update Card Lama
                            var card = $('#food-card-' + formData.get('id'));
                            card.find('.food-name').text(formData.get('name'));
                            card.find('.food-price').text('$' + parseFloat(formData.get('price')).toFixed(2));
                            card.find('.card-img-wrapper').html(imgTag); // Ubah gambar secara instan

                            // Perbarui data-attribute tombol edit dengan value baru dari server
                            var editBtn = card.find('.btn-edit-food');
                            editBtn.data('category', formData.get('category'));
                            editBtn.data('image', response.raw_name);
                        }

                        $('#foodModal').modal('hide');
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan jaringan/sistem backend saat memproses upload.');
                }
            });
        });

        // Tombol Delete Klik
        $(document).on('click', '.btn-delete-food', function() {
            var id = $(this).data('id');
            var cardItem = $(this).closest('.food-card-item');

            if (confirm("Are you sure you want to delete this food item?")) {
                $.ajax({
                    url: '<?= base_url('menu/delete') ?>/' + id,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            cardItem.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Gagal menghapus data dari server.');
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>