<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['update'])) {
    $pid = $_POST['pid'];
    $pid = filter_var($pid, FILTER_SANITIZE_STRING);
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);
    $brand = $_POST['brand'];
    $brand = filter_var($brand, FILTER_SANITIZE_STRING);
    $bag_color = $_POST['bag_color'];
    $bag_color = filter_var($bag_color, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $stock = $_POST['stock'];
    $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT);

    $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ?, brand = ?, bag_color = ?, description = ?, stock = ? WHERE id = ?");
    $update_product->execute([$name, $category, $price, $brand, $bag_color, $description, $stock, $pid]);

    $message[] = 'Product updated successfully!';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $pid]);
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('../uploaded_img/' . $old_image);
            $message[] = 'Image updated!';
        }
    }
}

if (isset($_GET['update'])) {
    $update_id = $_GET['update'];
    $select_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $select_product->execute([$update_id]);
    if ($select_product->rowCount() > 0) {
        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
    } else {
        header('location:products.php');
    }
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $brand = $_POST['brand'];
    $brand = filter_var($brand, FILTER_SANITIZE_STRING);
    $bag_color = $_POST['bag_color'];
    $bag_color = filter_var($bag_color, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $stock = $_POST['stock'];
    $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT);

    $update_query = $conn->prepare("UPDATE products SET brand = ?, bag_color = ?, description = ?, stock = ? WHERE id = ?");
    $update_query->execute([$brand, $bag_color, $description, $stock, $id]);

    $message[] = 'Product updated successfully!';
    header('location:products.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- update product section starts  -->

<section class="update-product">

   <h1 class="heading">update product</h1>

   <?php
      $update_id = $_GET['update'];
      $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $show_products->execute([$update_id]);
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">

      <span>Update Name</span>
      <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box" value="<?= $fetch_products['name']; ?>">

      <span>Update Price</span>
      <input type="number" min="0" max="9999999999" required placeholder="Enter product price" name="price" onkeypress="if(this.value.length == 10) return false;" class="box" value="<?= $fetch_products['price']; ?>">

      <span>Update Category</span>
      <select name="category" class="box" required>
         <option selected value="<?= $fetch_products['category']; ?>"><?= $fetch_products['category']; ?></option>
         <option value="Male Bags">Male Bags</option>
         <option value="Female Bags">Female Bags</option>
         <option value="Unisex Bags">Unisex Bags</option>
         <option value="Wallet and Purse">Wallet and Purse</option>
      </select>

      <span>Update Brand</span>
      <select name="brand" class="box" required>
         <option selected value="<?= isset($fetch_products['brand']) ? $fetch_products['brand'] : ''; ?>">
            <?= isset($fetch_products['brand']) ? $fetch_products['brand'] : 'Select a Brand'; ?>
         </option>
         <option value="Celine">Celine</option>
         <option value="Chanel">Chanel</option>
         <option value="Chloé">Chloé</option>
         <option value="Christian_Dior">Christian Dior</option>
         <option value="Fendi">Fendi</option>
         <option value="Gucci">Gucci</option>
         <option value="Hermès">Hermès</option>
         <option value="Louis_Vuitton">Louis Vuitton</option>
         <option value="Prada">Prada</option>
         <option value="Saint_Laurent">Saint Laurent</option>
      </select>

      <span>Update Bag Color</span>
      <input type="text" required placeholder="Enter bag color" name="bag_color" maxlength="50" class="box" value="<?= isset($fetch_products['bag_color']) ? $fetch_products['bag_color'] : ''; ?>">

      <span>Update Description</span>
      <textarea name="description" required placeholder="Enter product description" class="box" maxlength="500"><?= isset($fetch_products['description']) ? $fetch_products['description'] : ''; ?></textarea>

      <span>Update Stock</span>
      <input type="number" min="0" required placeholder="Enter stock quantity" name="stock" class="box" value="<?= isset($fetch_products['stock']) ? $fetch_products['stock'] : ''; ?>">

      <span>Update Image</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">

      <div class="flex-btn">
         <input type="submit" value="Update" class="btn" name="update">
         <a href="products.php" class="option-btn">Go Back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>

</section>

<!-- update product section ends -->










<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>