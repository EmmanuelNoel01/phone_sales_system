<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Staff Dashboard";

// Get staff-specific stats
$user_id = $_SESSION['user_id'];
$today_sales = $conn->query("
    SELECT COUNT(*) as count, SUM(sale_price) as total 
    FROM sales 
    WHERE DATE(sale_date) = CURDATE()
")->fetch_assoc();
?>

<?php require '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card dashboard-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-primary mb-0">Today's Sales</h6>
                        <h2 class="my-2"><?= $today_sales['count'] ?? 0 ?></h2>
                        <p class="mb-0"><?= number_format($today_sales['total'] ?? 0, ) ?></p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card dashboard-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-success mb-0">Monthly Performance</h6>
                        <h2 class="my-2">UGX. <?= number_format($conn->query("
                            SELECT SUM(sale_price) as total 
                            FROM sales 
                            WHERE sold_by = $user_id AND MONTH(sale_date) = MONTH(CURRENT_DATE())
                            ")->fetch_assoc()['total'] ?? 0, ) 
                        ?></h2>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up fs-1 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>