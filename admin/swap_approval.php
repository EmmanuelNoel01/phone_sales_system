<?php
session_start();
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Swap Approval Requests";

// Pagination variables
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Approve swap debt clearance
if (isset($_POST['approve_swap'])) {
    $swap_id = $_POST['swap_id'];
    
    // Fetch current swap data
    $swap_query = $conn->query("SELECT amount_paid, balance_due FROM swaps WHERE id = $swap_id");

    if ($swap_query === false) {
        $_SESSION['error'] = "Error fetching swap data: " . $conn->error;
    } else {
        $swap_data = $swap_query->fetch_assoc();
        
        if ($swap_data) {
            $current_amount_paid = $swap_data['amount_paid'];
            $current_balance_due = $swap_data['balance_due'];

            // Update amount paid and clear balance due
            $new_amount_paid = $current_amount_paid + $current_balance_due;
            $update_query = $conn->query("UPDATE swaps SET amount_paid = $new_amount_paid, balance_due = 0 WHERE id = $swap_id");

            if ($update_query === false) {
                $_SESSION['error'] = "Error updating swap data: " . $conn->error;
            } else {
                $_SESSION['notification'] = "Swap debt clearance approved successfully.";
            }
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit;
}

// Fetch pending swap debt clearance requests
$pending_swaps = $conn->query("SELECT * FROM swaps WHERE approval_status = 'Pending' AND balance_due > 0 LIMIT $limit OFFSET $offset");

if ($pending_swaps === false) {
    $_SESSION['error'] = "Error fetching pending swaps: " . $conn->error;
}

$total_requests = $conn->query("SELECT COUNT(*) AS total FROM swaps WHERE approval_status = 'Pending' AND balance_due > 0");
$total_requests_count = $total_requests ? $total_requests->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_requests_count / $limit);

require '../includes/header.php';

// Check for notifications
if (isset($_SESSION['notification'])) {
    echo '<div class="alert alert-success">' . $_SESSION['notification'] . '</div>';
    unset($_SESSION['notification']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-warning">Pending Swap Approval Requests</h6>
    </div>
    <div class="card-body">
        <?php if ($pending_swaps && $pending_swaps->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipient</th>
                            <th>Balance Due</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($swap = $pending_swaps->fetch_assoc()): ?>
                            <tr>
                                <td><?= $swap['id'] ?></td>
                                <td><?= htmlspecialchars($swap['recipient_name']) ?></td>
                                <td>UGX <?= number_format($swap['balance_due']) ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="swap_id" value="<?= $swap['id'] ?>">
                                        <button type="submit" name="approve_swap" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No pending swap requests for approval.</div>
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