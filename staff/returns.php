<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Process Returns";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = (int)$_POST['sale_id'];
    $reason = $conn->real_escape_string($_POST['reason']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $conn->query("
        INSERT INTO returns (sale_id, phone_id, return_reason, status, processed_by)
        SELECT id, phone_id, '$reason', '$status', {$_SESSION['user_id']}
        FROM sales WHERE id = $sale_id
    ");
    
    $_SESSION['success'] = "Return processed successfully!";
    header("Location: returns.php");
    exit();
}

// Get pending returns
$returns = $conn->query("
    SELECT r.*, p.brand, p.model, s.customer_name 
    FROM returns r
    JOIN phones p ON r.phone_id = p.id
    JOIN sales s ON r.sale_id = s.id
    WHERE r.status = 'Repairing'
");
?>

<?php require '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">New Return</h6>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Sale ID</label>
                        <input type="number" name="sale_id" class="form-control" required>
                        <div class="invalid-feedback">Please enter sale ID</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback">Please enter return reason</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Select Action --</option>
                            <option value="Repairing">Send for Repair</option>
                            <option value="Swapped">Swap Phone</option>
                            <option value="Refunded">Issue Refund</option>
                        </select>
                        <div class="invalid-feedback">Please select action</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Process Return
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pending Returns</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Phone</th>
                                <th>Customer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($return = $returns->fetch_assoc()): ?>
                            <tr>
                                <td><?= $return['id'] ?></td>
                                <td><?= $return['brand'] ?> <?= $return['model'] ?></td>
                                <td><?= $return['customer_name'] ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?= $return['status'] ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>