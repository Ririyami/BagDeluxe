<?php
include '../components/connect.php';

$inventoryQuery = $conn->query("SELECT * FROM inventory");
?>

<table>
    <tr>
        <th>ID</th>
        <th>Product Name</th>
        <th>Brand Name</th>
        <th>Category</th>
        <th>Variant Color</th>
        <th>Stock Quantity</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $inventoryQuery->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['product_name'] ?></td>
        <td><?= $row['brand_name'] ?? 'N/A' ?></td>
        <td><?= $row['category'] ?></td>
        <td><?= $row['variant_color'] ?></td>
        <td><?= $row['stock_quantity'] ?></td>
        <td>
            <a href="javascript:void(0);" class="remove" onclick="confirmDelete('?delete=<?= $row['id'] ?>')">Remove</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
