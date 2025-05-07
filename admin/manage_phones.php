<?php
require '../includes/config.php';
require '../includes/auth.php';

// Delete phone if requested
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM phones WHERE id = $id");
    $_SESSION['message'] = "Phone deleted successfully";
    header("Location: manage_phones.php");
    exit();
}

// Get all phones
$phones = $conn->query("
    SELECT p.*, u.name as added_by 
    FROM phones p
    LEFT JOIN users u ON p.added_by = u.id
    ORDER BY p.added_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Phones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Phone Inventory</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Added By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($phone = $phones->fetch_assoc()): ?>
                <tr>
                    <td><?= $phone['id'] ?></td>
                    <td><?= htmlspecialchars($phone['brand']) ?></td>
                    <td><?= htmlspecialchars($phone['model']) ?></td>
                    <td><?= substr($phone['imei'], 0, 4) ?>...<?= substr($phone['imei'], -4) ?></td>
                    <td>$<?= number_format($phone['price'], 2) ?></td>
                    <td><?= $phone['quantity'] ?></td>
                    <td><?= $phone['added_by'] ?? 'System' ?></td>
                    <td>
                        <a href="edit_phone.php?id=<?= $phone['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="manage_phones.php?delete=<?= $phone['id'] ?>" class="btn btn-sm btn-danger delete-btn">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="add_phone.php" class="btn btn-primary">Add New Phone</a>
    </div>
</body>
</html>