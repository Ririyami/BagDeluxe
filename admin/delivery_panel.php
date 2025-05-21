<!-- filepath: c:\xampp\htdocs\BagDeluxe(Latest)\BagDeluxe\admin\delivery_panel.php -->
<?php
include '../components/connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<p>Debug: order_id is missing or empty.</p>";
    echo "<script>alert('Order ID is missing! Redirecting to orders page.'); window.location.href='placed_orders.php';</script>";
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details
$select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
$select_order->execute([$order_id]);

if ($select_order->rowCount() > 0) {
    $order = $select_order->fetch(PDO::FETCH_ASSOC);
} else {
    echo "<p>Debug: order_id does not exist in the database.</p>";
    echo "<script>alert('Order not found! Redirecting to orders page.'); window.location.href='placed_orders.php';</script>";
    exit;
}

// Fetch all registered drivers
$select_drivers = $conn->prepare("SELECT id, name FROM `drivers`");
$select_drivers->execute();
$drivers = $select_drivers->fetchAll(PDO::FETCH_ASSOC);

// Handle driver assignment
if (isset($_POST['assign_driver'])) {
    $driver_id = $_POST['driver_id'];

    if ($driver_id) {
        $assign_driver = $conn->prepare("UPDATE `orders` SET driver_id = ? WHERE id = ?");
        $assign_driver->execute([$driver_id, $order_id]);

        echo "<script>alert('Driver assigned successfully!'); window.location.href='placed_orders.php';</script>";
    } else {
        echo "<script>alert('Please select a driver.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Delivery Driver</title>
    <link rel="stylesheet" href="../css/delivery.css">
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Order Details</h1>
        </div>
        <div class="receipt-body">
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>Total Price:</strong> ₱<?php echo htmlspecialchars($order['total_price']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Current Driver:</strong> 
                <?php 
                if ($order['driver_id']) {
                    // Fetch the driver's name
                    $driver_query = $conn->prepare("SELECT name FROM `drivers` WHERE id = ?");
                    $driver_query->execute([$order['driver_id']]);
                    $driver = $driver_query->fetch(PDO::FETCH_ASSOC);
                    echo htmlspecialchars($driver['name']);
                } else {
                    echo "None";
                }
                ?>
            </p>
        </div>
        <div class="receipt-footer">
            <form method="POST">
                <h3>Select a Driver</h3>
                <select name="driver_id" required>
                    <option value="">Select a driver</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['id']); ?>">
                            <?php echo htmlspecialchars($driver['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="assign_driver" class="btn">Assign Driver</button>
            </form>
        </div>
    </div>
</body>
</html>