<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle item removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_index'])) {
        $removeIndex = (int)$_POST['remove_index'];
        if (isset($_SESSION['cart'][$removeIndex])) {
            unset($_SESSION['cart'][$removeIndex]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
            echo "<script>location.reload();</script>";
            exit;
        }
    }

    // Handle clearing all cart items
    if (isset($_POST['clear_all'])) {
        unset($_SESSION['cart']);
        echo "<script>location.reload();</script>";
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!-- Toggle Button (Visible only on mobile) -->
<button class="toggle-sidebar-btn" onclick="toggleSidebar()">🛒 View Cart</button>

<div class="checkout-sidebar" id="checkoutSidebar">
    <h2>Order Summary</h2>

    <?php if (!empty($cart)): ?>
        <?php foreach ($cart as $index => $item): 
            $itemTotal = $item['price'] * $item['qty'];
            $total += $itemTotal;
        ?>
        <div class="cart-item">
            <img src="uploaded_img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="details">
                <p class="name"><?= htmlspecialchars($item['name']) ?></p>
                <p>₱<?= number_format($item['price'], 0); ?> x <?= $item['qty']; ?></p>
                <p><strong>Total:</strong> ₱<?= number_format($itemTotal, 0); ?></p>
                <form method="post" class="remove-form">
                    <input type="hidden" name="remove_index" value="<?= $index ?>">
                    <button type="submit" class="remove-btn" title="Remove">Remove</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="cart-summary">
            <p><strong>Subtotal:</strong> ₱<?= number_format($total, 0); ?></p>
            <p><strong>Shipping:</strong> Free</p>
            <p class="grand-total"><strong>Total:</strong> ₱<?= number_format($total, 0); ?></p>

            <form method="post" class="clear-all-form">
                <button type="submit" name="clear_all" class="clear-all-btn" title="Clear Cart">Clear All</button>
            </form>

            <form action="sync_cart.php" method="post">
    <button type="submit" class="checkout-btn">Checkout</button>
</form>

        </div>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php endif; ?>
</div>

<!-- Styles -->
<style>
.checkout-sidebar {
    position: fixed;
    right: 0;
    top: 70px;
    width: 300px;
    height: calc(100% - 70px);
    background: #fff;
    border-left: 2px solid #ddd;
    padding: 20px;
    overflow-y: auto;
    box-shadow: -4px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: transform 0.3s ease;
}

.checkout-sidebar.hidden {
    transform: translateX(100%);
}

.toggle-sidebar-btn {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 30px;
    padding: 12px 18px;
    font-size: 16px;
    z-index: 1001;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.cart-item {
    display: flex;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.cart-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 10px;
}

.cart-item .details {
    font-size: 14px;
    flex-grow: 1;
}

.remove-form {
    margin-top: 5px;
}

.remove-btn {
    background: #f44336;
    border: none;
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
}

.remove-btn:hover {
    background-color: #c62828;
}

.clear-all-form {
    margin-top: 10px;
}

.clear-all-btn {
    background: #9E9E9E;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin-bottom: 10px;
}

.clear-all-btn:hover {
    background-color: #757575;
}

.cart-summary {
    margin-top: 20px;
    font-size: 15px;
}

.cart-summary p {
    margin: 5px 0;
}

.checkout-btn {
    display: block;
    text-align: center;
    background: #4CAF50;
    color: white;
    padding: 10px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.empty-cart {
    font-size: 16px;
    color: #666;
    text-align: center;
}

/* Mobile styles */
@media (max-width: 768px) {
    .checkout-sidebar {
        transform: translateX(100%);
    }

    .checkout-sidebar.active {
        transform: translateX(0);
    }

    .toggle-sidebar-btn {
        display: block;
    }
}
</style>

<!-- JavaScript -->
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('checkoutSidebar');
    sidebar.classList.toggle('active');
}
</script>
