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
    <link href="../assets/css/style.css" rel="stylesheet"> <!-- Optional: Keep your styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #03246b;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar .logo {
            padding: 20px;
            text-align: center;
            background-color: #021c58;
        }

        .sidebar .logo img {
            height: 50px;
            margin-bottom: 10px;
        }

        .sidebar .logo span {
            font-size: 1.25rem;
            font-weight: bold;
            display: block;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a:hover {
            background-color: #021c58;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .main-content {
            flex-grow: 1;
            background-color: #f8f9fa;
            overflow-y: auto;
        }

        .top-bar {
            background-color: #03246b;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="/phone_sales_system/assets/img/logo.jpg" alt="Logo">
            <span>Gadget Store</span>
        </div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="/phone_sales_system/admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/phone_sales_system/admin/add_phone.php"><i class="bi bi-plus-circle"></i> Add Phone</a>
            <a href="/phone_sales_system/admin/manage_users.php"><i class="bi bi-people"></i> Manage Staff</a>
            <a href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack"></i> Sales</a>
            <a href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left"></i> Returns</a>
            <a href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right"></i> Swaps(Top-Up)</a>
            <a href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle"></i> Swap Debtors</a>
            <a href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle"></i> Sales Debtors</a>
            <a href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack"></i> Expenditure</a>
        <?php else: ?>
            <a href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack"></i> Sales</a>
            <a href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left"></i> Returns</a>
            <a href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right"></i> Swaps(Top-Up)</a>
            <a href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle"></i> Swap Debtors</a>
            <a href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle"></i> Sales Debtors</a>
            <a href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack"></i> Expenditure</a>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top bar -->
        <div class="top-bar">
            <div>
                <i class="bi bi-person-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['email'] ?? '') ?>
            </div>
            <a href="/phone_sales_system/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>

        <!-- Page Content Starts -->
        <main class="container mt-4">
