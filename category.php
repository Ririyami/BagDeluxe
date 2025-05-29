<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';

include 'components/add_cart.php';

// Get the category from the URL (if available)
$category = $_GET['category'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>category</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
   .box-container {
      display: flex;
      flex-wrap: wrap;
      gap: 18px;
      justify-content: center;
      align-items: stretch;
   }
   .box.product-box {
      position: relative;
      overflow: hidden;
      background: #fff;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      min-height: 350px;
      max-width: 240px;
      width: 100%;
      border-radius: 12px;
      border: 2px solid #39303025;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
      margin-bottom: 12px;
   }
   .box.product-box img {
      width: 85%;
      max-width: 220px;
      height: 170px;
      object-fit: contain;
      display: block;
      margin: 20px auto 12px auto;
      background: #fff;
   }
   .product-details-hover {
      display: none;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: calc(100% - 62px);
      background: rgba(255,255,255,0.96);
      color: #333;
      padding: 18px 14px 12px 14px;
      z-index: 5;
      box-sizing: border-box;
      text-align: left;
      font-size: 14px;
      border-radius: 12px 12px 0 0;
      border: 1.5px solid #c2bdbd;
      border-bottom: none;
   }
   .box.product-box:hover .product-details-hover {
      display: flex;
      animation: fadeIn 0.15s;
   }
   @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
   }
   .product-details-hover h4 {
      margin: 0 0 6px 0;
      font-size: 1.08em;
      font-weight: bold;
   }
   .product-details-hover p {
      margin: 0 0 6px 0;
      font-size: 0.98em;
   }
   .box.product-box .bottom-bar {
      background: #fff;
      border-top: 1.5px solid #c2bdbd;
      width: 100%;
      min-height: 62px;
      height: 62px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 14px 0 14px;
      box-sizing: border-box;
      position: absolute;
      left: 0;
      bottom: 0;
      z-index: 10;
      border-radius: 0 0 12px 12px;
   }
   .box.product-box .price {
      font-weight: bold;
      font-size: 1.15em;
      color: #7d2626;
      letter-spacing: 1px;
   }
   .box.product-box .cart-action {
      display: flex;
      align-items: center;
      gap: 8px;
   }
   .box.product-box .qty {
      width: 52px;
      padding: 2px 4px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1em;
      height: 40px;
      text-align: center;
   }
   .box.product-box .fa-shopping-cart {
      background: #7d2626;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      color: #fff;
      font-size: 2.1em;
      transition: color 0.2s, background 0.2s, box-shadow 0.2s;
      padding: 2px 14px 2px 14px;
      margin-left: 2px;
      height: 44px;
      width: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 6px #0001;
   }
   .box.product-box .fa-shopping-cart:hover {
      background: #9d3434;
      color: #fff;
      box-shadow: 0 4px 12px #0002;
   }
   .box .fa-eye { display: none !important; }
   .box.product-box > *:not(.bottom-bar):not(.product-details-hover) {
      width: 100%;
      text-align: center;
   }

   /* Brand filter styles */
   .brand-filter {
      margin-bottom: 32px;
      text-align: center;
   }
   .brand-filter h2 {
      font-size: 1.2em;
      color: #7d2626;
      margin-bottom: 12px;
      font-weight: 600;
      letter-spacing: 1px;
   }
   .brand-filter-container {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      justify-content: center;
      align-items: stretch;
      margin-bottom: 10px;
   }
   .brand-filter-box {
      background: #fff;
      border-radius: 12px;
      border: 2px solid #39303025;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
      padding: 12px 24px;
      text-align: center;
      font-size: 1.08em;
      font-weight: bold;
      color: #7d2626;
      text-decoration: none;
      transition: background 0.2s, color 0.2s;
      min-width: 120px;
      margin-bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .brand-filter-box:hover, .brand-filter-box.active {
      background: #f5eaea;
      color: #9d3434;
   }
   .brand-sidebar ul li a:hover {
      background: #fbeaea;
    }
.brand-sidebar ul li a:hover img {
      transform: scale(1.13);
      box-shadow: 0 4px 16px #b2323233;
      border-color: #7d2626;
    }

    .brand-sidebar ul li a:hover span {
      color: #b23232;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .brand-sidebar ul li img {
      transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
    }

    .brand-sidebar ul li span {
      transition: color 0.18s, letter-spacing 0.18s;
    }

   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="category">
<div class="categories-container">
   <div class="brand-sidebar">
      <ul>
         <li><a href="category.php?brand=Celine"><img src="images/b4.jpg" alt=""><span>Celine</span></a></li>
         <li><a href="category.php?brand=Chanel"><img src="images/b5.jpg" alt=""><span>Chanel</span></a></li>
         <li><a href="category.php?brand=Chloé"><img src="images/b6.jpg" alt=""><span>Chloé</span></a></li>
         <li><a href="category.php?brand=Christian_Dior"><img src="images/b9.jpg" alt=""><span>Christian Dior</span></a></li>
         <li><a href="category.php?brand=Fendi"><img src="images/b8.png" alt=""><span>Fendi</span></a></li>
         <li><a href="category.php?brand=Gucci"><img src="images/b7.jpg" alt=""><span>Gucci</span></a></li>
         <li><a href="category.php?brand=Hermès"><img src="images/b1.jpg" alt=""><span>Hermès</span></a></li>
         <li><a href="category.php?brand=Louis_Vuitton"><img src="images/b2.jpg" alt=""><span>Louis Vuitton</span></a></li>
         <li><a href="category.php?brand=Prada"><img src="images/b0.jpg" alt=""><span>Prada</span></a></li>
         <li><a href="category.php?brand=Saint_Laurent"><img src="images/b3.jpg" alt=""><span>Saint Laurent</span></a></li>
      </ul>
   </div>
</div>
</section>

<section class="products">
   <h1 class="title">
      <?php
         if (isset($_GET['brand'])) {
            echo htmlspecialchars($_GET['brand']) . " Products";
         } elseif (isset($_GET['category'])) {
            echo htmlspecialchars($_GET['category']) . " Category";
         } else {
            echo "All Products";
         }
      ?>
   </h1>
   <?php if (isset($_GET['category'])): ?>
      <div class="brand-filter">
         <h2>Select a Brand</h2>
         <div class="brand-filter-container">
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Celine" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Celine') ? ' active' : '' ?>"><h3>Celine</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Chanel" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Chanel') ? ' active' : '' ?>"><h3>Chanel</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Chloé" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Chloé') ? ' active' : '' ?>"><h3>Chloé</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Christian_Dior" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Christian_Dior') ? ' active' : '' ?>"><h3>Christian Dior</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Fendi" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Fendi') ? ' active' : '' ?>"><h3>Fendi</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Gucci" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Gucci') ? ' active' : '' ?>"><h3>Gucci</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Hermès" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Hermès') ? ' active' : '' ?>"><h3>Hermès</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Louis_Vuitton" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Louis_Vuitton') ? ' active' : '' ?>"><h3>Louis Vuitton</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Prada" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Prada') ? ' active' : '' ?>"><h3>Prada</h3></a>
            <a href="category.php?category=<?= urlencode($_GET['category']); ?>&brand=Saint_Laurent" class="brand-filter-box<?= (isset($_GET['brand']) && $_GET['brand'] == 'Saint_Laurent') ? ' active' : '' ?>"><h3>Saint Laurent</h3></a>
         </div>
      </div>
   <?php endif; ?>
   <div class="box-container">
      <?php
         $category = $_GET['category'] ?? '';
         $brand = $_GET['brand'] ?? '';
         if ($category && $brand) {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ? AND brand = ?");
            $select_products->execute([$category, $brand]);
         } elseif ($category) {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
            $select_products->execute([$category]);
         } elseif ($brand) {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE brand = ?");
            $select_products->execute([$brand]);
         } else {
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
         }
         if ($select_products->rowCount() > 0) {
            while ($fetch = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box product-box">
         <img src="uploaded_img/<?= $fetch['image']; ?>" alt="">
         <div class="name"><?= $fetch['name']; ?></div>
         <div class="bottom-bar">
            <div class="price"><span>₱</span><?= number_format($fetch['price'], 0); ?></div>
            <div class="cart-action">
               <input type="number" class="qty" min="1" max="99" value="1">
               <button type="button" class="fas fa-shopping-cart"
                  onclick="addToCart(
                    <?= $fetch['id']; ?>,
                    '<?= addslashes($fetch['name']); ?>',
                    <?= $fetch['price']; ?>,
                    '<?= $fetch['image']; ?>',
                    this.previousElementSibling.value
                  )"></button>
            </div>
         </div>
         <div class="product-details-hover">
            <h4><?= htmlspecialchars($fetch['name']); ?></h4>
            <?php if (!empty($fetch['category'])): ?>
              <p><strong>Category:</strong> <?= htmlspecialchars($fetch['category']); ?></p>
            <?php endif; ?>
            <?php if (!empty($fetch['brand'])): ?>
              <p><strong>Brand:</strong> <?= htmlspecialchars($fetch['brand']); ?></p>
            <?php endif; ?>
            <?php if (!empty($fetch['description'])): ?>
              <p><?= htmlspecialchars($fetch['description']); ?></p>
            <?php endif; ?>
            <?php if (!empty($fetch['stock'])): ?>
              <p><strong>Stock:</strong> <?= htmlspecialchars($fetch['stock']); ?></p>
            <?php endif; ?>
         </div>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No products found!</p>';
         }
      ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
   const sidebar = document.querySelector('.brand-sidebar');
   const header = document.querySelector('.header');
   if (sidebar && header) {
      sidebar.style.top = `${header.offsetHeight}px`;

      let lastScrollY = window.scrollY;
      window.addEventListener('scroll', () => {
         if (window.scrollY > lastScrollY && window.scrollY > header.offsetHeight) {
            sidebar.style.top = '0';
         } else {
            sidebar.style.top = `${header.offsetHeight}px`;
         }
         lastScrollY = window.scrollY;
      });
   }
});

function addToCart(pid, name, price, image, qty) {
    fetch('add_to_session_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ pid, name, price, image, qty: parseInt(qty) || 1 })
    })
    .then(res => res.json())
    .then data => {
        if (data.success) {
            alert("Item added to cart!");
            location.reload();
        } else {
            alert("Failed: " + data.error);
        }
    });
}
</script>
<?php include 'checkform.php'; ?>
</body>
</html>