<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - REDY-MED</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/home.css"> 
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="products-section" style="padding: 4rem 2rem;">
        <div class="section-header">
            <div class="section-label">Our Products</div>
            <h2 class="section-title">Medical Equipment & Accessories</h2>
        </div>
        
        <div class="products-grid">
            <?php
            // Fetch products from database
            $sql = "SELECT p.*, c.category_name, pi.image_url 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                    WHERE p.is_active = 1 
                    ORDER BY p.created_at DESC 
                    LIMIT 12";
            
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $product_id = $row['product_id'];
                    $product_name = htmlspecialchars($row['product_name']);
                    $price = $row['price'] ? '$' . number_format($row['price'], 2) : 'Contact for Price';
                    $image_url = $row['image_url'] ?: 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400&h=300&fit=crop';
                    $category = htmlspecialchars($row['category_name']);
                    ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>" class="product-image">
                            <div class="product-badge"><?php echo $category; ?></div>
                        </div>
                        <div class="product-content">
                            <div class="product-name"><?php echo $product_name; ?></div>
                            <div class="product-price"><?php echo $price; ?></div>
                            <a href="index.php?page=productOpen&id=<?php echo $product_id; ?>" class="view-details-btn">View Details</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="text-align: center; width: 100%; padding: 2rem;">No products available at the moment.</p>';
            }
            ?>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>

