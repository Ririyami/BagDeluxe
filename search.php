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
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Product Card Quick View Icon Top Right */
      .products .box-container .box {
         position: relative;
      }

      .products .box-container .box .fa-eye {
         position: absolute;
         top: 1.2rem;
         right: 1.2rem;
         z-index: 10;
         display: flex;
         align-items: center;
         justify-content: center;
         width: 2.7rem;
         height: 2.7rem;
         font-size: 1.5rem;
         border-radius: 50%;
         border: 3px solid #381320;
         background: #fff;
         color: #381320;
         transition: background 0.2s, color 0.2s, box-shadow 0.2s;
         box-shadow: 0 2px 8px #0001;
      }

      .products .box-container .box .fa-eye:hover {
         background: #381320;
         color: #fff;
         box-shadow: 0 4px 16px #38132033;
      }
      .products .box-container .box .fa-shopping-cart {
         position: static; /* Remove absolute positioning */
         display: inline-flex;
         align-items: center;
         justify-content: center;
         margin-left: 0.5rem;
         width: 2.5rem;
         height: 2.5rem;
         border-radius: 5px; /* <-- round the cart button */
         border: 2px solid #7d2626;
         background-color: #5a1e2d;
         color: #fff;
         cursor: pointer;
         font-size: 1.5rem;
         transition: background 0.2s, color 0.2s, transform 0.2s;
         box-shadow: 0 2px 8px #0001;
         padding: 0;
         vertical-align: middle;
      }

      .products .box-container .box .fa-shopping-cart:hover {
         background-color: #a13c3c;
         color: #fffbe7;
         transform: scale(1.08);
      }
      .products .box-container .box img {
         width: 100%;
         height: 320px;
         object-fit: cover;
         margin-bottom: 1rem;
         border-radius: 5px;
         transition: 0.3s ease;
      }

      .products .box-container .box:hover img {
         transform: scale(1.05); /* Slight zoom effect on hover */
      }

      .products .box-container .box {
         position: relative;
         width: 300px;
         height: 505px;
         border: var(--border);
         border-radius: 5px; /* <-- round the box */
         padding: 1rem;
         text-align: center;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s ease, box-shadow 0.3s ease;
         background-color: #ffffff;
         overflow: hidden;
      }

      .products .box-container .box:hover {
         transform: scale(1.05);
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }
   </style>

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<!-- search form section starts  -->

<section class="search-form">
   <form method="post" action="">
      <input type="text" name="search_box" placeholder="search here..." class="box">
      <button type="submit" name="search_btn" class="fas fa-search"></button>
   </form>
</section>

<!-- search form section ends -->


<section class="products" style="min-height: 100vh; padding-top:0;">

<div class="box-container">

      <?php
         if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
         $search_box = $_POST['search_box'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
         <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= $fetch_products['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">no products added yet!</p>';
         }
      }
      ?>

   </div>

</section>











<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>