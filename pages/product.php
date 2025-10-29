<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - REDY-MED Medical Equipment</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/product.css"> 
</head>
<body>
    <?php include 'include/header.php'; ?>
    <!-- Page Hero -->
<section class="page-hero">
    <div>
        <h1>View Our Products</h1>
        <p>Your Trusted Medical Supply Partner</p>
    </div>
</section>
    <div class="products-page">
        <div class="products-container">
            <div class="page-header">
                <h1>Our Medical Products</h1>
                <p>Professional medical equipment and accessories for healthcare providers</p>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="product">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="category">Category</label>
                            <select name="category" id="category" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php
                                // Fetch categories from database
                                $cat_sql = "SELECT * FROM categories ORDER BY category_name ASC";
                                $cat_result = $conn->query($cat_sql);

                                $selected_category = isset($_GET['category']) ? $_GET['category'] : '';

                                if ($cat_result && $cat_result->num_rows > 0) {
                                    while($cat = $cat_result->fetch_assoc()) {
                                        $selected = ($selected_category == $cat['category_id']) ? 'selected' : '';
                                        echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' .
                                             htmlspecialchars($cat['category_name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="search">Search Products</label>
                            <input type="text" name="search" id="search"
                                   placeholder="Search by name or SKU..."
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>

                        <div class="filter-group search-btn-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary" style="width: 100%; margin: 0; padding: 0.75rem 1rem;">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php
            // Build SQL query based on filters
            $where_conditions = ["p.is_active = 1"];

            // Category filter
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category_id = intval($_GET['category']);
                $where_conditions[] = "p.category_id = $category_id";
            }

            // Search filter
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $conn->real_escape_string($_GET['search']);
                $where_conditions[] = "(p.product_name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.short_description LIKE '%$search%')";
            }

            $where_clause = implode(' AND ', $where_conditions);

            // Fetch products from database
            $sql = "SELECT p.*, c.category_name, pi.image_url
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                    WHERE $where_clause
                    ORDER BY p.created_at DESC";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $product_count = $result->num_rows;
                echo '<div class="product-count"><strong>' . $product_count . '</strong> product' . ($product_count != 1 ? 's' : '') . ' found</div>';
                echo '<div class="products-grid">';

                while($row = $result->fetch_assoc()) {
                    $product_id = $row['product_id'];
                    $product_name = htmlspecialchars($row['product_name']);
                    $sku = htmlspecialchars($row['sku']);
                    $price = $row['price'] ? '$' . number_format($row['price'], 2) : 'Contact for Price';
                    $image_url = $row['image_url'] ?: 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400&h=300&fit=crop';
                    $category = htmlspecialchars($row['category_name']);
                    $short_desc = $row['short_description'] ? htmlspecialchars(substr($row['short_description'], 0, 100)) : '';
                    ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>" class="product-image">
                            <div class="product-badge"><?php echo $category; ?></div>
                        </div>
                        <div class="product-content">
                            <div class="product-name"><?php echo $product_name; ?></div>
                            <?php if ($sku): ?>
                                <div style="color: #999; font-size: 0.85rem; margin: 0.25rem 0;">SKU: <?php echo $sku; ?></div>
                            <?php endif; ?>
                            <?php if ($short_desc): ?>
                                <div style="color: #666; font-size: 0.9rem; margin: 0.5rem 0;"><?php echo $short_desc; ?>...</div>
                            <?php endif; ?>
                            <div class="product-price"><?php echo $price; ?></div>
                            <div class="product-actions">
                                <button onclick="addToCart(<?php echo $product_id; ?>, 1, {
                                    product_name: '<?php echo addslashes($product_name); ?>',
                                    price: <?php echo $row['price'] ?: 0; ?>,
                                    image_url: '<?php echo addslashes($image_url); ?>',
                                    sku: '<?php echo addslashes($row['sku']); ?>'
                                })" class="add-to-cart-btn">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <a href="index.php?page=productOpen&id=<?php echo $product_id; ?>" class="view-details-btn">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                echo '</div>';
            } else {
                ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your filters or search terms.</p>
                    <?php if (isset($_GET['search']) || isset($_GET['category'])): ?>
                        <a href="index.php?page=product" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">
                            <i class="fas fa-redo"></i> Clear Filters
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
