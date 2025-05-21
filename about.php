<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>about us</h3>
   <p><a href="home.php">home</a> <span> / about</span></p>
</div>

<!-- about section starts  -->

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/789.jpg" alt="">
      </div>

      <div class="content">
         <h3>Why Choose Us?</h3>
         <p>We are an online store committed to offering high-quality products at the best prices. </p>
         <a href="menu.php" class="btn">our products</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- steps section starts  -->

<section class="steps">

   <h1 class="title">simple steps</h1>

   <div class="box-cox`ntainer">

      <div class="box">
         <img src="images/newicon2.png" alt="">
         <h3>choose order</h3>
         <p>basta pili lang.</p>
      </div>

      <div class="box">
         <img src="images/step-2.png" alt="">
         <h3>fast delivery</h3>
         <p>yah, mulupad rami lahos.</p>
      </div>

      <div class="box">
         <img src="images/newicon1.png" alt="">
         <h3>enjoy your bag!</h3>
         <p>post dayun.</p>
      </div>

   </div>

</section>

<!-- steps section ends -->

<!-- reviews section starts  -->

<section class="reviews">

   <h1 class="title">customer's reviews</h1>

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <img src="images/billy.png" alt="">
            <p>I recently bought the Black Backpack from this store, and I’m beyond impressed! The quality is top-notch—the leather feels premium, and it's super comfortable to carry.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Billy Bilyonaryo</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/kath.png" alt="">
            <p>I purchased the Designer's Bag, and I’m absolutely in love! The compartments inside make organizing super easy. Plus, it looks way more expensive than it actually is!</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Sasa Gurl</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/rona.png" alt="">
            <p>I bought the Unisex Leather Bag, and it has quickly become my everyday essential! The size is just right and the adjustable strap makes it super easy to wear. </p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Diwata Pares Overload</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-4.png" alt="">
            <p>The Women’s Comfortable Backpack is exactly what I was looking for! It’s elegant, spacious, and perfect for daily use. I love the green-tone hardware details. </p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3> Marian Rivera</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-5.png" alt="">
            <p>I bought the Unisex Waterproof Travel Bag for weekend trips, and it has exceeded my expectations. The design is sleek and modern, making it perfect for both men and women.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Manny Pacquiao</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/joren.png" alt="">
            <p>I recently got the Men’s Leather Duffel Bag, and it's been a game-changer for my business trips and weekend getaways. Also, the sturdy handles add a classic touch.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>John Lloyd Cruz</h3>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<!-- reviews section ends -->



















<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->=






<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
      slidesPerView: 1,
      },
      700: {
      slidesPerView: 2,
      },
      1024: {
      slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>