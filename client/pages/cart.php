<?php
// Get cart items
$cart_items = [];
$cart_total = 0;
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    // Get cart from database for logged-in users
    $user_id = $_SESSION['user_id'];
    
    $cart_sql = "SELECT ci.*, p.product_name, p.price, p.sku, p.stock_quantity, p.short_description, c.category_name,
                 (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url,
                 (p.price * ci.quantity) as subtotal
                 FROM cart cart_table
                 JOIN cart_items ci ON cart_table.cart_id = ci.cart_id
                 JOIN products p ON ci.product_id = p.product_id
                 LEFT JOIN categories c ON p.category_id = c.category_id
                 WHERE cart_table.user_id = ? AND p.is_active = 1
                 ORDER BY ci.added_at DESC";
    
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $cart_total += $row['subtotal'];
        $cart_count += $row['quantity'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - REDY-MED</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>" data-user-id="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>

        <div id="cart-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged-in user: Show database cart -->
                <?php if (count($cart_items) > 0): ?>
                    <div class="cart-content">
                        <!-- Cart Items -->
                        <div class="cart-items-section">
                            <div class="cart-items-header">
                                <h2>Cart Items (<?php echo $cart_count; ?>)</h2>
                            </div>

                            <div class="cart-items-list">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="cart-item" data-cart-item-id="<?php echo $item['cart_item_id']; ?>">
                                        <div class="cart-item-image">
                                            <?php 
                                            $image_url = $item['image_url'] ? '../' . $item['image_url'] : '../images/placeholder.jpg';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        </div>

                                        <div class="cart-item-details">
                                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                            <p class="item-category"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                            <p class="item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                                            
                                            <?php if ($item['stock_quantity'] > 0): ?>
                                                <span class="stock-status in-stock">
                                                    <i class="fas fa-check-circle"></i> In Stock (<?php echo $item['stock_quantity']; ?> available)
                                                </span>
                                            <?php else: ?>
                                                <span class="stock-status out-of-stock">
                                                    <i class="fas fa-times-circle"></i> Out of Stock
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="cart-item-price">
                                            <span class="price-label">Price:</span>
                                            <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                                        </div>

                                        <div class="cart-item-quantity">
                                            <label>Quantity:</label>
                                            <div class="quantity-controls">
                                                <button class="qty-btn qty-decrease" onclick="updateCartQuantity(<?php echo $item['cart_item_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="qty-input" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" 
                                                       max="<?php echo $item['stock_quantity']; ?>"
                                                       onchange="updateCartQuantity(<?php echo $item['cart_item_id']; ?>, this.value)">
                                                <button class="qty-btn qty-increase" onclick="updateCartQuantity(<?php echo $item['cart_item_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cart-item-subtotal">
                                            <span class="subtotal-label">Subtotal:</span>
                                            <span class="subtotal">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                        </div>

                                        <div class="cart-item-actions">
                                            <button class="btn-remove" onclick="removeCartItem(<?php echo $item['cart_item_id']; ?>)" title="Remove">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Cart Summary -->
                        <div class="cart-summary">
                            <h2>Order Summary</h2>
                            
                            <div class="summary-row">
                                <span>Subtotal (<?php echo $cart_count; ?> items):</span>
                                <span class="summary-value">$<?php echo number_format($cart_total, 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span class="summary-value">Calculated at checkout</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Tax:</span>
                                <span class="summary-value">Calculated at checkout</span>
                            </div>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span class="summary-value">$<?php echo number_format($cart_total, 2); ?></span>
                            </div>
                            
                            <button class="btn-checkout">
                                <i class="fas fa-lock"></i> Proceed to Checkout
                            </button>
                            
                            <a href="../../index.php?page=product" class="btn-continue">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            
                            <div class="security-badges">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Checkout</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-cart">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h2>Your Cart is Empty</h2>
                        <p>Add some products to get started!</p>
                        <a href="../../index.php?page=product" class="btn-browse">
                            <i class="fas fa-box-open"></i> Browse Products
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Guest user: Cart will be loaded via JavaScript -->
                <div id="guest-cart-container">
                    <div class="loading-cart">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading your cart...</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>
    
    <script src="js/sidebar.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/cart-page.js"></script>
</body>
</html>

