<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Recent Transactions<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 600px;
    }

    th, td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }

    tr:hover { background-color: #f8fafc; }

    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-success { background-color: #dcfce7; color: #15803d; }
    .badge-pending { background-color: #fef9c3; color: #a16207; }
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-header">
        <h2>Recent Transactions</h2>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#TRX-00124</td>
                    <td>Alpha Team</td>
                    <td>May 19, 2026</td>
                    <td>$350.00</td>
                    <td><span class="badge badge-success">Success</span></td>
                </tr>
                <tr>
                    <td>#TRX-00125</td>
                    <td>Beta Branch</td>
                    <td>May 18, 2026</td>
                    <td>$1,200.00</td>
                    <td><span class="badge badge-success">Success</span></td>
                </tr>
                <tr>
                    <td>#TRX-00126</td>
                    <td>Medan Hub</td>
                    <td>May 18, 2026</td>
                    <td>$420.50</td>
                    <td><span class="badge badge-pending">Pending</span></td>
                </tr>
            </tbody>
        </table>
    </div>
<?= $this->endSection() ?>