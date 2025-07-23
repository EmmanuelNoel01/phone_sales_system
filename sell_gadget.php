<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'includes/config.php';
require 'includes/auth.php';

$page_title = "Gadgets";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_type = $_POST['item_type'] ?? ''; // 'phone' or 'gadget'

    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
    $sale_price = (float) str_replace(',', '', $_POST['sale_price']);
    $amount_paid = (float) str_replace(',', '', $_POST['amount_paid']);

    $phone_id = null;
    $gadget_id = null;

    if ($item_type === 'phone') {
        $phone_id = (int) $_POST['phone_id'];
        if (!$phone_id) {
            $_SESSION['error'] = "Please select a phone to sell.";
        }
    } elseif ($item_type === 'gadget') {
        $gadget_id = (int) $_POST['gadget_id'];
        if (!$gadget_id) {
            $_SESSION['error'] = "Please select a gadget to sell.";
        }
    } else {
        $_SESSION['error'] = "Please select the item type (phone or gadget).";
    }

    if (!isset($_SESSION['error'])) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO sales (phone_id, gadget_id, customer_name, customer_phone, sale_price, amount_paid, sold_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissdii", $phone_id, $gadget_id, $customer_name, $customer_phone, $sale_price, $amount_paid, $_SESSION['user_id']);
            if (!$stmt->execute()) {
                throw new Exception("Failed to record sale: " . $stmt->error);
            }
            $sale_id = $conn->insert_id;

            // Update quantity and status
            if ($item_type === 'phone') {
                $conn->query("UPDATE phones SET quantity = quantity - 1 WHERE id = $phone_id");
                $conn->query("UPDATE phones SET status = 'Sold' WHERE id = $phone_id AND quantity <= 0");
            } elseif ($item_type === 'gadget') {
                $conn->query("UPDATE gadgets SET quantity = quantity - 1 WHERE id = $gadget_id");
            }

            $conn->commit();
            header("Location: /phone_sales_system/sale_success.php?sale_id=$sale_id");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
}

// Fetch available phones and gadgets for selection
$phones = $conn->query("SELECT id, brand, model, price, quantity FROM phones WHERE quantity > 0 AND status = 'Available' ORDER BY brand, model");
$gadgets = $conn->query("SELECT id, name, model, price, quantity FROM gadgets WHERE quantity > 0 ORDER BY name, model");

require 'includes/header.php';
?>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Other Gadgets Sale</h6>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form id="saleForm" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Select Item Type</label>
                <div>
                    <!-- <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="item_type" id="itemTypePhone" value="phone" required>
                        <label class="form-check-label" for="itemTypePhone">Phone</label>
                    </div> -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="item_type" id="itemTypeGadget" value="gadget" required>
                        <label class="form-check-label" for="itemTypeGadget">Gadget</label>
                    </div>
                    <div class="invalid-feedback">Please select an item type.</div>
                </div>
            </div>

            <div class="mb-3" id="phoneSelectDiv" style="display:none;">
                <label for="phoneSelect" class="form-label">Select Phone</label>
                <select id="phoneSelect" name="phone_id" class="form-select">
                    <option value="">-- Select Phone --</option>
                    <?php while ($phone = $phones->fetch_assoc()): ?>
                        <option value="<?= $phone['id'] ?>" data-price="<?= $phone['price'] ?>">
                            <?= htmlspecialchars($phone['brand'] . ' ' . $phone['model']) ?> - UGX <?= number_format($phone['price']) ?> (Qty: <?= $phone['quantity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">Please select a phone.</div>
            </div>

            <div class="mb-3" id="gadgetSelectDiv" style="display:none;">
                <label for="gadgetSelect" class="form-label">Select Gadget</label>
                <select id="gadgetSelect" name="gadget_id" class="form-select">
                    <option value="">-- Select Gadget --</option>
                    <?php while ($gadget = $gadgets->fetch_assoc()): ?>
                        <option value="<?= $gadget['id'] ?>" data-price="<?= $gadget['price'] ?>">
                            <?= htmlspecialchars($gadget['name'] . ' ' . $gadget['model']) ?> - UGX <?= number_format($gadget['price']) ?> (Qty: <?= $gadget['quantity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">Please select a gadget.</div>
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="sale_price" class="form-label">Selling Price (UGX)</label>
                    <input type="text" name="sale_price" id="sale_price" class="form-control formatted" required>
                    <div class="invalid-feedback">Please enter sale price.</div>
                </div>
                <div class="col-md-6">
                    <label for="amount_paid" class="form-label">Amount Paid (UGX)</label>
                    <input type="text" name="amount_paid" id="amount_paid" class="form-control formatted" required>
                    <div class="invalid-feedback">Please enter amount paid.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Balance Due</label>
                <input type="text" id="balance_due" class="form-control" readonly>
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                    <div class="invalid-feedback">Please enter customer name.</div>
                </div>
                <div class="col-md-6">
                    <label for="customer_phone" class="form-label">Customer Phone</label>
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control" required>
                    <div class="invalid-feedback">Please enter customer phone.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Record Sale</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemTypeRadios = document.querySelectorAll('input[name="item_type"]');
    const phoneSelectDiv = document.getElementById('phoneSelectDiv');
    const gadgetSelectDiv = document.getElementById('gadgetSelectDiv');
    const phoneSelect = document.getElementById('phoneSelect');
    const gadgetSelect = document.getElementById('gadgetSelect');
    const salePriceInput = document.getElementById('sale_price');
    const amountPaidInput = document.getElementById('amount_paid');
    const balanceDueInput = document.getElementById('balance_due');

    // Show/hide selects based on item type selection
    itemTypeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'phone' && radio.checked) {
                phoneSelectDiv.style.display = 'block';
                gadgetSelectDiv.style.display = 'none';
                gadgetSelect.value = '';
                updatePriceFromSelect(phoneSelect);
            } else if (radio.value === 'gadget' && radio.checked) {
                gadgetSelectDiv.style.display = 'block';
                phoneSelectDiv.style.display = 'none';
                phoneSelect.value = '';
                updatePriceFromSelect(gadgetSelect);
            }
            updateBalance();
        });
    });

    function updatePriceFromSelect(selectElem) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            salePriceInput.value = Number(selectedOption.dataset.price).toLocaleString();
        } else {
            salePriceInput.value = '';
        }
        updateBalance();
    }

    phoneSelect.addEventListener('change', () => updatePriceFromSelect(phoneSelect));
    gadgetSelect.addEventListener('change', () => updatePriceFromSelect(gadgetSelect));

    function parseNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }

    function updateBalance() {
        const price = parseNumber(salePriceInput.value);
        const paid = parseNumber(amountPaidInput.value);
        const balance = price - paid;
        balanceDueInput.value = balance.toLocaleString();
    }

    salePriceInput.addEventListener('input', updateBalance);
    amountPaidInput.addEventListener('input', updateBalance);

    // Bootstrap validation example
    (function () {
        'use strict'
        const form = document.querySelector('#saleForm')
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })()
});
</script>

<?php
require 'includes/footer.php';
?>
