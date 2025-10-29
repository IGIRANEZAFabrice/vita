-- ============================================
-- WISHLIST TABLE
-- ============================================

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES user_credentials(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    INDEX idx_added_at (added_at)
);

-- Sample data (optional - for testing)
-- INSERT INTO wishlist (user_id, product_id, notes) VALUES
-- (2, 9, 'Need this for the new clinic'),
-- (3, 9, 'Interested in bulk purchase');

-- ============================================
-- USEFUL QUERIES
-- ============================================

-- Get user's wishlist with product details
-- SELECT w.*, p.product_name, p.price, p.sku, c.category_name,
--        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
-- FROM wishlist w
-- JOIN products p ON w.product_id = p.product_id
-- LEFT JOIN categories c ON p.category_id = c.category_id
-- WHERE w.user_id = ? AND p.is_active = 1
-- ORDER BY w.added_at DESC;

-- Check if product is in user's wishlist
-- SELECT COUNT(*) as is_in_wishlist 
-- FROM wishlist 
-- WHERE user_id = ? AND product_id = ?;

-- Get wishlist count for user
-- SELECT COUNT(*) as total 
-- FROM wishlist 
-- WHERE user_id = ?;

