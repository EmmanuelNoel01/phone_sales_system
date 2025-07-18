<?php
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 3600, 
        'cookie_secure' => isset($_SERVER['HTTPS']), 
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

// Redirect to login if no valid session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /phone_sales_system/login.php");
    exit();
}

// Define allowed pages for each role (relative to root)
$admin_pages = [
    'dashboard.php',
    'add_phone.php',
    'manage_users.php',
    'add_user.php',
    'edit_user.php',
    'delete_user.php',
    'view_sales.php',
    'view_returns.php',
    'swaps_debtors.php',
    'sales_debtors.php',
    'balance_sheet.php',
    'sales.php',
    'returns.php',
    'swaps.php',
    'swaps_debtors.php',
    'expenditures.php',
    'print_receipt.php',
    'sale_success.php',
    'debt_approval.php',
    'swap_approval.php',
    'sales_debtors.php'
];

$staff_pages = [
    'dashboard.php',
    'sales.php',
    'returns.php',
    'swaps.php',
    'swaps_debtors.php',
    'expenditures.php',
    'print_receipt.php',
    'sale_success.php',
    'sales_debtors.php'
];

// Get current page (handle subdirectories)
$current_script = basename($_SERVER['SCRIPT_FILENAME']);
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$current_path = ltrim(str_replace($current_dir, '', $_SERVER['SCRIPT_NAME']), '/');

// Verify access based on role
switch ($_SESSION['role']) {
    case 'admin':
        if (!in_array($current_script, $admin_pages) && !in_array($current_path, $admin_pages)) {
            error_log("Unauthorized admin access attempt to: " . $_SERVER['REQUEST_URI']);
            header("Location: unauthorized.php");
            exit();
        }
        break;
        
    case 'staff':
        if (!in_array($current_script, $staff_pages) && !in_array($current_path, $staff_pages)) {
            error_log("Unauthorized staff access attempt to: " . $_SERVER['REQUEST_URI']);
            header("Location: unauthorized.php");
            exit();
        }
        break;
        
    default:
        // Invalid role detected - force logout
        error_log("Invalid role detected: " . $_SESSION['role']);
        header("Location: logout.php");
        exit();
}

// Verify user still exists in database (prevent session hijacking)
$stmt = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    $user = $result->fetch_assoc();
    if ($user['role'] !== $_SESSION['role']) {
        $_SESSION['role'] = $user['role'];
        header("Refresh:0");
        exit();
    }
}
$stmt->close();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>