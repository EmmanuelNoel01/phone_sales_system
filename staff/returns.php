<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Process Returns";

// --- Handle filter inputs ---
$filter_customer = isset($_GET['customer']) ? $conn->real_escape_string($_GET['customer']) : '';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build sales query with filters
$sales_sql = "SELECT id, customer_name, phone_id FROM sales WHERE 1=1";

if ($filter_customer !== '') {
    $sales_sql .= " AND customer_name LIKE '%$filter_customer%'";
}

if ($filter_date_from !== '') {
    $sales_sql .= " AND sale_date >= '$filter_date_from 00:00:00'";
}

if ($filter_date_to !== '') {
    $sales_sql .= " AND sale_date <= '$filter_date_to 23:59:59'";
}

$sales_sql .= " ORDER BY id DESC";

$sales = $conn->query($sales_sql);

// --- Handle form submission for return ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = (int)$_POST['sale_id'];
    $reason = $conn->real_escape_string($_POST['reason']);
    $status = 'Returned'; // default status
    $returned_by = $conn->real_escape_string($_POST['returned_by']);
    $processed_by = $_SESSION['user_id'];

    $result = $conn->query("SELECT phone_id FROM sales WHERE id = $sale_id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $phone_id = (int)$row['phone_id'];

        $insert = $conn->query("INSERT INTO returns (sale_id, phone_id, return_reason, status, processed_by, return_date)
                                VALUES ($sale_id, $phone_id, '$reason', '$status', $processed_by, NOW())");

        if ($insert) {
            $update = $conn->query("UPDATE phones SET status = 'Returned' WHERE id = $phone_id");

            if ($update) {
                $_SESSION['success'] = "Return processed successfully!";
            } else {
                $_SESSION['error'] = "Return recorded, but failed to update phone status.";
            }
        } else {
            $_SESSION['error'] = "Failed to record return.";
        }
    } else {
        $_SESSION['error'] = "Invalid sale selected.";
    }

    // Redirect to avoid form resubmission
    header("Location: returns.php");
    exit();
}

// Load returned phones for table
$returns = $conn->query("
    SELECT r.*, p.brand, p.model, s.customer_name 
    FROM returns r
    JOIN phones p ON r.phone_id = p.id
    JOIN sales s ON r.sale_id = s.id
    WHERE r.status = 'Returned'
");
?>

<?php require '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-6">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">New Return</h6>
            </div>
            <div class="card-body">

                <!-- Filter form -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($filter_date_from) ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($filter_date_to) ?>">
                    </div>
                    <div class="col-md-10">
                        <label for="customer" class="form-label">Customer Name</label>
                        <input type="text" id="customer" name="customer" class="form-control" placeholder="Enter customer name" value="<?= htmlspecialchars($filter_customer) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                <!-- Return processing form -->
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Select Sale</label>
                        <select name="sale_id" class="form-select" required>
                            <option value="">-- Select Sale --</option>
                            <?php while($sale = $sales->fetch_assoc()): ?>
                                <option value="<?= $sale['id'] ?>">
                                    Sale #<?= $sale['id'] ?> - <?= htmlspecialchars($sale['customer_name']) ?> (Phone ID: <?= $sale['phone_id'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">Please select a sale</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Who Returned It?</label>
                        <input type="text" name="returned_by" class="form-control" required>
                        <div class="invalid-feedback">Please enter who returned the phone</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                        <div class="invalid-feedback">Please enter return reason</div>
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
                <h6 class="m-0 font-weight-bold text-primary">Returned Phones</h6>
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
