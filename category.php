<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';

// Get the category from the URL (if available)
$category = isset($_GET['category']) ? $_GET['category'] : ''; // Check if category is set, otherwise, assign empty string.

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>category</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="products">
   <h1 class="title">
      <?php
         if (isset($_GET['brand'])) {
            echo htmlspecialchars($_GET['brand']) . " Products";
         } elseif (isset($_GET['category'])) {
            echo htmlspecialchars($_GET['category']) . " Category";
         } else {
            echo "All Products";
         }
      ?>
   </h1>

   <!-- Show brands for the selected category -->
   <?php if (isset($_GET['category'])): ?>
      <div class="brand-filter">
         <h2>Select a Brand</h2>
         <div class="box-container">
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Celine" class="box">
               <h3>Celine</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Chanel" class="box">
               <h3>Chanel</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Chloé" class="box">
               <h3>Chloé</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Christian_Dior" class="box">
               <h3>Christian Dior</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Fendi" class="box">
               <h3>Fendi</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Gucci" class="box">
               <h3>Gucci</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Hermès" class="box">
               <h3>Hermès</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Louis_Vuitton" class="box">
               <h3>Louis Vuitton</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Prada" class="box">
               <h3>Prada</h3>
            </a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Saint_Laurent" class="box">
               <h3>Saint Laurent</h3>
            </a>
         </div>
      </div>
   <?php endif; ?>

   <div class="box-container">
      <?php
         // Check if a category or brand is set in the URL
         $category = isset($_GET['category']) ? $_GET['category'] : '';
         $brand = isset($_GET['brand']) ? $_GET['brand'] : '';

         if ($category && $brand) {
            // Fetch products for the selected category and brand
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ? AND brand = ?");
            $select_products->execute([$category, $brand]);
         } elseif ($category) {
            // Fetch products for the selected category
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
            $select_products->execute([$category]);
         } elseif ($brand) {
            // Fetch products for the selected brand
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE brand = ?");
            $select_products->execute([$brand]);
         } else {
            // Fetch all products if no category or brand is selected
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
         }

         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
         <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= $fetch_products['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products found!</p>';
         }
      ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>