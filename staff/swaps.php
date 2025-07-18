<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Phone Swaps";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_phone_name = $conn->real_escape_string($_POST['old_phone_name']);
    $old_serial = $conn->real_escape_string($_POST['old_serial']);
    $new_phone_id = (int)$_POST['new_phone_id'];
    $new_serial = $conn->real_escape_string($_POST['new_serial']);
    $recipient_name = $conn->real_escape_string($_POST['recipient_name']);

    $valued_amount = (int) str_replace(',', '', $_POST['valued_amount']);
    $amount_paid = (int) str_replace(',', '', $_POST['amount_paid']);
    $selling_price = (int) str_replace(',', '', $_POST['selling_price']);
    $balance_due = $selling_price - $valued_amount - $amount_paid;

    $new_phone = $conn->query("SELECT brand, model FROM phones WHERE id = $new_phone_id")->fetch_assoc();
    $new_brand_model = $conn->real_escape_string($new_phone['brand'] . ' ' . $new_phone['model']);

    $conn->query("
        INSERT INTO swaps (
            old_phone_id, new_phone_id, top_up_amount,
            swapped_by, old_phone_serial, new_phone_serial,
            old_phone_brand_model, new_phone_brand_model,
            valued_amount, amount_paid, recipient_name, balance_due
        ) VALUES (
            NULL, $new_phone_id, $amount_paid,
            {$_SESSION['user_id']}, '$old_serial', '$new_serial',
            '$old_phone_name', '$new_brand_model',
            $valued_amount, $amount_paid, '$recipient_name', $balance_due
        )
    ");

    $_SESSION['success'] = "Phone swap recorded successfully!";
    header("Location: swaps.php");
    exit();
}

$phones = $conn->query("SELECT id, brand, model, imei, price FROM phones WHERE quantity > 0");
?>

<?php require '../includes/header.php'; ?>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Complete Phone Swap (UGX)</h6>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-4 border-end">
                    <h5>Old Phone Info</h5>

                    <label>Old Phone Name</label>
                    <input type="text" name="old_phone_name" class="form-control" value="iPhone " required>

                    <label class="mt-2">Old Serial / IMEI</label>
                    <input type="text" name="old_serial" class="form-control" required>

                    <label class="mt-2">Valued Amount (UGX)</label>
                    <input type="text" id="valued_display" class="form-control formatted" required>
                    <input type="hidden" name="valued_amount" id="valued_amount">
                </div>

                <div class="col-md-4 border-end">
                    <h5 class="text-center">Recipient</h5>

                    <label>Name</label>
                    <input type="text" name="recipient_name" class="form-control" required>

                    <label class="mt-2">Amount Paid (UGX)</label>
                    <input type="text" id="paid_display" class="form-control formatted" required>
                    <input type="hidden" name="amount_paid" id="amount_paid">

                    <label class="mt-2">Balance Due</label>
                    <input type="text" id="balance_due" class="form-control bg-light" readonly>
                </div>

                <div class="col-md-4">
                    <h5>New Phone Info</h5>

                    <label>Select New Phone</label>
                    <select name="new_phone_id" id="new_phone_id" class="form-select" required>
                        <option disabled selected>-- Select Phone --</option>
                        <?php while ($phone = $phones->fetch_assoc()): ?>
                            <option value="<?= $phone['id'] ?>" data-price="<?= $phone['price'] ?>">
                                <?= $phone['brand'] ?> <?= $phone['model'] ?> (UGX <?= number_format($phone['price']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label class="mt-2">New Serial / IMEI</label>
                    <input type="text" name="new_serial" class="form-control" required>

                    <label class="mt-2">Selling Price (UGX)</label>
                    <input type="text" id="selling_display" class="form-control formatted" required>
                    <input type="hidden" name="selling_price" id="selling_price">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-left-right me-1"></i> Complete Swap
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const formatNumber = (num) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    const unformatNumber = (str) => str.replace(/,/g, '');

    const sellingDisplay = document.getElementById('selling_display');
    const sellingHidden = document.getElementById('selling_price');
    const valuedDisplay = document.getElementById('valued_display');
    const valuedHidden = document.getElementById('valued_amount');
    const paidDisplay = document.getElementById('paid_display');
    const paidHidden = document.getElementById('amount_paid');
    const balanceField = document.getElementById('balance_due');

    function updateFields() {
        const selling = parseInt(unformatNumber(sellingDisplay.value)) || 0;
        const valued = parseInt(unformatNumber(valuedDisplay.value)) || 0;
        const paid = parseInt(unformatNumber(paidDisplay.value)) || 0;
        const balance = selling - valued - paid;

        sellingHidden.value = selling;
        valuedHidden.value = valued;
        paidHidden.value = paid;

        balanceField.value = "UGX " + formatNumber(balance);
    }

    [sellingDisplay, valuedDisplay, paidDisplay].forEach(el => {
        el.addEventListener('input', function () {
            let raw = unformatNumber(this.value);
            this.value = formatNumber(raw);
            updateFields();
        });
    });

    document.getElementById('new_phone_id').addEventListener('change', function () {
        const price = this.selectedOptions[0].dataset.price;
        sellingDisplay.value = formatNumber(price);
        sellingHidden.value = price;
        updateFields();
    });
});
</script>

<?php require '../includes/footer.php'; ?>
