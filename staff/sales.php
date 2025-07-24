<?php
require '../includes/config.php';
require '../includes/auth.php';

$page_title = "Record Sale";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_id = (int) $_POST['phone_id'];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
    $sale_price = (float) str_replace(',', '', $_POST['sale_price']);
    $amount_paid = (float) str_replace(',', '', $_POST['amount_paid']);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO sales (phone_id, customer_name, customer_phone, sale_price, amount_paid, sold_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issddi", $phone_id, $customer_name, $customer_phone, $sale_price, $amount_paid, $_SESSION['user_id']);

        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        // Get the last inserted sale ID
        $sale_id = $conn->insert_id;

        // Update the phone quantity
        $conn->query("UPDATE phones SET quantity = quantity - 1 WHERE id = $phone_id");

        $conn->commit();

        // Redirect to success page with the sale ID
        header("Location: /phone_sales_system/sale_success.php?sale_id=$sale_id");
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

        <form id="saleForm" method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Select Phone</label>
                    <div class="custom-dropdown">
                        <div class="selected-option" id="selected-phone"> Select Phone </div>
                        <div class="dropdown-options" id="phone-dropdown-options" style="display: none;">
                            <!-- <div class="option" data-value="">
                                Scroll Through and Select
                            </div> -->
                            <?php while ($phone = $phones->fetch_assoc()): ?>
                                <div class="option" data-value="<?= $phone['id'] ?>" data-price="<?= number_format($phone['price']) ?>">
                                    <?= $phone['brand'] ?> <?= $phone['model'] ?> - UGX <?= number_format($phone['price']) ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <input type="hidden" name="phone_id" id="phone-id" required>
                    <div class="invalid-feedback">Please select a phone</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Selling Price (UGX)</label>
                    <input type="text" name="sale_price" id="sale_price" class="form-control formatted" required>
                    <div class="invalid-feedback">Please enter sale price</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Amount Paid (UGX)</label>
                    <input type="text" name="amount_paid" id="amount_paid" class="form-control formatted" required>
                    <div class="invalid-feedback">Please enter amount paid</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Balance Due</label>
                    <input type="text" id="balance_due" class="form-control bg-light" readonly>
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

            <button type="button" class="btn btn-primary" id="previewButton">
                <i class="bi bi-eye me-1"></i> Preview Sale
            </button>
            <button type="submit" class="btn btn-success" style="display: none;" id="recordSaleButton">
                <i class="bi bi-cash-stack me-1"></i> Record Sale
            </button>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Sale Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Phone:</strong> <span id="previewPhone"></span></p>
                <p><strong>Selling Price:</strong> <span id="previewSalePrice"></span></p>
                <p><strong>Amount Paid:</strong> <span id="previewAmountPaid"></span></p>
                <p><strong>Balance Due:</strong> <span id="previewBalanceDue"></span></p>
                <p><strong>Customer Name:</strong> <span id="previewCustomerName"></span></p>
                <p><strong>Customer Phone:</strong> <span id="previewCustomerPhone"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmSaleButton">Confirm Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneSelect = document.querySelector('select[name="phone_id"]');
        const priceInput = document.getElementById('sale_price');
        const paidInput = document.getElementById('amount_paid');
        const balanceDisplay = document.getElementById('balance_due');

        const format = num => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        const unformat = str => str.replace(/,/g, '');

        phoneSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                priceInput.value = selectedOption.dataset.price;
                priceInput.dispatchEvent(new Event('input'));
            }
        });

        [priceInput, paidInput].forEach(el => {
            el.addEventListener('input', function() {
                this.value = format(unformat(this.value));
                const price = parseFloat(unformat(priceInput.value)) || 0;
                const paid = parseFloat(unformat(paidInput.value)) || 0;
                const balance = price - paid;
                balanceDisplay.value = 'UGX ' + format(balance);
            });
        });

        document.getElementById('previewButton').addEventListener('click', function() {
            const selectedPhone = phoneSelect.options[phoneSelect.selectedIndex];
            document.getElementById('previewPhone').innerText = selectedPhone.textContent;
            document.getElementById('previewSalePrice').innerText = priceInput.value;
            document.getElementById('previewAmountPaid').innerText = paidInput.value;
            document.getElementById('previewBalanceDue').innerText = balanceDisplay.value;
            document.getElementById('previewCustomerName').innerText = document.querySelector('input[name="customer_name"]').value;
            document.getElementById('previewCustomerPhone').innerText = document.querySelector('input[name="customer_phone"]').value;

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        });

        document.getElementById('confirmSaleButton').addEventListener('click', function() {
            document.getElementById('recordSaleButton').click();
        });
    });
</script>

<style>
    .custom-dropdown {
        position: relative;
        width: 100%;
    }

    .selected-option {
        padding: 10px;
        border: 1px solid #ccc;
        cursor: pointer;
        background-color: #fff;
    }

    .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        background-color: #fff;
        z-index: 1000;
    }

    .option {
        padding: 10px;
        cursor: pointer;
    }

    .option:hover {
        background-color: #f0f0f0;
    }
</style>

<script>
    document.getElementById('selected-phone').addEventListener('click', function() {
        const dropdown = document.getElementById('phone-dropdown-options');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', function() {
            const selectedValue = this.getAttribute('data-value');
            const selectedPrice = this.getAttribute('data-price');
            document.getElementById('selected-phone').textContent = this.textContent;
            document.getElementById('phone-id').value = selectedValue;
            document.getElementById('sale_price').value = selectedPrice; // Set price in the input
            document.getElementById('phone-dropdown-options').style.display = 'none';
        });
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.custom-dropdown');
        if (!dropdown.contains(event.target)) {
            document.getElementById('phone-dropdown-options').style.display = 'none';
        }
    });
</script>

<?php require '../includes/footer.php'; ?>