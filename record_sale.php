<?php
require 'includes/config.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_id = $_POST['phone_id'];
    $customer_name = $_POST['customer_name'];
    $sale_price = $_POST['sale_price'];
    $user_id = $_SESSION['user_id'];

    // Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (phone_id, customer_name, sale_price, user_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Failed to prepare insert: " . $conn->error);
    }

    $stmt->bind_param("isdi", $phone_id, $customer_name, $sale_price, $user_id);
    if ($stmt->execute()) {
        $sale_id = $stmt->insert_id;
        header("Location: /phone_sales_system/sale_success.php?sale_id=$sale_id");
        exit();
    } else {
        die("Sale insert failed: " . $stmt->error);
    }
}
?>
