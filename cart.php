<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
   $product_id = $_POST['product_id'];
   $quantity = $_POST['quantity'];

   // Fetch product details
   $productQuery = $conn->prepare("SELECT * FROM products WHERE id = ?");
   $productQuery->execute([$product_id]);
   $product = $productQuery->fetch(PDO::FETCH_ASSOC);

   if ($product) {
       $product_name = $product['name'];
       $product_price = $product['price'];
       $product_image = $product['image'];

       // Check if item is already in the cart
       $checkCart = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND pid = ?");
       $checkCart->execute([$user_id, $product_id]);

       if ($checkCart->rowCount() > 0) {
           // Update quantity if already exists in cart
           $updateCart = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND pid = ?");
           $updateCart->execute([$quantity, $user_id, $product_id]);
       } else {
           // Insert into cart
           $insertCart = $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
           $insertCart->execute([$user_id, $product_id, $product_name, $product_price, $quantity, $product_image]);
       }

       echo "Product added to cart successfully!";
   } else {
       echo "Product not found!";
   }
}


if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
   $message[] = 'cart item deleted!';
}

if(isset($_POST['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   // header('location:cart.php');
   $message[] = 'deleted all from cart!';
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated';
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Products Section */
.products {
   padding: 4rem 2rem;
   background: #805858; /* Light background for contrast */
   text-align: center;
   border-radius: 0; /* Rounded corners for the section */
   box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
   margin: 2rem 2rem 2rem 2rem;
}

.products .box-container {
   display: flex; 
   flex-wrap: wrap; 
   justify-content: center; 
   align-items: flex-start;
   gap: 2rem; 
   margin-top: 2rem; 
   
}

/* Adjust button positioning and hover effects */
.products .box-container .box .fa-eye,
.products .box-container .box .fa-shopping-cart {
   position: absolute;
   padding: 0.5rem;
   height: 3rem;
   width: 3rem;
   line-height: 4.3rem;
   border: 2px solid black;
   background-color: white;
   cursor: pointer;
   font-size: 2rem;
   color: black;
   transition: 0.3s ease;
   text-align: center;
   z-index: 10;
}

.products .box-container .box .fa-eye {
   left: 1rem; /* Adjusted position */
}

.products .box-container .box .fa-shopping-cart {
   right: 5rem;
   bottom: 0.5rem; /* Adjusted position */
}

.products .box-container .box:hover .fa-eye,
.products .box-container .box:hover .fa-shopping-cart {
   transform: scale(1.1); /* Slight zoom effect on hover */
   background-color: var(--yellow); /* Highlight color */
   color: white;
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
   border-radius: 10px;
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

.products h1{
   color:white;
}

.products .box-container h3{
   color: white;
   margin: 0.5rem 0;
   font-size: 20px;
}

.products .box-container .box .cat{
   font-size: 20px;
   font-weight: bold;
   color: #381320;             /*category name - in featured products*/
   text-decoration: none;
}

.products .box-container .box .cat:hover{
   color:black;
   text-decoration: underline;
}

.products .box-container .box .name{
   font-size: 20px;
   text-align: center;
   font-weight: bold;
   color: black;        /*product name - in featured product*/
   margin:1rem 0;
}

.products .box-container .box .flex{
   display: flex;
   align-items: center;
   gap:.4rem;
   margin-top: 1.5rem;
}

.products .box-container .box .flex .price{
   margin-right: auto;
   font-size: 23px;
   font-weight: bold;
   color:#682020;      /*price name - in featured product*/
}

.products .box-container .box .flex .price span{
   color: #682020;                /*peso sign color*/
   font-size: 23px;
}

.products .box-container .box .flex .qty{
   border-radius: 5px;
   border:.1rem solid black;
   font-size: 1rem;
   margin-left: 1rem;
   color:black;
   height: 2rem;
   width: 50px;
}
/*.products .box-container .box .flex .qty:hover{
   background-color: #5a1e2d;
   color:white; 
}*/
.products .box-container .box .flex .fa-edit{
   width: 2.3rem;
   background-color: #5a1e2d;
   color:black;
   cursor: pointer;
   font-size: 1rem;
   height: 2.3rem;
   border:.1rem solid black;
}

.products .box-container .box .flex .fa-edit:hover{
   background-color: black;
   color:white;
}

.products .box-container .box .fa-times{
   position: absolute;
   top:1rem; right:1rem;
   background-color: #381820;
   color: white;
   border:.2rem solid black;
   line-height:4rem;
   height: 4.3rem;
   width: 4.5rem;
   cursor: pointer;
   font-size: 2rem;
   z-index: 10; /* << Add this line to keep it above image */
}

.products .box-container .box .fa-times:hover{
   background-color: black;
   color: white;
}

.products .box-container .box .sub-total{
   position: absolute; 
   bottom: 10px;
   left: 10px; 
   font-size: 1rem; 
   color: #333;
   background-color: #f9f9f9; 
   padding: 5px 10px;
   border-radius: 5px; 
}

.products .box-container .box .sub-total span{
   color: red;
}

.products .more-btn{
   margin-top: 1rem;
   text-align: center;
   color: white;
}

.products .cart-total{
   display: flex;
   align-items: center;
   gap:1.5rem;
   flex-wrap: wrap;
   justify-content: space-between;
   margin-top: 3rem;
   border:.2rem solid black;
   padding:1rem;
}

.products .cart-total p{
   font-size: 2.5rem;
   color:#777;
}

.products .cart-total p span{
   color:red;
}

.products .cart-total .btn{
   margin-top: 0;
}

.products .box-container .box .top-actions {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: absolute;
    top: 1rem;
    left: 1rem;
    right: 1rem;
    z-index: 20;
    gap: 0.5rem;
}

.products .box-container .box .cart-check {
    width: 1.3rem;
    height: 1.3rem;
    accent-color: #381320;
    cursor: pointer;
    margin: 0;
}

.products .box-container .box .fa-eye {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1.3rem;
    border-radius: 50%;
    border: 2px solid #381320;
    background: #fff;
    color: #381320;
    transition: background 0.2s, color 0.2s;
    margin-top: 1.9rem;
    margin-right: .5rem;
}

.products .box-container .box .fa-eye:hover {
    background: #381320;
    color: #fff;
}

.products .box-container .box .delete-btn {
    background: #381820;
    color: #fff;
    border: .15rem solid black;
    border-radius: 50%;
    width: 2.2rem;
    height: 2.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    transition: background 0.2s, color 0.2s;
    position: relative;
}

.products .box-container .box .delete-btn:after {
    content: "×";
    font-size: 1.5rem;
    line-height: 2.2rem;
    color: #fff;
}

.products .box-container .box .delete-btn:hover {
    background: black;
    color: white;
}

.actions-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* align to left */
    gap: 0.3rem;            /* small space between checkbox and eye */
}

   </style>

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>shopping cart</h3>
   <p><a href="home.php">home</a> <span> / cart</span></p>
</div>

<!-- shopping cart section starts  -->

<section class="products">

   <h1 class="title">your cart</h1>

   <div class="box-container">

      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); // <-- Move this up!
      ?>
      
      <form action="" method="post" class="box">
         <div class="top-actions">
            <div class="actions-left">
               <input type="checkbox" name="selected_items[]" value="<?= $fetch_cart['id']; ?>" class="cart-check">
               <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
            </div>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('delete this item?');"></button>
         </div>
         <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
         <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
         <div class="name"><?= $fetch_cart['name']; ?></div>
         <div class="flex">
            <div class="price">₱<?= number_format($sub_total, 2); ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" maxlength="2">
            <button type="submit" class="fas fa-edit" name="update_qty"></button>
         </div>
      </form>
      <?php
               $grand_total += $sub_total;
            }
         }else{
            echo '<p class="empty">your cart is empty</p>';
         }
      ?>

   </div>

   <div class="cart-total">
      <p>cart total : <span>₱<?= number_format($grand_total, 2); ?></span></p>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
   </div>

   <div class="more-btn">
      <form action="" method="post">
         <button type="submit" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" name="delete_all" onclick="return confirm('delete all from cart?');">delete all</button>
      </form>
      <a href="menu.php" class="btn">continue shopping</a>
   </div>

</section>

<!-- shopping cart section ends -->










<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->








<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
