<!-- filepath: c:\Users\Wrhinnon\OneDrive\Desktop\Xaampp\htdocs\bagdeluxe\Delivery\update_order_status.php -->
<?php
include '../components/connect.php';

if (!isset($_GET['order_id'])) {
    echo "<p style='color:red; text-align:center;'>Order ID is missing. Please scan the barcode again.</p>";
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p style='color:red; text-align:center;'>Order not found. Please check the barcode.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    // Update order status
    $update_stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    if ($update_stmt->execute([$status, $order_id])) {
        echo "<p style='color:green; text-align:center;'>Order status updated successfully!</p>";
        // Refresh the page to reflect the updated status
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<p style='color:red; text-align:center;'>Failed to update order status.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <form method="post" style="max-width: 400px; margin: auto;">
            <h3>Update Order Status for Order ID: <?= htmlspecialchars($order_id) ?></h3>
            <select name="status" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
                <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="on the way" <?= $order['payment_status'] === 'on the way' ? 'selected' : '' ?>>On the Way</option>
                <option value="completed" <?= $order['payment_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button type="submit" style="padding: 10px; width: 100%; background-color: #d9534f; color: white; border: none; border-radius: 5px;">Update Status</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <!-- Updated Go Back Button -->
            <a href="driver_home.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Go Back</a>
        </div>
    </div>
</body>
</html>