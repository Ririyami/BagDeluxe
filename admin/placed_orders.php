<?php

include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['assign_driver'])) {
    $order_id = $_POST['order_id'];
    $driver_id = $_POST['driver_id'];

    // Update the driver assignment in the database
    $update_driver = $conn->prepare("UPDATE `orders` SET driver_id = ? WHERE id = ?");
    $update_driver->execute([$driver_id, $order_id]);

    $message[] = 'Driver assigned successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Placed Orders</title>
    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS File Link -->
    <link rel="stylesheet" href="../css/orders.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <div class="logo">AdminPanel</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="placed_orders.php" class="active">Orders</a>
        <a href="admin_reports.php">Reports</a>
        <a href="messages.php">Messages</a>
        <a href="inventory_panel.php">Product Stock</a>
    </nav>
</div>

<!-- Placed Orders Section Starts -->
<section class="placed-orders">
    <h1 class="heading">Placed Orders</h1>
    <div class="orders-table-container">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Placed On</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Number</th>
                    <th>Address</th>
                    <th>Total Products</th>
                    <th>Total Price</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>QR Code</th>
                    <th>Assign Driver</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_orders = $conn->prepare("SELECT * FROM `orders`");
                $select_orders->execute();

                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        // Google Chart QR URL:
                        $qr_url = "https://msoshub.com/__bagdeluxe/Delivery/update_order_status.php?order_id=" . urlencode($fetch_orders['id']);
                        $qr_code = "https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=" . urlencode($qr_url) . "&chld=L|1";
                ?>
                        <tr>
                            <td><?= $fetch_orders['user_id']; ?></td>
                            <td><?= $fetch_orders['placed_on']; ?></td>
                            <td><?= $fetch_orders['name']; ?></td>
                            <td><?= $fetch_orders['email']; ?></td>
                            <td><?= $fetch_orders['number']; ?></td>
                            <td><?= $fetch_orders['address']; ?></td>
                            <td><?= $fetch_orders['total_products']; ?></td>
                            <td>₱<?= number_format($fetch_orders['total_price'], 2); ?></td>
                            <td><?= $fetch_orders['method']; ?></td>
                            <td><?= $fetch_orders['payment_status']; ?></td>
                            <td>
                                <a href="https://msoshub.com/__bagdeluxe/Delivery/update_order_status.php?order_id=<?= $fetch_orders['id']; ?>" target="_blank">
                                    <img src="<?= $qr_code ?>" alt="QR Code">
                                </a>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                                    <select name="driver_id" required>
                                        <option value="" disabled selected>Select Driver</option>
                                        <?php
                                        $select_drivers = $conn->prepare("SELECT * FROM `drivers`");
                                        $select_drivers->execute();
                                        if ($select_drivers->rowCount() > 0) {
                                            while ($fetch_drivers = $select_drivers->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = ($fetch_orders['driver_id'] == $fetch_drivers['id']) ? 'selected' : '';
                                                echo "<option value='{$fetch_drivers['id']}' $selected>{$fetch_drivers['name']}</option>";
                                            }
                                        } else {
                                            echo '<option value="" disabled>No drivers available</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="assign_driver" class="btn">Assign</button>
                                </form>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="12" class="empty">No orders placed yet!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Custom JS File Link -->
<script src="../js/admin_script.js"></script>

</body>
</html>
