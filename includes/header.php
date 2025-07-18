<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gadget Store | <?= htmlspecialchars($page_title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet"> <!-- your original CSS path kept -->
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex-grow: 1;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <a class="navbar-brand"
                href="<?php echo ($user_role === 'admin') ? '/phone_sales_system/admin/dashboard.php' : '/phone_sales_system/staff/dashboard.php'; ?>">
                <img src="/phone_sales_system/assets/img/logo.png" height="30" class="me-2" alt="Logo">
                Gadget Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/admin/add_phone.php"><i class="bi bi-plus-circle me-1"></i> Add Phone</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/admin/manage_users.php"><i class="bi bi-people me-1"></i> Manage Staff</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack me-1"></i> Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left me-1"></i> Returns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right me-1"></i> Swaps(Top-Up)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle me-1"></i> Swap Debtors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle me-1"></i> Sales Debtors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack me-1"></i> Expenditure</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack me-1"></i> Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left me-1"></i> Returns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right me-1"></i> Swaps(Top-Up)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle me-1"></i> Swap Debtors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle me-1"></i> Sales Debtors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack me-1"></i> Expenditure</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['email'] ?? '') ?>
                    </span>
                    <a href="/phone_sales_system/logout.php" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Fluid and full-height main wrapper -->
    <main class="container-fluid mt-4">
