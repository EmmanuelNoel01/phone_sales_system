<?php
require '../includes/config.php';
require '../includes/auth.php';
$page_title = "Swap Debtors";

$swaps = $conn->query("
    SELECT s.*, u.name AS staff_name
    FROM swaps s
    LEFT JOIN users u ON s.swapped_by = u.id
    WHERE s.balance_due > 0
    ORDER BY s.swap_date DESC
");

require '../includes/header.php';
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
