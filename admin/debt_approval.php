<?php
session_start();
require '../includes/config.php';
require '../includes/auth.php';

// Set the page title
$page_title = "Debt Approval Requests";

// Pagination variables
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Approve debt action
if (isset($_POST['approve_debt'])) {
    $sale_id = $_POST['sale_id'];
    
    // Fetch current sale data
    $sale_query = $conn->query("SELECT amount_paid, balance_due FROM sales WHERE id = $sale_id");
    $sale_data = $sale_query->fetch_assoc();
    
    if ($sale_data) {
        $current_amount_paid = $sale_data['amount_paid'];
        $current_balance_due = $sale_data['balance_due'];

        // Update amount paid and clear balance due
        $new_amount_paid = $current_amount_paid + $current_balance_due;
        
        $conn->query("UPDATE sales SET amount_paid = $new_amount_paid, balance_due = 0, approval_status = 'Approved' WHERE id = $sale_id");
        $_SESSION['notification'] = "Debt clearance approved successfully.";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page"); // Redirect to the same page
    exit;
}

// Fetch pending debt clearance requests
$pending_requests = $conn->query("SELECT * FROM sales WHERE approval_status = 'Pending' AND balance_due > 0 LIMIT $limit OFFSET $offset");
$total_requests = $pending_requests->num_rows;
$total_pages = ceil($total_requests / $limit);

require '../includes/header.php';

// Check for notifications
if (isset($_SESSION['notification'])) {
    echo '<div class="alert alert-success">' . $_SESSION['notification'] . '</div>';
    unset($_SESSION['notification']);
}
?>

<div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-warning">Pending Debt Clearance Requests</h6>
    </div>
    <div class="card-body">
        <?php if ($pending_requests->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Amount Due</th>
                            <th>Requested On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = $pending_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $request['id'] ?></td>
                                <td><?= htmlspecialchars($request['customer_name']) ?></td>
                                <td>UGX <?= number_format($request['balance_due']) ?></td>
                                <td><?= date('Y-m-d', strtotime($request['sale_date'])) ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="sale_id" value="<?= $request['id'] ?>">
                                        <button type="submit" name="approve_debt" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No pending requests for debt clearance.</div>
        <?php endif; ?>
    </div>
</div>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
        </li>
    </ul>
</nav>

<?php require '../includes/footer.php'; ?>