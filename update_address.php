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

if(isset($_POST['submit'])){

   // Construct the address in the Philippines format
   $address = $_POST['flat'] .', '.$_POST['building'].', '.$_POST['street'].', '.$_POST['barangay'] .', '. $_POST['city'] .', '. $_POST['province'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);

   // Update the user's address in the database
   $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE id = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'Address saved!';

   // Refresh the page after saving
   header('Location: checkout.php');
   exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Address</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php' ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Your Address</h3>

      <!-- Address input fields tailored to Philippines address format -->
      <input type="text" class="box" placeholder="Flat/Unit No." maxlength="50" name="flat">
      <input type="text" class="box" placeholder="Building Name" maxlength="50" name="building">
      <input type="text" class="box" placeholder="Street Name" required maxlength="100" name="street">
      <input type="text" class="box" placeholder="Barangay" required maxlength="50" name="barangay">
      <input type="text" class="box" placeholder="City/Municipality" required maxlength="50" name="city">
      <input type="text" class="box" placeholder="Province (optional)" maxlength="50" name="province">
      <input type="text" class="box" placeholder="Country (should be 'Philippines')" value="Philippines" readonly name="country">
      <input type="number" class="box" placeholder="Postal Code" required maxlength="6" min="0" max="999999" name="pin_code">

      <input type="submit" value="Save Address" name="submit" class="btn">
   </form>

</section>

<?php include 'components/footer.php' ?>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>
