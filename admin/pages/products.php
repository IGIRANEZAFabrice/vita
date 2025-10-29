<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - REDY-MED Admin</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00e600, #00b300);
            color: #000;
            box-shadow: 0 4px 15px rgba(0, 230, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 230, 0, 0.4);
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-edit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .product-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .status-inactive {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .filter-section {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-box"></i> Manage Products</h1>
            <p>Create, edit, and manage your product catalog</p>
        </div>

        <?php
        // Handle form submissions
        $success_message = '';
        $error_message = '';

        // Delete Product
        if (isset($_GET['delete'])) {
            $product_id = intval($_GET['delete']);
            
            // Delete related records first
            $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
            $conn->query("DELETE FROM product_specifications WHERE product_id = $product_id");
            $conn->query("DELETE FROM product_compatibility WHERE product_id = $product_id");
            $conn->query("DELETE FROM product_certifications WHERE product_id = $product_id");
            
            // Delete product
            $delete_sql = "DELETE FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $product_id);
            
            if ($stmt->execute()) {
                $success_message = "Product deleted successfully!";
            } else {
                $error_message = "Error deleting product: " . $conn->error;
            }
            $stmt->close();
        }

        // Display messages
        if ($success_message) {
            echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($success_message) . '</div>';
        }
        if ($error_message) {
            echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($error_message) . '</div>';
        }
        ?>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <input type="hidden" name="page" value="products">
                <div class="filter-row">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="filter_category">Category</label>
                        <select name="category" id="filter_category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php
                            $cat_sql = "SELECT * FROM categories ORDER BY category_name ASC";
                            $cat_result = $conn->query($cat_sql);
                            $selected_category = isset($_GET['category']) ? $_GET['category'] : '';
                            while ($cat = $cat_result->fetch_assoc()) {
                                $selected = ($selected_category == $cat['category_id']) ? 'selected' : '';
                                echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . htmlspecialchars($cat['category_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="filter_status">Status</label>
                        <select name="status" id="filter_status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="filter_search">Search</label>
                        <input type="text" name="search" id="filter_search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary btn-small">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="index.php?page=products" class="btn btn-small" style="background: #6c757d; color: #fff;">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="action-buttons">
            <a href="index.php?page=add-product" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>

        <div class="card">
            <h3><i class="fas fa-list"></i> All Products</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Build query with filters
                    $where_clauses = [];
                    $params = [];
                    $types = '';

                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $where_clauses[] = "p.category_id = ?";
                        $params[] = intval($_GET['category']);
                        $types .= 'i';
                    }

                    if (isset($_GET['status']) && $_GET['status'] !== '') {
                        $where_clauses[] = "p.is_active = ?";
                        $params[] = intval($_GET['status']);
                        $types .= 'i';
                    }

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $where_clauses[] = "(p.product_name LIKE ? OR p.description LIKE ?)";
                        $search_term = '%' . $_GET['search'] . '%';
                        $params[] = $search_term;
                        $params[] = $search_term;
                        $types .= 'ss';
                    }

                    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

                    $sql = "SELECT p.*, c.category_name, 
                            (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id
                            $where_sql
                            ORDER BY p.created_at DESC";

                    if (!empty($params)) {
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $result = $conn->query($sql);
                    }

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $image_url = $row['primary_image'] ? $row['primary_image'] : 'images/placeholder.jpg';
                            echo '<tr>';
                            echo '<td>' . $row['product_id'] . '</td>';
                            echo '<td><img src="../' . htmlspecialchars($image_url) . '" alt="Product" class="product-image"></td>';
                            echo '<td><strong>' . htmlspecialchars($row['product_name']) . '</strong></td>';
                            echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
                            echo '<td>$' . number_format($row['price'], 2) . '</td>';
                            echo '<td>' . $row['stock_quantity'] . '</td>';
                            echo '<td><span class="product-status status-' . ($row['is_active'] ? 'active' : 'inactive') . '">' . ($row['is_active'] ? 'Active' : 'Inactive') . '</span></td>';
                            echo '<td style="display: flex; gap: 0.5rem;">';
                            echo '<a href="index.php?page=edit-product&id=' . $row['product_id'] . '" class="btn btn-edit btn-small"><i class="fas fa-edit"></i></a>';
                            echo '<button class="btn btn-delete btn-small" onclick="deleteProduct(' . $row['product_id'] . ', \'' . htmlspecialchars(addslashes($row['product_name'])) . '\')"><i class="fas fa-trash"></i></button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: #666;">No products found. Add your first product!</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
    <script>
        function deleteProduct(id, name) {
            if (confirm('Are you sure you want to delete "' + name + '"?\n\nThis will also delete all associated images, specifications, and compatibility data.')) {
                window.location.href = 'index.php?page=products&delete=' + id;
            }
        }
    </script>
</body>
</html>

