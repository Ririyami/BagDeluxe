<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit();
}

// Fetch user profile details
$fetch_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$fetch_profile->execute([$user_id]);
if($fetch_profile->rowCount() > 0){
   $fetch_profile = $fetch_profile->fetch(PDO::FETCH_ASSOC);
}else{
   $fetch_profile = ['name' => '', 'number' => '', 'email' => '', 'address' => ''];
}

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $subtotal = floatval($_POST['subtotal']);

   // Calculate tax and discount
   $tax = $subtotal * 0.10; // 10% tax
   $discount = ($subtotal > 1000) ? $subtotal * 0.08 : 0; // 8% discount if subtotal > 1000
   $final_total = ($subtotal + $tax) - $discount;

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      if(empty($address)){
         $message[] = 'Please add your address!';
      }else{
         // Insert order
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $final_total]);

         // Update stock and remove items from the cart
         while ($fetch_cart = $check_cart->fetch(PDO::FETCH_ASSOC)) {
            $pid = $fetch_cart['pid'];
            $quantity = $fetch_cart['quantity'];

            // Update stock quantity
            $update_stock = $conn->prepare("UPDATE `inventory` SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $update_stock->execute([$quantity, $pid]);

            // Remove product from the cart after order
            $remove_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ? AND pid = ?");
            $remove_cart_item->execute([$user_id, $pid]);
         }

         $message[] = 'Order placed successfully!';
      }
      
   }else{
      $message[] = 'Your cart is empty!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Checkout</h3>
   <p><a href="home.php">Home</a> <span> / Checkout</span></p>
</div>

<section class="checkout">

   <h1 class="title">Order Summary</h1>

   <form action="" method="post">

      <div class="cart-items">
         <h3>Cart Items</h3>
         <?php
            $subtotal = 0;
            $cart_items = [];
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);

            if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                  $cart_items[] = $fetch_cart['name'].' (₱'.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
                  $total_products = implode(", ", $cart_items);
                  $subtotal += ($fetch_cart['price'] * $fetch_cart['quantity']);
         ?>
         <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price">₱<?= number_format($fetch_cart['price'], 2); ?> x <?= $fetch_cart['quantity']; ?></span></p>
         <?php
               }
            }else{
               echo '<p class="empty">Your cart is empty!</p>';
            }
         ?>

         <?php 
            // Calculate tax and discount
            $tax = $subtotal * 0.10;
            $discount = ($subtotal > 1000) ? $subtotal * 0.08 : 0;
            $final_total = ($subtotal + $tax) - $discount;
         ?>

         <p class="subtotal"><span class="name">Subtotal :</span><span class="price">₱<?= number_format($subtotal, 2); ?></span></p>
         <p class="tax"><span class="name">Tax (10%) :</span><span class="price">₱<?= number_format($tax, 2); ?></span></p>
         <p class="discount"><span class="name">Discount (8% if >₱1000) :</span><span class="price">-₱<?= number_format($discount, 2); ?></span></p>
         <p class="grand-total"><span class="name">Final Total :</span><span class="price" style="font-size: 20px; font-weight: bold; color: red;">₱<?= number_format($final_total, 2); ?></span></p>
         <a href="cart.php" class="btn">View Cart</a>
      </div>

      <input type="hidden" name="total_products" value="<?= $total_products; ?>">
      <input type="hidden" name="subtotal" value="<?= $subtotal; ?>">
      <input type="hidden" name="tax" value="<?= $tax; ?>">
      <input type="hidden" name="discount" value="<?= $discount; ?>">
      <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
      <input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
      <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
      <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

      <div class="user-info">
         <h3>Your Info</h3>
         <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
         <p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
         <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
         <a href="update_profile.php" class="btn">Update Info</a>

         <h3>Delivery Address</h3>
         <p><i class="fas fa-map-marker-alt"></i><span><?= !empty($fetch_profile['address']) ? $fetch_profile['address'] : 'Please enter your address'; ?></span></p>
         <a href="update_address.php" class="btn">Update Address</a>

         <select name="method" class="box" required>
            <option value="" disabled selected>Payment Method --</option>
            <option value="cash on delivery">Cash on Delivery</option>
         </select>
         
         <input type="submit" value="Place Order" class="btn" style="width:100%; background:var(--red); color:var(--white);" name="submit">
      </div>

   </form>
   
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>