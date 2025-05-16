<?php
session_start();
include '../components/connect.php';

// Handle form submission to add/update inventory
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $product_id = $_POST['product_id'];
    $variant_color = $_POST['variant_color'];
    $stock_quantity = $_POST['stock_quantity'];
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : null;

    // Fetch product details
    $productQuery = $conn->prepare("SELECT name, category FROM products WHERE id = ?");
    $productQuery->execute([$product_id]);
    $product = $productQuery->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $product_name = $product['name'];
        $category = $product['category'];

        if ($edit_id) {
            // Update existing record by ID
            $updateQuery = $conn->prepare("UPDATE inventory SET product_id = ?, product_name = ?, category = ?, variant_color = ?, stock_quantity = ? WHERE id = ?");
            $updateQuery->execute([$product_id, $product_name, $category, $variant_color, $stock_quantity, $edit_id]);
        } else {
            // Check if product variant already exists
            $checkQuery = $conn->prepare("SELECT stock_quantity FROM inventory WHERE product_id = ? AND variant_color = ?");
            $checkQuery->execute([$product_id, $variant_color]);
            $existingProduct = $checkQuery->fetch(PDO::FETCH_ASSOC);

            if ($existingProduct) {
                $new_stock = $existingProduct['stock_quantity'] + $stock_quantity;
                $updateQuery = $conn->prepare("UPDATE inventory SET stock_quantity = ? WHERE product_id = ? AND variant_color = ?");
                $updateQuery->execute([$new_stock, $product_id, $variant_color]);
            } else {
                $insertQuery = $conn->prepare("INSERT INTO inventory (product_id, product_name, category, variant_color, stock_quantity) 
                                               VALUES (?, ?, ?, ?, ?)");
                $insertQuery->execute([$product_id, $product_name, $category, $variant_color, $stock_quantity]);
            }
        }

        $_SESSION['receipt'] = [
            'product_name' => $product_name,
            'variant_color' => $variant_color,
            'stock_quantity' => $stock_quantity,
            'category' => $category
        ];
    }

    header("Location: inventory_panel.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    // Ensure the delete ID is valid before proceeding
    if (is_numeric($deleteId)) {
        $deleteQuery = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $deleteQuery->execute([$deleteId]);
    }
    header("Location: inventory_panel.php");
    exit;
}

// Fetch inventory items
$inventoryQuery = $conn->query("SELECT * FROM inventory");

// For editing
$editMode = false;
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editQuery = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
    $editQuery->execute([$editId]);
    $editItem = $editQuery->fetch(PDO::FETCH_ASSOC);
    $editMode = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Panel</title>
    <link rel="stylesheet" href="inven.css">
    <script>
        // Function to confirm deletion
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this item?')) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>
    <h2>Inventory Management</h2>

    <form method="post">
        <?php if ($editMode): ?>
            <input type="hidden" name="edit_id" value="<?= $editItem['id'] ?>">
        <?php endif; ?>
        <label>Product ID:</label>
        <input type="number" name="product_id" value="<?= $editMode ? $editItem['product_id'] : '' ?>" required><br>

        <label>Variant Color:</label>
        <input type="text" name="variant_color" value="<?= $editMode ? $editItem['variant_color'] : '' ?>" required><br>

        <label>Stock Quantity:</label>
        <input type="number" name="stock_quantity" value="<?= $editMode ? $editItem['stock_quantity'] : '' ?>" required><br>

        <button type="submit" name="save"><?= $editMode ? 'Update' : 'Add / Update' ?> Inventory</button>
        <?php if ($editMode): ?>
            <a href="inventory_panel.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h3>Current Inventory</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Variant Color</th>
            <th>Stock Quantity</th>
            <th>Actions</th> <!-- Actions column -->
        </tr>
        <?php while ($row = $inventoryQuery->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['variant_color'] ?></td>
            <td><?= $row['stock_quantity'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>" class="edit">Edit</a> |
                <a href="javascript:void(0);" class="remove" onclick="confirmDelete('?delete=<?= $row['id'] ?>')">Remove</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if (isset($_SESSION['receipt'])): ?>
        <div class="receipt">
            <h3>Receipt</h3>
            <p>Product: <?= $_SESSION['receipt']['product_name'] ?></p>
            <p>Category: <?= $_SESSION['receipt']['category'] ?></p>
            <p>Variant: <?= $_SESSION['receipt']['variant_color'] ?></p>
            <p>Stock Added: <?= $_SESSION['receipt']['stock_quantity'] ?></p>
            <button onclick="window.print()">Print Receipt</button>
        </div>
        <?php unset($_SESSION['receipt']); ?>
    <?php endif; ?>
</body>
</html>
