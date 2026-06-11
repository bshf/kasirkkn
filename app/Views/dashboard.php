<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="page active" id="page-dashboard">
    <div class="section-header">
        <div>
            <h2>Overview</h2>
            <p id="todayDate"></p>
        </div>
        <button class="btn-accent"><i class="fa-solid fa-download me-1"></i>Export</button>
    </div>

    <!-- Stats -->

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card yellow">
                <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
                <div class="stat-value" id="statRevenue"><span class="text-muted" style="font-size:1rem">Loading…</span></div>
                <div class="stat-label">Pendapatan Hari Ini</div>
                <div class="stat-change up" id="statRevenueChange"><i class="fa-solid fa-minus"></i> —</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="stat-value" id="statOrders"><span class="text-muted" style="font-size:1rem">Loading…</span></div>
                <div class="stat-label">Orderan Hari Ini</div>
                <div class="stat-change up" id="statOrdersChange"><i class="fa-solid fa-minus"></i> —</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                <div class="stat-value" id="statProducts"><span class="text-muted" style="font-size:1rem">Loading…</span></div>
                <div class="stat-label">Total Menu</div>
                <div class="stat-change up"><i class="fa-solid fa-layer-group"></i> Total Menu</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-value" id="statLowStock"><span class="text-muted" style="font-size:1rem">Loading…</span></div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-change down"><i class="fa-solid fa-arrow-trend-down"></i> Needs restock</div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables -->

    <!-- Charts + Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-title">Pendapatan Mingguan</div>
                <div class="chart-subtitle">Performance penjualan selama 7 hari terakhir</div>
                <div class="bar-chart" id="barChart"></div>
                <div style="display:flex;gap:10px;margin-top:12px;justify-content:center" id="barLabels"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-title">Rincian Pembayaran</div>
                <div class="chart-subtitle">Bulan ini</div>
                <div style="display:flex;flex-direction:column;gap:12px;margin-top:8px">
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:5px">
                            <span>Cash</span><span style="color:var(--accent);font-weight:600" id="pbCashPct">—</span>
                        </div>
                        <div style="background:var(--border);border-radius:20px;height:6px">
                            <div id="pbCashBar" style="background:var(--accent);width:0%;height:100%;border-radius:20px;transition:width .6s ease"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:5px">
                            <span>QRIS</span><span style="color:var(--accent3);font-weight:600" id="pbQrisPct">—</span>
                        </div>
                        <div style="background:var(--border);border-radius:20px;height:6px">
                            <div id="pbQrisBar" style="background:var(--accent3);width:0%;height:100%;border-radius:20px;transition:width .6s ease"></div>
                        </div>
                    </div>
                </div>
                <!-- <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
                    <div class="chart-title" style="margin-bottom:8px">Top Category</div>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:.8rem" id="topCategoryList">
                        <div style="color:var(--muted);font-size:.75rem">Loading…</div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="table-card">
        <div class="table-header">
            <h5>Transaksi Terbaru</h5>
            <button class="btn-ghost" style="font-size:.75rem" onclick="navigate('transaction')">Lihat Semua</button>
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
                    </tr>
                </thead>
                <tbody id="recentTxnBody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ─── HELPERS ──────────────────────────────────────────────────────────
    // fmt() diasumsikan sudah didefinisikan di layouts/master (global helper)

    // ─── INIT ─────────────────────────────────────────────────────────────
    $(document).ready(function () {
        // Tampilkan tanggal hari ini
        const d = new Date();
        $('#todayDate').text(d.toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        }));

        // Muat semua data dashboard secara paralel via AJAX
        loadDashboardStats();
        loadWeeklyChart();
        loadPaymentBreakdown();
        loadRecentTransactions();
    });

    // ─── 1. STAT CARDS ────────────────────────────────────────────────────
    function loadDashboardStats() {
        $.ajax({
            url: '<?= base_url("dashboard/get_stats") ?>',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Revenue Today
                $('#statRevenue').text(fmt(data.revenue_today));
                $('#statRevenueChange')
                    .html(`<i class="fa-solid fa-arrow-trend-${data.revenue_diff >= 0 ? 'up' : 'down'}"></i>
                           ${data.revenue_diff >= 0 ? '+' : ''}${data.revenue_diff}% vs kemarin`)
                    .removeClass('up down')
                    .addClass(data.revenue_diff >= 0 ? 'up' : 'down');

                // Orders Today
                $('#statOrders').text(data.orders_today);
                $('#statOrdersChange')
                    .html(`<i class="fa-solid fa-arrow-trend-${data.orders_diff >= 0 ? 'up' : 'down'}"></i>
                           ${data.orders_diff >= 0 ? '+' : ''}${data.orders_diff} vs kemarin`)
                    .removeClass('up down')
                    .addClass(data.orders_diff >= 0 ? 'up' : 'down');

                // Products Listed
                $('#statProducts').text(data.total_products);

                // Low Stock
                $('#statLowStock').text(data.low_stock_count);
            },
            error: function () { console.error('Gagal memuat stats dashboard.'); }
        });
    }

    // ─── 2. WEEKLY BAR CHART ──────────────────────────────────────────────
    function loadWeeklyChart() {
        $.ajax({
            url: '<?= base_url("dashboard/get_weekly_chart") ?>',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // data = [{ day: 'Mon', label: '2026-06-09', total: 5200000 }, ...]
                const vals   = data.map(d => d.total);
                const max    = Math.max(...vals) || 1;
                const todayIdx = data.length - 1; // hari terakhir = hari ini

                const chart  = document.getElementById('barChart');
                const labels = document.getElementById('barLabels');
                chart.innerHTML  = '';
                labels.innerHTML = '';

                data.forEach(function (d, i) {
                    const pct  = (d.total / max) * 100;
                    const disp = d.total >= 1000000
                        ? (d.total / 1000000).toFixed(1) + 'M'
                        : (d.total >= 1000 ? (d.total / 1000).toFixed(0) + 'K' : d.total);

                    const wrap = document.createElement('div');
                    wrap.className = 'bar-wrap';
                    wrap.innerHTML = `
                        <span style="font-size:.65rem;color:var(--muted)">${disp}</span>
                        <div class="bar${i === todayIdx ? ' highlight' : ''}"
                             style="height:${Math.max(pct, 2)}%"
                             title="${d.day}: ${fmt(d.total)}"></div>`;
                    chart.appendChild(wrap);

                    const lbl = document.createElement('span');
                    lbl.style.cssText = 'font-size:.65rem;color:var(--muted);flex:1;text-align:center';
                    lbl.textContent   = d.day;
                    labels.appendChild(lbl);
                });
            },
            error: function () { console.error('Gagal memuat chart mingguan.'); }
        });
    }

    // ─── 3. PAYMENT BREAKDOWN ─────────────────────────────────────────────
    function loadPaymentBreakdown() {
        $.ajax({
            url: '<?= base_url("dashboard/get_payment_breakdown") ?>',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // data = { cash: { count: 70, pct: 70 }, qris: { count: 30, pct: 30 } }
                const cashPct = data.cash  ? data.cash.pct  : 0;
                const qrisPct = data.qris  ? data.qris.pct  : 0;

                $('#pbCashPct').text(cashPct + '%');
                $('#pbCashBar').css('width', cashPct + '%');
                $('#pbQrisPct').text(qrisPct + '%');
                $('#pbQrisBar').css('width', qrisPct + '%');

                // Top Category
                if (data.top_categories && data.top_categories.length) {
                    const emojis = { 'Minuman': '☕', 'Makanan': '🍔', 'Dessert': '🍰', 'Snack': '🍿' };
                    $('#topCategoryList').html(
                        data.top_categories.slice(0, 3).map(c => `
                            <div style="display:flex;justify-content:space-between">
                                <span>${emojis[c.kategori] || '🏷'} ${c.kategori}</span>
                                <span style="color:var(--accent2)">${fmt(c.total)}</span>
                            </div>`).join('')
                    );
                }
            },
            error: function () { console.error('Gagal memuat payment breakdown.'); }
        });
    }

    // ─── 4. RECENT TRANSACTIONS TABLE ─────────────────────────────────────
    function loadRecentTransactions() {
        $.ajax({
            url: '<?= base_url("dashboard/get_recent_transactions") ?>',
            type: 'GET',
            dataType: 'json',
            success: function (list) {
                const tbody = document.getElementById('recentTxnBody');
                if (!list || list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada transaksi hari ini.</td></tr>`;
                    return;
                }
                tbody.innerHTML = list.map(t => {
                    const icon = t.payment_via === 'cash' ? 'money-bill-wave' : 'qrcode';
                    return `
                        <tr>
                          <td style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem">${t.tanggal}</td>
                          <td>${t.nama || 'Customer'}</td>
                          <td style="color:var(--muted)">Lvl ${t.level || 0}</td>
                          <td style="font-weight:600">${fmt(t.total)}</td>
                          <td>
                            <span style="font-size:.78rem;text-transform:uppercase">
                              <i class="fa-solid fa-${icon} me-1" style="color:var(--muted)"></i>${t.payment_via}
                            </span>
                          </td>
                        </tr>`;
                }).join('');
            },
            error: function () { console.error('Gagal memuat recent transactions.'); }
        });
    }
</script>
<?= $this->endSection() ?>