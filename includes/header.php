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
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }

        .sidebar {
            width: 300px;
            background-color: #03246b;
            color: white;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100vh;
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
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: silver;
            color: #03246b;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .top-bar {
            background-color: #03246b;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        main.container {
            flex: 1;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
            }

            .sidebar a span {
                display: none;
            }

            .sidebar .logo span {
                display: none;
            }

            .sidebar .logo img {
                height: 40px;
            }
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
            <a href="/phone_sales_system/admin/dashboard.php"><i class="bi bi-speedometer2"></i> <span>Dashboard</span></a>
            <a href="/phone_sales_system/admin/add_phone.php"><i class="bi bi-plus-circle"></i> <span>Add Phone</span></a>
            <a href="/phone_sales_system/admin/manage_users.php"><i class="bi bi-people"></i> <span>Manage Staff</span></a>
            <a href="/phone_sales_system/admin/manage_inventory.php"><i class="bi bi-pencil"></i> <span>Edit Inventory</span></a>
            <a href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack"></i> <span>Sell Phones</span></a>
            <a href="/phone_sales_system/sell_gadget.php"><i class="bi bi-receipt"></i> <span>Sell Gadgets</span></a>
            <a href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left"></i> <span>Returns</span></a>
            <a href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right"></i> <span>Swaps</span></a>
            <a href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle"></i> <span>Swap Debtors</span></a>
            <a href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle"></i> <span>Sales Debtors</span></a>
            <a href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack"></i> <span>Expenditure</span></a>
        <?php else: ?>
            <a href="/phone_sales_system/staff/sales.php"><i class="bi bi-cash-stack"></i> <span>Sell Phones</span></a>
            <a href="/phone_sales_system/sell_gadget.php"><i class="bi bi-receipt"></i> <span>Sell Gadgets</span></a>
            <a href="/phone_sales_system/staff/returns.php"><i class="bi bi-arrow-return-left"></i> <span>Returns</span></a>
            <a href="/phone_sales_system/staff/swaps.php"><i class="bi bi-arrow-left-right"></i> <span>Swaps</span></a>
            <a href="/phone_sales_system/staff/swaps_debtors.php"><i class="bi bi-exclamation-circle"></i> <span>Swap Debtors</span></a>
            <a href="/phone_sales_system/staff/sales_debtors.php"><i class="bi bi-exclamation-triangle"></i> <span>Sales Debtors</span></a>
            <a href="/phone_sales_system/expenditures.php"><i class="bi bi-cash-stack"></i> <span>Expenditure</span></a>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <i class="bi bi-person-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['email'] ?? '') ?>
            </div>
            <a href="/phone_sales_system/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>

        <main class="container mt-4">
