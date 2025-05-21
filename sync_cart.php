<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';

if (!$user_id) {
    header('location:login.php');
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('location:checkform.php');
    exit;
}

foreach ($_SESSION['cart'] as $item) {
    $product_id = $item['pid'];
    $name = $item['name'];
    $price = $item['price'];
    $quantity = $item['qty'];
    $image = $item['image'];

    // ✅ FIXED: Use correct column name 'stock_quantity'
    $stock_check = $conn->prepare("SELECT stock_quantity FROM inventory WHERE product_id = ?");
    $stock_check->execute([$product_id]);
    $stock = $stock_check->fetchColumn();

    if ($stock === false) {
        die("Error: Product not found in inventory.");
    }

    // Get existing quantity in user's cart (if any)
    $existing_cart = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND pid = ?");
    $existing_cart->execute([$user_id, $product_id]);
    $existing_qty = $existing_cart->fetchColumn() ?? 0;

    // Total requested
    $total_requested = $existing_qty + $quantity;

    if ($total_requested > $stock) {
        die("Error: Not enough stock available. Requested: $total_requested, Available: $stock");
    }

    // Insert or update cart
    if ($existing_qty > 0) {
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND pid = ?");
        $update->execute([$quantity, $user_id, $product_id]);
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([$user_id, $product_id, $name, $price, $quantity, $image]);
    }
}

// Clear session cart
unset($_SESSION['cart']);

// Redirect to cart page
header("Location: cart.php");
exit;
?>
