<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Add New Phone";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = $conn->real_escape_string($_POST['brand']);
    $model = $conn->real_escape_string($_POST['model']);
    $imei = $conn->real_escape_string($_POST['imei']);
    $storage = $conn->real_escape_string($_POST['storage']);
    $color = $conn->real_escape_string($_POST['color']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    
    $conn->query("
        INSERT INTO phones (brand, model, imei, storage, color, `condition`, price, quantity, added_by)
        VALUES ('$brand', '$model', '$imei', '$storage', '$color', '$condition', $price, $quantity, {$_SESSION['user_id']})
    ");
    
    $_SESSION['success'] = "Phone added successfully!";
    header("Location: dashboard.php");
    exit();
}
?>

<?php require '../includes/header.php'; ?>

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
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" name="price" required>
                    <div class="invalid-feedback">Please enter price</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" class="form-control" name="quantity" value="1" required>
                    <div class="invalid-feedback">Please enter quantity</div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Add Phone
            </button>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>