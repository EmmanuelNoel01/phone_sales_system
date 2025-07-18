<?php
session_start();
require '../includes/config.php';
require '../includes/auth.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set the page title
$page_title = "Sales Debtors";

// Pagination variables
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Clear debt action
if (isset($_POST['clear_debt'])) {
    $sale_id = $_POST['sale_id'];

    // Fetch the sale price to update amount paid
    $sale_query = $conn->query("SELECT sale_price FROM sales WHERE id = $sale_id");
    $sale_data = $sale_query->fetch_assoc();
    
    if ($sale_data) {
        $sale_price = $sale_data['sale_price'];
        // Update approval status to 'Pending'
        $conn->query("UPDATE sales SET approval_status = 'Pending' WHERE id = $sale_id");
        // Set notification message
        $_SESSION['notification'] = "Your request to clear the debt has been submitted for approval.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page"); // Redirect to the same page
    exit; // Stop further execution
}

// Fetch total sales with balance due > 0 for pagination
$total_sales_result = $conn->query("SELECT COUNT(*) AS total FROM sales WHERE balance_due > 0");
$total_sales = $total_sales_result->fetch_assoc()['total'];
$total_pages = ceil($total_sales / $limit);

// Fetch sales with pagination
$sales = $conn->query("
    SELECT s.*, u.name AS staff_name, p.brand, p.model
    FROM sales s
    LEFT JOIN users u ON s.sold_by = u.id
    LEFT JOIN phones p ON s.phone_id = p.id
    WHERE s.balance_due > 0
    ORDER BY s.sale_date DESC
    LIMIT $limit OFFSET $offset
");

require '../includes/header.php';

// Check for notifications
if (isset($_SESSION['notification'])) {
    echo '<div class="alert alert-success">' . $_SESSION['notification'] . '</div>';
    unset($_SESSION['notification']);
}
?>

<div class="card shadow">
    <div class="card-header">
        <h5 class="m-0 font-weight-bold text-danger">Outstanding Sales Balances</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Sale Date</th>
                    <th>Total Price (UGX)</th>
                    <th>Amount Paid (UGX)</th>
                    <th>Balance Due (UGX)</th>
                    <th>Processed By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales->num_rows > 0): ?>
                    <?php while ($row = $sales->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['brand'] . ' ' . $row['model']) ?></td>
                            <td><?= date('d-M-Y', strtotime($row['sale_date'])) ?></td>
                            <td><?= number_format($row['sale_price']) ?></td>
                            <td><?= number_format($row['amount_paid']) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($row['balance_due']) ?></td>
                            <td><?= htmlspecialchars($row['staff_name']) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="sale_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="clear_debt" class="btn btn-danger btn-sm">Clear Debt</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No outstanding sales balances found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($total_pages > 1): ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php require '../includes/footer.php'; ?>