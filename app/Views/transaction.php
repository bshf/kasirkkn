<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page active" id="page-transaction">
    <div class="section-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="header-title">
            <h2 class="text-uppercase m-0 fw-bold">Transaksi</h2>
            <p class="text-muted text-uppercase small m-0 mt-1">Buat dan kelola transaksi penjualan</p>
        </div>

        <div class="header-actions d-flex flex-row flex-wrap gap-2 w-100 w-md-auto">
            <button class="btn-accent flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fa-solid fa-cart-plus me-1"></i> Tambah Transaksi
            </button>
            <button class="btn-accent flex-grow-1 flex-md-grow-0" onclick="printerConnection.connect().then(() => alert('Printer terhubung!')).catch(e => alert(e.message))">
                <i class="fa-solid fa-rss me-1"></i> Hubungkan Printer
            </button>
        </div>
    </div>


    <!-- Transaction History -->
    <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
        <input type="date" id="filterTanggal" value="<?= date('Y-m-d'); ?>" class="form-control" style="width: 180px;">
    </div>
    <div class="table-card mt-4">
        <div class="table-header">
            <h5>Riwayat Transaksi</h5>
            <div style="display:flex;gap:8px">
                <input class="cf-input" id="txnSearch" style="max-width:180px;padding:7px 12px" type="text" placeholder="Search…" />
                <button class="btn-ghost" style="font-size:.75rem"><i class="fa-solid fa-filter me-1"></i>Filter</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="txnHistoryBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-cart-plus me-2" style="color:var(--accent)"></i>Select Products
                    &amp; Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-dark-subtle">
                <div class="row h-100 g-3">
                    <!-- Left side: Products search and grid selection -->
                    <div class="col-lg-8 d-flex flex-column h-100">
                        <div class="mb-3">
                            <input class="cf-input" type="text" id="modalSearch" placeholder="Search products by name…" />
                        </div>
                        <div class="prod-select-grid flex-grow-1" id="prodSelectGrid"></div>
                    </div>

                    <!-- Right side: Running invoice summary (No Payment Method - Scrollable Items List) -->
                    <div
                        class="col-lg-4 d-flex flex-column justify-content-between border-start border-secondary-subtle ps-lg-4">
                        <div>
                            <p class="form-section-title">Order </p>
                            <div class="mb-3">
                                <label class="cf-label">Nama Customer</label>
                                <input class="cf-input" id="customerName" />
                            </div>

                            <div class="mb-4">
                                <label class="cf-label">Pembayaran</label>
                                <select class="cf-input" id="pembayaran" name="pembayaran">
                                    <option value="CASH">CASH</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-auto d-flex flex-column" style="min-height: 0;">
                            <p class="form-section-title">Invoice Summary</p>

                            <!-- Scrollable Product Items List inside Invoice Summary -->
                            <div class="summary-active-items flex-grow-1" id="sumActiveItems">
                                <!-- Dynamically filled with products having quantity > 0 -->
                            </div>

                            <div class="summary-row total mb-3"><span>Total</span><span class="val" id="sumTotal">Rp 0</span></div>
                            <div class="mb-3">
                                <label class="cf-label">Bayar</label>
                                <input class="cf-input" id="bayar" placeholder="0" style="text-align: right;" />
                            </div>
                            <div class="summary-row total mb-3"><span>Kembalian</span><span class="val" id="kembalian">Rp 0</span></div>
                            <div class="d-flex gap-2">
                                <button class="btn-ghost w-50" onclick="clearCart()" style="padding:10px">
                                    <i class="fa-solid fa-trash me-1"></i>Clear
                                </button>
                                <button class="btn-accent w-50" onclick="processPayment()" style="padding:12px;font-size:.9rem">
                                    <i class="fa-solid fa-credit-card me-2"></i>Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-circle-check me-2" style="color:var(--accent2)"></i>Payment
                    Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="receipt" id="receiptContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn-ghost" data-bs-dismiss="modal">Close</button>
                <button class="btn-accent" onclick="printReceipt()"><i class="fa-solid fa-print me-1"></i>Print
                    Receipt</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <i class="fa-solid fa-triangle-exclamation text-warning mb-3" style="font-size: 3rem;"></i>
                <h5>Hapus Transaksi?</h5>
                <p class="small">Apakah Anda yakin ingin membatalkan transaksi ini?</p>
            </div>
            <div class="d-flex gap-2 px-3 pb-2">
                <button type="button" class="btn-ghost w-50" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-accent w-50 bg-danger border-danger" id="btnExecuteDelete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<p id="printStatus" style="font-size:.8rem;color:#777;"></p>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/@point-of-sale/receipt-printer-encoder@latest/dist/receipt-printer-encoder.umd.js"></script>
<script>
    let cart = [];
    let txnCounter = 6;
    let products = <?= json_encode($menus ?? []) ?>;
    let transactions = [];

    $(document).ready(function() {
        renderProductSelector()
        fetchTransactionHistory();
        $('#txnSearch').on('input', function() {
            const q = $(this).val().trim().toLowerCase();
            const filtered = q ?
                transactions.filter(t =>
                    (t.tanggal || '').toLowerCase().includes(q) ||
                    (t.nama || '').toLowerCase().includes(q) ||
                    (t.payment_via || '').toLowerCase().includes(q) ||
                    String(t.total || '').includes(q)
                ) :
                transactions;
            renderTxnHistoryFromDB(filtered, q);
        });
    })
    $('#filterTanggal').on('change', function() {
        fetchTransactionHistory();
    });
    $('#bayar').on('input', function() {
        kembalian();
    });

    function kembalian() {
        let totalText = $('#sumTotal').text();
        let total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
        let bayar = parseInt($('#bayar').val()) || 0;
        let kembalian = bayar - total;
        if (kembalian < 0) {
            kembalian = 0;
        }

        $('#kembalian').text('Rp ' + kembalian.toLocaleString('id-ID'));
    }

    function kembalian() {
        let inputVal = $('#bayar').val().replace(/[^0-9]/g, '');

        let bayar = parseInt(inputVal) || 0;

        if (inputVal !== '') {
            $('#bayar').val(bayar.toLocaleString('id-ID'));
        } else {
            $('#bayar').val('');
        }
        let totalText = $('#sumTotal').text();
        let total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
        let kembalian = bayar - total;
        if (kembalian < 0) {
            kembalian = 0;
        }
        $('#kembalian').text('Rp ' + kembalian.toLocaleString('id-ID'));
    }
    $('#modalSearch').on('input', function() {
        renderProductSelector($(this).val().trim());
    });
    $('#addItemModal').on('shown.bs.modal', () => {
        $('#modalSearch').val('');
        renderProductSelector();
    })

    function renderProductSelector(filter = '') {
        const grid = document.getElementById('prodSelectGrid');
        if (!grid) return;

        // Filter pencarian berdasarkan kolom 'nama'
        const list = products.filter(p => p.nama.toLowerCase().includes(filter.toLowerCase()));

        if (list.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted py-3">Menu tidak ditemukan.</div>';
            return;
        }

        grid.innerHTML = list.map(p => {
            // Cek ketersediaan gambar, jika tidak ada gunakan placeholder
            const cartItem = cart.find(c => c.id === p.id);
            const qty = cartItem ? cartItem.qty : 0;
            const imgSrc = p.image_url ? '<?= base_url("uploads/menu/") ?>/' + p.image_url : 'https://placehold.co/150x150?text=No+Image';

            return `
                <div class="prod-select-item" data-id="${p.id}">
                    <div class="prod-img-wrapper">
                        <img src="${imgSrc}" alt="${p.nama}" loading="lazy" />
                    </div>
                    <div class="p-name">${p.nama}</div>
                    <div class="p-price">${fmt(p.harga)}</div>
                    <div class="qty-ctrl mt-2">
                    <button class="qty-btn" ${viewMode ? 'disabled style="opacity:.4"' : ''} onclick="modifyTransaction(${p.id}, -1)">−</button>
                    <span class="qty-num text-dark" id="modal-qty-${p.id}">${qty}</span>
                    <button class="qty-btn" ${viewMode ? 'disabled style="opacity:.4"' : ''} onclick="modifyTransaction(${p.id}, 1)">+</button>
                    </div>
                </div>`;
        }).join('');
    }

    function modifyTransaction(transId, change) {
        console.log('disdiasd');
        if (viewMode) return; // blokir perubahan saat mode view
        const product = products.find(p => p.id == transId);
        if (!product) return;

        const existing = cart.find(c => c.id == transId);
        if (existing) {
            existing.qty += change;
            if (existing.qty <= 0) {
                cart = cart.filter(c => c.id != transId);
            }
        } else if (change > 0) {
            cart.push({
                ...product,
                qty: 1,
                note: ''
            });
        }

        // Update counter display inside modal grid directly
        const finalItem = cart.find(c => c.id == transId);
        $(`#modal-qty-${transId}`).text(finalItem ? finalItem.qty : 0);

        updateSummary();
    }

    function renderCart() {
        if (cart.length == 0) {
            $('#cartEmpty').show();
            $('#cartWrapper').hide();
            return;
        }
        $('#cartEmpty').hide();
        $('#cartWrapper').show();
        $('#cartBody').html(cart.map((item, i) => `
            <tr>
            <td>
                <span style="margin-right:6px">${item.emoji}</span>
                <span style="font-weight:600">${item.name}</span>
            </td>
            <td style="color:var(--muted)">${fmt(item.price)}</td>
            <td>
                <div class="qty-ctrl">
                <button class="qty-btn" onclick="changeCartQty(${i},-1)">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="changeCartQty(${i},1)">+</button>
                </div>
            </td>
            <td style="font-weight:600">${fmt(item.price * item.qty)}</td>
            <td><button class="btn-sm-icon danger" onclick="removeFromCart(${i})"><i class="fa-solid fa-xmark"></i></button></td>
            </tr>`).join(''));
    }

    function changeCartQty(index, change) {
        const item = cart[index];
        item.qty += change;
        if (item.qty <= 0) {
            cart.splice(index, 1);
        }
        renderProductSelector($('#modalSearch').val());
        updateSummary();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderProductSelector($('#modalSearch').val());
        updateSummary();
    }

    function clearCart() {
        cart = [];
        renderProductSelector($('#modalSearch').val());
        updateSummary();
        toast('Cart cleared.');
    }

    function updateSummary() {
        const subtotal = cart.reduce((s, c) => s + c.harga * c.qty, 0);
        const total = subtotal;

        // Render scrollable breakdown in Invoice Summary
        const activeItemsDiv = document.getElementById('sumActiveItems');
        if (cart.length === 0) {
            activeItemsDiv.innerHTML = `<div style="text-align:center;color:var(--muted);font-size:.78rem;padding:10px 0;">No items selected</div>`;
        } else {
            activeItemsDiv.innerHTML = cart.map(c => `
            <div class="active-item-row">
                <div>
                <span class="qty-badge">x${c.qty}</span>
                <span style="font-weight:500;color:var(--text)"> ${c.nama}</span>
                </div>
                <span style="font-weight:500;color:var(--text)">${fmt(c.harga * c.qty)}</span>
            </div>
            `).join('');
        }

        $('#sumTotal').text(fmt(total));
        kembalian()
    }

    function processPayment() {
        if (cart.length === 0) {
            toast('Pilih Menu!');
            return;
        }
        const subtotal = cart.reduce((s, c) => s + c.harga * c.qty, 0);
        const total = subtotal;
        const orderId = genId();
        const customer = $('#customerName').val().trim() || 'Customer';
        const paymentVia = $('#pembayaran').val() || 'cash';
        const bayar = parseInt($('#bayar').val().replace(/[^0-9]/g, '')) || 0;

        // Susun objek data terstruktur untuk dikirim ke Backend CI4
        const payload = {
            nama: customer,
            payment_via: paymentVia,
            total: total,
            bayar: bayar,
            items: cart.map(c => ({
                menu_id: c.id,
                qty: c.qty
            }))
        };

        // Mulai pengiriman data transaksi via AJAX
        $.ajax({
            url: '<?= base_url("transaction/save") ?>', // Endpoint Route CI4 Anda
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json', // Menandakan bahwa kita mengirim data bertipe JSON string
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
            },
            beforeSend: function() {
                // Nonaktifkan tombol sementara agar tidak terjadi double-input transaksi
                $('.mt-auto .btn-accent').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                $('meta[name="X-CSRF-TOKEN"]').attr('content', response.token);
                if (response.status === 'success') {
                    toast(response.message);

                    // Ambil waktu realtime saat ini untuk cetak struk nota visual
                    const now = new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    /* Build receipt secara dinamis untuk dicetak user */
                    const rows = cart.map(c => `
                    <div class="r-row">
                        <span>${c.name || c.nama} x${c.qty}</span>
                        <span>${fmt((parseInt(c.price || c.harga) * c.qty))}</span>
                    </div>`).join('');

                    /* Reset state aplikasi */
                    cart = [];
                    if (typeof renderCart === "function") renderCart();
                    if (typeof renderProductSelector === "function") renderProductSelector();

                    $('#customerName').val('');
                    $('#pembayaran').val('cash');
                    $('#bayar').val('');
                    $('#kembalian').text('Rp 0');
                    $('#sumTotal').text('Rp 0');
                    $('#modalSearch').val('');
                    $('#sumActiveItems').html('')
                    // Tutup modal belanja & tampilkan modal struk sukses pembayaran
                    // bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal'), {
                    //     backdrop: false
                    // }).show();
                    cetakStruk(response.transaksi_id)
                    // Fungsi opsional untuk memuat ulang riwayat tabel di halaman utama transaksi jika ada
                    if (typeof fetchTransactionHistory === "function") {
                        fetchTransactionHistory();
                    } else {
                        // Fallback jika belum membuat real-time fetcher history: reload setelah 1.5 detik
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    toast('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                // 1. Ambil response JSON dari server jika ada
                let response = xhr.responseJSON;

                // 2. Cek pesan error dari server, jika tidak ada pakai pesan default
                let errorMessage = (response && response.message) ? response.message : 'Failed to process payment. Server error.';
                toast(errorMessage);

                // 3. Perbarui token CSRF dengan token baru yang dikirim server saat error
                if (response && response.token) {
                    $('meta[name="X-CSRF-TOKEN"]').attr('content', response.token);
                }

                // 4. Log error asli ke konsol untuk kebutuhan debugging
                console.error(xhr.responseText);
            },
            complete: function() {
                // Kembalikan tombol ke keadaan semula setelah proses selesai
                $('.mt-auto .btn-accent').prop('disabled', false).html('<i class="fa-solid fa-credit-card me-2"></i>Payment');
            }
        });
    }
    // 1. Fungsi untuk menarik data transaksi terbaru dari database
    function fetchTransactionHistory() {
        const tanggalDipilih = $('#filterTanggal').val();
        $.ajax({
            url: '<?= base_url("transaction/get_all_json") ?>',
            type: 'GET',
            data: {
                tanggal: tanggalDipilih,
            },
            dataType: 'json',
            success: function(data) {
                // Perbarui data local array jika ada komponen lain yang membutuhkannya
                transactions = data;

                // Render ulang isi tabel history transaksi
                renderTxnHistoryFromDB(data);
            },
            error: function(xhr) {
                console.error('Gagal memuat riwayat transaksi:', xhr.responseText);
            }
        });
    }

    function renderTxnHistoryFromDB(list, query = '') {
        const tbody = document.getElementById('txnHistoryBody');
        if (!tbody) return;

        if (list.length === 0) {
            const msg = query ?
                `Tidak ada transaksi yang cocok dengan <strong>"${query}"</strong>` :
                'Belum ada riwayat transaksi.';
            tbody.innerHTML = `
                <tr>
                  <td colspan="6" class="text-center py-4" style="color:var(--muted)">
                    <i class="fa-solid fa-magnifying-glass" style="display:block;font-size:1.6rem;opacity:.3;margin-bottom:8px"></i>
                    <span style="font-size:.83rem">${msg}</span>
                  </td>
                </tr>`;
            return;
        }

        // Helper: wrap matched text with a highlight span
        function hl(text, q) {
            if (!q || !text) return text ?? '';
            const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return String(text).replace(new RegExp(`(${escaped})`, 'gi'),
                '<mark style="background:rgba(240,180,41,.3);color:var(--accent);border-radius:3px;padding:0 2px">$1</mark>');
        }

        tbody.innerHTML = list.map(t => {
            const pMethod = (t.payment_via || '').toLowerCase();
            const icon = pMethod === 'cash' ? 'money-bill-wave' : (pMethod === 'qris' ? 'qrcode' : 'credit-card');

            return `
                <tr>
                <td style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem">${hl(t.tanggal, query)}</td>
                <td>${hl(t.nama || 'Customer', query)}</td>
                <td style="font-weight:600">${fmt(t.total)}</td>
                <td>
                    <span style="font-size:.78rem;text-transform:uppercase">
                    <i class="fa-solid fa-${icon} me-1" style="color:var(--muted)"></i>${hl(t.payment_via, query)}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px">
                    <button class="btn-sm-icon" title="View" onclick="viewTxn('${t.id}')"><i class="fa-solid fa-eye"></i></button>
                    <button class="btn-sm-icon danger" title="Void" onclick="voidTxn('${t.id}')"><i class="fa-solid fa-ban"></i></button>
                    <button class="btn-sm-icon warning" title="Cetak Ulang" onclick="cetakStruk('${t.id}')"><i class="fa-solid fa-print"></i></button>
                    </div>
                </td>
                </tr>`;
        }).join('');
    }

    // ── VIEW TRANSACTION ──────────────────────────────────────────────────
    // Mode: 'view'  → buka modal dalam mode lihat saja (tombol Payment diganti Close)
    // Alur: fetch detail → isi cart dari transaksidetail → render grid semua menu
    let viewMode = false; // flag global: true = sedang mode view, false = mode baru

    function viewTxn(id) {
        // Cari data header transaksi dari array lokal yang sudah di-load
        const txn = transactions.find(x => x.id == id);

        // Aktifkan flag view mode agar processPayment diblokir
        viewMode = true;

        // Kosongkan cart terlebih dahulu
        cart = [];

        // Fetch detail item (transaksidetail JOIN menu) dari backend CI4
        $.ajax({
            url: '<?= base_url("transaction/get_detail_json") ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(details) {
                // details = array of { menu_id, qty, nama, harga, image_url, ... }
                // Isi cart berdasarkan transaksidetail
                details.forEach(function(d) {
                    // Cari produk lengkapnya dari array products (sudah ada di halaman)
                    const prod = products.find(p => p.id == d.menu_id);
                    if (prod) {
                        cart.push({
                            ...prod,
                            qty: parseInt(d.qty)
                        });
                    } else {
                        // Fallback jika produk tidak ada di array lokal (sudah dihapus, dll.)
                        cart.push({
                            id: d.menu_id,
                            nama: d.nama || 'Menu #' + d.menu_id,
                            harga: parseFloat(d.harga) || 0,
                            image_url: d.image_url || null,
                            qty: parseInt(d.qty)
                        });
                    }
                });

                // Isi header form dari data transaksi yang dipilih
                if (txn) {
                    $('#customerName').val(txn.nama || '');
                    $('#pembayaran').val(txn.payment_via || 'cash');
                    $('#bayar').val(txn.bayar);
                }

                // Render ulang grid produk — qty otomatis diambil dari cart
                renderProductSelector($('#modalSearch').val());
                updateSummary();

                // Ubah tampilan modal ke mode View (non-editable)
                _applyViewModeUI(true);

                // Buka modal
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addItemModal')).show();
            },
            error: function(xhr) {
                viewMode = false;
                toast('Gagal memuat detail transaksi.');
                console.error(xhr.responseText);
            }
        });
    }

    // Terapkan / batalkan perubahan UI untuk mode view
    function _applyViewModeUI(isView) {
        const $modal = $('#addItemModal');

        if (isView) {
            // Ganti judul modal
            $modal.find('.modal-title').html('<i class="fa-solid fa-eye me-2" style="color:var(--accent)"></i>View Transaction');

            // Nonaktifkan semua tombol qty di grid produk
            $modal.find('.qty-btn').prop('disabled', true).css('opacity', '.4');

            // Ganti tombol Payment → Close, sembunyikan Clear
            $modal.find('.btn-accent[onclick="processPayment()"]')
                .html('<i class="fa-solid fa-xmark me-2"></i>Close')
                .attr('onclick', "bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide()");
            $modal.find('.btn-ghost[onclick="clearCart()"]').hide();

            // Nonaktifkan input form
            $('#customerName, #pembayaran').prop('disabled', true);
        } else {
            // Kembalikan ke mode normal (Add / Edit)
            $modal.find('.modal-title').html('<i class="fa-solid fa-cart-plus me-2" style="color:var(--accent)"></i>Select Products &amp; Checkout');
            $modal.find('.qty-btn').prop('disabled', false).css('opacity', '');
            $modal.find('.btn-accent[onclick*="hide()"]')
                .html('<i class="fa-solid fa-credit-card me-2"></i>Payment')
                .attr('onclick', 'processPayment()');
            $modal.find('.btn-ghost').show();
            $('#customerName, #pembayaran').prop('disabled', false);
        }
    }

    // Reset mode saat modal ditutup agar modal "Add Item" normal kembali
    document.getElementById('addItemModal').addEventListener('hidden.bs.modal', function() {
        if (viewMode) {
            viewMode = false;
            cart = [];
            renderProductSelector();
            updateSummary();
            // Kembalikan UI ke normal untuk next open
            _applyViewModeUI(false);
            // Reset form
            $('#customerName').val('');
            $('#bayar').val('');
            $('#pembayaran').val('cash');
        }
    });

    function voidTxn(id) {
        // 1. Tampilkan ID transaksi di dalam teks modal
        $('#deleteTargetId').text('#' + id);

        // 2. Munculkan modal Bootstrap secara programatik
        const confirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmDeleteModal'));
        confirmModal.show();

        // 3. Set action ketika tombol "Ya, Hapus" di dalam modal diklik
        $('#btnExecuteDelete').off('click').on('click', function() {
            $.ajax({
                url: '<?= base_url("transaction/delete") ?>/' + id,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
                },
                success: function(response) {
                    confirmModal.hide(); // Tutup modal konfirmasi

                    if (response.status === 'success') {
                        toast(response.message);
                        fetchTransactionHistory();
                    } else {
                        toast('Error: ' + response.message);
                    }

                    $('meta[name="X-CSRF-TOKEN"]').attr('content', response.token);
                },
                error: function(xhr) {
                    confirmModal.hide();
                    toast('Gagal memproses penghapusan transaksi.');
                    console.error(xhr.responseText);
                }
            });
        });
    }

    function printReceipt() {
        const content = $('#receiptContent').html();
        const w = window.open('', '_blank', 'width=400,height=600');
        w.document.write(`<html><head><title>Receipt</title>
            <link href="https://fonts.googleapis.com/css2?family=Syne:wght@800&family=DM+Sans&display=swap" rel="stylesheet">
            <style>body{font-family:'DM Sans',sans-serif;padding:20px;max-width:320px;margin:auto}
            .r-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;text-align:center}
            .r-sub{text-align:center;color:#777;font-size:.75rem;margin-bottom:4px}
            .r-row{display:flex;justify-content:space-between;margin:4px 0;font-size:.82rem}
            .r-total{font-weight:800;font-size:.95rem}hr{border-color:#ddd;border-style:dashed;margin:8px 0}</style>
        </head><body>${content}</body></html>`);
        w.print();
        w.close();
    }
</script>
<?= $this->endSection() ?>