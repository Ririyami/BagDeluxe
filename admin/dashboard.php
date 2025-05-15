<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin dashboard section starts -->

<section class="dashboard">

   <h1 class="heading">Dashboard</h1>

   <div class="table-container">
      <table>
         <thead>
            <tr>
               <th>Category</th>
               <th>Count</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>Total Pendings</td>
               <td>
                  <?php
                     $total_pendings = 0;
                     $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                     $select_pendings->execute(['pending']);
                     while ($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
                        $total_pendings += $fetch_pendings['total_price'];
                     }
                     echo "₱" . $total_pendings;
                  ?>
               </td>
               <td><a href="placed_orders.php" class="btn">See Pending Orders</a></td>
            </tr>
            <tr>
               <td>Total Completes</td>
               <td>
                  <?php
                     $total_completes = 0;
                     $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                     $select_completes->execute(['completed']);
                     while ($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)) {
                        $total_completes += $fetch_completes['total_price'];
                     }
                     echo "₱" . $total_completes;
                  ?>
               </td>
               <td><a href="placed_orders.php" class="btn">See Complete Orders</a></td>
            </tr>
            <tr>
               <td>Total Orders</td>
               <td>
                  <?php
                     $select_orders = $conn->prepare("SELECT * FROM `orders`");
                     $select_orders->execute();
                     echo $select_orders->rowCount();
                  ?>
               </td>
               <td><a href="placed_orders.php" class="btn">See Orders</a></td>
            </tr>
            <tr>
               <td>Products Added</td>
               <td>
                  <?php
                     $select_products = $conn->prepare("SELECT * FROM `products`");
                     $select_products->execute();
                     echo $select_products->rowCount();
                  ?>
               </td>
               <td><a href="products.php" class="btn">See Products</a></td>
            </tr>
            <tr>
               <td>Users Accounts</td>
               <td>
                  <?php
                     $select_users = $conn->prepare("SELECT * FROM `users`");
                     $select_users->execute();
                     echo $select_users->rowCount();
                  ?>
               </td>
               <td><a href="users_accounts.php" class="btn">See Users</a></td>
            </tr>
            <tr>
               <td>Admins</td>
               <td>
                  <?php
                     $select_admins = $conn->prepare("SELECT * FROM `admin`");
                     $select_admins->execute();
                     echo $select_admins->rowCount();
                  ?>
               </td>
               <td><a href="admin_accounts.php" class="btn">See Admins</a></td>
            </tr>
            <tr>
               <td>New Messages</td>
               <td>
                  <?php
                     $select_messages = $conn->prepare("SELECT * FROM `messages`");
                     $select_messages->execute();
                     echo $select_messages->rowCount();
                  ?>
               </td>
               <td><a href="messages.php" class="btn">See Messages</a></td>
            </tr>
         </tbody>
      </table>
   </div>

</section>

<!-- admin dashboard section ends -->

<!-- custom js file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>