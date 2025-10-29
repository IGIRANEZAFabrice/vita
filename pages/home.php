<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDY-MED - Medical Equipment & Supplies</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <?php include 'include/header.php'; ?>
    <div class="hero-slider">
        <?php
        // Fetch hero slides from database
        $slides_sql = "SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY slide_order ASC";
        $slides_result = $conn->query($slides_sql);

        $slide_index = 0;
        if ($slides_result && $slides_result->num_rows > 0) {
            while($slide = $slides_result->fetch_assoc()) {
                $active_class = ($slide_index === 0) ? 'active' : '';
                $small_title = htmlspecialchars($slide['small_title']);
                $main_title = htmlspecialchars($slide['main_title']);
                $image_path = htmlspecialchars($slide['image_path']);
        ?>
        <!-- Slide <?php echo $slide_index + 1; ?> -->
        <div class="slide <?php echo $active_class; ?>" data-slide="<?php echo $slide_index; ?>" style="background-image: url('<?php echo $image_path; ?>');">
            <div class="slide-content">
                <p class="small-title"><?php echo $small_title; ?></p>
                <h1 class="main-title"><?php echo $main_title; ?></h1>
                <div class="button-group">
                    <button class="btn btn-primary">Shop Now</button>
                    <button class="btn btn-secondary">Category</button>
                </div>
            </div>
        </div>
        <?php
                $slide_index++;
            }
        } else {
            // Fallback if no slides in database
        ?>
        <div class="slide active" data-slide="0">
            <div class="slide-content">
                <p class="small-title">Medical Equipment</p>
                <h1 class="main-title">Professional Stethoscopes For Healthcare</h1>
                <div class="button-group">
                    <button class="btn btn-primary">Shop Now</button>
                    <button class="btn btn-secondary">Category</button>
                </div>
            </div>
        </div>
        <?php
        }
        ?>

        <!-- Arrow Navigation -->
        <div class="arrow arrow-left" onclick="changeSlide(-1)">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="arrow arrow-right" onclick="changeSlide(1)">
            <i class="fas fa-chevron-right"></i>
        </div>

        <!-- Navigation Dots -->
        <div class="navigation-dots">
            <?php
            // Generate dots based on number of slides
            $slides_sql = "SELECT COUNT(*) as total FROM hero_slides WHERE is_active = 1";
            $slides_count_result = $conn->query($slides_sql);
            $slides_count = $slides_count_result->fetch_assoc()['total'];

            for ($i = 0; $i < $slides_count; $i++) {
                $active = ($i === 0) ? 'active' : '';
                echo '<div class="dot ' . $active . '" onclick="goToSlide(' . $i . ')"></div>';
            }
            ?>
        </div>
    </div>
     <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <div class="section-label">Main Products</div>
            <h2 class="section-title">Featured Products</h2>
            <a href="index.php?page=products" class="view-all-btn">
                View All Products
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="products-grid">
            <?php
            // Fetch top 6 products from database
            $sql = "SELECT p.*, c.category_name, pi.image_url
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                    WHERE p.is_active = 1
                    ORDER BY p.created_at DESC
                    LIMIT 6";

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
                echo '<p style="text-align: center; width: 100%; padding: 2rem; grid-column: 1 / -1;">No products available at the moment.</p>';
            }
            ?>
        </div>
    </section>

    <!-- Company Section -->
    <section class="company-section">
        <div class="company-container">
            <div class="company-content">
                <h2>SHENZHEN REDY-MED TECHNOLOGY CO.,LTD</h2>
                <p>Shenzhen Redy-Med is a professional Monitor Accessories manufacturer and main products have reusable spo2sensor, disposable spo2 sensor, spo2extension cable, ECG cable, EKGCable, NIBP Cuff, IBP Cable, temperature probe, Fetal Toco Probe, Medical Machine Battery and different types monitor's OEM and ODM projects services etc.</p>
                <a href="#" class="explore-btn">
                    Explore more
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="company-image">
                <img src="https://via.placeholder.com/600x400/1a1a1a/00e600?text=Company+Booth" alt="Company Booth">
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number">221+</div>
                <div class="stat-label">Founding Time</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">6+</div>
                <div class="stat-label">Employee Count</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">220m²</div>
                <div class="stat-label">Factory Covered</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">11+</div>
                <div class="stat-label">Countries Served</div>
            </div>
        </div>
    </section>
 <section class="product-showcase">
    <div class="showcase-container">
      <?php
      // Fetch the latest product from database
      $latest_sql = "SELECT p.*, c.category_name, pi.image_url
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.category_id
                     LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                     WHERE p.is_active = 1
                     ORDER BY p.created_at DESC
                     LIMIT 1";

      $latest_result = $conn->query($latest_sql);

      if ($latest_result && $latest_result->num_rows > 0) {
          $product = $latest_result->fetch_assoc();
          $product_id = $product['product_id'];
          $product_name = htmlspecialchars($product['product_name']);
          $price = $product['price'] ? '$' . number_format($product['price'], 2) : 'Contact for Price';
          $image_url = $product['image_url'] ?: 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400&h=300&fit=crop';
          $category = htmlspecialchars($product['category_name'] ?: 'Medical Equipment');
          $description = htmlspecialchars($product['description'] ?: $product['short_description'] ?: 'Premium medical equipment from REDY-MED');
          $brand = htmlspecialchars($product['brand'] ?: 'REDY-MED');
          $sku = htmlspecialchars($product['sku'] ?: 'N/A');
          $stock = $product['stock_quantity'] ?: 0;
      ?>
      <div class="showcase-image">
        <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>">
        <div class="image-hotspot hotspot-1">
          <i class="fas fa-plus"></i>
        </div>
        <div class="image-hotspot hotspot-2">
          <i class="fas fa-plus"></i>
        </div>
        <div class="image-hotspot hotspot-3">
          <i class="fas fa-plus"></i>
        </div>
      </div>

      <div class="showcase-content">
        <div class="product-price"><?php echo $price; ?></div>

        <h1 class="product-title"><?php echo $product_name; ?></h1>

        <div class="product-features">
          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-tag"></i>
            </div>
            <div class="feature-content">
              <h3>Category</h3>
              <p><?php echo $category; ?></p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-building"></i>
            </div>
            <div class="feature-content">
              <h3>Brand</h3>
              <p><?php echo $brand; ?></p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-barcode"></i>
            </div>
            <div class="feature-content">
              <h3>SKU</h3>
              <p><?php echo $sku; ?></p>
            </div>
          </div>
        </div>

        <p class="product-description">
          <?php echo $description; ?>
        </p>

        <div class="product-actions">
          <a href="index.php?page=productOpen&id=<?php echo $product_id; ?>" class="btn btn-primary">View Details</a>
          <a href="index.php?page=products" class="btn btn-secondary">Browse All</a>
        </div>

        <div class="countdown-timer">
          <div class="countdown-item">
            <span class="countdown-number"><?php echo $stock; ?></span>
            <span class="countdown-label">In Stock</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number"><?php echo date('d', strtotime($product['created_at'])); ?></span>
            <span class="countdown-label">Added</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number"><?php echo date('m', strtotime($product['created_at'])); ?></span>
            <span class="countdown-label">Month</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number"><?php echo date('Y', strtotime($product['created_at'])); ?></span>
            <span class="countdown-label">Year</span>
          </div>
        </div>
      </div>
      <?php
      } else {
          echo '<p style="text-align: center; width: 100%; padding: 2rem;">No products available to showcase.</p>';
      }
      ?>
    </div>
  </section>
    <?php include 'include/footer.php'; ?>
    <script src="js/main.js"></script>
    <script src="js/home.js"></script>
</body>
</html>