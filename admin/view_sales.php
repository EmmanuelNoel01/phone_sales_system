<?php
require '../includes/config.php';
require '../includes/auth.php';

// Get all sales
$sales = $conn->query("
    SELECT s.*, p.brand, p.model, u.name as seller 
    FROM sales s
    JOIN phones p ON s.phone_id = p.id
    JOIN users u ON s.sold_by = u.id
    ORDER BY s.sale_date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Sales History</h2>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phone</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Seller</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($sale = $sales->fetch_assoc()): ?>
                <tr>
                    <td><?= $sale['id'] ?></td>
                    <td><?= $sale['brand'] ?> <?= $sale['model'] ?></td>
                    <td><?= $sale['customer_name'] ?></td>
                    <td>$<?= number_format($sale['sale_price'], 2) ?></td>
                    <td><?= $sale['seller'] ?></td>
                    <td><?= date('M d, Y h:i A', strtotime($sale['sale_date'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>