<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expenditure'])) {
    $title = $_POST['title'];
    $amount = str_replace(',', '', $_POST['amount']); 
    $category = $_POST['category'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO expenditures (title, amount, category, description, added_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sdssi", $title, $amount, $category, $description, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Expenditure added successfully!";
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filters
$where = "";
$params = [];
$param_types = "";
$from = $_GET['from_date'] ?? '';
$to = $_GET['to_date'] ?? '';

if (!empty($from) && !empty($to)) {
    $where = "WHERE DATE(created_at) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $param_types = "ss";
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM expenditures $where";
$count_stmt = $conn->prepare($count_sql);
if ($param_types) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_rows / $limit);

// Get paginated results
$data_sql = "SELECT * FROM expenditures $where ORDER BY created_at DESC LIMIT ?, ?";
$data_stmt = $conn->prepare($data_sql);

if ($param_types) {
    $param_types .= "ii";
    $params[] = $offset;
    $params[] = $limit;
    $data_stmt->bind_param($param_types, ...$params);
} else {
    $data_stmt->bind_param("ii", $offset, $limit);
}

$data_stmt->execute();
$result = $data_stmt->get_result();
?>

<div class="container-fluid mt-4">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'];
        unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- Add Expenditure Form -->
    <form method="post" class="row g-3 mb-4" id="expenditure-form">
        <div class="col-md-2">
            <input type="text" name="title" class="form-control" placeholder="Spent" required>
        </div>
        <div class="col-md-1">
            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" min="1">
        </div>
        <div class="col-md-2">
            <input type="number" name="unit_price" id="unit_price" class="form-control" placeholder="Amount" required>
        </div>
        <div class="col-md-2">
            <input type="text" id="amount" class="form-control" placeholder="Total Amount (UGX)" readonly required>
            <input type="hidden" name="amount" id="hidden_amount">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-control" required>
                <option value="">Select Category</option>
                <option value="Food">Food</option>
                <option value="Transport">Transport</option>
                <option value="Rent">Rent</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="description" class="form-control" placeholder="Description">
        </div>
        <div class="col-md-1">
            <button type="submit" name="add_expenditure" class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <!-- Filter Form -->
    <form method="get" class="row g-2 align-items-end mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label mb-1">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control"
                value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label mb-1">To Date</label>
            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label d-block mb-1 invisible">Filter</label>
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <label class="form-label d-block mb-1 invisible">Reset</label>
            <a href="expenditures.php" class="btn btn-outline-danger w-100">Reset</a>
        </div>
    </form>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Spent</th>
                    <th>Amount (UGX)</th>
                    <th>Category</th>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <th>Description</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= number_format($row['amount'], 0) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&from_date=<?= urlencode($from) ?>&to_date=<?= urlencode($to) ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&from_date=<?= urlencode($from) ?>&to_date=<?= urlencode($to) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&from_date=<?= urlencode($from) ?>&to_date=<?= urlencode($to) ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const amountInput = document.getElementById('amount');
    const hiddenAmountInput = document.getElementById('hidden_amount');

    function calculateAmount() {
        const qty = parseFloat(quantityInput.value) || 0;
        const unit = parseFloat(unitPriceInput.value) || 0;
        const rawAmount = qty * unit;

        amountInput.value = rawAmount.toLocaleString('en-UG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        hiddenAmountInput.value = rawAmount.toFixed(2);
    }

    quantityInput.addEventListener('input', calculateAmount);
    unitPriceInput.addEventListener('input', calculateAmount);
    window.addEventListener('load', calculateAmount);
</script>

<?php require_once 'includes/footer.php'; ?>