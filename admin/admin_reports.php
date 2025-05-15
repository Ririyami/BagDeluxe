<?php
include '../components/connect.php';
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit;
}

// Set the correct timezone
date_default_timezone_set('Asia/Manila');

// Get today's date
$today = date('Y-m-d');
$current_month = date('Y-m');

// Get total sales for today
$query_sales = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM orders WHERE DATE(placed_on) = ?");
$query_sales->execute([$today]);
$total_sales = $query_sales->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

// Get total orders for today
$query_orders = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE DATE(placed_on) = ?");
$query_orders->execute([$today]);
$total_orders = $query_orders->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;

// Get pending and completed orders
$query_pending = $conn->prepare("SELECT COUNT(*) AS pending_orders FROM orders WHERE DATE(placed_on) = ? AND payment_status = 'pending'");
$query_pending->execute([$today]);
$pending_orders = $query_pending->fetch(PDO::FETCH_ASSOC)['pending_orders'] ?? 0;

$query_completed = $conn->prepare("SELECT COUNT(*) AS completed_orders FROM orders WHERE DATE(placed_on) = ? AND payment_status = 'completed'");
$query_completed->execute([$today]);
$completed_orders = $query_completed->fetch(PDO::FETCH_ASSOC)['completed_orders'] ?? 0;

// Get today's orders with customer names
$query_today_orders = $conn->prepare("SELECT orders.*, users.name AS customer_name FROM orders JOIN users ON orders.user_id = users.id WHERE DATE(placed_on) = ?");
$query_today_orders->execute([$today]);
$orders_today = $query_today_orders->fetchAll(PDO::FETCH_ASSOC);

// Get monthly orders with customer names
$query_monthly_orders = $conn->prepare("SELECT orders.*, users.name AS customer_name FROM orders JOIN users ON orders.user_id = users.id WHERE DATE_FORMAT(placed_on, '%Y-%m') = ?");
$query_monthly_orders->execute([$current_month]);
$orders_month = $query_monthly_orders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
        /* General Styles */
        body {
    font-family: Arial, sans-serif;
    background-color: #5a1e2d;
}

/* Main Container */
.container {
    max-width: 70%; /* Make container wider */
    margin: auto;
}

/* Page Title */
h2.text-center {
    font-size: 30px; /* Increase font size */
    font-weight: bold;
    color: white; /* Change color to black */
}

/* Header */
h2, h3 {
    margin-bottom: 20px;
    font-weight: bold;
    color: white; /* Primary color */
}

/* Card Styling */
.card {
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 15px; /* Increased padding */
    font-size: 10px; /* Larger text */
    min-height: 110px; /* Increased height */
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background-color:white!important; /* Pink theme */
    color: black !important;
}

.card h4 {
    font-size: 28px; /* Bigger numbers */
    font-weight: bold;
}

.card p {
    font-size: 20px; /* Bigger text */
}

/* Table Styles */
.table {
    width: 100%;
    margin-top: 20px;
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    font-size: 20px; /* Bigger text */
}

.table th {
    background-color: #805858; /* Pink theme */
    color: white;
    text-align: center;
    font-size: 18px; /* Increase table header size */
    padding: 15px;
}

.table td {
    padding: 15px;
    font-size: 18px; /* Increase table data size */
    text-align: center;
}

/* Badge Styling */
.badge {
    font-size: 18px;
    padding: 10px 15px;
    border-radius: 5px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .container {
        max-width: 100%;
        padding: 10px;
    }

    .table {
        font-size: 18px;
    }

    .table th, .table td {
        padding: 12px;
    }

    .card {
        padding: 15px;
        font-size: 20px;
    }

    .card h4 {
        font-size: 24px;
    }

    .card p {
        font-size: 18px;
    }
}



    </style>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Daily Sales Report - <?= date('F j, Y'); ?></h2>
        
        <div class="row text-center">
            <div class="col-md-3">
                <div class="card bg-primary text-white p-3">
                    <h4>₱<?= number_format($total_sales, 2); ?></h4>
                    <p>Total Sales</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white p-3">
                    <h4><?= $total_orders; ?></h4>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white p-3">
                    <h4><?= $pending_orders; ?></h4>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white p-3">
                    <h4><?= $completed_orders; ?></h4>
                    <p>Completed Orders</p>
                </div>
            </div>
        </div>

        <h3 class="mt-5">Today's Orders</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Order Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_today): ?>
                    <?php foreach ($orders_today as $order): ?>
                        <tr>
                            <td><?= $order['id']; ?></td>
                            <td><?= htmlspecialchars($order['customer_name']); ?></td>
                            <td>₱<?= number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span class="badge <?= $order['payment_status'] == 'completed' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= ucfirst($order['payment_status']); ?>
                                </span>
                            </td>
                            <td><?= date('h:i A', strtotime($order['placed_on'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No orders today.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <h2 class="text-center">Monthly Sales Report - <?= date('F Y'); ?></h2>
        
        <div class="row text-center">
            <div class="col-md-3">
                <div class="card bg-primary text-white p-3">
                    <h4>₱<?= number_format($total_sales, 2); ?></h4>
                    <p>Total Sales</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white p-3">
                    <h4><?= $total_orders; ?></h4>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white p-3">
                    <h4><?= $pending_orders; ?></h4>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white p-3">
                    <h4><?= $completed_orders; ?></h4>
                    <p>Completed Orders</p>
                </div>
            </div>
        </div>

        <h3 class="mt-5">Monthly Orders</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_month): ?>
                    <?php foreach ($orders_month as $order): ?>
                        <tr>
                            <td><?= $order['id']; ?></td>
                            <td><?= htmlspecialchars($order['customer_name']); ?></td>
                            <td>₱<?= number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span class="badge <?= $order['payment_status'] == 'completed' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= ucfirst($order['payment_status']); ?>
                                </span>
                            </td>
                            <td><?= date('F j, Y', strtotime($order['placed_on'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No orders this month.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

