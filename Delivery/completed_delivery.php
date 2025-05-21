<!-- filepath: c:\Users\Wrhinnon\OneDrive\Xaampp\htdocs\bagdeluxe\Delivery\completed_delivery.php -->
<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['driver_id'])) {
    echo "<p style='color:red; text-align:center;'>You are not logged in. Please log in first.</p>";
    exit;
}

$driver_id = $_SESSION['driver_id'];

try {
    // Fetch completed deliveries
    $stmt = $conn->prepare("SELECT * FROM orders WHERE driver_id = ? AND payment_status = 'completed'");
    $stmt->execute([$driver_id]);
    $completed_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Deliveries</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .order {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .order h3 {
            margin: 0;
            color: #d9534f;
        }
        .order p {
            margin: 5px 0;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .back-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Completed Deliveries</h3>
        <?php if (count($completed_orders) > 0): ?>
            <?php foreach ($completed_orders as $order): ?>
                <div class="order">
                    <h4>Order ID: <?= htmlspecialchars($order['id']) ?></h4>
                    <p><strong>Customer:</strong> <?= htmlspecialchars($order['user_id']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No completed deliveries found.</p>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="driver_home.php" class="back-button">Back</a>
    </div>
</body>
</html>