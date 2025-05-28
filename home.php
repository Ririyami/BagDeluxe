<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php

@include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

@include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/home.css">


</head>
<body>

<?php include 'components/user_header.php';
?>



<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">
         
      <div class="swiper-slide slide">
            <div class="content">

               <a href="menu.php" class="btn">shop now</a>
            </div>
            <div class="image">
               <img src="images/9866.jpg" alt="">
            </div>
         </div>

      <div class="swiper-slide slide">
            <div class="content">

               <a href="menu.php" class="btn">shop now</a>
            </div>
            <div class="image">
               <img src="images/9876.gif" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
            
               <a href="menu.php" class="btn">shop now</a>
            </div>
            <div class="image">
               <img src="images/9999.gif" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
              
               <a href="menu.php" class="btn">shop now</a>
            </div>
            <div class="image">
               <img src="images/9987.gif" alt="">
            </div>
         </div>

      </div>


   </div>

</section>

      <div class="luxe-picks-section">
         <h1>This Week’s Luxe Picks—Carry with Confidence</h1>
         <hr class="luxe-divider">
         <h2>Discover curated bags made to match your style and support your stride every step of the way.</h2>
      </div>




<section class="category">
<div class="categories-container">
   <div class="box-container">
      <a href="category.php?category=Male Bags" class="box">
         <img src="images/men.png" alt="">
         <h3>Male Bags</h3>
      </a>

      <a href="category.php?category=Female Bags" class="box">
         <img src="images/women.png" alt="">
         <h3>Female Bags</h3>
      </a>

   </div>
   
</section>

<section class="" style="background-color: #6d2323;">
<div class="categories-container">
   <div class="brand-cards-grid">
      <a href="category.php?brand=Celine" class="brand-card" style="background-image: url('images/b4.jpg');">
         <span class="brand-card-title">Celine</span>
      </a>
      <a href="category.php?brand=Chanel" class="brand-card" style="background-image: url('images/b5.jpg');">
         <span class="brand-card-title">Chanel</span>
      </a>
      <a href="category.php?brand=Chloé" class="brand-card" style="background-image: url('images/b6.jpg');">
         <span class="brand-card-title">Chloé</span>
      </a>
      <a href="category.php?brand=Christian_Dior" class="brand-card" style="background-image: url('images/b9.jpg');">
         <span class="brand-card-title">Christian Dior</span>
      </a>
      <a href="category.php?brand=Fendi" class="brand-card" style="background-image: url('images/b8.png');">
         <span class="brand-card-title">Fendi</span>
      </a>
      <a href="category.php?brand=Gucci" class="brand-card" style="background-image: url('images/b7.jpg');">
         <span class="brand-card-title">Gucci</span>
      </a>
      <a href="category.php?brand=Hermès" class="brand-card" style="background-image: url('images/b1.jpg');">
         <span class="brand-card-title">Hermès</span>
      </a>
      <a href="category.php?brand=Louis_Vuitton" class="brand-card" style="background-image: url('images/b2.jpg');">
         <span class="brand-card-title">Louis Vuitton</span>
      </a>
      <a href="category.php?brand=Prada" class="brand-card" style="background-image: url('images/b0.jpg');">
         <span class="brand-card-title">Prada</span>
      </a>
      <a href="category.php?brand=Saint_Laurent" class="brand-card" style="background-image: url('images/b3.jpg');">
         <span class="brand-card-title">Saint Laurent</span>
      </a>
   </div>
</div>
</section>



<section class="products">

<h1 class="title" style="color: black;">Featured Products</h1>

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 3");
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
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= number_format($fetch_products['price'], 0); ?></div>

            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">no products added yet!</p>';
         }
      ?>

   </div>

   <div class="more-btn">
      <a href="menu.php" class="btn">view all</a>
   </div>

</section>



<?php @include 'components/footer.php'; ?>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".hero-slider", {
   loop: true,
   grabCursor: true,
   direction: "horizontal", // Set direction to horizontal
   effect: "slide", // Set effect to slide
   autoplay: {
      delay: 3000, // 3 seconds delay between slides
      disableOnInteraction: false,
   },
   pagination: {
      el: ".swiper-pagination",
  clickable: true,
   },
});

document.addEventListener('DOMContentLoaded', () => {
    const brandSidebar = document.querySelector('.brand-sidebar');
    const header = document.querySelector('.header');

    // Set the initial position of the sidebar below the header
    brandSidebar.style.top = `${header.offsetHeight}px`;

    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', () => {
        if (window.scrollY > lastScrollY && window.scrollY > header.offsetHeight) {
            // When scrolling down and header is out of view, move the sidebar to the top
            brandSidebar.style.top = '0';
        } else {
            // When scrolling up or header is visible, keep the sidebar below the header
            brandSidebar.style.top = `${header.offsetHeight}px`;
        }

        lastScrollY = window.scrollY;
    });

    const brandLinks = document.querySelectorAll('.brand-sidebar ul li a');

    brandLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Remove 'active' class from all links
            brandLinks.forEach(link => link.classList.remove('active'));

            // Add 'active' class to the clicked link
            e.target.classList.add('active');
        });
    });
});

</script>


</body>
</html>
