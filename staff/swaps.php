<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Phone Swaps";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $return_id = (int)$_POST['return_id'];
    $new_phone_id = (int)$_POST['new_phone_id'];
    $top_up = (float)$_POST['top_up_amount'];
    
    // Get old phone from return
    $return = $conn->query("SELECT phone_id FROM returns WHERE id = $return_id")->fetch_assoc();
    
    // Record swap
    $conn->query("
        INSERT INTO swaps (return_id, old_phone_id, new_phone_id, top_up_amount, swapped_by)
        VALUES ($return_id, {$return['phone_id']}, $new_phone_id, $top_up, {$_SESSION['user_id']})
    ");
    
    // Update return status
    $conn->query("UPDATE returns SET status = 'Swapped' WHERE id = $return_id");
    
    $_SESSION['success'] = "Phone swap recorded successfully!";
    header("Location: swaps.php");
    exit();
}

// Get phones available for swapping
$phones = $conn->query("SELECT id, brand, model, price FROM phones WHERE quantity > 0");
// Get returns eligible for swapping
$returns = $conn->query("
    SELECT r.id, p.brand, p.model, p.price 
    FROM returns r
    JOIN phones p ON r.phone_id = p.id
    WHERE r.status = 'Repairing'
");
?>

<?php require '../includes/header.php'; ?>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Process Phone Swap</h6>
    </div>
    <div class="card-body">
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
                    <label class="form-label">Select Return</label>
                    <select name="return_id" class="form-select" required data-price-calc>
                        <option value="">-- Select Return --</option>
                        <?php while($return = $returns->fetch_assoc()): ?>
                        <option value="<?= $return['id'] ?>" data-price="<?= $return['price'] ?>">
                            #<?= $return['id'] ?> - <?= $return['brand'] ?> <?= $return['model'] ?> ($<?= number_format($return['price'], 2) ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Please select a return</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Phone</label>
                    <select name="new_phone_id" class="form-select" required data-price-calc>
                        <option value="">-- Select Phone --</option>
                        <?php while($phone = $phones->fetch_assoc()): ?>
                        <option value="<?= $phone['id'] ?>" data-price="<?= $phone['price'] ?>">
                            <?= $phone['brand'] ?> <?= $phone['model'] ?> ($<?= number_format($phone['price'], 2) ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Please select a phone</div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Top-Up Amount ($)</label>
                    <input type="number" name="top_up_amount" class="form-control" step="0.01" min="0" value="0" data-price-calc>
                    <div class="invalid-feedback">Please enter amount</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price Difference</label>
                    <input type="text" class="form-control" id="price-difference" readonly>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-arrow-left-right me-1"></i> Complete Swap
            </button>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>