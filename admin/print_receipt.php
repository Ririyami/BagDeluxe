<?php
include '../components/connect.php';
require '../vendor/autoload.php'; // Make sure Composer's autoloader is included

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order details
$stmt = $conn->prepare("
    SELECT o.id AS order_id, o.total_products, o.total_price, o.payment_status, 
           d.name AS driver_name, u.name AS user_name, u.email, u.number 
    FROM orders o
    LEFT JOIN drivers d ON o.driver_id = d.id
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p style='color:red;'>Order not found!</p>";
    exit;
}

// Generate QR code content (adjust this URL as needed)
$qrContent = "https://localhost/Delivery/delivery_panel.php?order_id=" . $order['order_id'];

// Create QR code image
$result = Builder::create()
    ->writer(new PngWriter())
    ->data($qrContent)
    ->encoding(new Encoding('UTF-8'))
    ->size(200)
    ->margin(10)
    ->build();

$qrImageData = base64_encode($result->getString());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        .barcode, .print-btn {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn button {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .print-btn button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <h2>LOGO SHOP</h2>
    <p style="text-align: center;">Bag Deluxe</p>
    <p style="text-align: center;">"carry me softly"</p>
    <hr>

    <p><strong>Name:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($order['number']) ?></p>
    <hr>

    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
    <p><strong>Items:</strong></p>
    <p><?= nl2br(htmlspecialchars($order['total_products'])) ?></p>
    <p><strong>Total Amount:</strong> ₱<?= htmlspecialchars($order['total_price']) ?></p>
    <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
    <p><strong>Driver Assigned:</strong> <?= htmlspecialchars($order['driver_name']) ?></p>
    <hr>

    <div class="barcode">
        <img src="data:image/png;base64,<?= $qrImageData ?>" alt="QR Code">
        <p style="font-size: 14px;">Scan to update delivery</p>
    </div>

    <div class="print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</div>
</body>
</html>
