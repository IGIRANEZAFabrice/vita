<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: index.php?page=product');
    exit;
}

// Fetch product details
$sql = "SELECT p.*, c.category_name, c.category_id
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = $product_id AND p.is_active = 1";

$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header('Location: index.php?page=product');
    exit;
}

$product = $result->fetch_assoc();

// Fetch product images
$images_sql = "SELECT * FROM product_images WHERE product_id = $product_id ORDER BY is_primary DESC, display_order ASC";
$images_result = $conn->query($images_sql);
$images = [];
if ($images_result && $images_result->num_rows > 0) {
    while($img = $images_result->fetch_assoc()) {
        $images[] = $img;
    }
}

// Fetch product specifications
$specs_sql = "SELECT sa.attribute_name, sa.unit, ps.spec_value
              FROM product_specifications ps
              JOIN specification_attributes sa ON ps.attribute_id = sa.attribute_id
              WHERE ps.product_id = $product_id
              ORDER BY sa.display_order ASC";
$specs_result = $conn->query($specs_sql);
$specifications = [];
if ($specs_result && $specs_result->num_rows > 0) {
    while($spec = $specs_result->fetch_assoc()) {
        $specifications[] = $spec;
    }
}

// Fetch compatible devices
$compat_sql = "SELECT m.manufacturer_name, dm.model_name, pc.notes
               FROM product_compatibility pc
               JOIN device_models dm ON pc.model_id = dm.model_id
               JOIN manufacturers m ON dm.manufacturer_id = m.manufacturer_id
               WHERE pc.product_id = $product_id
               ORDER BY m.manufacturer_name, dm.model_name";
$compat_result = $conn->query($compat_sql);
$compatibility = [];
if ($compat_result && $compat_result->num_rows > 0) {
    while($comp = $compat_result->fetch_assoc()) {
        $manufacturer = $comp['manufacturer_name'];
        if (!isset($compatibility[$manufacturer])) {
            $compatibility[$manufacturer] = [];
        }
        $compatibility[$manufacturer][] = $comp;
    }
}

// Fetch certifications
$cert_sql = "SELECT c.certification_name, c.description, pc.certificate_number
             FROM product_certifications pc
             JOIN certifications c ON pc.certification_id = c.certification_id
             WHERE pc.product_id = $product_id";
$cert_result = $conn->query($cert_sql);
$certifications = [];
if ($cert_result && $cert_result->num_rows > 0) {
    while($cert = $cert_result->fetch_assoc()) {
        $certifications[] = $cert;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - REDY-MED</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/home.css">

    <style>
        :root {
            --primary-color: #00e600;
            --primary-dark: #00b300;
            --gray-light: #f5f5f5;
            --gray-medium: #e0e0e0;
            --gray-dark: #666;
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem 1rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
            opacity: 0.9;
        }

        .breadcrumb a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .breadcrumb span {
            margin: 0 0.5rem;
        }

        .main-wrapper {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }

        /* Category Sidebar */
        .category-sidebar {
            background: white;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .category-sidebar h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .category-list {
            list-style: none;
        }

        .category-list li {
            margin-bottom: 0.5rem;
        }

        .category-list a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 0.8rem;
            color: #555;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .category-list a:hover {
            background: var(--gray-light);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .category-list a.active {
            background: var(--primary-color);
            color: white;
        }

        .category-list a i {
            font-size: 0.85rem;
            width: 16px;
        }

        /* Product Content */
        .product-content {
            background: white;
        }

        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        /* Image Gallery */
        .image-gallery {
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        .main-image {
            width: 100%;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 1rem;
            background: white;
        }

        .main-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .thumbnail-strip {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.5rem;
        }

        .thumbnail {
            border: 2px solid var(--gray-medium);
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: var(--primary-color);
        }

        .thumbnail.active {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 230, 0, 0.2);
        }

        .thumbnail img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Product Info */
        .product-info {
            padding: 1rem 0;
        }

        .product-header {
            margin-bottom: 1.5rem;
        }

        .product-sku {
            color: var(--gray-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .product-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .product-brand {
            color: var(--primary-color);
            font-weight: 600;
        }

        .product-price {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: 700;
            margin: 1rem 0;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1rem;
            background: #e8f5e9;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .stock-status i {
            color: var(--primary-color);
        }

        .purchase-section {
            margin: 2rem 0;
        }

        .quantity-selector {
            margin-bottom: 1rem;
        }

        .quantity-selector label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .quantity-controls button {
            width: 40px;
            height: 40px;
            border: 1px solid var(--gray-medium);
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-controls button:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .quantity-controls input {
            width: 80px;
            height: 40px;
            text-align: center;
            border: 1px solid var(--gray-medium);
            border-radius: 6px;
            font-size: 1rem;
        }

        .btn-primary {
            width: 100%;
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 230, 0, 0.3);
        }

        /* Features List */
        .features-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-light);
            border-radius: 6px;
        }

        .feature-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .feature-text h4 {
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .feature-text p {
            font-size: 0.85rem;
            color: var(--gray-dark);
        }

        /* Tabs */
        .tabs-container {
            margin-top: 3rem;
        }

        .tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid var(--gray-medium);
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1rem;
            color: var(--gray-dark);
            transition: all 0.3s ease;
        }

        .tab:hover {
            color: var(--primary-color);
        }

        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            display: none;
            padding: 2rem;
            background: var(--gray-light);
            border-radius: var(--border-radius);
        }

        .tab-content.active {
            display: block;
        }

        .tab-content h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .tab-content ul {
            padding-left: 2rem;
        }

        .tab-content li {
            margin-bottom: 0.5rem;
        }

        .spec-table {
            width: 100%;
            border-collapse: collapse;
        }

        .spec-table tr {
            border-bottom: 1px solid var(--gray-medium);
        }

        .spec-table td {
            padding: 1rem;
        }

        .spec-table td:first-child {
            font-weight: 600;
            width: 40%;
        }

        .compatibility-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .manufacturer-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-medium);
        }

        .manufacturer-card h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .manufacturer-card ul {
            list-style: none;
        }

        .manufacturer-card li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-light);
        }

        .manufacturer-card li:last-child {
            border-bottom: none;
        }

        .cert-badges {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .cert-badge {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            border: 1px solid var(--gray-medium);
        }

        .cert-badge i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .cert-badge strong {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-wrapper {
                grid-template-columns: 200px 1fr;
                gap: 1.5rem;
            }

            .category-sidebar {
                padding: 1rem;
            }

            .product-grid {
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .main-wrapper {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .category-sidebar {
                position: static;
                margin-bottom: 1rem;
            }

            .category-list {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .image-gallery {
                position: static;
            }

            .hero h1 {
                font-size: 1.5rem;
            }

            .product-title {
                font-size: 1.4rem;
            }

            .features-list {
                grid-template-columns: 1fr;
            }

            .tabs {
                overflow-x: auto;
            }

            .tab {
                padding: 0.8rem 1rem;
                font-size: 0.9rem;
                white-space: nowrap;
            }
        }

        @media (max-width: 480px) {
            .category-list {
                grid-template-columns: 1fr;
            }

            .thumbnail-strip {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <h1><i class="fas fa-heartbeat"></i> <?php echo htmlspecialchars($product['product_name']); ?></h1>
        <div class="breadcrumb">
            <a href="index.php?page=home"><i class="fas fa-home"></i> Home</a>
            <span>/</span>
            <a href="index.php?page=product">Products</a>
            <span>/</span>
            <?php if ($product['category_name']): ?>
            <a href="index.php?page=product&category=<?php echo $product['category_id']; ?>">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
            <span>/</span>
            <?php endif; ?>
            <span><?php echo htmlspecialchars($product['product_name']); ?></span>
        </div>
    </div>

    <!-- Main Wrapper with Sidebar -->
    <div class="main-wrapper">
        <!-- Category Sidebar -->
        <aside class="category-sidebar">
            <h3><i class="fas fa-list"></i> Categories</h3>
            <ul class="category-list">
                <li>
                    <a href="index.php?page=product">
                        <i class="fas fa-th"></i> All Products
                    </a>
                </li>
                <?php
                // Fetch all categories
                $cat_sql = "SELECT * FROM categories ORDER BY category_name ASC";
                $cat_result = $conn->query($cat_sql);

                if ($cat_result && $cat_result->num_rows > 0) {
                    while($cat = $cat_result->fetch_assoc()) {
                        $active_class = ($cat['category_id'] == $product['category_id']) ? 'active' : '';
                        echo '<li>';
                        echo '<a href="index.php?page=product&category=' . $cat['category_id'] . '" class="' . $active_class . '">';
                        echo '<i class="fas fa-chevron-right"></i> ';
                        echo htmlspecialchars($cat['category_name']);
                        echo '</a>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </aside>

        <!-- Product Content -->
        <div class="product-content">
            <div class="product-grid">
                <!-- Image Gallery -->
                <div class="image-gallery">
                    <div class="main-image">
                        <?php
                        $main_image = !empty($images) ? $images[0]['image_url'] : 'https://via.placeholder.com/500x500/ffffff/00e600?text=' . urlencode($product['product_name']);
                        ?>
                        <img src="<?php echo htmlspecialchars($main_image); ?>"
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             id="mainImage">
                    </div>
                    <?php if (count($images) > 1): ?>
                    <div class="thumbnail-strip">
                        <?php foreach($images as $index => $img): ?>
                        <div class="thumbnail <?php echo $index == 0 ? 'active' : ''; ?>"
                             onclick="changeImage(this, '<?php echo htmlspecialchars($img['image_url']); ?>')">
                            <img src="<?php echo htmlspecialchars($img['image_url']); ?>"
                                 alt="Thumbnail <?php echo $index + 1; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="product-header">
                        <div class="product-sku">
                            <i class="fas fa-barcode"></i> SKU: <?php echo htmlspecialchars($product['sku']); ?>
                        </div>
                        <h2 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                        <span class="product-brand"><i class="fas fa-certificate"></i> REDY-MED</span>
                    </div>

                    <div class="product-price">
                        <?php if ($product['price'] > 0): ?>
                            <i class="fas fa-dollar-sign"></i><?php echo number_format($product['price'], 2); ?>
                        <?php else: ?>
                            <span style="font-size: 1.2rem;">Contact for Price</span>
                        <?php endif; ?>
                    </div>

                    <div class="stock-status">
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>In Stock</strong> - <?php echo $product['stock_quantity']; ?> units available</span>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle" style="color: #ff9800;"></i>
                            <span><strong>Out of Stock</strong> - Contact us for availability</span>
                        <?php endif; ?>
                    </div>

                    <div class="purchase-section">
                        <div class="quantity-selector">
                            <label for="quantity"><i class="fas fa-shopping-cart"></i> Quantity:</label>
                            <div class="quantity-controls">
                                <button onclick="decrementQuantity()"><i class="fas fa-minus"></i></button>
                                <input type="number" id="quantity" value="1" min="1" max="<?php echo max(1, $product['stock_quantity']); ?>">
                                <button onclick="incrementQuantity()"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <button class="btn-primary" <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>

                    <!-- Quick Features -->
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Quality Assured</h4>
                                <p>Medical grade materials</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Universal Compatibility</h4>
                                <p>Works with multiple devices</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Warranty</h4>
                                <p>Quality guaranteed</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Fast Shipping</h4>
                                <p>Quick delivery worldwide</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="tabs-container">
                <div class="tabs">
                    <button class="tab active" onclick="openTab(event, 'description')">
                        <i class="fas fa-info-circle"></i> Description
                    </button>
                    <?php if (!empty($specifications)): ?>
                    <button class="tab" onclick="openTab(event, 'specifications')">
                        <i class="fas fa-list-ul"></i> Specifications
                    </button>
                    <?php endif; ?>
                    <?php if (!empty($compatibility)): ?>
                    <button class="tab" onclick="openTab(event, 'compatibility')">
                        <i class="fas fa-plug"></i> Compatibility
                    </button>
                    <?php endif; ?>
                    <?php if (!empty($certifications)): ?>
                    <button class="tab" onclick="openTab(event, 'certifications')">
                        <i class="fas fa-certificate"></i> Certifications
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Description Tab -->
                <div id="description" class="tab-content active">
                    <div class="description-content">
                        <h3>Product Overview</h3>
                        <?php if ($product['long_description']): ?>
                            <p><?php echo nl2br(htmlspecialchars($product['long_description'])); ?></p>
                        <?php else: ?>
                            <p><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
                        <?php endif; ?>

                        <?php if ($product['features']): ?>
                        <h3 style="margin-top: 1.5rem;">Key Features</h3>
                        <ul style="padding-left: 2rem; margin-top: 1rem;">
                            <?php
                            $features = explode("\n", $product['features']);
                            foreach($features as $feature) {
                                if (trim($feature)) {
                                    echo '<li style="margin-bottom: 0.5rem;">' . htmlspecialchars(trim($feature)) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <?php if (!empty($specifications)): ?>
                <div id="specifications" class="tab-content">
                    <table class="spec-table">
                        <?php foreach($specifications as $spec): ?>
                        <tr>
                            <td><i class="fas fa-cog"></i> <?php echo htmlspecialchars($spec['attribute_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($spec['spec_value']); ?>
                                <?php if ($spec['unit']): ?>
                                    <?php echo htmlspecialchars($spec['unit']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Compatibility Tab -->
                <?php if (!empty($compatibility)): ?>
                <div id="compatibility" class="tab-content">
                    <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-check-double"></i> Compatible Devices</h3>
                    <div class="compatibility-grid">
                        <?php foreach($compatibility as $manufacturer => $models): ?>
                        <div class="manufacturer-card">
                            <h4><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($manufacturer); ?></h4>
                            <ul>
                                <?php foreach($models as $model): ?>
                                <li>
                                    <?php echo htmlspecialchars($model['model_name']); ?>
                                    <?php if ($model['notes']): ?>
                                        <small style="color: var(--gray-dark); display: block; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($model['notes']); ?>
                                        </small>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Certifications Tab -->
                <?php if (!empty($certifications)): ?>
                <div id="certifications" class="tab-content">
                    <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-award"></i> Product Certifications</h3>
                    <div class="cert-badges">
                        <?php foreach($certifications as $cert): ?>
                        <div class="cert-badge">
                            <i class="fas fa-certificate"></i>
                            <strong><?php echo htmlspecialchars($cert['certification_name']); ?></strong>
                            <?php if ($cert['description']): ?>
                            <p style="text-align: center; color: var(--gray-dark); margin-top: 0.5rem;">
                                <?php echo htmlspecialchars($cert['description']); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($cert['certificate_number']): ?>
                            <small style="display: block; margin-top: 0.5rem; color: var(--gray-dark);">
                                Cert #: <?php echo htmlspecialchars($cert['certificate_number']); ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 2rem; padding: 1.5rem; background: white; border-radius: var(--border-radius); border: 1px solid var(--gray-medium);">
                        <p><i class="fas fa-info-circle" style="color: var(--primary-color);"></i> <strong>Quality Assurance:</strong> All REDY-MED products undergo rigorous testing and quality control procedures to ensure they meet the highest international standards for medical devices.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <?php include 'include/footer.php'; ?>

    <script>
        // Image gallery functionality
        function changeImage(thumbnail, imageUrl) {
            document.getElementById('mainImage').src = imageUrl;

            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });

            // Add active class to clicked thumbnail
            thumbnail.classList.add('active');
        }

        // Quantity controls
        function incrementQuantity() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max);
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
            }
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const min = parseInt(input.min);
            const current = parseInt(input.value);
            if (current > min) {
                input.value = current - 1;
            }
        }

        // Tab functionality
        function openTab(event, tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.currentTarget.classList.add('active');
        }
    </script>

</body>
</html>