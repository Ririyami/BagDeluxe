<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../components/connect.php'; // Ensure database connection is included

if (!isset($_SESSION['driver_id'])) {
    echo "<p style='color:red; text-align:center;'>You are not logged in. Please log in first.</p>";
    echo '<div style="text-align:center; margin-top:20px;">
            <a href="driver_login.php" style="padding:10px 20px; background-color:maroon; color:white; text-decoration:none; border-radius:5px;">Log In</a>
          </div>';
    exit;
}

$driver_id = $_SESSION['driver_id'];

try {
    // Fetch driver details
    $stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt->execute([$driver_id]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$driver) {
        echo "<p style='color:red; text-align:center;'>Driver not found.</p>";
        exit;
    }

    // Fetch assigned orders
    $orders_stmt = $conn->prepare("
        SELECT o.id AS order_id, o.total_products, o.payment_status, u.name AS customer_name 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.driver_id = ? AND o.payment_status = 'pending'
    ");
    $orders_stmt->execute([$driver_id]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Driver Home</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Dashboard -->
    <div class="dashboard">
        <h2>Dashboard</h2>
        <a href="update_driver_profile.php">Update Profile</a>
        <a href="completed_delivery.php">Completed Deliveries</a>
        <a href="driver_logout.php" class="logout">Log Out</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($driver['name']) ?></h1>
        </div>

        <div class="profile">
            <img src="../drivers_profile/<?= htmlspecialchars($driver['drivers_picture']) ?>" alt="Profile Picture">
            <h2><?= htmlspecialchars($driver['name']) ?></h2>
        </div>

        <div class="orders-container">
            <h2>Your Assigned Pending Orders</h2>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order">
                        <h3>Order ID: <?= htmlspecialchars($order['order_id']) ?></h3>
                        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
                        <a href="scan_order.php?order_id=<?= $order['order_id'] ?>" class="view-receipt">View Receipt</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No pending orders assigned to you.</p>
            <?php endif; ?>
        </div>

        <!-- Scan Container -->
        <div class="scan-container">
            <h2>Scan QR or Enter Order ID to Update Order Status</h2>
            <form action="update_order_status.php" method="GET">
                <input type="text" name="order_id" placeholder="Enter Order ID" required>
                <button type="submit">Go</button>
            </form>
           
        </div>

        <script>
            // JavaScript to open the barcode scanner app
            function openBarcodeScanner() {
                // Redirect to a URL scheme for a barcode scanner app
                window.location.href = "intent://scan/#Intent;scheme=zxing;package=com.google.zxing.client.android;end";
            }
        </script>
    </div>
</body>
</html>
