<?php
require 'includes/config.php';
require 'includes/auth.php';

$sale_id = isset($_GET['sale_id']) ? (int) $_GET['sale_id'] : 0;
if ($sale_id <= 0) {
    die("Invalid sale ID");
}

// Fetch sale details
$stmt = $conn->prepare("
    SELECT 
        s.id, s.customer_name, s.customer_phone, s.sale_price, s.amount_paid, s.sale_date, 
        p.brand AS phone_brand, p.model AS phone_model,
        g.name AS gadget_name, g.model AS gadget_model,
        u.role
    FROM sales s
    LEFT JOIN phones p ON s.phone_id = p.id
    LEFT JOIN gadgets g ON s.gadget_id = g.id
    JOIN users u ON s.sold_by = u.id
    WHERE s.id = ?
");


if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}

$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$sale = $result->fetch_assoc();

if (!$sale) {
    die("Sale not found");
}

$balance = $sale['sale_price'] - $sale['amount_paid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Print Receipt</title>
<style>
  body, html {
    margin: 0; padding: 0; background: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #222;
  }

  .receipt-container {
    max-width: 900px;
    margin: 20px auto;
    padding: 40px;
    border: 2px solid #222;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    page-break-inside: avoid;
  }

  .logo {
    text-align: center;
    margin-bottom: 40px;
  }
  .logo img {
    max-height: 90px;
    object-fit: contain;
  }

  .header-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
  }
  .header-section h3 {
    margin-bottom: 10px;
    border-bottom: 2px solid #222;
    padding-bottom: 5px;
    font-size: 22px;
  }
  .header-section p {
    margin: 6px 0;
    font-size: 18px;
  }

  .receipt-title {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 30px;
    letter-spacing: 1.5px;
  }

  table.sale-details {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
    font-size: 18px;
  }
  table.sale-details th, table.sale-details td {
    padding: 12px 15px;
    border: 1px solid #ccc;
    text-align: left;
  }
  table.sale-details th {
    background-color: #f0f0f0;
  }
  table.sale-details td.amount {
    text-align: right;
    font-weight: 600;
  }

  .signature-area {
    margin-top: 60px;
    display: flex;
    justify-content: space-between;
  }
  .signature-block {
    width: 40%;
    border-top: 2px solid #222;
    padding-top: 10px;
    font-size: 18px;
    text-align: center;
    color: #555;
  }

  @media print {
    body, html {
      margin: 0;
      padding: 0;
    }

    .receipt-container {
      border: none;
      box-shadow: none;
      max-width: 100%;
      padding: 0;
      margin: 0;
      page-break-after: avoid;
    }

    .logo img {
      max-height: 70px;
    }
  }
</style>
</head>
<body onload="printAndRedirect()">
  <div class="receipt-container">
    <div class="logo">
      <img src="/phone_sales_system/assets/img/logo.jpg" alt="Shop Logo" />
    </div>

    <div class="header-grid">
      <div class="header-section shop-details">
        <h3>Kampala Phone Shop</h3>
        <p>Address: Kampala, Uganda</p>
        <p>Phone: +256 123 456 789</p>
      </div>
      <div class="header-section customer-details">
        <h3>Customer Details</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($sale['customer_name']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($sale['customer_phone']) ?></p>
      </div>
    </div>

    <div class="receipt-title">Sales Receipt</div>

    <table class="sale-details">
      <tr>
        <th>Sale Date</th>
        <td><?= date('d M Y', strtotime($sale['sale_date'])) ?></td>
      </tr>
      <tr>
        <th>Sale Time</th>
        <td><?= date('H:i:s', strtotime($sale['sale_date'])) ?></td>
      </tr>
      <!-- <tr>
        <th>Phone</th>
        <td><?= htmlspecialchars($sale['brand']) . ' ' . htmlspecialchars($sale['model']) ?></td>
      </tr> -->
      <tr>
  <th>Item Sold</th>
  <td>
    <?php 
    if ($sale['phone_brand']) {
        echo htmlspecialchars($sale['phone_brand']) . ' ' . htmlspecialchars($sale['phone_model']);
    } elseif ($sale['gadget_name']) {
        echo htmlspecialchars($sale['gadget_name']) . ' ' . htmlspecialchars($sale['gadget_model']);
    } else {
        echo "Unknown Item";
    }
    ?>
  </td>
</tr>

      <tr>
        <th>Total Price</th>
        <td class="amount">UGX <?= number_format($sale['sale_price']) ?></td>
      </tr>
      <tr>
        <th>Amount Paid</th>
        <td class="amount">UGX <?= number_format($sale['amount_paid']) ?></td>
      </tr>
      <tr>
        <th>Balance</th>
        <td class="amount">UGX <?= number_format($balance) ?></td>
      </tr>
    </table>

    <div class="signature-area">
      <div class="signature-block">
        Buyer Signature
      </div>
      <div class="signature-block">
        Date
      </div>
    </div>
  </div>
  <script>
function printAndRedirect() {
  window.print();

  const params = new URLSearchParams(window.location.search);
  const from = params.get("from"); 
  console.log("Current path:", params);
  
  let targetUrl;

  if (from && from.includes('sell_gadget.php')) {
    targetUrl = '/phone_sales_system/sell_gadget.php';
  } else {
    targetUrl = '/phone_sales_system/staff/sales.php';
  }

  setTimeout(function () {
    window.location.href = targetUrl;
  }, 5000);
}


</script>

</body>
</html>
