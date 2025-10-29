<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - REDY-MED</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <!-- Main Content -->
    <main class="cart-main">
        <div class="cart-container">
            <div class="cart-page-header">
                <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
                <p>Review your items and proceed to checkout</p>
            </div>

            <!-- Loading State -->
            <div id="loading-cart" class="loading-cart">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading your cart...</p>
            </div>

            <!-- Cart Content (will be populated by JavaScript) -->
            <div id="cart-content" style="display: none;">
                <!-- Cart Items Section -->
                <div class="cart-layout">
                    <div class="cart-items-section">
                        <div class="cart-items-header">
                            <h2>Cart Items (<span id="total-items-count">0</span>)</h2>
                            <button id="clear-cart-btn" class="btn-clear-cart">
                                <i class="fas fa-trash-alt"></i> Clear Cart
                            </button>
                        </div>

                        <div id="cart-items-list" class="cart-items-list">
                            <!-- Items will be inserted here by JavaScript -->
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h2>Order Summary</h2>

                        <div class="summary-row">
                            <span>Subtotal (<span id="summary-items-count">0</span> items):</span>
                            <span class="summary-value" id="summary-subtotal">$0.00</span>
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
                            <span class="summary-value" id="summary-total">$0.00</span>
                        </div>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="checkout-info">
                            <i class="fas fa-info-circle"></i>
                            <p>Please login to proceed with checkout</p>
                        </div>

                        <a href="index.php?page=login" class="btn-checkout">
                            <i class="fas fa-sign-in-alt"></i> Login to Checkout
                        </a>
                        <?php else: ?>
                        <a href="index.php?page=checkout" class="btn-checkout">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                        <?php endif; ?>

                        <a href="index.php?page=product" class="btn-continue">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>

                        <div class="security-badges">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Checkout</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart -->
            <div id="empty-cart" style="display: none;">
                <div class="empty-cart">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Your Cart is Empty</h2>
                    <p>Add some products to get started!</p>
                    <a href="index.php?page=product" class="btn-browse">
                        <i class="fas fa-box-open"></i> Browse Products
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <script src="js/simple-cart.js"></script>
    <script src="js/cart-display.js"></script>
</body>
</html>
