<?php
session_start();
include '../components/connect.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    if (is_numeric($deleteId)) {
        $deleteQuery = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $deleteQuery->execute([$deleteId]);
    }
    header("Location: inventory_panel.php");
    exit;
}

// Fetch inventory items
$inventoryQuery = $conn->query("SELECT * FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Panel</title>
    <link rel="stylesheet" href="inven.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        .chart-container {
            margin: 0 auto 30px auto; /* Center horizontally with auto margins */
            width: 600px;
            max-width: 100%;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #ddd;
        }

        .edit, .remove {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>

    <div class="chart-container">
        <h3>Stock Quantity by Brand</h3>
        <canvas id="brandChart" width="400" height="200"></canvas>
    </div>

    <h3>Current Inventory</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Variant Color</th>
                <th>Stock Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $inventoryQuery->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['brand_name'] ?? 'N/A' ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= $row['variant_color'] ?></td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="edit">Edit</a> |
                        <a href="javascript:void(0);" class="remove" onclick="confirmDelete('?delete=<?= $row['id'] ?>')">Remove</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this item?')) {
                window.location.href = url;
            }
        }

        // Initialize Chart.js
        let ctx = document.getElementById('brandChart').getContext('2d');
        let brandChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Stock Quantity',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 2000 // Limit chart to 2000
                    }
                },
                responsive: true
            }
        });

        function fetchChartData() {
            $.ajax({
                url: 'fetch_inventory_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    brandChart.data.labels = data.brands;
                    brandChart.data.datasets[0].data = data.stockQuantities;
                    brandChart.update();
                },
                error: function() {
                    console.error("Failed to fetch inventory data.");
                }
            });
        }

        // Fetch chart data every 10 seconds
        fetchChartData();
        setInterval(fetchChartData, 10000);
    </script>
</body>
</html>
