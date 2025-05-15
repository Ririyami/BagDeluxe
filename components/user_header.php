<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
include 'components/connect.php'; // Ensure the database connection is included
}

// Check if the user is logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's profile details
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);

    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    } else {
        $fetch_profile = null; // Set to null if no profile is found
    }
} else {
    $user_id = null;
    $fetch_profile = null; // Set to null if the user is not logged in
}

$total_cart_items = 0;

if (isset($user_id) && !empty($user_id)) {
    $select_cart = $conn->prepare("SELECT SUM(quantity) AS total_items FROM `cart` WHERE user_id = ?");
    $select_cart->execute([$user_id]);
    $fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC);
    $total_cart_items = $fetch_cart['total_items'] ?? 0;
}

if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
<!-- Link to user header CSS -->
<link rel="stylesheet" href="css/header.css">

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">
         <img src="images/Wbdlogog.png" alt="Logo" style="height: 90px; vertical-align: middle; margin-left: 30px;">
         <img src="images/5556.png" alt="Logo" style="height: 75px; vertical-align: middle; margin-left: 2px;">
      </a>

      <nav class="navbar">
         <a href="home.php">Home</a>
         <a href="about.php">About</a>
         <a href="menu.php">Products</a>
         <a href="orders.php">Orders</a>
         <a href="contact.php">Contact</a>
      </nav>

      <div class="icons">
        <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= isset($total_cart_items) ? $total_cart_items : 0; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php if (isset($user_id) && !empty($user_id) && $fetch_profile) { ?>
            <p class="name"><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <div class="flex">
               <a href="profile.php" class="btn">profile</a>
               <a href="components/user_logout.php" onclick="return confirm('Are you sure you want to log out?');" class="delete-btn">logout</a>
            </div>
         <?php } else { ?>
            <p class="account">
               <a href="login.php">LOGIN</a> or
               <a href="register.php">REGISTER</a>
            </p>
         <?php } ?>
      </div>

   </section>
</header>

<script>
   document.addEventListener('DOMContentLoaded', () => {
      const userBtn = document.getElementById('user-btn');
      const profile = document.querySelector('.profile');

      // Toggle the profile section visibility
      userBtn.addEventListener('click', () => {
         profile.classList.toggle('active');
      });

      // Close the profile section when clicking outside
      document.addEventListener('click', (e) => {
         if (!userBtn.contains(e.target) && !profile.contains(e.target)) {
            profile.classList.remove('active');
         }
      });
   });
</script>

