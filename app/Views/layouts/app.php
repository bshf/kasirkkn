<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - AppWorkspace</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* --- Base & Reset Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            line-height: 1.6;
        }

        /* --- Top Navbar --- */
        .navbar {
            background-color: #1e293b;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-decoration: none;
            color: #fff;
        }

        .navbar-user {
            font-size: 0.9rem;
            background-color: #334155;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
        }

        /* --- Sub-Navigation Menu (Multipage Links) --- */
        .sub-nav {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 53px;
            z-index: 99;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            list-style: none;
        }

        .nav-item {
            flex: 1;
            text-align: center;
        }

        .nav-link {
            display: block;
            padding: 1rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .nav-link:hover {
            color: #2563eb;
            background-color: #f8fafc;
        }

        /* Kelas active akan menyala sesuai dengan segment URL saat ini */
        .nav-link.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
            background-color: #f0fdf4;
        }

        /* --- Main Content Area --- */
        .main-container {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 0 1rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .card h3 {
            font-size: 0.9rem;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .card .value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0.75rem 1rem;
            }

            .sub-nav {
                top: 49px;
            }

            .nav-link {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }

            .main-container {
                margin: 1rem auto;
            }
        }

        /* Untuk stylesheet tambahan jika dibutuhkan oleh view anak */
        <?= $this->renderSection('styles') ?>
    </style>
</head>

<body>

    <header class="navbar">
        <a href="<?= base_url('/') ?>" class="navbar-brand">AppWorkspace</a>
        <div class="navbar-user">Admin Node</div>
    </header>

    <?php $uri = service('uri'); ?>
    <nav class="sub-nav">
        <ul class="nav-container">
            <li class="nav-item">
                <a class="nav-link <?= ($uri->getSegment(1) == '') ? 'active' : '' ?>" href="<?= base_url('/') ?>">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($uri->getSegment(1) == 'menu') ? 'active' : '' ?>" href="<?= base_url('menu') ?>">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($uri->getSegment(1) == 'transaction') ? 'active' : '' ?>" href="<?= base_url('transaction') ?>">Transaction</a>
            </li>
        </ul>
    </nav>

    <main class="main-container">
        <?= $this->renderSection('content') ?>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>