<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Admin Dashboard";

// Stats with status check
$stats = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM phones WHERE status != 'sold' AND quantity != '0') as phone_count,
        (SELECT COUNT(*) FROM sales WHERE DATE(sale_date) = CURDATE()) as today_sales,
        (SELECT COUNT(*) FROM returns WHERE status = 'Repairing') as pending_returns,
        (SELECT SUM(sale_price) FROM sales WHERE MONTH(sale_date) = MONTH(CURRENT_DATE())) as monthly_revenue
")->fetch_assoc();

// Pagination for recent sales
$limit_recent = 12;
$page_recent = isset($_GET['page_recent']) ? (int) $_GET['page_recent'] : 1;
$offset_recent = ($page_recent - 1) * $limit_recent;

$total_recent_sales = $conn->query("SELECT COUNT(*) as total FROM sales")->fetch_assoc()['total'];
$total_pages_recent = ceil($total_recent_sales / $limit_recent);

$recent_sales = $conn->query("
    SELECT s.*, p.brand, p.model 
    FROM sales s
    JOIN phones p ON s.phone_id = p.id
    ORDER BY s.sale_date DESC
    LIMIT $limit_recent OFFSET $offset_recent
");

// Pagination for available phones
$limit_available = 10;
$page_available = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset_available = ($page_available - 1) * $limit_available;

$total_phones = $conn->query("SELECT COUNT(*) AS total FROM phones WHERE status != 'sold' AND quantity != '0'")->fetch_assoc()['total'];
$total_pages_available = ceil($total_phones / $limit_available);

$available_phones = $conn->query("
    SELECT * FROM phones 
    WHERE status != 'sold'
    AND quantity != 0
    ORDER BY added_at DESC
    LIMIT $limit_available OFFSET $offset_available
");

// Pagination for today's sales
$limit_today_sales = 10;
$page_today_sales = isset($_GET['page_today']) ? (int) $_GET['page_today'] : 1;
$offset_today_sales = ($page_today_sales - 1) * $limit_today_sales;

$today_sales_details = $conn->query("
SELECT 
    s.amount_paid,
    s.sale_date,
    s.customer_name,
    CASE 
        WHEN s.phone_id IS NOT NULL THEN CONCAT(p.brand, ' ', p.model)
        WHEN s.gadget_id IS NOT NULL THEN g.name
        ELSE 'Unknown Product'
    END AS product_name
FROM sales s
LEFT JOIN phones p ON s.phone_id = p.id
LEFT JOIN gadgets g ON s.gadget_id = g.id
WHERE DATE(s.sale_date) = CURDATE()
ORDER BY s.sale_date DESC
LIMIT $limit_today_sales OFFSET $offset_today_sales
");


// Show daily breakdown of current month sales if requested
$show_monthly_breakdown = isset($_GET['show']) && $_GET['show'] === 'monthly_breakdown';
if ($show_monthly_breakdown) {
    $daily_breakdown = $conn->query("
        SELECT 
            DATE(sale_date) as sale_day, 
            COUNT(*) as total_sales, 
            SUM(sale_price) as total_revenue
        FROM sales
        WHERE MONTH(sale_date) = MONTH(CURRENT_DATE())
        GROUP BY DATE(sale_date)
        ORDER BY sale_day DESC
    ");
}

// Show today's sales if requested
$show_today_sales = isset($_GET['show']) && $_GET['show'] === 'today_sales';

require '../includes/header.php';
?>

<!-- DASHBOARD CARDS -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="?show=available" class="text-decoration-none">
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
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="?show=today_sales" class="text-decoration-none">
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
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="balance_sheet.php" style="text-decoration: none; color: inherit;">
            <div class="card dashboard-card warning h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-warning mb-0">Balance Sheet</h6>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bar-chart-line fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="?show=monthly_breakdown" class="text-decoration-none">
            <div class="card dashboard-card info h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-info mb-0">Monthly Revenue</h6>
                            <h2 class="my-2">UGX <?= number_format($stats['monthly_revenue']) ?></h2>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="swap_approval.php" style="text-decoration: none; color: inherit;">
            <div class="card dashboard-card secondary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-secondary mb-0">Approve Swap Debts</h6>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="debt_approval.php" style="text-decoration: none; color: inherit;">
            <div class="card dashboard-card danger h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase text-danger mb-0">Approve Sales Debts</h6>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
    <a href="upload_gadget.php" style="text-decoration: none; color: inherit;">
        <div class="card dashboard-card danger h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-dark mb-0">Upload other Gadgets</h6>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt text-sky-blue" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<style>
    .text-sky-blue {
        color: #87CEEB;
    }
</style>
</div>

<!-- AVAILABLE PHONES -->
<?php if (isset($_GET['show']) && $_GET['show'] === 'available'): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Available Phones</h6>
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
        </div>
        <div class="card-body">
            <?php if ($available_phones->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>IMEI</th>
                                <th>Storage</th>
                                <th>Color</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Added At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($phone = $available_phones->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $phone['id'] ?></td>
                                    <td><?= $phone['brand'] ?></td>
                                    <td><?= $phone['model'] ?></td>
                                    <td><?= $phone['imei'] ?></td>
                                    <td><?= $phone['storage'] ?></td>
                                    <td><?= $phone['color'] ?></td>
                                    <td><?= $phone['condition'] ?></td>
                                    <td><span class="badge bg-info text-dark"><?= $phone['status'] ?></span></td>
                                    <td>UGX <?= number_format($phone['price']) ?></td>
                                    <td><?= $phone['quantity'] ?></td>
                                    <td><?= date('Y-m-d', strtotime($phone['added_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls for Available Phones -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?= $page_available <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?show=available&page=<?= $page_available - 1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages_available; $i++): ?>
                            <li class="page-item <?= $i === $page_available ? 'active' : '' ?>">
                                <a class="page-link" href="?show=available&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page_available >= $total_pages_available ? 'disabled' : '' ?>">
                            <a class="page-link" href="?show=available&page=<?= $page_available + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="alert alert-info">No available phones found.</div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- MONTHLY BREAKDOWN -->
<?php if ($show_monthly_breakdown): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-info">Daily Breakdown - This Month</h6>
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
        </div>
        <div class="card-body">
            <?php if ($daily_breakdown->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Number of Sales</th>
                                <th>Total Revenue (UGX)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $daily_breakdown->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($row['sale_day'])) ?></td>
                                    <td><?= $row['total_sales'] ?></td>
                                    <td>UGX <?= number_format($row['total_revenue']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No sales recorded for this month.</div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- TODAY'S SALES DETAILS -->
<?php if ($show_today_sales): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">Today's Sales Details</h6>
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
        </div>
        <div class="card-body">
            <?php if ($today_sales_details->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>product_name</th>
                                <!-- <th>Phone</th> -->
                                <th>Amount Paid (UGX)</th>
                                <th>Customer</th>
                                <th>Sale Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $today_sales_details->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['product_name'] ?></td>
                                    <!-- <td><?= $row['brand'] . ' ' . $row['model'] ?></td> -->
                                    <td>UGX <?= number_format($row['amount_paid']) ?></td>
                                    <td><?= $row['customer_name'] ?></td>
                                    <td><?= date('H:i:s', strtotime($row['sale_date'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls for Today's Sales -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?= $page_today_sales <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?show=today_sales&page_today=<?= $page_today_sales - 1 ?>">Previous</a>
                        </li>
                        <?php
                        $total_today_sales = $conn->query("SELECT COUNT(*) as total FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch_assoc()['total'];
                        $total_pages_today = ceil($total_today_sales / $limit_today_sales);
                        for ($i = 1; $i <= $total_pages_today; $i++): ?>
                            <li class="page-item <?= $i === $page_today_sales ? 'active' : '' ?>">
                                <a class="page-link" href="?show=today_sales&page_today=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page_today_sales >= $total_pages_today ? 'disabled' : '' ?>">
                            <a class="page-link" href="?show=today_sales&page_today=<?= $page_today_sales + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="alert alert-info">No sales recorded for today.</div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- RECENT SALES -->
<!-- <div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
        <a href="#" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <!-- <th>Phone</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr> -->
                <!-- </thead>
                <tbody>
                    <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                        <tr> -->
                             <!-- <td><?= $sale['id'] ?></td>
                            <td><?= $sale['brand'] ?>     <?= $sale['model'] ?></td>
                            <td><?= $sale['customer_name'] ?></td>
                            <td>UGX <?= number_format($sale['sale_price']) ?></td> -->
                            <!-- <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody> -->
            <!-- </table>
        </div> -->

        <!-- Pagination Controls for Recent Sales -->
        <!-- <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= $page_recent <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page_recent=<?= $page_recent - 1 ?>">Previous</a>
                </li> -->
                <!-- <?php for ($i = 1; $i <= $total_pages_recent; $i++): ?>
                    <li class="page-item <?= $i === $page_recent ? 'active' : '' ?>">
                        <a class="page-link" href="?page_recent=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?> -->
                <!-- <li class="page-item <?= $page_recent >= $total_pages_recent ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page_recent=<?= $page_recent + 1 ?>">Next</a>
                </li>
            </ul>
        </nav> -->
    <!-- </div>
</div>  -->

<?php require '../includes/footer.php'; ?>