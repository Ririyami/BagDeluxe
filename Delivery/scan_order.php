<?php
$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
include '../components/connect.php';

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->bindValue(':id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p style='color:red;'>Order not found!</p>";
    exit;
}

// Fetch user details
$user_stmt = $conn->prepare("SELECT name, email, number FROM users WHERE id = :user_id");
$user_stmt->bindValue(':user_id', $order['user_id'], PDO::PARAM_INT);
$user_stmt->execute();
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red;'>User not found!</p>";
    exit;
}

// Generate QR Code URL using Google API
$qr_target_url = "https://msoshub.com/__bagdeluxe/Delivery/update_order_status.php?order_id=" . urlencode($order_id);
$google_qr_url = "https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=" . urlencode($qr_target_url) . "&chld=L|1";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .receipt-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .receipt-container h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .receipt-container p {
            font-size: 18px;
            margin: 5px 0;
        }
        .receipt-container hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
        }
        .print-btn, .back-btn {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn button, .back-btn button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .print-btn button {
            background-color: #007BFF;
        }
        .print-btn button:hover {
            background-color: #0056b3;
        }
        .back-btn button {
            background-color: #6c757d;
        }
        .back-btn button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <h2>LOGO SHOP</h2>
    <p style="text-align: center; font-size: 14px;">Bag Deluxe</p>
    <p style="text-align: center; font-size: 14px;">"carry me softly"</p>
    <hr>

    <!-- User Info -->
    <p><strong>User Details:</strong></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($user['number']) ?></p>
    <hr>

    <!-- Order Info -->
    <p><strong>Order Details:</strong></p>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
    <p><strong>Items:</strong></p>
    <p><?= nl2br(htmlspecialchars($order['total_products'])) ?></p>
    <p><strong>Total Amount:</strong> ₱<?= htmlspecialchars($order['total_price']) ?></p>
    <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
    <hr>

    <!-- QR Code -->
    <div class="barcode">
        <img src="<?= $google_qr_url ?>" alt="QR Code for Order <?= htmlspecialchars($order['id']) ?>">
    </div>

    <!-- Print Button -->
    <div class="print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>

    <!-- Back Button -->
    <div class="back-btn">
        <button onclick="window.location.href='driver_home.php'">Back to Home</button>
    </div>
</div>
</body>
</html>
