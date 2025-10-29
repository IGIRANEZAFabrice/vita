-- ============================================
-- SHOPPING CART TABLES
-- ============================================

-- Cart Table (stores cart sessions)
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_credentials(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    INDEX idx_updated (updated_at)
);

-- Cart Items Table (stores individual products in cart)
CREATE TABLE IF NOT EXISTS cart_items (
    cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id),
    INDEX idx_cart (cart_id),
    INDEX idx_product (product_id),
    CHECK (quantity > 0)
);

-- ============================================
-- USEFUL QUERIES
-- ============================================

-- Get cart items with product details for a user
-- SELECT ci.*, p.product_name, p.price, p.sku, p.stock_quantity,
--        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url,
--        (p.price * ci.quantity) as subtotal
-- FROM cart c
-- JOIN cart_items ci ON c.cart_id = ci.cart_id
-- JOIN products p ON ci.product_id = p.product_id
-- WHERE c.user_id = ? AND p.is_active = 1
-- ORDER BY ci.added_at DESC;

-- Get cart total for a user
-- SELECT SUM(p.price * ci.quantity) as total
-- FROM cart c
-- JOIN cart_items ci ON c.cart_id = ci.cart_id
-- JOIN products p ON ci.product_id = p.product_id
-- WHERE c.user_id = ? AND p.is_active = 1;

-- Get cart items count for a user
-- SELECT SUM(ci.quantity) as total_items
-- FROM cart c
-- JOIN cart_items ci ON c.cart_id = ci.cart_id
-- WHERE c.user_id = ?;

-- Merge guest cart to user cart on login
-- UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ?;

-- Clean up old guest carts (older than 30 days)
-- DELETE FROM cart WHERE user_id IS NULL AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

