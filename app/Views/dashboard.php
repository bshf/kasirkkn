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
                <div class="stat-value">Rp 8.4M</div>
                <div class="stat-label">Revenue Today</div>
                <div class="stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> +12.4% vs yesterday</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="stat-value">124</div>
                <div class="stat-label">Orders Today</div>
                <div class="stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> +7 vs yesterday</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                <div class="stat-value">48</div>
                <div class="stat-label">Products Listed</div>
                <div class="stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> +3 new items</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-value">5</div>
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
                <div class="chart-title">Weekly Revenue</div>
                <div class="chart-subtitle">Sales performance over the past 7 days</div>
                <div class="bar-chart" id="barChart"></div>
                <div style="display:flex;gap:10px;margin-top:12px;justify-content:center" id="barLabels"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-title">Payment Breakdown</div>
                <div class="chart-subtitle">Today's breakdown</div>
                <div style="display:flex;flex-direction:column;gap:12px;margin-top:8px">
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:5px">
                            <span>Cash</span><span style="color:var(--accent);font-weight:600">52%</span>
                        </div>
                        <div style="background:var(--border);border-radius:20px;height:6px">
                            <div style="background:var(--accent);width:52%;height:100%;border-radius:20px"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:5px">
                            <span>Card</span><span style="color:var(--accent3);font-weight:600">30%</span>
                        </div>
                        <div style="background:var(--border);border-radius:20px;height:6px">
                            <div style="background:var(--accent3);width:30%;height:100%;border-radius:20px"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:5px">
                            <span>E-Wallet</span><span style="color:var(--accent2);font-weight:600">18%</span>
                        </div>
                        <div style="background:var(--border);border-radius:20px;height:6px">
                            <div style="background:var(--accent2);width:18%;height:100%;border-radius:20px"></div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
                    <div class="chart-title" style="margin-bottom:8px">Top Category</div>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:.8rem">
                        <div style="display:flex;justify-content:space-between"><span>☕ Beverages</span><span
                                style="color:var(--accent2)">Rp 3.1M</span></div>
                        <div style="display:flex;justify-content:space-between"><span>🍔 Food</span><span
                                style="color:var(--accent2)">Rp 2.8M</span></div>
                        <div style="display:flex;justify-content:space-between"><span>🍰 Dessert</span><span
                                style="color:var(--accent2)">Rp 1.5M</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="table-card">
        <div class="table-header">
            <h5>Recent Transactions</h5>
            <button class="btn-ghost" style="font-size:.75rem" onclick="navigate('transaction')">View All</button>
        </div>
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Time</th>
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
    // Script logic khusus kalkulasi bagan dashboard jalankan di sini
    
    let transactions = [
      { id: 'TXN-0001', customer: 'Budi S.', items: [{ name: 'Iced Latte', qty: 2, price: 28000 }, { name: 'Cheesecake', qty: 1, price: 30000 }], total: 86000, payment: 'Cash', status: 'paid', time: '09:12' },
      { id: 'TXN-0002', customer: 'Rina A.', items: [{ name: 'Chicken Burger', qty: 1, price: 45000 }, { name: 'Orange Juice', qty: 1, price: 20000 }], total: 71500, payment: 'Card', status: 'paid', time: '09:45' },
      { id: 'TXN-0003', customer: 'Walk-in', items: [{ name: 'Matcha Latte', qty: 3, price: 32000 }], total: 96000, payment: 'E-Wallet', status: 'paid', time: '10:03' },
      { id: 'TXN-0004', customer: 'Dewi M.', items: [{ name: 'Popcorn', qty: 2, price: 15000 }, { name: 'Mineral Water', qty: 2, price: 8000 }], total: 46000, payment: 'Cash', status: 'pending', time: '10:20' },
      { id: 'TXN-0005', customer: 'Agus P.', items: [{ name: 'Fried Rice', qty: 1, price: 38000 }], total: 41800, payment: 'Card', status: 'void', time: '10:55' },
    ];

    $(document).ready(function() {
        const d = new Date();
        $('#todayDate').text(d.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }));

        initDashboard();
    });

    function initDashboard() {
        /* Bar chart */
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const vals = [5.2, 6.8, 4.9, 7.3, 8.1, 9.4, 8.4];
        const max = Math.max(...vals);
        const chart = document.getElementById('barChart');
        const labels = document.getElementById('barLabels');
        chart.innerHTML = '';
        labels.innerHTML = '';
        days.forEach((d, i) => {
            const pct = (vals[i] / max) * 100;
            const wrap = document.createElement('div');
            wrap.className = 'bar-wrap';
            wrap.innerHTML = `
                <span style="font-size:.65rem;color:var(--muted)">${vals[i]}M</span>
                <div class="bar${i === 6 ? ' highlight' : ''}" style="height:${pct}%" title="Rp ${vals[i]}M"></div>`;
            chart.appendChild(wrap);

            const lbl = document.createElement('span');
            lbl.style.cssText = `font-size:.65rem;color:var(--muted);flex:1;text-align:center`;
            lbl.textContent = d;
            labels.appendChild(lbl);
        });

        /* Recent table */
        const tbody = document.getElementById('recentTxnBody');
        tbody.innerHTML = transactions.slice(-5).reverse().map(t => `
            <tr>
            <td style="font-family:'Syne',sans-serif;font-weight:700;font-size:.8rem">${t.id}</td>
            <td>${t.customer}</td>
            <td style="color:var(--muted)">${t.items.length} item(s)</td>
            <td style="font-weight:600">${fmt(t.total)}</td>
            <td><span style="font-size:.78rem"><i class="fa-solid fa-${t.payment === 'Cash' ? 'money-bill-wave' : t.payment === 'Card' ? 'credit-card' : 'wallet'} me-1" style="color:var(--muted)"></i>${t.payment}</span></td>
            <td><span class="status-pill ${t.status}">${t.status}</span></td>
            <td style="color:var(--muted);font-size:.78rem">${t.time}</td>
            </tr>`).join('');
    }
</script>
<?= $this->endSection() ?>