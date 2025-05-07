<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Admin Dashboard";

// Get statistics
$stats = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM phones) as phone_count,
        (SELECT COUNT(*) FROM sales WHERE DATE(sale_date) = CURDATE()) as today_sales,
        (SELECT COUNT(*) FROM returns WHERE status = 'Repairing') as pending_returns,
        (SELECT SUM(sale_price) FROM sales WHERE MONTH(sale_date) = MONTH(CURRENT_DATE())) as monthly_revenue
")->fetch_assoc();

// Recent sales
$recent_sales = $conn->query("
    SELECT s.*, p.brand, p.model 
    FROM sales s
    JOIN phones p ON s.phone_id = p.id
    ORDER BY s.sale_date DESC
    LIMIT 5
");
?>

<?php require '../includes/header.php'; ?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-primary mb-0">Total Phones</h6>
                        <h2 class="my-2"><?= $stats['phone_count'] ?></h2>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-phone fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-success mb-0">Today's Sales</h6>
                        <h2 class="my-2"><?= $stats['today_sales'] ?></h2>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash fs-1 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-warning mb-0">Pending Returns</h6>
                        <h2 class="my-2"><?= $stats['pending_returns'] ?></h2>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card info h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-info mb-0">Monthly Revenue</h6>
                        <h2 class="my-2">UGX. <?= number_format($stats['monthly_revenue']) ?></h2>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fs-1 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
        <a href="#" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phone</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($sale = $recent_sales->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= $sale['brand'] ?> <?= $sale['model'] ?></td>
                        <td><?= $sale['customer_name'] ?></td>
                        <td>$<?= number_format($sale['sale_price'], 2) ?></td>
                        <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>