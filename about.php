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
   <title>About Us</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      /* General Styles */
      body {
   font-family: 'serif', serif; /* Serif font for an old-style feel */
   background: linear-gradient(to right,rgb(97, 8, 8),rgb(235, 175, 175)); /* Red to Beige-white gradient */
   color: #333;
   line-height: 1.6;
}
      .heading {
         background-color: #8B0000; /* Dark red heading */
         color: #fff;
         text-align: center;
         padding: 2rem 0;
         margin-bottom: 2rem;
      }

      .heading h3 {
         font-size: 2.5rem;
         margin-bottom: 0.5rem;
         font-family: 'Georgia', serif; /* Elegant serif font */
      }

      .heading p {
         font-size: 1.6rem;
      }

      .heading a {
         color: #fff;
         text-decoration: none;
      }

      .heading span {
         color: #ddd;
      }

      /* About Section Styles */
      .about {
         padding: 3rem 0;
      }

      .about .row {
         display: flex;
         align-items: center;
         justify-content: center;
         flex-wrap: wrap;
         gap: 3rem;
         padding: 2rem;
         background-color:rgb(46, 1, 1); /* Lighter beige background */
         border-radius: 10px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         margin-bottom: 2rem;
      }

      .about .row .image {
         flex: 1 1 400px;
         display: flex;
         justify-content: center;
      }

      .about .row .image img {
         width: 100%;
         max-width: 400px;
         border-radius: 12px;
         transition: transform 0.3s ease;
      }

      .about .row .image img:hover {
         transform: scale(1.05);
      }

      .about .row .content {
         flex: 1 1 500px;
         padding: 1rem;
      }

      .about .row .content h3 {
         font-size: 2.4rem;
         color: #8B0000; /* Dark red */
         margin-bottom: 1rem;
         font-family: 'Georgia', serif; /* Elegant serif font */
      }

      .about .row .content p {
         font-size: 1.6rem;
         color: #555;
         margin-bottom: 1.5rem;
      }

      .about .row .content ul {
         padding-left: 2rem;
         margin-bottom: 1.5rem;
      }

      .about .row .content li {
         font-size: 1.6rem;
         color: #555;
         list-style-type: disc;
         margin-bottom: 0.5rem;
      }

      .about .row .content .btn {
         display: inline-block;
         padding: 1rem 2rem;
         background-color: #8B0000; /* Dark red */
         color: white;
         text-decoration: none;
         border-radius: 5px;
         transition: background-color 0.3s ease;
      }

      .about .row .content .btn:hover {
         background-color: #5a1a1a;
      }

      /* Developer Section Styles */
      .about .row.devs {
         margin-top: 0;
         background-color: #f5f5dc; /* Beige-white background */
         box-shadow: none;
      }

      .about .row.devs .content {
         text-align: center;
      }

      .about .row.devs .content h3 {
         font-size: 2.2rem;
         color: #8B0000; /* Dark red */
         margin-bottom: 1rem;
         font-family: 'Georgia', serif; /* Elegant serif font */
      }

      .about .row.devs .content ul {
         list-style: none;
         padding: 0;
         display: flex;
         flex-direction: column;
         align-items: center;
      }

      .about .row.devs .content li {
         margin-bottom: 1rem;
      }

      .dev-link {
         font-size: 1.8rem;
         font-family: cursive;
         color: #8B0000; /* Dark red */
         text-decoration: underline;
         transition: color 0.2s;
      }

      .dev-link:hover {
         color: #5a1a1a;
      }

      /* Steps Section Styles */
      .steps {
         background-color: #8B0000; /* Dark red */
         padding: 4rem 0;
         text-align: center;
      }

      .steps .title {
         font-size: 3rem;
         color: white;
         margin-bottom: 3rem;
         font-family: 'Georgia', serif; /* Elegant serif font */
      }

      .steps .box-container {
         display: flex;
         justify-content: center;
         flex-wrap: wrap;
         gap: 2rem;
      }

      .steps .box {
         flex: 1 1 300px;
         background-color: #fff8dc; /* Lighter beige background */
         border-radius: 10px;
         padding: 2rem;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         text-align: center;
      }

      .steps .box img {
         width: 80px;
         margin-bottom: 1rem;
      }

      .steps .box h3 {
         font-size: 2rem;
         color: #8B0000; /* Dark red */
         margin-bottom: 0.5rem;
         font-family: 'Georgia', serif; /* Elegant serif font */
      }

      .steps .box p {
         font-size: 1.6rem;
         color: #555;
      }

      /* Responsive Styles */
      @media (max-width: 900px) {
         .about .row {
            flex-direction: column;
            text-align: center;
         }

         .about .row .image,
         .about .row .content {
            justify-content: center;
            align-items: center;
         }

         .about .row.devs .content ul {
            align-items: center;
         }
      }

      @media (max-width: 700px) {
         .heading h3 {
            font-size: 2rem;
         }

         .heading p {
            font-size: 1.4rem;
         }

         .about .row .content h3 {
            font-size: 2rem;
         }

         .about .row .content p,
         .about .row .content li {
            font-size: 1.4rem;
         }

         .steps .title {
            font-size: 2.4rem;
         }

         .steps .box h3 {
            font-size: 1.8rem;
         }

         .steps .box p {
            font-size: 1.4rem;
         }
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>About Us</h3>
   <p><a href="home.php">home</a> <span> / about</span></p>
</div>

<!-- About Section -->
<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/Wbdlogog.png" alt="Bag Deluxe Logo">
      </div>
      <div class="content">
         <h3>About Bag Deluxe</h3>
         <p><strong>Bag Deluxe</strong> is your go-to destination for timeless, luxurious bags that embody elegance and quality. From iconic fashion houses like Chanel, Louis Vuitton, Dior, and Gucci, we hand-pick authentic pieces that let you express your style with confidence. Whether it’s your first designer bag or your next statement piece, each item is carefully sourced, handled, and shipped with love.</p>
      </div>
   </div>
</section>

<!-- What We Offer -->
<section class="about">
   <div class="row">
      <div class="content">
         <h3>What We Offer</h3>
         <p>We specialize in offering hand-picked, high-end bags from the most iconic luxury houses—Chanel, Louis Vuitton, Dior, Gucci, and more. Whether you're buying your first designer piece or adding to your collection, we make sure each bag is:</p>
         <ul>
            <li>✔️ 100% authentic, sourced from trusted suppliers</li>
            <li>✔️ Carefully inspected and beautifully packaged</li>
            <li>✔️ Available as brand new or preloved</li>
            <li>✔️ Delivered quickly and safely to your doorstep</li>
         </ul>
      </div>
      <div class="image">
         <img src="images/bags.jpg" alt="Bags Collection">
      </div>
   </div>
</section>

<!-- Mission & Values -->
<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/pink.png" alt="Pink Bag">
      </div>
      <div class="content">
         <h3>Our Mission & Values</h3>
         <p>Our mission is to deliver <strong>luxury, trust, and style</strong> with every purchase. At Bag Deluxe, we value:</p>
         <ul>
            <li>💎 Authenticity — All bags are 100% genuine</li>
            <li>💼 Customer Satisfaction — Your happiness is our top priority</li>
            <li>👛 Timeless Fashion — Style that speaks softly but powerfully</li>
         </ul>
      </div>
   </div>
</section>

<!-- Why Choose Us -->
<section class="about">
   <div class="row">
      <div class="content">
         <h3>Why Choose Us?</h3>
         <ul>
            <li>🔐 Safe and secure shopping experience</li>
            <li>✅ Trusted source of authentic luxury bags</li>
            <li>🚚 Fast and reliable customer service</li>
            <li>❤️ A passionate team that truly cares</li>
         </ul>
         <a href="menu.php" class="btn">Shop now</a>
      </div>
      <div class="image">
         <img src="images/red.jpg" alt="Red Bag">
      </div>
   </div>
</section>

<!-- Meet the Founder -->
<section class="about">
   <div class="row devs">
      <div class="content">
         <h3>Meet the Bag Deluxe Developers</h3>
         <p>“We started Bag Deluxe because we wanted to help people express their style through iconic pieces that last. Each bag has a story—and now it’s your turn to carry it beautifully.”</p>
         <p><strong>– AICE</strong></p>
         <ul>
            <li><strong>Project Manager</strong>
               <ul>
                  <li><a class="dev-link" href="https://www.facebook.com/princess1.pilapil" target="_blank">Pilapil, Princess</a></li>
               </ul>
            </li>
            <li><strong>Full Stack Programmer</strong>
               <ul>
                  <li><a class="dev-link" href="https://www.facebook.com/Anime.ainah" target="_blank">Azcuna, Avril Ainah</a></li>
               </ul>
            </li>
            <li><strong>UI/UX Designer</strong>
               <ul>
                  <li><a class="dev-link" href="https://www.facebook.com/atet.cane" target="_blank">Cane, Althea</a></li>
                  <li><a class="dev-link" href="https://www.facebook.com/kathy.juntilla.1118/" target="_blank">Juntilla, Kathleen</a></li>
               </ul>
            </li>
            <li><strong>Back-end Programmer</strong>
               <ul>
                  <li><a class="dev-link" href="https://www.facebook.com/audreyhannah.chiba" target="_blank">Chiba, Audrey Hannah</a></li>
               </ul>
            </li>
            <li><strong>Documentation</strong>
               <ul>
                  <li><a class="dev-link" href="https://www.facebook.com/JOoReNn" target="_blank">Cortes, Joren</a></li>
               </ul>
            </li>
         </ul>
      </div>
   </div>
</section>

<!-- Call to Action -->
<section class="about">
   <div class="row">
      <div class="content">
         <h3>Ready to find the bag of your dreams?</h3>
         <p>Browse our collections and let luxury carry you softly. You deserve it.</p>
         <a href="menu.php" class="btn">Explore Collection</a>
      </div>
   </div>
</section>

<!-- Steps Section -->
<section class="steps">
   <h1 class="title">simple steps</h1>
   <div class="box-container">
      <div class="box">
         <img src="images/newicon2.png" alt="Choose Order">
         <h3>1. Choose Order</h3>
         <p>Choose your luxury bag according to your style.</p>
      </div>
      <div class="box">
         <img src="images/step-2.png" alt="Fast Delivery">
         <h3>2. Fast Delivery</h3>
         <p>Within 1-7 days upon ordering.</p>
      </div>
      <div class="box">
         <img src="images/newicon1.png" alt="Enjoy Your Bag">
         <h3>3. Enjoy your Bag!</h3>
         <p>Now carry it softly with confidence.</p>
      </div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
var swiper = new Swiper(".reviews-slider", {
   loop: true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable: true,
   },
   breakpoints: {
      0: { slidesPerView: 1 },
      700: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
   },
});
</script>

</body>
</html>
