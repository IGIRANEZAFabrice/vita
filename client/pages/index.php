<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - REDY-MED</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <?php include '../include/sidebar.php'; ?>

 <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-tachometer-alt"></i> Client Dashboard</h1>
            <p>Welcome back, <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Client'; ?>! Browse our medical equipment catalog.</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-shopping-bag"></i> My Orders</h3>
                <div class="value">0</div>
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">Total orders placed</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-file-invoice"></i> Quotes</h3>
                <div class="value">0</div>
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">Pending quotes</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-shopping-cart"></i> Cart Items</h3>
                <div class="value">0</div>
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">Items in cart</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-heart"></i> Wishlist</h3>
                <div class="value">0</div>
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">Saved items</p>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-box"></i> Featured Products</h3>
            <?php
            // Get featured products
            $featured_sql = "SELECT p.*, c.category_name
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id
                            WHERE p.is_active = 1
                            ORDER BY p.created_at DESC
                            LIMIT 4";
            $featured_result = $conn->query($featured_sql);

            if ($featured_result->num_rows > 0) {
                echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">';

                while ($product = $featured_result->fetch_assoc()) {
                    // Get product image
                    $img_sql = "SELECT image_url FROM product_images WHERE product_id = {$product['product_id']} AND is_primary = 1 LIMIT 1";
                    $img_result = $conn->query($img_sql);
                    $image = $img_result->fetch_assoc();
                    $image_url = $image ? $image['image_url'] : 'images/placeholder.jpg';

                    echo '<div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1rem; background: #fff;">';
                    echo '<img src="../../' . htmlspecialchars($image_url) . '" alt="' . htmlspecialchars($product['product_name']) . '" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 1rem;">';
                    echo '<h4 style="margin: 0 0 0.5rem 0; font-size: 1rem;">' . htmlspecialchars($product['product_name']) . '</h4>';
                    echo '<p style="color: #666; font-size: 0.85rem; margin: 0 0 0.5rem 0;">' . htmlspecialchars($product['category_name']) . '</p>';

                    if ($product['price'] > 0) {
                        echo '<p style="color: #00e600; font-weight: 600; font-size: 1.1rem; margin: 0 0 1rem 0;">$' . number_format($product['price'], 2) . '</p>';
                    } else {
                        echo '<p style="color: #666; font-size: 0.9rem; margin: 0 0 1rem 0;">Contact for Price</p>';
                    }

                    echo '<a href="../../index.php?page=productOpen&id=' . $product['product_id'] . '" style="display: inline-block; background: #00e600; color: #000; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">View Details</a>';
                    echo '</div>';
                }

                echo '</div>';

                echo '<div style="text-align: center; margin-top: 2rem;">';
                echo '<a href="../../index.php?page=product" style="display: inline-block; background: #000; color: #fff; padding: 0.75rem 2rem; border-radius: 4px; text-decoration: none; font-weight: 600;">Browse All Products</a>';
                echo '</div>';
            } else {
                echo '<p style="color: #666; margin-top: 1rem; font-size: 0.9rem;">No products available at the moment.</p>';
            }
            ?>
        </div>
    </main>

    <?php include '../include/footer.php'; ?>
    <script src="../js/sidebar.js"></script>
</body>
</html>