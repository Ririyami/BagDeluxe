<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quick View</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="title">Quick View</h1>

   <?php
      if(isset($_GET['pid'])){
         $pid = $_GET['pid'];

         // Join products with inventory to get real-time stock
         $select_products = $conn->prepare("
            SELECT p.*, i.stock_quantity 
            FROM products p
            LEFT JOIN inventory i ON p.id = i.product_id
            WHERE p.id = ?
         ");
         $select_products->execute([$pid]);

         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <a href="category.php?category=<?= htmlspecialchars($fetch_products['category']); ?>" class="cat"><?= htmlspecialchars($fetch_products['category']); ?></a>
      <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
      <div class="flex">
         <div class="price"><span>₱</span><?= number_format($fetch_products['price'], 0); ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
      </div>
      <div class="details">
         <p><strong>Brand:</strong> <?= htmlspecialchars($fetch_products['brand']); ?></p>
         <p><strong>Bag Color:</strong> <?= htmlspecialchars($fetch_products['bag_color']); ?></p>
         <p><strong>Stock Available:</strong> <?= $fetch_products['stock_quantity'] !== null ? $fetch_products['stock_quantity'] : 'N/A'; ?></p>
         <p><strong>Description:</strong> <?= htmlspecialchars($fetch_products['description']); ?></p>
      </div>
      <button type="submit" name="add_to_cart" class="cart-btn">Add to Cart</button>
   </form>
   <?php
            }
         } else {
            echo '<p class="empty">No product found!</p>';
         }
      } else {
         echo '<p class="empty">No product selected!</p>';
      }
   ?>

</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>