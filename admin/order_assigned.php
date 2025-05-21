<!-- filepath: c:\Users\Wrhinnon\OneDrive\Desktop\Xaampp\htdocs\bagdeluxe\admin\order_assigned.php -->
<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Fetch assigned orders
$select_orders = $conn->prepare("
    SELECT o.id AS order_id, o.total_products, o.total_price, o.payment_status, 
           d.name AS driver_name, u.name AS user_name, u.email, u.number 
    FROM orders o
    LEFT JOIN drivers d ON o.driver_id = d.id
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.driver_id IS NOT NULL
");
$select_orders->execute();
$orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Assigned</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders-assigned">
    <h1 class="title">Assigned Orders</h1>

    <div class="box-container">
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="box">
                    <h3>Order ID: <?= htmlspecialchars($order['order_id']) ?></h3>
                    <p><strong>User Name:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Contact Number:</strong> <?= htmlspecialchars($order['number']) ?></p>
                    <p><strong>Driver Assigned:</strong> <?= htmlspecialchars($order['driver_name']) ?></p>
                    <p><strong>Items:</strong> <?= nl2br(htmlspecialchars($order['total_products'])) ?></p>
                    <p><strong>Total Amount:</strong> ₱<?= htmlspecialchars($order['total_price']) ?></p>
                    <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
                    <a href="print_receipt.php?order_id=<?= $order['order_id'] ?>" class="btn">Print Receipt</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">No assigned orders found!</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>