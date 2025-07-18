<?php
require '../includes/config.php';
require '../includes/auth.php';
require '../includes/header.php';

$page_title = "Balance Sheet";

// Pagination setup
$income_page = isset($_GET['income_page']) ? max(1, (int)$_GET['income_page']) : 1;
$expense_page = isset($_GET['expense_page']) ? max(1, (int)$_GET['expense_page']) : 1;
$per_page = 10;

// Filter dates
$from = $_GET['from_date'] ?? '';
$to = $_GET['to_date'] ?? '';

$where_sales = '';
$where_exp = '';
$params_sales = [];
$params_exp = [];

if (!empty($from) && !empty($to)) {
    $where_sales = "WHERE sale_date BETWEEN ? AND ?";
    $where_exp = "WHERE created_at BETWEEN ? AND ?";
    $params_sales = [$from, $to];
    $params_exp = [$from, $to];
}

// Calculate today's profit
$today = date('Y-m-d');

$sql_income_today = "SELECT IFNULL(SUM(sale_price), 0) as today_income FROM sales WHERE DATE(sale_date) = ?";
$stmt_today_income = $conn->prepare($sql_income_today);
$stmt_today_income->bind_param('s', $today);
$stmt_today_income->execute();
$today_income = $stmt_today_income->get_result()->fetch_assoc()['today_income'] ?? 0;

$sql_expense_today = "SELECT IFNULL(SUM(amount), 0) as today_expense FROM expenditures WHERE DATE(created_at) = ?";
$stmt_today_expense = $conn->prepare($sql_expense_today);
$stmt_today_expense->bind_param('s', $today);
$stmt_today_expense->execute();
$today_expense = $stmt_today_expense->get_result()->fetch_assoc()['today_expense'] ?? 0;

$today_profit = $today_income - $today_expense;

// Define profit, loss and break even amounts
$profit_amount = $today_profit > 0 ? $today_profit : 0;
$loss_amount = $today_profit < 0 ? abs($today_profit) : 0;
$break_even = ($today_profit == 0) ? 1 : 0;

// Get total income records count for pagination
$count_income_sql = "SELECT COUNT(*) as total FROM sales $where_sales";
$stmt_count_income = $conn->prepare($count_income_sql);
if (!empty($params_sales)) {
    $stmt_count_income->bind_param('ss', ...$params_sales);
}
$stmt_count_income->execute();
$total_income_rows = $stmt_count_income->get_result()->fetch_assoc()['total'] ?? 0;
$income_offset = ($income_page - 1) * $per_page;

// Get total expenditure records count for pagination
$count_expense_sql = "SELECT COUNT(*) as total FROM expenditures $where_exp";
$stmt_count_expense = $conn->prepare($count_expense_sql);
if (!empty($params_exp)) {
    $stmt_count_expense->bind_param('ss', ...$params_exp);
}
$stmt_count_expense->execute();
$total_expense_rows = $stmt_count_expense->get_result()->fetch_assoc()['total'] ?? 0;
$expense_offset = ($expense_page - 1) * $per_page;

// Fetch income records with pagination
$income_detail_sql = "SELECT sale_date, customer_name, sale_price, amount_paid, balance_due FROM sales $where_sales ORDER BY sale_date DESC LIMIT ? OFFSET ?";
$stmt_detail = $conn->prepare($income_detail_sql);

if ($stmt_detail === false) {
    die("Prepare failed (income details): " . $conn->error);
}

if (!empty($params_sales)) {
    // bind date params + limit + offset
    $types = 'ssii'; // 2 strings for dates + 2 integers for limit/offset
    $stmt_detail->bind_param($types, $params_sales[0], $params_sales[1], $per_page, $income_offset);
} else {
    // no date filters: bind only limit and offset
    $stmt_detail->bind_param('ii', $per_page, $income_offset);
}
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();

// Fetch expenditure records with pagination
$expense_detail_sql = "SELECT created_at, title, description, amount FROM expenditures $where_exp ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt_exp_detail = $conn->prepare($expense_detail_sql);

if ($stmt_exp_detail === false) {
    die("Prepare failed (expense details): " . $conn->error);
}

if (!empty($params_exp)) {
    $types_exp = 'ssii';
    $stmt_exp_detail->bind_param($types_exp, $params_exp[0], $params_exp[1], $per_page, $expense_offset);
} else {
    $stmt_exp_detail->bind_param('ii', $per_page, $expense_offset);
}
$stmt_exp_detail->execute();
$result_exp_detail = $stmt_exp_detail->get_result();

function renderPagination($currentPage, $totalRows, $perPage, $pageParam, $extraParams=[]) {
    $totalPages = ceil($totalRows / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Build URL with extra GET params
    $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
    parse_str($_SERVER['QUERY_STRING'] ?? '', $queryParams);

    // Remove page param to avoid duplicates
    unset($queryParams[$pageParam]);

    // Add extra params like date filters
    foreach ($extraParams as $key => $value) {
        $queryParams[$key] = $value;
    }

    // Previous
    $prevClass = $currentPage <= 1 ? 'disabled' : '';
    $prevPage = max(1, $currentPage - 1);
    $queryParams[$pageParam] = $prevPage;
    $html .= '<li class="page-item ' . $prevClass . '"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">&laquo;</a></li>';

    // Page numbers (show max 5 pages around current)
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $queryParams[$pageParam] = $i;
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
    }

    // Next
    $nextClass = $currentPage >= $totalPages ? 'disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);
    $queryParams[$pageParam] = $nextPage;
    $html .= '<li class="page-item ' . $nextClass . '"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">&raquo;</a></li>';

    $html .= '</ul></nav>';

    return $html;
}
?>

<div class="container mt-4 mb-5">
    <!-- <h3 class="mb-3"><?= htmlspecialchars($page_title) ?></h3> -->

    <div class="row mb-4 g-3 text-center">
      <div class="col-md-4">
        <div class="p-4 rounded shadow-sm" style="background-color:#e6f4ea;">
          <h5 class="fw-semibold">Profits Made Today</h5>
          <div class="fs-2 fw-bold text-success">UGX <?= number_format($profit_amount) ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 rounded shadow-sm" style="background-color:#f9e6e6;">
          <h5 class="fw-semibold">Losses Made Today</h5>
          <div class="fs-2 fw-bold text-danger">UGX <?= number_format($loss_amount) ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 rounded shadow-sm" style="background-color:#f0f0f0;">
          <h5 class="fw-semibold">Break Even Today</h5>
          <div class="fs-2 fw-bold text-secondary"><?= $break_even ? 'Yes' : 'No' ?></div>
        </div>
      </div>
    </div>

    <form method="get" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" id="from_date" name="from_date" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" id="to_date" name="to_date" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="balance_sheet.php" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    <div class="row">
        <!-- Income Table -->
        <div class="col-md-6 mb-4">
            <h5>Income (Sales) Records</h5>
            <div class="table-responsive border rounded p-2">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Sale Date</th>
                            <th>Customer</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-end">Amount Paid</th>
                            <th class="text-end">Balance Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = $income_offset + 1;
                        if ($result_detail->num_rows === 0) {
                            echo '<tr><td colspan="6" class="text-center">No income records found.</td></tr>';
                        } else {
                            while ($row = $result_detail->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$row['sale_date']}</td>
                                    <td>" . htmlspecialchars($row['customer_name']) . "</td>
                                    <td class='text-end'>UGX " . number_format($row['sale_price']) . "</td>
                                    <td class='text-end'>UGX " . number_format($row['amount_paid']) . "</td>
                                    <td class='text-end'>UGX " . number_format($row['balance_due']) . "</td>
                                </tr>";
                                $i++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?= renderPagination($income_page, $total_income_rows, $per_page, 'income_page', ['from_date'=>$from, 'to_date'=>$to, 'expense_page'=>$expense_page]) ?>
        </div>

        <!-- Expenditure Table -->
        <div class="col-md-6 mb-4">
            <h5>Expenditure Records</h5>
            <div class="table-responsive border rounded p-2">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = $expense_offset + 1;
                        if ($result_exp_detail->num_rows === 0) {
                            echo '<tr><td colspan="5" class="text-center">No expenditure records found.</td></tr>';
                        } else {
                            while ($row = $result_exp_detail->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$row['created_at']}</td>
                                    <td>" . htmlspecialchars($row['title']) . "</td>
                                    <td>" . htmlspecialchars($row['description']) . "</td>
                                    <td class='text-end'>UGX " . number_format($row['amount']) . "</td>
                                </tr>";
                                $i++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?= renderPagination($expense_page, $total_expense_rows, $per_page, 'expense_page', ['from_date'=>$from, 'to_date'=>$to, 'income_page'=>$income_page]) ?>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
