<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>" />
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>" />
</head>

<body>

    <div class="overlay" id="overlay"></div>

    <nav id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-cash-register"></i></div>
            <span class="brand-name">Cash<span>Flow</span></span>
        </div>

        <div class="nav-section">
            <p class="nav-label">Menu</p>
            <a href="<?= base_url('dashboard') ?>" class="nav-item <?= ($activeMenu == 'dashboard') ? 'active' : '' ?>" data-page="dashboard">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= base_url('menu') ?>" class="nav-item <?= ($activeMenu == 'menu') ? 'active' : '' ?>" data-page="catalogue">
                <i class="fa-solid fa-box-open"></i> Menu
            </a>
            <a href="<?= base_url('transaction') ?>" class="nav-item <?= ($activeMenu == 'transaction') ? 'active' : '' ?>" data-page="transaction">
                <i class="fa-solid fa-receipt"></i> Transaction
            </a>
            <a href="<?= base_url('logout') ?>" class="nav-item">
                <i class="fa-solid fa-sign-out"></i> Logout
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="avatar">AD</div>
            <div class="user-info">
                <div class="name">Admin User</div>
                <div class="role">Cashier Admin</div>
            </div>
        </div>
    </nav>

    <div id="main">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
                <span class="page-title" id="pageTitle"><?= $pageTitle; ?></span>
            </div>
        </div>

        <div class="page-content">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script>
        const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        const genId = () => 'TXN-' + String(txnCounter).padStart(4, '0');
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('open');
            $('#overlay').toggleClass('show');
        });
        $('#overlay').on('click', function() {
            $('#sidebar').removeClass('open');
            $('#overlay').removeClass('show');
        });

        function toast(msg) {
            const t = document.createElement('div');
            t.className = 'cf-toast';
            t.innerHTML = `<i class="fa-solid fa-circle-check"></i>${msg}`;
            document.getElementById('toastContainer').appendChild(t);
            setTimeout(() => t.remove(), 3200);
        }

        /* ─── NAVIGATION ─── */
        function navigate(page) {
            $('.nav-item').removeClass('active');
            $(`.nav-item[data-page="${page}"]`).addClass('active');
            $('.page').removeClass('active');
            $(`#page-${page}`).addClass('active');
            const titles = {
                dashboard: 'Dashboard',
                menu: 'Catalogue',
                transaction: 'Transaction'
            };
            $('#pageTitle').text(titles[page]);
            if (window.innerWidth < 768) closeSidebar();
        }

        function closeSidebar() {
            $('#sidebar').removeClass('open');
            $('#overlay').removeClass('show');
        }

        $(document).ready(function() {
            /* Sidebar toggle */
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('open');
                $('#overlay').toggleClass('show');
            });

            $('#overlay').on('click', closeSidebar);

            /* Nav items */
            $('.nav-item').on('click', function() {
                navigate($(this).data('page'));
            });
        })
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>