<?php
require '../includes/config.php';
require '../includes/auth.php';

// Get all returns
$returns = $conn->query("
    SELECT r.*, p.brand, p.model, s.customer_name, u.name as processed_by_name
    FROM returns r
    JOIN phones p ON r.phone_id = p.id
    JOIN sales s ON r.sale_id = s.id
    JOIN users u ON r.processed_by = u.id
    ORDER BY r.return_date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Returns Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Returns History</h2>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phone</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Processed By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($return = $returns->fetch_assoc()): ?>
                <tr>
                    <td><?= $return['id'] ?></td>
                    <td><?= $return['brand'] ?> <?= $return['model'] ?></td>
                    <td><?= $return['customer_name'] ?></td>
                    <td><?= $return['status'] ?></td>
                    <td><?= $return['processed_by_name'] ?></td>
                    <td><?= date('M d, Y', strtotime($return['return_date'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>