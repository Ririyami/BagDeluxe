<?php

    include '../components/connect.php';

    session_start();

    $admin_id = $_SESSION['admin_id'];

    if(!isset($admin_id)){
    header('location:admin_login.php');
    };

    if(isset($_POST['update_stock'])){
    $product_id = $_POST['product_id'];
    $new_stock = $_POST['new_stock'];

    $update_stock = $conn->prepare("UPDATE inventory SET stock_quantity = ? WHERE product_id = ?");
    $update_stock->execute([$new_stock, $product_id]);

    $message[] = 'Stock updated successfully!';
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Panel</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
    

    </head>
    <body>

    <?php include '../components/admin_header.php'; ?> <!-- Corrected include file -->

    <section class="inventory-panel">

    <h1 class="heading">Current Inventory</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Variant Color</th>
                <th>Stock Quantity</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $select_inventory = $conn->prepare("SELECT i.product_id, i.stock_quantity, p.name, p.category, p.bag_color FROM inventory i JOIN products p ON i.product_id = p.id");
            $select_inventory->execute();
            if($select_inventory->rowCount() > 0){
                while($fetch_inventory = $select_inventory->fetch(PDO::FETCH_ASSOC)){
        ?>
            <tr>
                <td><?= $fetch_inventory['product_id']; ?></td>
                <td><?= $fetch_inventory['name']; ?></td>
                <td><?= $fetch_inventory['category']; ?></td>
                <td><?= $fetch_inventory['bag_color']; ?></td>
                <td><?= $fetch_inventory['stock_quantity']; ?></td>
            </tr>
        <?php
                }
            }else{
                echo '<tr><td colspan="5" class="empty">No inventory records found!</td></tr>';
            }
        ?>
        </tbody>
    </table>

    </section>

    <!-- custom js file link -->
    <script src="../js/admin_script.js"></script>

    </body>
    </html>
