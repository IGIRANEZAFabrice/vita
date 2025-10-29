<?php
// Fetch dashboard statistics
$user_id = $_SESSION['user_id'];

// Get total orders count (you'll need to create orders table)
$orders_count = 0;
// $orders_sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
// $stmt = $conn->prepare($orders_sql);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $orders_count = $stmt->get_result()->fetch_assoc()['total'];

// Get quotes count (you'll need to create quotes table)
$quotes_count = 0;
// $quotes_sql = "SELECT COUNT(*) as total FROM quotes WHERE user_id = ?";

// Cart is stored in localStorage (JavaScript), not in database
// Cart count will be updated by JavaScript
$cart_count = 0;

// Get wishlist count
$wishlist_count = 0;
$wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
$stmt = $conn->prepare($wishlist_sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $wishlist_count = $result->fetch_assoc()['total'];
    }
    $stmt->close();
}

// Get total products available
$products_sql = "SELECT COUNT(*) as total FROM products WHERE is_active = 1";
$products_result = $conn->query($products_sql);
$total_products = $products_result->fetch_assoc()['total'];

// Get total categories
$categories_sql = "SELECT COUNT(*) as total FROM categories";
$categories_result = $conn->query($categories_sql);
$total_categories = $categories_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - REDY-MED</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

 <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-tachometer-alt"></i> Client Dashboard</h1>
            <p>Welcome back, <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Client'; ?>! Browse our medical equipment catalog.</p>
        </div>

        <!-- Quick Stats Cards -->
        <div class="dashboard-cards">
            <div class="card stat-card">
                <div class="stat-icon orders-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3>My Orders</h3>
                    <div class="value"><?php echo $orders_count; ?></div>
                    <p>Total orders placed</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon quotes-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-content">
                    <h3>Quotes</h3>
                    <div class="value"><?php echo $quotes_count; ?></div>
                    <p>Pending quotes</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>Cart Items</h3>
                    <div class="value" data-cart-count><?php echo $cart_count; ?></div>
                    <p>Items in cart</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon wishlist-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <h3>Wishlist</h3>
                    <div class="value"><?php echo $wishlist_count; ?></div>
                    <p>Saved items</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="action-buttons">
                <a href="../../index.php?page=product" class="action-btn browse-btn">
                    <i class="fas fa-box-open"></i>
                    <span>Browse Products</span>
                    <small><?php echo $total_products; ?> products available</small>
                </a>
                <a href="index.php?page=cart" class="action-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span>View Cart</span>
                    <small><?php echo $cart_count; ?> items</small>
                </a>
                <a href="../../index.php?page=quote" class="action-btn quote-btn">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Request Quote</span>
                    <small>Get custom pricing</small>
                </a>
                <a href="index.php?page=orders" class="action-btn orders-btn">
                    <i class="fas fa-history"></i>
                    <span>Order History</span>
                    <small><?php echo $orders_count; ?> orders</small>
                </a>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="featured-section">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> Featured Products</h2>
                <a href="../../index.php?page=product" class="view-all-link">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php
            // Get featured products
            $featured_sql = "SELECT p.*, c.category_name
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id
                            WHERE p.is_active = 1
                            ORDER BY p.created_at DESC
                            LIMIT 4";
            $featured_result = $conn->query($featured_sql);

            if ($featured_result && $featured_result->num_rows > 0) {
                echo '<div class="products-grid">';

                while ($product = $featured_result->fetch_assoc()) {
                    // Get product image
                    $img_sql = "SELECT image_url FROM product_images WHERE product_id = {$product['product_id']} AND is_primary = 1 LIMIT 1";
                    $img_result = $conn->query($img_sql);
                    $image = $img_result->fetch_assoc();
                    $image_url = $image ? '../' . $image['image_url'] : '../images/placeholder.jpg';

                    echo '<div class="product-card">';
                    echo '<div class="product-image">';
                    echo '<img src="' . htmlspecialchars($image_url) . '" alt="' . htmlspecialchars($product['product_name']) . '">';
                    echo '<div class="product-overlay">';
                    echo '<a href="../../index.php?page=productOpen&id=' . $product['product_id'] . '" class="quick-view-btn"><i class="fas fa-eye"></i> Quick View</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<span class="product-category">' . htmlspecialchars($product['category_name']) . '</span>';
                    echo '<h4 class="product-name">' . htmlspecialchars($product['product_name']) . '</h4>';

                    if ($product['price'] > 0) {
                        echo '<p class="product-price">$' . number_format($product['price'], 2) . '</p>';
                    } else {
                        echo '<p class="product-price-contact">Contact for Price</p>';
                    }

                    echo '<div class="product-actions">';
                    echo '<a href="../../index.php?page=productOpen&id=' . $product['product_id'] . '" class="btn-view-details">View Details</a>';
                    echo '<button class="btn-add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }

                echo '</div>';
            } else {
                echo '<div class="no-products">';
                echo '<i class="fas fa-box-open"></i>';
                echo '<p>No products available at the moment.</p>';
                echo '<a href="../../index.php?page=contact" class="btn-contact">Contact Us</a>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Categories Overview -->
        <div class="categories-section">
            <div class="section-header">
                <h2><i class="fas fa-th-large"></i> Browse by Category</h2>
            </div>

            <?php
            // Get categories with product count
            $categories_sql = "SELECT c.*, COUNT(p.product_id) as product_count
                              FROM categories c
                              LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
                              GROUP BY c.category_id
                              ORDER BY product_count DESC
                              LIMIT 6";
            $categories_result = $conn->query($categories_sql);

            if ($categories_result && $categories_result->num_rows > 0) {
                echo '<div class="categories-grid">';

                while ($category = $categories_result->fetch_assoc()) {
                    echo '<a href="../../index.php?page=product&category=' . $category['category_id'] . '" class="category-card">';
                    echo '<div class="category-icon"><i class="fas fa-box"></i></div>';
                    echo '<h4>' . htmlspecialchars($category['category_name']) . '</h4>';
                    echo '<p>' . $category['product_count'] . ' products</p>';
                    echo '</a>';
                }

                echo '</div>';
            }
            ?>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
    <script src="../js/simple-cart.js"></script>
</body>
</html>