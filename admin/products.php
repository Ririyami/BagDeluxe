<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['add_product'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $brand = filter_var($_POST['brand'], FILTER_SANITIZE_STRING);
   $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
   $bag_color = filter_var($_POST['bag_color'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   $select_products = $conn->prepare("SELECT * FROM products WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'Product name already exists!';
   } else {
      if($image_size > 2000000){
         $message[] = 'Image size is too large';
      } else {
         move_uploaded_file($image_tmp_name, $image_folder);

         // Insert into products table
         $insert_product = $conn->prepare("INSERT INTO `products` (name, brand, category, bag_color, price, description, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_product->execute([$name, $brand, $category, $bag_color, $price, $description, $stock, $image]);

         // Insert into inventory table
         $product_id = $conn->lastInsertId();
         $insert_inventory = $conn->prepare("INSERT INTO `inventory` (product_id, product_name, brand_name, category, variant_color, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
         $insert_inventory->execute([$product_id, $name, $brand, $category, $bag_color, $stock]);

         $message[] = 'New product added!';
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];

   // Delete product image
   $delete_product_image = $conn->prepare("SELECT * FROM products WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image']);

   // Delete from products table
   $delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
   $delete_product->execute([$delete_id]);

   // Delete from inventory
   $delete_inventory = $conn->prepare("DELETE FROM inventory WHERE product_id = ?");
   $delete_inventory->execute([$delete_id]);

   // Delete from cart
   $delete_cart = $conn->prepare("DELETE FROM cart WHERE pid = ?");
   $delete_cart->execute([$delete_id]);

   header('location:products.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <!-- font awesome cdn -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css -->
   <link rel="stylesheet" href="../css/productsadd.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
   <div class="logo">AdminPanel</div>
   <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="products.php" class="active">Products</a>
      <a href="placed_orders.php">Orders</a>
      <a href="admin_reports.php">Reports</a>
      <a href="messages.php">Messages</a>
      <a href="inventory_panel.php">Product Stock</a>
   </nav>
</div>

<!-- Add Products Section -->
<section class="add-products">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Add Product</h3>
      <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box">
      <input type="number" min="0" max="9999999999" required placeholder="Enter product price" name="price" onkeypress="if(this.value.length == 10) return false;" class="box">

      <!-- Brand Selection -->
      <select name="brand" class="box" required>
         <option value="" disabled selected>Select brand --</option>
         <option value="Celine">Celine</option>
         <option value="Chanel">Chanel</option>
         <option value="Chloé">Chloé</option>
         <option value="Christian_Dior">Christian Dior</option>
         <option value="Fendi">Fendi</option>
         <option value="Gucci">Gucci</option>
         <option value="Hermès">Hermès</option>
         <option value="Louis_Vuitton">Louis Vuitton</option>
         <option value="Prada">Prada</option>
         <option value="Saint_Laurent">Saint Laurent</option>
      </select>

      <!-- Category -->
      <select name="category" class="box" required>
         <option value="" disabled selected>Select category --</option>
         <option value="Male Bags">Male Bags</option>
         <option value="Female Bags">Female Bags</option>
         <option value="Unisex Bags">Unisex Bags</option>
         <option value="Wallet and Purse">Wallet and Purse</option>
      </select>

      <input type="text" required placeholder="Enter bag color" name="bag_color" maxlength="50" class="box">
      <textarea name="description" required placeholder="Enter product description" class="box" maxlength="500"></textarea>
      <input type="number" min="0" required placeholder="Enter stock quantity" name="stock" class="box">
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
      <input type="submit" value="Add Product" name="add_product" class="btn">
   </form>
</section>

<!-- Show Products Section -->
<section class="show-products" style="padding-top: 0;">
   <div class="box-container">
   <?php
      $show_products = $conn->prepare("SELECT * FROM products");
      $show_products->execute();
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="flex">
         <div class="price"><span>₱</span><?= $fetch_products['price']; ?></div> 
      </div>
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="description">
         <?= isset($fetch_products['description']) ? $fetch_products['description'] : 'No description available'; ?>
      </div>
      <div class="details">
         <p><strong>Brand:</strong> <?= $fetch_products['brand'] ?></p>
         <p><strong>Bag Color:</strong> <?= $fetch_products['bag_color'] ?></p>
         <p><strong>Stock:</strong> <?= $fetch_products['stock'] ?></p>
      </div>
      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
         <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
      </div>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
   ?>
   </div>
</section>

<!-- Custom JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
