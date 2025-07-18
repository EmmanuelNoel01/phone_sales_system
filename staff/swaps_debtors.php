<?php
session_start();
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Swap Debtors";

// Clear debt action
if (isset($_POST['clear_debt'])) {
    $swap_id = $_POST['swap_id'];
    
    // Fetch current swap data
    $swap_query = $conn->query("SELECT amount_paid, balance_due FROM swaps WHERE id = $swap_id");
    $swap_data = $swap_query->fetch_assoc();
    
    if ($swap_data) {
        $current_amount_paid = $swap_data['amount_paid'];
        $current_balance_due = $swap_data['balance_due'];

        // Update amount paid and clear balance due
        $new_amount_paid = $current_amount_paid + $current_balance_due;
        
        $conn->query("UPDATE swaps SET amount_paid = $new_amount_paid, balance_due = 0 WHERE id = $swap_id");
        $_SESSION['notification'] = "Debt clearance approved successfully.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit;
}

// Fetch swaps with balance due
$swaps = $conn->query("
    SELECT s.*, u.name AS staff_name
    FROM swaps s
    LEFT JOIN users u ON s.swapped_by = u.id
    WHERE s.balance_due > 0
    ORDER BY s.swap_date DESC
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
        <h5 class="m-0 font-weight-bold text-danger">Outstanding Swap Balances</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Recipient</th>
                    <th>Phone Given</th>
                    <th>Swap Date</th>
                    <th>Valued (UGX)</th>
                    <th>Paid (UGX)</th>
                    <th><span class="text-danger">Balance (UGX)</span></th>
                    <th>Processed By</th>
                    <th>Action</th>  <!-- Added Action Column -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $swaps->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['recipient_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['new_phone_brand_model']) ?><br>
                            <small>IMEI: <?= htmlspecialchars($row['new_phone_serial']) ?></small>
                        </td>
                        <td><?= date('d-M-Y', strtotime($row['swap_date'])) ?></td>
                        <td><?= number_format($row['valued_amount']) ?></td>
                        <td><?= number_format($row['amount_paid']) ?></td>
                        <td class="text-danger fw-bold"><?= number_format($row['balance_due']) ?></td>
                        <td><?= htmlspecialchars($row['staff_name']) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="swap_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="clear_debt" class="btn btn-danger btn-sm">Clear Debt</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>