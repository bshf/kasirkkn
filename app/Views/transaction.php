<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page active" id="page-transaction">
    <div class="section-header">
        <div>
            <h2>Transaction</h2>
            <p>Create and manage sales transactions</p>
        </div>
        <button class="btn-accent" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fa-solid fa-cart-plus me-1"></i>Add Item
        </button>
    </div>

    <!-- Transaction History -->
    <div class="table-card mt-4">
        <div class="table-header">
            <h5>Transaction History</h5>
            <div style="display:flex;gap:8px">
                <input class="cf-input" style="max-width:180px;padding:7px 12px" type="text" placeholder="Search…" />
                <button class="btn-ghost" style="font-size:.75rem"><i class="fa-solid fa-filter me-1"></i>Filter</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Level</th>
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
                        class="col-lg-4 d-flex flex-column h-100 justify-content-between border-start border-secondary-subtle ps-lg-4">
                        <div>
                            <p class="form-section-title">Order </p>
                            <div class="mb-3">
                                <label class="cf-label">Nama Customer</label>
                                <input class="cf-input" id="customerName" placeholder="Putri" />
                            </div>
                            <div class="mb-4">
                                <label class="cf-label">Level</label>
                                <input class="cf-input" id="levelInput" type="number" min="0" max="5" value="0"
                                    placeholder="0" />
                            </div>

                            <div class="mb-4">
                                <label class="cf-label">Pembayaran</label>
                                <select class="cf-input" id="pembayaran" name="pembayaran">
                                    <option value="cash">CASH</option>
                                    <option value="qris">QRIS</option>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let cart = [];
    let txnCounter = 6;
    let products = <?= json_encode($menus ?? []) ?>;
    let transactions = [{
            id: 'TXN-0001',
            customer: 'Budi S.',
            items: [{
                name: 'Iced Latte',
                qty: 2,
                price: 28000
            }, {
                name: 'Cheesecake',
                qty: 1,
                price: 30000
            }],
            total: 86000,
            payment: 'Cash',
            status: 'paid',
            time: '09:12'
        },
        {
            id: 'TXN-0002',
            customer: 'Rina A.',
            items: [{
                name: 'Chicken Burger',
                qty: 1,
                price: 45000
            }, {
                name: 'Orange Juice',
                qty: 1,
                price: 20000
            }],
            total: 71500,
            payment: 'Card',
            status: 'paid',
            time: '09:45'
        },
        {
            id: 'TXN-0003',
            customer: 'Walk-in',
            items: [{
                name: 'Matcha Latte',
                qty: 3,
                price: 32000
            }],
            total: 96000,
            payment: 'E-Wallet',
            status: 'paid',
            time: '10:03'
        },
        {
            id: 'TXN-0004',
            customer: 'Dewi M.',
            items: [{
                name: 'Popcorn',
                qty: 2,
                price: 15000
            }, {
                name: 'Mineral Water',
                qty: 2,
                price: 8000
            }],
            total: 46000,
            payment: 'Cash',
            status: 'pending',
            time: '10:20'
        },
        {
            id: 'TXN-0005',
            customer: 'Agus P.',
            items: [{
                name: 'Fried Rice',
                qty: 1,
                price: 38000
            }],
            total: 41800,
            payment: 'Card',
            status: 'void',
            time: '10:55'
        },
    ];

    $(document).ready(function() {
        renderProductSelector()
        fetchTransactionHistory();
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
                    <button class="qty-btn" onclick="modifyProductQty(${p.id}, -1)">−</button>
                    <span class="qty-num text-light" id="modal-qty-${p.id}">${qty}</span>
                    <button class="qty-btn" onclick="modifyProductQty(${p.id}, 1)">+</button>
                    </div>
                </div>`;
        }).join('');
    }

    function modifyProductQty(productId, change) {
        const product = products.find(p => p.id == productId);
        console.log('product', products, product, productId)
        if (!product) return;

        const existing = cart.find(c => c.id == productId);
        if (existing) {
            existing.qty += change;
            if (existing.qty <= 0) {
                cart = cart.filter(c => c.id != productId);
            }
        } else if (change > 0) {
            cart.push({
                ...product,
                qty: 1,
                note: ''
            });
        }

        // Update counter display inside modal grid directly
        const finalItem = cart.find(c => c.id == productId);
        $(`#modal-qty-${productId}`).text(finalItem ? finalItem.qty : 0);

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
                <span> ${c.nama}</span>
                </div>
                <span style="font-weight:500;color:var(--muted)">${fmt(c.harga * c.qty)}</span>
            </div>
            `).join('');
        }

        $('#sumTotal').text(fmt(total));
    }

    function processPayment() {
        if (cart.length === 0) {
            toast('Cart is empty!');
            return;
        }
        const subtotal = cart.reduce((s, c) => s + c.harga * c.qty, 0);
        const total = subtotal;
        const orderId = genId();
        const customer = $('#customerName').val().trim() || 'Customer';
        const level = $('#levelInput').val() || 0;
        const paymentVia = $('#pembayaran').val() || 'cash';

        // Susun objek data terstruktur untuk dikirim ke Backend CI4
        const payload = {
            nama: customer,
            level: level,
            payment_via: paymentVia,
            total: total,
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
            beforeSend: function() {
                // Nonaktifkan tombol sementara agar tidak terjadi double-input transaksi
                $('.mt-auto .btn-accent').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
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

                    document.getElementById('receiptContent').innerHTML = `
                    <div class="r-title">CashFlow</div>
                    <div class="r-sub">${new Date().toLocaleDateString('id-ID')} ${now}</div>
                    <hr>
                    <div class="r-row"><span>Order ID</span><span>#${response.transaksi_id}</span></div>
                    <div class="r-row"><span>Customer</span><span>${customer}</span></div>
                    <div class="r-row"><span>Metode</span><span style="text-transform:uppercase">${paymentVia}</span></div>
                    <hr>
                    ${rows}
                    <hr>
                    <div class="r-row r-total"><span>TOTAL</span><span>${fmt(total)}</span></div>
                    <hr>
                    <div style="text-align:center;margin-top:12px;font-size:.72rem;color:#777">Thank you for your purchase!<br>Please come again 😊</div>`;

                    /* Reset state aplikasi */
                    cart = [];
                    if (typeof renderCart === "function") renderCart();
                    if (typeof renderProductSelector === "function") renderProductSelector();

                    // Tutup modal belanja & tampilkan modal struk sukses pembayaran
                    bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal')).show();

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
                toast('Failed to process payment. Server error.');
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
        $.ajax({
            url: '<?= base_url("transaction/get_all_json") ?>',
            type: 'GET',
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

    function renderTxnHistoryFromDB(list) {
        const tbody = document.getElementById('txnHistoryBody');
        if (!tbody) return;

        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat transaksi.</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(t => {
            // Format tanggal dari database (YYYY-MM-DD HH:MM:SS) menjadi jam menit saja (HH:MM)
            const timeFormatted = t.tanggal ? t.tanggal.substring(11, 16) : '--:--';

            // Ikon dinamis berdasarkan tipe pembayaran
            const pMethod = t.payment_via.toLowerCase();
            const icon = pMethod === 'cash' ? 'money-bill-wave' : (pMethod === 'qris' ? 'qrcode' : 'credit-card');

            return `
        <tr>
          <td style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem">TXN-${String(t.id).padStart(4, '0')}</td>
          <td>${t.nama || 'Customer'}</td>
          <td style="color:var(--muted)">Lvl ${t.level || 0}</td>
          <td style="font-weight:600">${fmt(t.total)}</td>
          <td>
            <span style="font-size:.78rem; text-transform: uppercase;">
                <i class="fa-solid fa-${icon} me-1" style="color:var(--muted)"></i>${t.payment_via}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <button class="btn-sm-icon" title="View" onclick="viewTxn('${t.id}')"><i class="fa-solid fa-eye"></i></button>
              <button class="btn-sm-icon danger" title="Void" onclick="voidTxn('${t.id}')"><i class="fa-solid fa-ban"></i></button>
            </div>
          </td>
        </tr>`;
        }).join('');
    }

    function renderTxnHistory() {
        const tbody = document.getElementById('txnHistoryBody');
        tbody.innerHTML = [...transactions].reverse().map(t => `
        <tr>
        <td style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem">${t.id}</td>
        <td>${t.customer}</td>
        <td style="color:var(--muted)">${t.items.length} item(s)</td>
        <td style="font-weight:600">${fmt(t.total)}</td>
        <td><span style="font-size:.78rem"><i class="fa-solid fa-${t.payment === 'cash' ? 'money-bill-wave' : t.payment === 'Card' ? 'credit-card' : 'wallet'} me-1" style="color:var(--muted)"></i>${t.payment}</span></td>
        <td>
            <div style="display:flex;gap:6px">
            <button class="btn-sm-icon" title="View" onclick="viewTxn('${t.id}')"><i class="fa-solid fa-eye"></i></button>
            <button class="btn-sm-icon danger" title="Void" onclick="voidTxn('${t.id}')"><i class="fa-solid fa-ban"></i></button>
            </div>
        </td>
        </tr>`).join('');
    }

    function viewTxn(id) {
        toast('Viewing transaction ' + id);
    }

    function voidTxn(id) {
        const t = transactions.find(x => x.id === id);
        if (t) {
            t.status = 'void';
            fetchTransactionHistory();
            toast(id + ' marked as void.');
        }
    }

    function printReceipt() {
        const content = document.getElementById('receiptContent').innerHTML;
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