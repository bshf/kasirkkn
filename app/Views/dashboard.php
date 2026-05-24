<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Dashboard Overview<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-header">
        <h2>Dashboard Overview</h2>
    </div>
    
    <div class="grid-3">
        <div class="card">
            <h3>Total Sales</h3>
            <div class="value">$24,500</div>
        </div>
        <div class="card">
            <h3>Active Users</h3>
            <div class="value">1,240</div>
        </div>
        <div class="card">
            <h3>Pending Orders</h3>
            <div class="value">18</div>
        </div>
    </div>
<?= $this->endSection() ?>