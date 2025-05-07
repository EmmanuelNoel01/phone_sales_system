<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Record Sale";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_id = (int)$_POST['phone_id'];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
    $sale_price = (float)$_POST['sale_price'];
    
    $conn->begin_transaction();
    
    try {
        // Insert sale record
        $conn->query("
            INSERT INTO sales (phone_id, customer_name, customer_phone, sale_price, sold_by)
            VALUES ($phone_id, '$customer_name', '$customer_phone', $sale_price, {$_SESSION['user_id']})
        ");
        
        // Update phone quantity
        $conn->query("UPDATE phones SET quantity = quantity - 1 WHERE id = $phone_id");
        
        $conn->commit();
        $_SESSION['success'] = "Sale recorded successfully!";
        header("Location: sales.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error processing sale: " . $e->getMessage();
    }
}

// Get available phones
$phones = $conn->query("SELECT * FROM phones WHERE quantity > 0");
?>

<?php require '../includes/header.php'; ?>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Record New Sale</h6>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Select Phone</label>
                    <select name="phone_id" class="form-select" required>
                        <option value="">-- Select Phone --</option>
                        <?php while($phone = $phones->fetch_assoc()): ?>
                        <option value="<?= $phone['id'] ?>" data-price="<?= $phone['price'] ?>">
                            <?= $phone['brand'] ?> <?= $phone['model'] ?> - $<?= number_format($phone['price'], 2) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Please select a phone</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sale Price ($)</label>
                    <input type="number" name="sale_price" class="form-control" step="0.01" min="0" required>
                    <div class="invalid-feedback">Please enter sale price</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                    <div class="invalid-feedback">Please enter customer name</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Customer Phone</label>
                    <input type="text" name="customer_phone" class="form-control" required>
                    <div class="invalid-feedback">Please enter customer phone</div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cash-stack me-1"></i> Record Sale
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneSelect = document.querySelector('select[name="phone_id"]');
    const priceInput = document.querySelector('input[name="sale_price"]');
    
    phoneSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            priceInput.value = selectedOption.dataset.price;
        }
    });
});
</script>

<?php require '../includes/footer.php'; ?>