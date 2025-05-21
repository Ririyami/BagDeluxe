<?php

if (isset($_POST['add_to_cart'])) {

   if ($user_id == '') {
      header('location:login.php');
   } else {

      $pid = $_POST['pid'];
      $pid = filter_var($pid, FILTER_SANITIZE_STRING);

      // Debugging: Log the PID value
      error_log("PID: $pid");

      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $price = $_POST['price'];
      $price = filter_var($price, FILTER_SANITIZE_STRING);
      $image = $_POST['image'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      // Check if the product exists and has enough stock
      $check_stock = $conn->prepare("SELECT stock FROM `products` WHERE id = ?");
      $check_stock->execute([$pid]);
      $product = $check_stock->fetch(PDO::FETCH_ASSOC);

      // Debugging: Log the values
      error_log("Requested Quantity: $qty");
      error_log("Available Stock: " . ($product['stock'] ?? 'null'));

      if (!$product) {
         $message[] = 'Product not found!';
         error_log("Error: Product not found for PID: $pid");
      } elseif ((int)$product['stock'] < (int)$qty) { // Ensure both are integers
         $message[] = 'Not enough stock available!';
         error_log("Error: Not enough stock. Requested: $qty, Available: " . $product['stock']);
      } else {
         // Check if the product is already in the cart
         $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
         $check_cart_numbers->execute([$name, $user_id]);

         if ($check_cart_numbers->rowCount() > 0) {
            $message[] = 'Already added to cart!';
            error_log("Info: Product already in cart for User ID: $user_id");
         } else {
            // Insert the product into the cart
            $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
            $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);

            // Reduce the stock in the products table
            $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
            $update_stock->execute([$qty, $pid]);

            $message[] = 'Added to cart!';
            error_log("Info: Product added to cart. PID: $pid, User ID: $user_id, Quantity: $qty");
         }
      }
   }
}

?>