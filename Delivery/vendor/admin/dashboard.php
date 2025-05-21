<?php
include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Get total pendings
$total_pendings = 0; $pending_count = 0;
$select_pendings = $conn->prepare("SELECT total_price, DATE(placed_on) as date_only FROM `orders` WHERE payment_status = ?");
$select_pendings->execute(['pending']);
$pending_dates = [];
while ($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
    $total_pendings += $fetch_pendings['total_price'];
    $pending_count++;
    $date = $fetch_pendings['date_only'];
    $pending_dates[$date] = isset($pending_dates[$date]) ? $pending_dates[$date] + 1 : 1;
}

// Get total completes
$total_completes = 0; $complete_count = 0;
$select_completes = $conn->prepare("SELECT total_price, DATE(placed_on) as date_only FROM `orders` WHERE payment_status = ?");
$select_completes->execute(['completed']);
$complete_dates = [];
while ($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)) {
    $total_completes += $fetch_completes['total_price'];
    $complete_count++;
    $date = $fetch_completes['date_only'];
    $complete_dates[$date] = isset($complete_dates[$date]) ? $complete_dates[$date] + 1 : 1;
}

// Get total orders and dates for graph
$select_orders = $conn->prepare("SELECT id, payment_status, DATE(placed_on) as date_only FROM `orders`");
$select_orders->execute();
$order_rows = $select_orders->fetchAll(PDO::FETCH_ASSOC);
$total_orders = count($order_rows);

$order_dates = [];
foreach ($order_rows as $row) {
    $order_dates[$row['date_only']] = isset($order_dates[$row['date_only']]) ? $order_dates[$row['date_only']] + 1 : 1;
}

// Get products/users/admins count
$select_products = $conn->prepare("SELECT id FROM `products`");
$select_products->execute();
$total_products = $select_products->rowCount();

$select_users = $conn->prepare("SELECT id FROM `users`");
$select_users->execute();
$total_users = $select_users->rowCount();

$select_admins = $conn->prepare("SELECT id FROM `admin`");
$select_admins->execute();
$total_admins = $select_admins->rowCount();

// Pie chart: Pending vs Complete
$pie_data = ['pending' => $pending_count, 'complete' => $complete_count];

// Bar chart data for pending and completes
$all_dates = array_unique(array_merge(array_keys($pending_dates), array_keys($complete_dates)));
sort($all_dates);
$pending_bar = [];
$complete_bar = [];
foreach ($all_dates as $date) {
    $pending_bar[] = isset($pending_dates[$date]) ? $pending_dates[$date] : 0;
    $complete_bar[] = isset($complete_dates[$date]) ? $complete_dates[$date] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="../css/dashboard.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <style>
      body { background: var(--main-color,#5a1e2d); }
      .dashboard-cards { margin-bottom:2.3rem; }
      .dashboard-cards .card {
         background: var(--beige,#fdf6ee);
         border-radius: 1.2rem;
         box-shadow: var(--box-shadow,0 .5rem 1rem rgba(0,0,0,.06));
         text-align: center;
         padding: 1.6rem 0.9rem 1rem 0.9rem;
         min-height: 140px;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
      }
      .dashboard-cards .card .card-icon {
         font-size: 2.8rem;
         margin-bottom: 0.6rem;
         opacity: .8;
      }
      .dashboard-cards .bg-warning { background: #ffd6b0!important; color: #ad7d48!important; }
      .dashboard-cards .bg-success { background: #7eb386!important; color: #205a1e!important; }
      .dashboard-cards .bg-danger  { background: #b03d3d!important; color: #fff!important; }
      .dashboard-cards .bg-primary { background: #5a1e2d!important; color: #b4865a!important;}
      .dashboard-cards .bg-secondary { background: #a87d6d!important; color: #fff!important;}
      .dashboard-cards .card-title { font-size: 1.13rem; font-weight: bold; letter-spacing: 1px; }
      .dashboard-cards .card-value { font-size: 2.2rem; font-weight: bold; margin-bottom: 0; }
      .dashboard-cards .card-desc { font-size: 1.03rem; margin-top: .2rem; opacity: .8; }
      .dashboard-graphs .card {
         min-height: 400px;
         height: 400px;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         padding: 2rem 1.5rem;
         background: var(--beige,#fdf6ee);
         border-radius: 1.2rem;
         box-shadow: var(--box-shadow,0 .5rem 1rem rgba(0,0,0,.06));
      }
      .dashboard-graphs .card-title { font-size: 1.12rem; color: #5a1e2d; font-weight: bold; margin-bottom: .9rem; }
      .dashboard-graphs .card canvas {
         width: 100% !important;
         max-width: 360px;
         min-width: 220px;
         height: 320px !important;
         max-height: 320px;
         margin: 0 auto;
         display: block;
         /* This ensures both charts look the same */
      }
      .table-container table { margin-top: 3.2rem; }
      @media (max-width: 1000px) {
         .dashboard-cards .card { min-height: 115px; font-size: .97rem; }
         .dashboard-graphs .card { min-height: 260px; height:260px;}
         .dashboard-graphs .card canvas { min-width:120px; height:200px !important; max-height:220px; }
         .dashboard-cards .card .card-value { font-size: 1.3rem; }
      }
      @media (max-width:700px) {
         .dashboard-cards .card { min-height: 85px; padding:.6rem; }
         .dashboard-cards .card .card-title { font-size: .94rem; }
         .dashboard-cards .card .card-value { font-size: 1.1rem; }
         .dashboard-cards .card .card-icon { font-size:1.7rem; }
         .dashboard-graphs .card { min-height: 180px; height:180px;}
         .dashboard-graphs .card canvas { min-width:60px; height:110px !important; max-height:130px;}
      }
   </style>
</head>
<body>

<div class="sidebar">
   <div class="logo">AdminPanel</div>
   <nav>
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="products.php">Products</a>
      <a href="placed_orders.php">Orders</a>
      <a href="admin_reports.php">Reports</a>
      <a href="messages.php">Messages</a>
      <a href="inventory_panel.php">Product Stock</a>
   </nav>
</div>

<div class="main-content" style="max-width:1200px; margin-left:220px;">
<section class="dashboard px-2 px-md-4">

   <h1 class="heading mb-4" style="text-align:center;color:var(--accent-color,#b4865a);letter-spacing:1px;">Dashboard</h1>

   <!-- DASHBOARD CARDS -->
   <div class="row dashboard-cards g-4 mb-4">
      <div class="col-lg-3 col-md-6 col-12">
         <div class="card bg-warning">
            <span class="card-icon"><i class="bi bi-hourglass-split"></i></span>
            <div class="card-title">Total Pendings</div>
            <div class="card-value">₱<?= number_format($total_pendings,2) ?></div>
            <div class="card-desc"><?= $pending_count ?> pending orders</div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
         <div class="card bg-success">
            <span class="card-icon"><i class="bi bi-check2-circle"></i></span>
            <div class="card-title">Total Completes</div>
            <div class="card-value">₱<?= number_format($total_completes,2) ?></div>
            <div class="card-desc"><?= $complete_count ?> completed orders</div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
         <div class="card bg-primary">
            <span class="card-icon"><i class="bi bi-basket2"></i></span>
            <div class="card-title">Total Orders</div>
            <div class="card-value"><?= $total_orders ?></div>
            <div class="card-desc">All time orders</div>
         </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
         <div class="card bg-secondary">
            <span class="card-icon"><i class="bi bi-box-seam"></i></span>
            <div class="card-title">Products Added</div>
            <div class="card-value"><?= $total_products ?></div>
            <div class="card-desc">Total products</div>
         </div>
      </div>
   </div>

   <!-- DASHBOARD GRAPHS -->
   <div class="row dashboard-graphs g-4 mb-5">
      <div class="col-lg-6 col-md-12 mb-4">
         <div class="card p-3">
            <div class="card-title">Orders Per Day (Line Graph)</div>
            <canvas id="ordersPerDayChart"></canvas>
         </div>
      </div>
      <div class="col-lg-6 col-md-12 mb-4">
         <div class="card p-3">
            <div class="card-title">Pending vs Complete Orders (Pie Chart)</div>
            <canvas id="pendingCompletePie"></canvas>
         </div>
      </div>
      <div class="col-lg-12 col-md-12 mb-4">
         <div class="card p-3">
            <div class="card-title">Daily Pending vs Complete Orders (Bar Graph)</div>
            <canvas id="pendingCompleteBar"></canvas>
         </div>
      </div>
   </div>

   <!-- TABLE OF STATISTICS -->
   <div class="table-container mb-3">
      <table>
         <thead>
            <tr>
               <th>Category</th>
               <th>Count</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>Total Pendings</td>
               <td>₱<?= number_format($total_pendings,2) ?> (<?= $pending_count ?> orders)</td>
               <td><a href="placed_orders.php" class="btn">See Pending Orders</a></td>
            </tr>
            <tr>
               <td>Total Completes</td>
               <td>₱<?= number_format($total_completes,2) ?> (<?= $complete_count ?> orders)</td>
               <td><a href="placed_orders.php" class="btn">See Complete Orders</a></td>
            </tr>
            <tr>
               <td>Total Orders</td>
               <td><?= $total_orders ?></td>
               <td><a href="placed_orders.php" class="btn">See Orders</a></td>
            </tr>
            <tr>
               <td>Products Added</td>
               <td><?= $total_products ?></td>
               <td><a href="products.php" class="btn">See Products</a></td>
            </tr>
            <tr>
               <td>Users Accounts</td>
               <td><?= $total_users ?></td>
               <td><a href="users_accounts.php" class="btn">See Users</a></td>
            </tr>
            <tr>
               <td>Admins</td>
               <td><?= $total_admins ?></td>
               <td><a href="admin_accounts.php" class="btn">See Admins</a></td>
            </tr>
         </tbody>
      </table>
   </div>

</section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   // Orders per day graph
   const orderLabels = <?= json_encode(array_keys($order_dates)) ?>;
   const orderCounts = <?= json_encode(array_values($order_dates)) ?>;
   new Chart(document.getElementById('ordersPerDayChart'), {
      type: 'line',
      data: {
         labels: orderLabels,
         datasets: [{
            label: 'Orders',
            data: orderCounts,
            borderColor: '#b4865a',
            backgroundColor: 'rgba(180,134,90,0.15)',
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#5a1e2d'
         }]
      },
      options: {
         plugins: { legend: { labels: { color: '#5a1e2d', font: { weight: 'bold' } } } },
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            x: { ticks: { color: '#5a1e2d' } },
            y: { ticks: { color: '#5a1e2d' }, beginAtZero: true }
         }
      }
   });

   // Pending vs Complete Pie
   const pieLabels = ['Pending', 'Completed'];
   const pieData = [<?= $pending_count ?>, <?= $complete_count ?>];
   new Chart(document.getElementById('pendingCompletePie'), {
      type: 'pie',
      data: {
         labels: pieLabels,
         datasets: [{
            data: pieData,
            backgroundColor: ['#ffd6b0', '#7eb386']
         }]
      },
      options: {
         plugins: {
            legend: { labels: { color: '#5a1e2d', font: { weight: 'bold' } } }
         },
         responsive: true,
         maintainAspectRatio: false
      }
   });

   // Bar chart: daily pending and completes
   const barLabels = <?= json_encode($all_dates) ?>;
   const barPending = <?= json_encode($pending_bar) ?>;
   const barComplete = <?= json_encode($complete_bar) ?>;
   new Chart(document.getElementById('pendingCompleteBar'), {
      type: 'bar',
      data: {
         labels: barLabels,
         datasets: [
            {
               label: 'Pending',
               data: barPending,
               backgroundColor: '#ffd6b0'
            },
            {
               label: 'Completed',
               data: barComplete,
               backgroundColor: '#7eb386'
            }
         ]
      },
      options: {
         plugins: {
            legend: { labels: { color: '#5a1e2d', font: { weight: 'bold' } } }
         },
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            x: { ticks: { color: '#5a1e2d' } },
            y: { ticks: { color: '#5a1e2d' }, beginAtZero: true }
         }
      }
   });
</script>
<script src="../js/admin_script.js"></script>
</body>
</html>