<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Add New Phone";

// CSV Template download
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="phone_template.csv"');
    echo "Brand,Model,IMEI,Storage,Color,Condition,Price (UGX),Quantity\n";
    echo "Apple,iPhone 13,123456789012345,128GB,Blue,New,1,500,000,5\n";
    exit();
}

// CSV Import
if (isset($_POST['import_csv'])) {
    $import_errors = [];
    $import_success = 0;

    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 8) continue;

            [$brand, $model, $imei, $storage, $color, $condition, $ignored_price, $ignored_quantity] = $row;

            $imei = trim($imei);
            // Ignore price and quantity from CSV, always set:
            $price = 0;
            $quantity = 1;

            // Validate duplicate IMEI
            $check = $conn->query("SELECT id FROM phones WHERE imei = '$imei'");
            if ($check->num_rows > 0) {
                $import_errors[] = "Duplicate IMEI: $imei";
                continue;
            }

            $conn->query("INSERT INTO phones (brand, model, imei, storage, color, `condition`, price, quantity, added_by)
                VALUES (
                    '{$conn->real_escape_string($brand)}',
                    '{$conn->real_escape_string($model)}',
                    '$imei',
                    '{$conn->real_escape_string($storage)}',
                    '{$conn->real_escape_string($color)}',
                    '{$conn->real_escape_string($condition)}',
                    $price,
                    $quantity,
                    {$_SESSION['user_id']}
                )");

            $import_success++;
        }

        fclose($file);
        $_SESSION['success'] = "$import_success phones imported.";
        if (!empty($import_errors)) {
            $_SESSION['error'] = implode('<br>', $import_errors);
        }
        header("Location: add_phone.php");
        exit();
    }
}

// Manual form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['brand'])) {
    $brand = trim($conn->real_escape_string($_POST['brand']));
    $model = trim($conn->real_escape_string($_POST['model']));
    $imei = trim($conn->real_escape_string($_POST['imei']));
    $storage = trim($conn->real_escape_string($_POST['storage']));
    $color = trim($conn->real_escape_string($_POST['color']));
    $condition = trim($conn->real_escape_string($_POST['condition']));
    $price = 0;  // Default 0
    $quantity = 1; // Default 1

    if (
        empty($brand) || empty($model) || empty($imei) || empty($storage) ||
        empty($color) || empty($condition)
    ) {
        $_SESSION['error'] = "Please fill in all fields correctly.";
        header("Location: add_phone.php");
        exit();
    }

    $conn->query("INSERT INTO phones (brand, model, imei, storage, color, `condition`, price, quantity, added_by)
        VALUES ('$brand', '$model', '$imei', '$storage', '$color', '$condition', $price, $quantity, {$_SESSION['user_id']})");

    $_SESSION['success'] = "Phone added successfully!";
    header("Location: dashboard.php");
    exit();
}

require '../includes/header.php';
?>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Import Phones via CSV</h6>
        <a href="add_phone.php?download_template=1" class="btn btn-sm btn-secondary">Download Template</a>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Upload CSV File</label>
                <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                <div class="form-text">Ensure your file matches the downloadable template. Prices should be in UGX.</div>
            </div>
            <button type="submit" name="import_csv" class="btn btn-success">
                <i class="bi bi-upload me-1"></i> Import CSV
            </button>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Phone</h6>
    </div>
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" class="form-control" name="brand" required>
                    <div class="invalid-feedback">Please enter brand</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" class="form-control" name="model" required>
                    <div class="invalid-feedback">Please enter model</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">IMEI</label>
                    <input type="text" class="form-control" name="imei" required>
                    <div class="invalid-feedback">Please enter IMEI</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Storage</label>
                    <select class="form-select" name="storage" required>
                        <option value="">Select storage</option>
                        <option value="64GB">64GB</option>
                        <option value="128GB">128GB</option>
                        <option value="256GB">256GB</option>
                        <option value="512GB">512GB</option>
                    </select>
                    <div class="invalid-feedback">Please select storage</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" required>
                    <div class="invalid-feedback">Please enter color</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Condition</label>
                    <select class="form-select" name="condition" required>
                        <option value="">Select condition</option>
                        <option value="New">New</option>
                        <option value="Refurbished">Refurbished</option>
                        <option value="Used">Used</option>
                    </select>
                    <div class="invalid-feedback">Please select condition</div>
                </div>
            </div>

            <!-- Removed price and quantity inputs from UI -->

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Add Phone
            </button>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

<script>
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
