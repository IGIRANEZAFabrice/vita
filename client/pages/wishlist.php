<?php
// Handle wishlist actions
$message = '';
$message_type = '';

// Remove from wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $wishlist_id = intval($_POST['wishlist_id']);
    $user_id = $_SESSION['user_id'];
    
    $delete_sql = "DELETE FROM wishlist WHERE wishlist_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $wishlist_id, $user_id);
    
    if ($stmt->execute()) {
        $message = "Item removed from wishlist successfully!";
        $message_type = "success";
    } else {
        $message = "Error removing item from wishlist.";
        $message_type = "error";
    }
    $stmt->close();
}

// Move to cart (if cart table exists)
if (isset($_POST['move_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $wishlist_id = intval($_POST['wishlist_id']);
    $user_id = $_SESSION['user_id'];
    
    // Note: This requires a cart table to be created
    // For now, we'll just show a message
    $message = "Cart functionality will be available soon!";
    $message_type = "info";
}

// Fetch wishlist items
$user_id = $_SESSION['user_id'];
$wishlist_sql = "SELECT w.*, p.product_name, p.price, p.sku, p.stock_quantity, p.short_description, c.category_name,
                 (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
                 FROM wishlist w
                 JOIN products p ON w.product_id = p.product_id
                 LEFT JOIN categories c ON p.category_id = c.category_id
                 WHERE w.user_id = ? AND p.is_active = 1
                 ORDER BY w.added_at DESC";

$stmt = $conn->prepare($wishlist_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_result = $stmt->get_result();
$wishlist_items = [];
while ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
$stmt->close();

$wishlist_count = count($wishlist_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - REDY-MED</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/wishlist.css">
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-heart"></i> My Wishlist</h1>
            <p>Save your favorite products for later. You have <?php echo $wishlist_count; ?> item<?php echo $wishlist_count != 1 ? 's' : ''; ?> in your wishlist.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($wishlist_count > 0): ?>
            <div class="wishlist-container">
                <div class="wishlist-header">
                    <div class="wishlist-stats">
                        <div class="stat-item">
                            <i class="fas fa-heart"></i>
                            <span><?php echo $wishlist_count; ?> Items</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-tag"></i>
                            <span>Total Value: $<?php 
                                $total_value = 0;
                                foreach ($wishlist_items as $item) {
                                    if ($item['price'] > 0) {
                                        $total_value += $item['price'];
                                    }
                                }
                                echo number_format($total_value, 2);
                            ?></span>
                        </div>
                    </div>
                    <div class="wishlist-actions">
                        <a href="../../index.php?page=product" class="btn-continue-shopping">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="wishlist-grid">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="wishlist-card">
                            <div class="wishlist-card-image">
                                <?php 
                                $image_url = $item['image_url'] ? '../' . $item['image_url'] : '../images/placeholder.jpg';
                                ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <div class="wishlist-badge">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                            
                            <div class="wishlist-card-content">
                                <span class="product-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                                <h3 class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                
                                <?php if ($item['short_description']): ?>
                                    <p class="product-description"><?php echo htmlspecialchars(substr($item['short_description'], 0, 100)) . '...'; ?></p>
                                <?php endif; ?>
                                
                                <div class="product-meta">
                                    <span class="product-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></span>
                                    <?php if ($item['stock_quantity'] > 0): ?>
                                        <span class="stock-status in-stock">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    <?php else: ?>
                                        <span class="stock-status out-of-stock">
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-price">
                                    <?php if ($item['price'] > 0): ?>
                                        <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="price-contact">Contact for Price</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($item['notes']): ?>
                                    <div class="product-notes">
                                        <i class="fas fa-sticky-note"></i>
                                        <span><?php echo htmlspecialchars($item['notes']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="added-date">
                                    <i class="fas fa-clock"></i>
                                    Added <?php echo date('M d, Y', strtotime($item['added_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="wishlist-card-actions">
                                <a href="../../index.php?page=productOpen&id=<?php echo $item['product_id']; ?>" class="btn-view" title="View Details">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                    <button type="submit" name="move_to_cart" class="btn-cart" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Remove this item from wishlist?');">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                    <button type="submit" name="remove_from_wishlist" class="btn-remove" title="Remove from Wishlist">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-wishlist">
                <div class="empty-icon">
                    <i class="fas fa-heart-broken"></i>
                </div>
                <h2>Your Wishlist is Empty</h2>
                <p>Start adding products you love to your wishlist!</p>
                <a href="../../index.php?page=product" class="btn-browse">
                    <i class="fas fa-box-open"></i> Browse Products
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
</body>
</html>

