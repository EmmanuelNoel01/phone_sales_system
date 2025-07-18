<?php
require 'includes/config.php';
require 'fpdf/fpdf.php';

if (!isset($_GET['sale_id'])) {
    die("Missing sale ID.");
}

$sale_id = (int)$_GET['sale_id'];

$result = $conn->query("
    SELECT s.*, p.brand, p.model 
    FROM sales s 
    JOIN phones p ON s.phone_id = p.id 
    WHERE s.id = $sale_id
");

if ($result->num_rows == 0) {
    die("Sale not found.");
}

$row = $result->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();

// --- Logo centered ---
$logoWidth = 50;
$pdf->Image('/phone_sales_system/assets/img/logo.png', ($pdf->GetPageWidth() - $logoWidth) / 2, 10, $logoWidth);
$pdf->Ln(35);

// --- Define columns ---
$margin = 10;
$colWidth = ($pdf->GetPageWidth() - 2 * $margin) / 2; // Half width minus margins
$startY = $pdf->GetY();

// Set font for headings
$pdf->SetFont('Arial', 'B', 12);

// Set cursor to left column start
$pdf->SetXY($margin, $startY);
$pdf->Cell($colWidth, 7, 'Kampala Phone Shop', 0, 2);  // 2 = line break after cell
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($colWidth, 6, 'Address: Kampala, Uganda', 0, 2);
$pdf->Cell($colWidth, 6, 'Phone: +256 123 456 789', 0, 2);

// Set cursor to right column start at same Y as left column start
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY($margin + $colWidth, $startY);
$pdf->Cell($colWidth, 7, 'Customer Details', 0, 2);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($colWidth, 6, 'Name: ' . $row['customer_name'], 0, 2);
$pdf->Cell($colWidth, 6, 'Phone: ' . $row['customer_phone'], 0, 2);

// Move cursor below columns
$yAfterDetails = max($pdf->GetY(), $startY + 27);
$pdf->SetY($yAfterDetails + 10);

// --- Receipt Title centered ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Phone Sales Receipt', 0, 1, 'C');

// --- Phone & Sale details ---
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Sale Date: ' . date('d M Y', strtotime($row['sale_date'])), 0, 1);
$pdf->Cell(0, 8, 'Sale Time: ' . date('H:i:s', strtotime($row['sale_date'])), 0, 1);
$pdf->Cell(0, 8, 'Phone: ' . $row['brand'] . ' ' . $row['model'], 0, 1);
$pdf->Ln(5);

// --- Price details ---
$sale_price = number_format($row['sale_price'], 0);
$amount_paid = number_format($row['amount_paid'], 0);
$balance = number_format($row['sale_price'] - $row['amount_paid'], 0);

$pdf->Cell(0, 8, 'Total Price: UGX ' . $sale_price, 0, 1);
$pdf->Cell(0, 8, 'Amount Paid: UGX ' . $amount_paid, 0, 1);
$pdf->Cell(0, 8, 'Balance: UGX ' . $balance, 0, 1);

$pdf->Ln(20);

// --- Signature line ---
$pdf->Cell(0, 8, 'Buyer Signature: ____________________________', 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 8, 'Date: ____________________________', 0, 1);

$pdf->Ln(20);
// --- Footer centered ---
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Thank you for your purchase!', 0, 1, 'C');
$pdf->Cell(0, 10, 'Visit us again!', 0, 1, 'C');

$pdf->Output('I', 'receipt_' . $sale_id . '.pdf');
?>
