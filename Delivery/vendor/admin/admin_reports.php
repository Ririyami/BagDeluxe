<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit;
}

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');
$current_month = date('Y-m');

// Get today's summary stats
$query_sales = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM orders WHERE DATE(placed_on) = ?");
$query_sales->execute([$today]);
$total_sales = $query_sales->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

$query_orders = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE DATE(placed_on) = ?");
$query_orders->execute([$today]);
$total_orders = $query_orders->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;

$query_pending = $conn->prepare("SELECT COUNT(*) AS pending_orders FROM orders WHERE DATE(placed_on) = ? AND payment_status = 'pending'");
$query_pending->execute([$today]);
$pending_orders = $query_pending->fetch(PDO::FETCH_ASSOC)['pending_orders'] ?? 0;

$query_completed = $conn->prepare("SELECT COUNT(*) AS completed_orders FROM orders WHERE DATE(placed_on) = ? AND payment_status = 'completed'");
$query_completed->execute([$today]);
$completed_orders = $query_completed->fetch(PDO::FETCH_ASSOC)['completed_orders'] ?? 0;

// Get today's orders
$query_today_orders = $conn->prepare("SELECT orders.*, users.name AS customer_name FROM orders JOIN users ON orders.user_id = users.id WHERE DATE(placed_on) = ?");
$query_today_orders->execute([$today]);
$orders_today = $query_today_orders->fetchAll(PDO::FETCH_ASSOC);

// Get monthly orders
$query_monthly_orders = $conn->prepare("SELECT orders.*, users.name AS customer_name FROM orders JOIN users ON orders.user_id = users.id WHERE DATE_FORMAT(placed_on, '%Y-%m') = ?");
$query_monthly_orders->execute([$current_month]);
$orders_month = $query_monthly_orders->fetchAll(PDO::FETCH_ASSOC);

// Graph data (monthly)
$start_month = date('Y-m-01');
$end_month = date('Y-m-t');
$query_graph = $conn->prepare("
    SELECT 
        DATE(placed_on) as order_date, 
        SUM(total_price) as daily_sales,
        COUNT(*) as daily_orders
    FROM orders 
    WHERE DATE(placed_on) BETWEEN ? AND ?
    GROUP BY order_date
    ORDER BY order_date
");
$query_graph->execute([$start_month, $end_month]);
$graph_data = $query_graph->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$sales = [];
$orders = [];
foreach ($graph_data as $row) {
    $labels[] = $row['order_date'];
    $sales[] = $row['daily_sales'] ?? 0;
    $orders[] = $row['daily_orders'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <link rel="stylesheet" href="../css/reports.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: var(--main-color,#5a1e2d); }
        .summary-row .card {
            min-width: 200px;
            margin-bottom: 1.5rem;
            display: flex; 
            align-items: center;
            justify-content: flex-start;
            gap: 1.3rem;
            font-size: 1.1rem;
        }
        .summary-icon {
            font-size: 2.6rem;
            opacity: .85;
        }
        .summary-row .bg-primary { background: #5a1e2d!important; color: #b4865a!important; }
        .summary-row .bg-secondary { background: #a87d6d!important; color: #fff!important; }
        .summary-row .bg-danger { background: #b03d3d!important; color: #fff!important; }
        .summary-row .bg-success { background: #7eb386!important; color: #205a1e!important; }
        .summary-row .card h5 { margin: 0; font-size: 2.1rem; font-weight: bold; }
        .section-title {
            color: var(--accent-color,#b4865a); 
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
            margin: 2.5rem 0 1.1rem 0;
        }
        .chart-card {
            background: var(--beige,#fdf6ee);
            border-radius: 1.2rem;
            box-shadow: var(--box-shadow,0 .5rem 1rem rgba(0,0,0,.06));
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
        }
        .table thead th {
            background: var(--sidebar-bg,#381820);
            color: var(--accent-color,#b4865a);
            font-size: 1.08rem;
            font-weight: 700;
            letter-spacing: .7px;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .badge {
            font-size: 1rem;
            padding: .4em 1em;
            border-radius: 1rem;
            font-weight: 600;
        }
        @media (max-width: 900px) {
            .summary-row .card { flex-direction: column; align-items: flex-start; min-width: 140px; }
            .section-title { font-size: 1.4rem; }
        }
        @media (max-width: 700px) {
            .summary-row .card { font-size: 1rem; }
        }
    </style>
</head>
<body>
<div class="sidebar">
   <div class="logo">AdminPanel</div>
   <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="products.php">Products</a>
      <a href="placed_orders.php">Orders</a>
      <a href="admin_reports.php" class="active">Reports</a>
      <a href="messages.php">Messages</a>
      <a href="inventory_panel.php">Product Stock</a>
   </nav>
</div>

<div class="container mt-5" style="margin-left:220px; max-width:1100px;">
    <!-- SUMMARY CARDS -->
    <div class="section-title">Summary for <?= date('F j, Y'); ?></div>
    <div class="row summary-row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm bg-primary text-white p-3">
                <i class="bi bi-cash-coin summary-icon"></i>
                <div>
                    <h5>₱<?= number_format($total_sales, 2); ?></h5>
                    <span>Total Sales</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm bg-secondary p-3">
                <i class="bi bi-basket2 summary-icon"></i>
                <div>
                    <h5><?= $total_orders; ?></h5>
                    <span>Total Orders</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm bg-danger p-3">
                <i class="bi bi-hourglass-split summary-icon"></i>
                <div>
                    <h5><?= $pending_orders; ?></h5>
                    <span>Pending Orders</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm bg-success p-3">
                <i class="bi bi-check2-circle summary-icon"></i>
                <div>
                    <h5><?= $completed_orders; ?></h5>
                    <span>Completed Orders</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT ORDERS TABLE -->
    <div class="section-title" style="font-size:1.5rem;">Today's Orders</div>
    <div class="card chart-card">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
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
        </div>
    </div>

    <!-- GRAPHS -->
    <div class="section-title">Trends This Month</div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="fw-bold mb-2" style="color:#5a1e2d;">Sales (₱) Per Day</div>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="fw-bold mb-2" style="color:#5a1e2d;">Order Count Per Day</div>
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pass PHP arrays to JS
    const chartLabels = <?= json_encode($labels) ?>;
    const chartSales = <?= json_encode($sales) ?>;
    const chartOrders = <?= json_encode($orders) ?>;

    // Sales Line Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Sales (₱)',
                data: chartSales,
                borderColor: '#b4865a',
                backgroundColor: 'rgba(180, 134, 90, 0.2)',
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#5a1e2d'
            }]
        },
        options: {
            plugins: {
                legend: { labels: { color: '#5a1e2d', font: { weight: 'bold' } } }
            },
            scales: {
                x: { ticks: { color: '#5a1e2d' } },
                y: { ticks: { color: '#5a1e2d' }, beginAtZero: true }
            }
        }
    });

    // Orders Bar Chart
    new Chart(document.getElementById('ordersChart'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Order Count',
                data: chartOrders,
                backgroundColor: '#5a1e2d'
            }]
        },
        options: {
            plugins: {
                legend: { labels: { color: '#5a1e2d', font: { weight: 'bold' } } }
            },
            scales: {
                x: { ticks: { color: '#5a1e2d' } },
                y: { ticks: { color: '#5a1e2d' }, beginAtZero: true }
            }
        }
    });
</script>
</body>
</html>