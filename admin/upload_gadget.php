<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Upload Gadgets";

// Handle CSV template download
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="gadget_template.csv"');
    echo "name,model,serial_number,specifications,price,quantity\n";
    echo "Example Gadget,Model X,12345,\"Specs info\",1000000,10\n";
    exit();
}

// Handle CSV import
if (isset($_POST['import_csv'])) {
    $import_errors = [];
    $import_success = 0;

    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            // Ensure row has at least name, price, quantity (6 columns)
            if (count($row) < 6) {
                $import_errors[] = "Invalid CSV format or missing fields.";
                continue;
            }

            // Assign columns, trim values
            [$name, $model, $serial_number, $specifications, $price, $quantity] = array_map('trim', $row);

            // Defaults if empty
            $price = ($price === '' || !is_numeric(str_replace(',', '', $price))) ? 0 : (int)str_replace([',', 'UGX', 'ugx', ' '], '', $price);
            $quantity = ($quantity === '' || !is_numeric($quantity)) ? 1 : (int)$quantity;

            if (empty($name)) {
                $import_errors[] = "Name is required.";
                continue;
            }

            // Check duplicate serial_number if given and not empty
            if ($serial_number !== '') {
                $check = $conn->query("SELECT id FROM gadgets WHERE serial_number = '".$conn->real_escape_string($serial_number)."'");
                if ($check && $check->num_rows > 0) {
                    $import_errors[] = "Duplicate serial number: $serial_number";
                    continue;
                }
            }

            $conn->query("INSERT INTO gadgets (name, model, serial_number, specifications, price, quantity, added_by) VALUES (
                '".$conn->real_escape_string($name)."',
                '".$conn->real_escape_string($model)."',
                '".$conn->real_escape_string($serial_number)."',
                '".$conn->real_escape_string($specifications)."',
                $price,
                $quantity,
                ".$_SESSION['user_id']."
            )");

            $import_success++;
        }

        fclose($file);
        $_SESSION['success'] = "$import_success gadgets imported.";
        if (!empty($import_errors)) {
            $_SESSION['error'] = implode('<br>', $import_errors);
        }
        header("Location: upload_gadget.php");
        exit();
    }
}

// Manual form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && !isset($_POST['import_csv'])) {
    $name = trim($conn->real_escape_string($_POST['name']));
    $model = trim($conn->real_escape_string($_POST['model'] ?? ''));
    $serial_number = trim($conn->real_escape_string($_POST['serial_number'] ?? ''));
    $specifications = trim($conn->real_escape_string($_POST['specifications'] ?? ''));
    $price = isset($_POST['price']) && is_numeric($_POST['price']) ? (int)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (empty($name)) {
        $_SESSION['error'] = "Name is required.";
        header("Location: upload_gadget.php");
        exit();
    }

    // Check duplicate serial number if given
    if ($serial_number !== '') {
        $check = $conn->query("SELECT id FROM gadgets WHERE serial_number = '".$conn->real_escape_string($serial_number)."'");
        if ($check && $check->num_rows > 0) {
            $_SESSION['error'] = "Duplicate serial number: $serial_number";
            header("Location: upload_gadget.php");
            exit();
        }
    }

    $conn->query("INSERT INTO gadgets (name, model, serial_number, specifications, price, quantity, added_by) VALUES (
        '$name', '$model', '$serial_number', '$specifications', $price, $quantity, ".$_SESSION['user_id']."
    )");

    $_SESSION['success'] = "Gadget added successfully!";
    header("Location: gadgets_list.php"); // Change to your gadgets list page
    exit();
}

require '../includes/header.php';
?>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Import Gadgets via CSV</h6>
        <a href="upload_gadget.php?download_template=1" class="btn btn-sm btn-secondary">Download Template</a>
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
                <div class="form-text">Ensure your file matches the downloadable template. Price and quantity are optional in CSV.</div>
            </div>
            <button type="submit" name="import_csv" class="btn btn-success">
                <i class="bi bi-upload me-1"></i> Import CSV
            </button>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Gadget</h6>
    </div>
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required>
                <div class="invalid-feedback">Please enter gadget name.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Model (optional)</label>
                <input type="text" class="form-control" name="model">
            </div>
            <div class="mb-3">
                <label class="form-label">Serial Number (optional, unique)</label>
                <input type="text" class="form-control" name="serial_number">
            </div>
            <div class="mb-3">
                <label class="form-label">Specifications (optional)</label>
                <textarea class="form-control" name="specifications" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (UGX, optional, default 0)</label>
                <input type="number" min="0" class="form-control" name="price" value="0" step="1">
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity (optional, default 1)</label>
                <input type="number" min="1" class="form-control" name="quantity" value="1" step="1">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Add Gadget
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
