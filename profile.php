<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
}

// Fetch user profile details
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
if($select_profile->rowCount() > 0){
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
} else {
   header('location:home.php'); // Redirect if no user is found
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- header section starts -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="user-details">

   <div class="user">
      <?php
         if (!empty($fetch_profile['profile_picture'])) {
            echo '<img src="user_imgs/' . htmlspecialchars($fetch_profile['profile_picture']) . '" alt="User Profile Image">';
         } else {
            echo '<img src="images/default-avatar.png" alt="Default Avatar">';
         }
      ?>
      <p><i class="fas fa-user"></i><span><?= htmlspecialchars($fetch_profile['name']); ?></span></p>
      <p><i class="fas fa-phone"></i><span><?= htmlspecialchars($fetch_profile['number']); ?></span></p>
      <p><i class="fas fa-envelope"></i><span><?= htmlspecialchars($fetch_profile['email']); ?></span></p>
      <a href="update_profile.php" class="btn">update info</a>
      <p class="address"><i class="fas fa-map-marker-alt"></i><span><?php if(empty($fetch_profile['address'])){echo 'please enter your address';}else{echo htmlspecialchars($fetch_profile['address']);} ?></span></p>
      <a href="update_address.php" class="btn">update address</a>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link -->
<script src="js/script.js"></script>

</body>
</html>