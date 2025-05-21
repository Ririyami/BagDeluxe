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
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<!-- menu section starts  -->

<div class="heading">
   <h3>Our Category</h3>
   <p><a href="home.php">Home</a> <span> / Products</span></p>

   <!-- Dropdown for Selecting Categories -->
   <form action="" method="GET" class="category-filter">
      <select name="category" onchange="this.form.submit()">
         <option value="">All Categories</option>
         <?php
            // Fetch distinct categories
            $categories = $conn->prepare("SELECT DISTINCT category FROM products");
            $categories->execute();
            while ($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                $selected = (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'selected' : '';
                echo "<option value='{$category['category']}' $selected>{$category['category']}</option>";
            }
         ?>
      </select>
   </form>
</div>


<section class="category">
<div class="categories-container">
   <!-- Brand Sidebar -->
   <div class="brand-sidebar">
      <ul>
         <li><a href="category.php?brand=Celine"><img src="images/b4.jpg" alt="Brand 1"><span>Celine</span></a></li>
         <li><a href="category.php?brand=Chanel"><img src="images/b5.jpg" alt="Brand 2"><span>Chanel</span></a></li>
         <li><a href="category.php?brand=Chloé"><img src="images/b6.jpg" alt="Brand 3"><span>Chloé</span></a></li>
         <li><a href="category.php?brand=Christian_Dior"><img src="images/b9.jpg" alt="Brand 4"><span>Christian Dior</span></a></li>
         <li><a href="category.php?brand=Fendi"><img src="images/b8.png" alt="Brand 5"><span>Fendi</span></a></li>
         <li><a href="category.php?brand=Gucci"><img src="images/b7.jpg" alt="Brand 6"><span>Gucci</span></a></li>
         <li><a href="category.php?brand=Hermès"><img src="images/b1.jpg" alt="Brand 7"><span>Hermès</span></a></li>
         <li><a href="category.php?brand=Louis_Vuitton"><img src="images/b2.jpg" alt="Brand 8"><span>Louis Vuitton</span></a></li>
         <li><a href="category.php?brand=Prada"><img src="images/b0.jpg" alt="Brand 9"><span>Prada</span></a></li>
         <li><a href="category.php?brand=Saint_Laurent"><img src="images/b3.jpg" alt="Brand 10"><span>Saint Laurent</span></a></li>
      </ul>
   </div>
   
</section>

<section class="products">

   <h1 class="title">Products</h1>

   <div class="box-container">

   <?php
        if (isset($_GET['category']) && $_GET['category'] !== '') {
             $selected_category = $_GET['category'];
             $query = "SELECT * FROM products WHERE category = ?";
             $select_products = $conn->prepare($query);
            $select_products->execute([$selected_category]);
       } else {
             $select_products = $conn->prepare("SELECT * FROM products ORDER BY id ASC");
             $select_products->execute();
       }

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

</section>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script>


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