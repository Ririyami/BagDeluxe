<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../components/connect.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch current order details
    $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_order->execute([$order_id]);

    if ($select_order->rowCount() > 0) {
        $fetch_order = $select_order->fetch(PDO::FETCH_ASSOC);
    } else {
        die("Order not found!");
    }
} else {
    die("Order ID not provided!");
}

if (isset($_POST['update_status'])) {
    $new_status = $_POST['order_status'];

    // Update the order status in the database
    $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_status->execute([$new_status, $order_id]);

    // Debugging: Check if the update was successful
    if ($update_status->rowCount() > 0) {
        echo "Order status updated successfully!";
    } else {
        echo "Order status update failed!";
    }

    // Debugging: Output the SQL query and parameters
    echo "<br>SQL Query: UPDATE `orders` SET payment_status = '$new_status' WHERE id = '$order_id'";

    // Remove the header redirect
    // header("Location: ../admin/placed_orders.php?message=Order status updated successfully!");
    // exit();

    // Refresh the page to show the updated status
    header("Location: update_order_status.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #800000, #D3BCA2); /* Red to Beige gradient */
            color: #333; /* Dark text for better readability */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8); /* Soft white container */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 500px;
            text-align: center;
        }

        h1 {
            color: #800000; /* Red title */
            margin-bottom: 25px;
            font-size: 2em;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #800000; /* Red label */
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 25px;
            border: 1px solid #D3BCA2; /* Beige border */
            border-radius: 8px;
            background-color: #f8f8f8; /* Light gray background */
            color: #333;
            box-sizing: border-box;
            font-size: 1em;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            background-color: #D9534F; /* Red button */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1.1em;
        }

        button:hover {
            background-color: #C9302C;
        }

        a {
            color: #800000; /* Red link */
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Order Status for Order ID: <?= htmlspecialchars($order_id) ?></h1>

        <form method="post">
            <label for="order_status">Update Status:</label>
            <select name="order_status" id="order_status" required>
                <option value="pending" <?= ($fetch_order['payment_status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="on the way" <?= ($fetch_order['payment_status'] == 'on the way') ? 'selected' : '' ?>>On the Way</option>
                <option value="completed" <?= ($fetch_order['payment_status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= ($fetch_order['payment_status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" name="update_status">Update Status</button>
        </form>
        <a href="../Delivery/driver_home.php">Go Back</a>
    </div>
</body>
</html>