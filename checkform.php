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
            // No redirect, just let the sidebar update via page refresh or form submit
            header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }

    // Handle clearing all cart items
    if (isset($_POST['clear_all'])) {
        unset($_SESSION['cart']);
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!-- Toggle Button (Always visible for toggling) -->
<button class="toggle-sidebar-btn" onclick="toggleSidebar()">🛒 View Cart</button>

<div class="checkout-sidebar hidden" id="checkoutSidebar">
    <button class="close-sidebar-btn" onclick="toggleSidebar()" style="float:right;background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
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
                <form method="post" class="remove-form" style="display:inline;">
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
    width: 320px;
    height: calc(100% - 70px);
    background: #fff;
    border-left: 3px solid #7d2626;
    padding: 22px 18px 18px 18px;
    overflow-y: auto;
    box-shadow: -4px 0 18px #7d262620;
    z-index: 1000;
    transition: transform 0.3s ease;
}

.checkout-sidebar.hidden {
    transform: translateX(100%);
}

.checkout-sidebar.active {
    transform: translateX(0);
}

.toggle-sidebar-btn {
    display: block;
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #7d2626;
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 13px 22px;
    font-size: 17px;
    z-index: 1001;
    box-shadow: 0 4px 12px #7d262655;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 1px;
}

.toggle-sidebar-btn:hover {
    background: #b23232;
}

.close-sidebar-btn {
    background: none;
    border: none;
    color: #7d2626;
    font-size: 28px;
    float: right;
    cursor: pointer;
    margin-bottom: 10px;
    font-weight: bold;
    transition: color 0.2s;
}

.close-sidebar-btn:hover {
    color: #b23232;
}

.checkout-sidebar h2 {
    color: #7d2626;
    font-size: 1.5rem;
    margin-bottom: 18px;
    text-align: left;
    font-weight: 700;
    letter-spacing: 1px;
}

.cart-item {
    display: flex;
    margin-bottom: 16px;
    border-bottom: 1.5px solid #b23232;
    padding-bottom: 12px;
    align-items: flex-start;
}

.cart-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 12px;
    border-radius: 7px;
    border: 2px solid #b23232;
    background: #fff;
}

.cart-item .details {
    font-size: 15px;
    flex-grow: 1;
    color: #7d2626;
}

.cart-item .details .name {
    font-weight: bold;
    color: #b23232;
    margin-bottom: 2px;
    font-size: 1.08em;
}

.remove-form {
    margin-top: 7px;
}

.remove-btn {
    background: #b23232;
    border: none;
    color: #fff;
    padding: 4px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: background 0.2s;
}

.remove-btn:hover {
    background: #7d2626;
}

.clear-all-form {
    margin-top: 12px;
}

.clear-all-btn {
    background: #a13c3c;
    border: none;
    color: #fff;
    padding: 7px 0;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
    width: 100%;
    margin-bottom: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    transition: background 0.2s;
}

.clear-all-btn:hover {
    background: #7d2626;
}

.cart-summary {
    margin-top: 22px;
    font-size: 16px;
    color: #7d2626;
}

.cart-summary p {
    margin: 7px 0;
}

.cart-summary .grand-total {
    font-size: 1.15em;
    color: #b23232;
    font-weight: bold;
}

.checkout-btn {
    display: block;
    text-align: center;
    background: #7d2626;
    color: #fff;
    padding: 11px 0;
    border-radius: 8px;
    font-weight: bold;
    border: none;
    margin-top: 14px;
    font-size: 1.1em;
    letter-spacing: 1px;
    width: 100%;
    transition: background 0.2s;
    cursor: pointer;
    box-shadow: 0 2px 8px #7d262633;
}

.checkout-btn:hover {
    background: #b23232;
}

.empty-cart {
    font-size: 16px;
    color: #b23232;
    text-align: center;
    margin-top: 40px;
    font-weight: 600;
}

/* Mobile styles */
@media (max-width: 768px) {
    .checkout-sidebar {
        width: 98vw;
        padding: 14px 4vw 14px 4vw;
    }
    .toggle-sidebar-btn {
        right: 10px;
        bottom: 10px;
        padding: 10px 16px;
        font-size: 15px;
    }
}
</style>

<!-- JavaScript -->
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('checkoutSidebar');
    sidebar.classList.toggle('active');
    sidebar.classList.toggle('hidden');
}
</script>