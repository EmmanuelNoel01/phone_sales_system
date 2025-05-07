<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Define allowed pages for each role
$admin_pages = ['dashboard.php', 'add_phone.php', 'manage_users.php', 'add_user.php', 'view_sales.php', 'view_returns.php'];
$staff_pages = ['dashboard.php', 'sales.php', 'returns.php', 'swaps.php'];

// Check access based on role
if ($_SESSION['role'] == 'admin') {
    if (!in_array($current_page, $admin_pages)) {
        header("Location: ../unauthorized.php");
        exit();
    }
} elseif ($_SESSION['role'] == 'staff') {
    if (!in_array($current_page, $staff_pages)) {
        header("Location: ../unauthorized.php");
        exit();
    }
} else {
    // Invalid role
    header("Location: ../logout.php");
    exit();
}
?>