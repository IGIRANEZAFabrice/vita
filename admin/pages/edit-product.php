<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: index.php?page=products");
    exit;
}

// Fetch product data
$product_sql = "SELECT * FROM products WHERE product_id = ?";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    header("Location: index.php?page=products");
    exit;
}

$product = $product_result->fetch_assoc();
$product_stmt->close();

// Fetch product images
$images_sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC";
$images_stmt = $conn->prepare($images_sql);
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();
$product_images = [];
while ($img = $images_result->fetch_assoc()) {
    $product_images[] = $img;
}
$images_stmt->close();

// Fetch product specifications
$specs_sql = "SELECT ps.*, sa.attribute_name, sa.unit FROM product_specifications ps JOIN specification_attributes sa ON ps.attribute_id = sa.attribute_id WHERE ps.product_id = ?";
$specs_stmt = $conn->prepare($specs_sql);
$specs_stmt->bind_param("i", $product_id);
$specs_stmt->execute();
$specs_result = $specs_stmt->get_result();
$product_specs = [];
while ($spec = $specs_result->fetch_assoc()) {
    $product_specs[] = $spec;
}
$specs_stmt->close();

// Fetch product compatibility
$compat_sql = "SELECT model_id FROM product_compatibility WHERE product_id = ?";
$compat_stmt = $conn->prepare($compat_sql);
$compat_stmt->bind_param("i", $product_id);
$compat_stmt->execute();
$compat_result = $compat_stmt->get_result();
$product_compatibility = [];
while ($compat = $compat_result->fetch_assoc()) {
    $product_compatibility[] = $compat['model_id'];
}
$compat_stmt->close();

// Fetch product certifications
$cert_sql = "SELECT certification_id FROM product_certifications WHERE product_id = ?";
$cert_stmt = $conn->prepare($cert_sql);
$cert_stmt->bind_param("i", $product_id);
$cert_stmt->execute();
$cert_result = $cert_stmt->get_result();
$product_certifications = [];
while ($cert = $cert_result->fetch_assoc()) {
    $product_certifications[] = $cert['certification_id'];
}
$cert_stmt->close();

// Process form submission
$success_message = '';
$error_message = '';

if (isset($_POST['update_product'])) {
    // Get form data
    $product_name = trim($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $manufacturer_id = !empty($_POST['manufacturer_id']) ? intval($_POST['manufacturer_id']) : null;
    $brand = !empty($_POST['brand']) ? trim($_POST['brand']) : 'REDY-MED';
    $short_description = trim($_POST['short_description']);
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : 0;
    $stock_quantity = !empty($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
    $sku = !empty($_POST['sku']) ? trim($_POST['sku']) : null;
    $spu = !empty($_POST['spu']) ? trim($_POST['spu']) : null;
    $model_number = !empty($_POST['model_number']) ? trim($_POST['model_number']) : null;
    $warranty_period = !empty($_POST['warranty_period']) ? trim($_POST['warranty_period']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Get specifications data
    $specifications = isset($_POST['specifications']) ? $_POST['specifications'] : [];

    // Get compatibility data
    $compatibility_models = isset($_POST['compatibility_models']) ? $_POST['compatibility_models'] : [];

    // Get certifications data
    $certifications = isset($_POST['certifications']) ? $_POST['certifications'] : [];

    // Validate required fields
    if (empty($product_name) || empty($category_id)) {
        $error_message = "Product Name and Category are required!";
    } else {
        // Check if SKU already exists (excluding current product)
        if (!empty($sku)) {
            $check_sql = "SELECT product_id FROM products WHERE sku = ? AND product_id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $sku, $product_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error_message = "SKU already exists! Please use a unique SKU.";
            } else {
                $check_stmt->close();

                // Update product
                $update_sql = "UPDATE products SET
                    product_name = ?,
                    category_id = ?,
                    manufacturer_id = ?,
                    brand = ?,
                    short_description = ?,
                    price = ?,
                    stock_quantity = ?,
                    sku = ?,
                    spu = ?,
                    model_number = ?,
                    warranty_period = ?,
                    is_active = ?,
                    is_featured = ?
                WHERE product_id = ?";

                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("siisdisissii",
                    $product_name,
                    $category_id,
                    $manufacturer_id,
                    $brand,
                    $short_description,
                    $price,
                    $stock_quantity,
                    $sku,
                    $spu,
                    $model_number,
                    $warranty_period,
                    $is_active,
                    $is_featured,
                    $product_id
                );

                if ($update_stmt->execute()) {
                    // Handle main image upload if provided
                    if (!empty($_FILES['main_image']['name'])) {
                        $upload_dir = __DIR__ . '/../../images/products/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $tmp_name = $_FILES['main_image']['tmp_name'];
                        $file_name = $_FILES['main_image']['name'];
                        $file_size = $_FILES['main_image']['size'];

                        // Validate file
                        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        if (in_array($file_ext, $allowed_ext) && $file_size <= 5242880) {
                            $new_filename = 'product_' . $product_id . '_main_' . uniqid() . '_' . time() . '.' . $file_ext;
                            $upload_path = $upload_dir . $new_filename;

                            if (move_uploaded_file($tmp_name, $upload_path)) {
                                // Delete old main image
                                $old_img_sql = "SELECT image_url FROM product_images WHERE product_id = ? AND is_primary = 1";
                                $old_img_stmt = $conn->prepare($old_img_sql);
                                $old_img_stmt->bind_param("i", $product_id);
                                $old_img_stmt->execute();
                                $old_img_result = $old_img_stmt->get_result();
                                if ($old_img = $old_img_result->fetch_assoc()) {
                                    $old_path = __DIR__ . '/../../' . $old_img['image_url'];
                                    if (file_exists($old_path)) {
                                        unlink($old_path);
                                    }
                                    // Delete from database
                                    $del_sql = "DELETE FROM product_images WHERE product_id = ? AND is_primary = 1";
                                    $del_stmt = $conn->prepare($del_sql);
                                    $del_stmt->bind_param("i", $product_id);
                                    $del_stmt->execute();
                                    $del_stmt->close();
                                }
                                $old_img_stmt->close();

                                // Insert new image
                                $image_url = 'images/products/' . $new_filename;
                                $insert_img = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 1)";
                                $img_stmt = $conn->prepare($insert_img);
                                $img_stmt->bind_param("is", $product_id, $image_url);
                                $img_stmt->execute();
                                $img_stmt->close();
                            }
                        }
                    }

                    // Handle gallery images upload if provided
                    if (!empty($_FILES['gallery_images']['name'][0])) {
                        $upload_dir = __DIR__ . '/../../images/products/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                        $gallery_count = count($_FILES['gallery_images']['name']);

                        for ($i = 0; $i < $gallery_count; $i++) {
                            if ($_FILES['gallery_images']['error'][$i] === 0) {
                                $file_name = $_FILES['gallery_images']['name'][$i];
                                $file_size = $_FILES['gallery_images']['size'][$i];
                                $tmp_name = $_FILES['gallery_images']['tmp_name'][$i];
                                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                                if (in_array($file_ext, $allowed_ext) && $file_size <= 5242880) {
                                    $new_filename = 'product_' . $product_id . '_gallery_' . uniqid() . '_' . time() . '.' . $file_ext;
                                    $upload_path = $upload_dir . $new_filename;

                                    if (move_uploaded_file($tmp_name, $upload_path)) {
                                        $image_url = 'images/products/' . $new_filename;
                                        $insert_img = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 0)";
                                        $img_stmt = $conn->prepare($insert_img);
                                        $img_stmt->bind_param("is", $product_id, $image_url);
                                        $img_stmt->execute();
                                        $img_stmt->close();
                                    }
                                }
                            }
                        }
                    }

                    // Update specifications
                    // Delete old specifications
                    $del_spec_sql = "DELETE FROM product_specifications WHERE product_id = ?";
                    $del_spec_stmt = $conn->prepare($del_spec_sql);
                    $del_spec_stmt->bind_param("i", $product_id);
                    $del_spec_stmt->execute();
                    $del_spec_stmt->close();

                    // Insert new specifications
                    if (!empty($specifications)) {
                        foreach ($specifications as $attribute_id => $spec_value) {
                            if (!empty($spec_value)) {
                                $spec_sql = "INSERT INTO product_specifications (product_id, attribute_id, spec_value) VALUES (?, ?, ?)";
                                $spec_stmt = $conn->prepare($spec_sql);
                                $spec_stmt->bind_param("iis", $product_id, $attribute_id, $spec_value);
                                $spec_stmt->execute();
                                $spec_stmt->close();
                            }
                        }
                    }

                    // Update compatibility
                    // Delete old compatibility
                    $del_compat_sql = "DELETE FROM product_compatibility WHERE product_id = ?";
                    $del_compat_stmt = $conn->prepare($del_compat_sql);
                    $del_compat_stmt->bind_param("i", $product_id);
                    $del_compat_stmt->execute();
                    $del_compat_stmt->close();

                    // Insert new compatibility
                    if (!empty($compatibility_models)) {
                        foreach ($compatibility_models as $model_id) {
                            if (!empty($model_id)) {
                                $compat_sql = "INSERT INTO product_compatibility (product_id, model_id) VALUES (?, ?)";
                                $compat_stmt = $conn->prepare($compat_sql);
                                $compat_stmt->bind_param("ii", $product_id, $model_id);
                                $compat_stmt->execute();
                                $compat_stmt->close();
                            }
                        }
                    }

                    // Update certifications
                    // Delete old certifications
                    $del_cert_sql = "DELETE FROM product_certifications WHERE product_id = ?";
                    $del_cert_stmt = $conn->prepare($del_cert_sql);
                    $del_cert_stmt->bind_param("i", $product_id);
                    $del_cert_stmt->execute();
                    $del_cert_stmt->close();

                    // Insert new certifications
                    if (!empty($certifications)) {
                        foreach ($certifications as $cert_id) {
                            if (!empty($cert_id)) {
                                $cert_sql = "INSERT INTO product_certifications (product_id, certification_id) VALUES (?, ?)";
                                $cert_stmt = $conn->prepare($cert_sql);
                                $cert_stmt->bind_param("ii", $product_id, $cert_id);
                                $cert_stmt->execute();
                                $cert_stmt->close();
                            }
                        }
                    }

                    // Redirect to products page
                    header("Location: index.php?page=products&success=1");
                    exit;
                } else {
                    $error_message = "Error updating product: " . $conn->error;
                }
                $update_stmt->close();
            }
        }
    }
}

// Fetch specifications for the category
$specifications_list = [];
$spec_sql = "SELECT * FROM specification_attributes WHERE category_id = ? OR category_id IS NULL ORDER BY display_order ASC";
$spec_stmt = $conn->prepare($spec_sql);
$spec_stmt->bind_param("i", $product['category_id']);
$spec_stmt->execute();
$spec_result = $spec_stmt->get_result();
while ($row = $spec_result->fetch_assoc()) {
    $specifications_list[] = $row;
}
$spec_stmt->close();

// Get all device models for compatibility
$model_sql = "SELECT dm.model_id, dm.model_name, m.manufacturer_name FROM device_models dm JOIN manufacturers m ON dm.manufacturer_id = m.manufacturer_id ORDER BY m.manufacturer_name, dm.model_name";
$model_result = $conn->query($model_sql);
$compatibility_models_list = [];
while ($row = $model_result->fetch_assoc()) {
    $compatibility_models_list[] = $row;
}

// Get all certifications
$cert_sql = "SELECT * FROM certifications ORDER BY certification_name";
$cert_result = $conn->query($cert_sql);
$certifications_list = [];
while ($row = $cert_result->fetch_assoc()) {
    $certifications_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: inherit;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: #f5f5f5;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            margin: 0 0 0.5rem 0;
            color: #333;
            font-size: 2rem;
        }

        .page-header p {
            margin: 0;
            color: #666;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .image-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .main-image-container {
            display: flex;
            flex-direction: column;
        }

        .main-image-container h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.1rem;
        }

        .image-upload-area {
            cursor: pointer;
            border: 2px dashed #00e600;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f9fff9;
            transition: all 0.3s ease;
            position: relative;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-upload-area:hover {
            border-color: #00cc00;
            background: #f0fff0;
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }

        .upload-placeholder i {
            font-size: 3rem;
            color: #00e600;
        }

        .upload-placeholder p {
            margin: 0.5rem 0 0 0;
            font-weight: 600;
            color: #333;
        }

        .upload-placeholder small {
            color: #999;
            font-size: 0.85rem;
        }

        .image-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            object-fit: contain;
        }

        .gallery-container {
            display: flex;
            flex-direction: column;
        }

        .gallery-container h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.1rem;
        }

        .gallery-upload-area {
            cursor: pointer;
            border: 2px dashed #00e600;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            background: #f9fff9;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .gallery-upload-area:hover {
            border-color: #00cc00;
            background: #f0fff0;
        }

        .gallery-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f0f0;
            aspect-ratio: 1;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item-remove {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: background 0.3s ease;
        }

        .gallery-item-remove:hover {
            background: rgba(255, 0, 0, 1);
        }

        .info-section {
            margin-top: 2rem;
        }

        .info-section h3 {
            margin: 0 0 1.5rem 0;
            color: #333;
            font-size: 1.2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .checkbox-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }

        .checkbox-section h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.1rem;
        }

        .tabs-container {
            display: flex;
            gap: 0;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
            overflow-x: auto;
        }

        .tab-button {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: #999;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            font-size: 1rem;
            white-space: nowrap;
        }

        .tab-button:hover {
            color: #666;
        }

        .tab-button.active {
            color: #00e600;
            border-bottom-color: #00e600;
        }

        .tab-button i {
            margin-right: 0.5rem;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-grid.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00e600;
            box-shadow: 0 0 0 3px rgba(0, 230, 0, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
            word-break: break-word;
        }

        .spec-item {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border-left: 3px solid #00e600;
        }

        .spec-item label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .spec-item input,
        .spec-item textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
            justify-content: flex-end;
            flex-wrap: wrap;
            width: 100%;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #00e600;
            color: #000;
        }

        .btn-primary:hover {
            background: #00cc00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 230, 0, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .image-preview {
            width: 150px;
            height: 150px;
            border-radius: 4px;
            object-fit: cover;
            margin-top: 0.5rem;
        }

        @media (max-width: 1024px) {
            .content-area {
                padding: 1.5rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .checkbox-group {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .image-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .content-area {
                padding: 1rem;
            }

            .form-container {
                padding: 1rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .tabs-container {
                flex-direction: column;
            }

            .tab-button {
                border-bottom: none;
                border-left: 3px solid transparent;
                padding: 0.75rem 1rem;
            }

            .tab-button.active {
                border-bottom: none;
                border-left-color: #00e600;
            }

            .checkbox-group {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .content-area {
                padding: 0.75rem;
            }

            .form-container {
                padding: 0.75rem;
                border-radius: 4px;
            }

            .tab-button {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }

            .tab-button i {
                margin-right: 0.25rem;
            }

            .checkbox-group {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .page-header h1 {
                font-size: 1.25rem;
            }

            .image-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .image-upload-area {
                min-height: 200px;
                padding: 1rem;
            }

            .upload-placeholder i {
                font-size: 2rem;
            }

            .gallery-preview {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/../include/sidebar.php'; ?>

        <div class="main-content">
            <div class="content-area">
                <div class="page-header">
                    <h1><i class="fas fa-edit"></i> Edit Product</h1>
                    <p>Update product information and details</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                    <!-- Tabs Navigation -->
                    <div class="tabs-container">
                        <button type="button" class="tab-button active" data-tab="overview">
                            <i class="fas fa-info-circle"></i> Overview
                        </button>
                        <button type="button" class="tab-button" data-tab="specifications">
                            <i class="fas fa-list"></i> Specifications
                        </button>
                        <button type="button" class="tab-button" data-tab="compatibility">
                            <i class="fas fa-plug"></i> Compatibility
                        </button>
                        <button type="button" class="tab-button" data-tab="certifications">
                            <i class="fas fa-certificate"></i> Certifications
                        </button>
                    </div>

                    <!-- Overview Tab -->
                    <div id="overview-tab" class="tab-content active">
                        <!-- Image Section at Top -->
                        <div class="image-section">
                            <div class="main-image-container">
                                <div class="image-upload-area" id="mainImageArea">
                                    <input type="file" id="main_image" name="main_image" accept="image/*" style="display: none;">
                                    <div class="upload-placeholder" id="mainImagePlaceholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload main product image</p>
                                        <small>Recommended: 800x800px, Max 5MB</small>
                                    </div>
                                    <img id="mainImagePreview" class="image-preview" style="display: none;">
                                </div>
                            </div>

                            <div class="gallery-container">
                                <h3><i class="fas fa-images"></i> Product Gallery</h3>
                                <div class="gallery-upload-area" id="galleryUploadArea">
                                    <input type="file" id="gallery_images" name="gallery_images[]" multiple accept="image/*" style="display: none;">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-plus-circle"></i>
                                        <p>Click to add gallery images</p>
                                        <small>Add up to 10 images</small>
                                    </div>
                                </div>
                                <div class="gallery-preview" id="galleryPreview"></div>
                            </div>
                        </div>

                        <!-- Product Info Section -->
                        <div class="info-section">
                            <h3><i class="fas fa-info-circle"></i> Product Information</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="product_name">Product Name *</label>
                                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" required onchange="loadSpecifications()">
                                    <option value="">Select Category</option>
                                    <?php
                                    $cat_sql = "SELECT * FROM categories ORDER BY category_name";
                                    $cat_result = $conn->query($cat_sql);
                                    while ($cat = $cat_result->fetch_assoc()) {
                                        $selected = ($cat['category_id'] == $product['category_id']) ? 'selected' : '';
                                        echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . htmlspecialchars($cat['category_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="manufacturer_id">Manufacturer</label>
                                <select id="manufacturer_id" name="manufacturer_id">
                                    <option value="">Select Manufacturer</option>
                                    <?php
                                    $mfg_sql = "SELECT * FROM manufacturers ORDER BY manufacturer_name";
                                    $mfg_result = $conn->query($mfg_sql);
                                    while ($mfg = $mfg_result->fetch_assoc()) {
                                        $selected = ($mfg['manufacturer_id'] == $product['manufacturer_id']) ? 'selected' : '';
                                        echo '<option value="' . $mfg['manufacturer_id'] . '" ' . $selected . '>' . htmlspecialchars($mfg['manufacturer_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="price">Price ($) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="stock_quantity">Stock Quantity *</label>
                                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="sku">SKU</label>
                                <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="spu">SPU</label>
                                <input type="text" id="spu" name="spu" value="<?php echo htmlspecialchars($product['spu'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="model_number">Model Number</label>
                                <input type="text" id="model_number" name="model_number" value="<?php echo htmlspecialchars($product['model_number'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="warranty_period">Warranty Period</label>
                                <input type="text" id="warranty_period" name="warranty_period" value="<?php echo htmlspecialchars($product['warranty_period'] ?? ''); ?>">
                            </div>
                        </div>

                            <div class="form-grid full">
                                <div class="form-group">
                                    <label for="short_description">Short Description</label>
                                    <textarea id="short_description" name="short_description" placeholder="Brief product description" rows="4"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="checkbox-section">
                                <h3><i class="fas fa-cog"></i> Product Status</h3>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo ($product['is_active'] ? 'checked' : ''); ?>>
                                        <label for="is_active">Active Product</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo ($product['is_featured'] ? 'checked' : ''); ?>>
                                        <label for="is_featured">Featured Product</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div id="specifications-tab" class="tab-content">
                        <h2><i class="fas fa-list"></i> Product Specifications</h2>
                        <p style="color: #666; margin-bottom: 1.5rem;">Update technical specifications for this product</p>

                        <div id="specifications-container">
                            <?php
                            foreach ($specifications_list as $spec) {
                                $spec_value = '';
                                foreach ($product_specs as $ps) {
                                    if ($ps['attribute_id'] == $spec['attribute_id']) {
                                        $spec_value = $ps['spec_value'];
                                        break;
                                    }
                                }
                                echo '<div class="spec-item">';
                                echo '<label for="spec_' . $spec['attribute_id'] . '">' . htmlspecialchars($spec['attribute_name']);
                                if ($spec['unit']) {
                                    echo ' (' . htmlspecialchars($spec['unit']) . ')';
                                }
                                echo '</label>';
                                echo '<input type="text" id="spec_' . $spec['attribute_id'] . '" name="specifications[' . $spec['attribute_id'] . ']" value="' . htmlspecialchars($spec_value) . '">';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Compatibility Tab -->
                    <div id="compatibility-tab" class="tab-content">
                        <h2><i class="fas fa-plug"></i> Device Compatibility</h2>
                        <p style="color: #666; margin-bottom: 1.5rem;">Select which devices this product is compatible with</p>

                        <div class="checkbox-group">
                            <?php
                            $current_manufacturer = '';
                            foreach ($compatibility_models_list as $model) {
                                if ($current_manufacturer !== $model['manufacturer_name']) {
                                    if ($current_manufacturer !== '') {
                                        echo '</div>';
                                    }
                                    $current_manufacturer = $model['manufacturer_name'];
                                    echo '<div style="grid-column: 1 / -1; margin-top: 1rem; font-weight: 600; color: #00e600;">' . htmlspecialchars($current_manufacturer) . '</div>';
                                }
                                $checked = in_array($model['model_id'], $product_compatibility) ? 'checked' : '';
                                echo '<div class="checkbox-item">';
                                echo '<input type="checkbox" id="model_' . $model['model_id'] . '" name="compatibility_models[]" value="' . $model['model_id'] . '" ' . $checked . '>';
                                echo '<label for="model_' . $model['model_id'] . '">' . htmlspecialchars($model['model_name']) . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Certifications Tab -->
                    <div id="certifications-tab" class="tab-content">
                        <h2><i class="fas fa-certificate"></i> Product Certifications</h2>
                        <p style="color: #666; margin-bottom: 1.5rem;">Select applicable certifications for this product</p>

                        <div class="checkbox-group">
                            <?php
                            foreach ($certifications_list as $cert) {
                                $checked = in_array($cert['certification_id'], $product_certifications) ? 'checked' : '';
                                echo '<div class="checkbox-item">';
                                echo '<input type="checkbox" id="cert_' . $cert['certification_id'] . '" name="certifications[]" value="' . $cert['certification_id'] . '" ' . $checked . '>';
                                echo '<label for="cert_' . $cert['certification_id'] . '">' . htmlspecialchars($cert['certification_name']) . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="button-group">
                        <a href="index.php?page=products" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" name="update_product" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                    </div>
                </form>
                </div>
            </div>

            <?php include __DIR__ . '/../include/footer.php'; ?>
        </div>
    </div>

    <script>
        // Main Image Upload Handler
        const mainImageArea = document.getElementById('mainImageArea');
        const mainImageInput = document.getElementById('main_image');
        const mainImagePreview = document.getElementById('mainImagePreview');
        const mainImagePlaceholder = document.getElementById('mainImagePlaceholder');

        mainImageArea.addEventListener('click', () => mainImageInput.click());
        mainImageArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            mainImageArea.style.borderColor = '#00cc00';
            mainImageArea.style.background = '#f0fff0';
        });
        mainImageArea.addEventListener('dragleave', () => {
            mainImageArea.style.borderColor = '#00e600';
            mainImageArea.style.background = '#f9fff9';
        });
        mainImageArea.addEventListener('drop', (e) => {
            e.preventDefault();
            mainImageArea.style.borderColor = '#00e600';
            mainImageArea.style.background = '#f9fff9';
            if (e.dataTransfer.files.length > 0) {
                mainImageInput.files = e.dataTransfer.files;
                handleMainImageChange();
            }
        });

        mainImageInput.addEventListener('change', handleMainImageChange);

        function handleMainImageChange() {
            const file = mainImageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    mainImagePreview.src = e.target.result;
                    mainImagePreview.style.display = 'block';
                    mainImagePlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        // Gallery Images Upload Handler
        const galleryUploadArea = document.getElementById('galleryUploadArea');
        const galleryInput = document.getElementById('gallery_images');
        const galleryPreview = document.getElementById('galleryPreview');
        let galleryFilesMap = new Map(); // Map to store files with their indices

        // Load existing gallery images
        function loadExistingGalleryImages() {
            <?php
            $gallery_images = [];
            foreach ($product_images as $img) {
                if (!$img['is_primary']) {
                    $gallery_images[] = $img;
                }
            }
            if (!empty($gallery_images)) {
                echo 'const existingImages = ' . json_encode($gallery_images) . ';';
                echo 'existingImages.forEach((img, index) => {';
                echo '  const galleryItem = document.createElement("div");';
                echo '  galleryItem.className = "gallery-item";';
                echo '  galleryItem.setAttribute("data-index", "existing_" + index);';
                echo '  galleryItem.innerHTML = `';
                echo '    <img src="../../${img.image_url}" alt="Gallery image">';
                echo '    <button type="button" class="gallery-item-remove" onclick="removeExistingGalleryItem(\'existing_\' + ${index})">';
                echo '      <i class="fas fa-trash"></i>';
                echo '    </button>`;';
                echo '  galleryPreview.appendChild(galleryItem);';
                echo '});';
            }
            ?>
        }

        loadExistingGalleryImages();

        galleryUploadArea.addEventListener('click', () => galleryInput.click());
        galleryUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            galleryUploadArea.style.borderColor = '#00cc00';
            galleryUploadArea.style.background = '#f0fff0';
        });
        galleryUploadArea.addEventListener('dragleave', () => {
            galleryUploadArea.style.borderColor = '#00e600';
            galleryUploadArea.style.background = '#f9fff9';
        });
        galleryUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            galleryUploadArea.style.borderColor = '#00e600';
            galleryUploadArea.style.background = '#f9fff9';
            if (e.dataTransfer.files.length > 0) {
                // Create a DataTransfer object to hold files
                const dataTransfer = new DataTransfer();

                // Add existing files
                for (let file of galleryInput.files) {
                    dataTransfer.items.add(file);
                }

                // Add new files
                for (let file of e.dataTransfer.files) {
                    dataTransfer.items.add(file);
                }

                galleryInput.files = dataTransfer.files;
                handleGalleryChange();
            }
        });

        galleryInput.addEventListener('change', handleGalleryChange);

        function handleGalleryChange() {
            const newFiles = Array.from(galleryInput.files);
            const existingFiles = Array.from(galleryFilesMap.values());
            const allFiles = [...existingFiles, ...newFiles];

            if (allFiles.length > 10) {
                alert('Maximum 10 images allowed');
                // Reset to previous state
                const dataTransfer = new DataTransfer();
                for (let file of existingFiles) {
                    dataTransfer.items.add(file);
                }
                galleryInput.files = dataTransfer.files;
                return;
            }

            // Add new files to the map and create previews
            newFiles.forEach((file) => {
                const index = galleryFilesMap.size; // Use current map size as index
                const reader = new FileReader();
                reader.onload = (e) => {
                    galleryFilesMap.set(index, file);

                    const galleryItem = document.createElement('div');
                    galleryItem.className = 'gallery-item';
                    galleryItem.setAttribute('data-index', index);
                    galleryItem.innerHTML = `
                        <img src="${e.target.result}" alt="Gallery image">
                        <button type="button" class="gallery-item-remove" onclick="removeGalleryItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    galleryPreview.appendChild(galleryItem);
                };
                reader.readAsDataURL(file);
            });

            // Update the file input with all files
            const dataTransfer = new DataTransfer();
            for (let file of allFiles) {
                dataTransfer.items.add(file);
            }
            galleryInput.files = dataTransfer.files;
        }

        function removeGalleryItem(index) {
            // Remove from map
            galleryFilesMap.delete(index);

            // Remove from DOM
            const item = document.querySelector(`[data-index="${index}"]`);
            if (item) {
                item.remove();
            }

            // Update the file input with remaining files
            const dataTransfer = new DataTransfer();
            for (let [idx, file] of galleryFilesMap) {
                dataTransfer.items.add(file);
            }
            galleryInput.files = dataTransfer.files;
        }

        function removeExistingGalleryItem(index) {
            // Remove from DOM
            const item = document.querySelector(`[data-index="${index}"]`);
            if (item) {
                item.remove();
            }
        }

        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tabName = this.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                document.getElementById(tabName + '-tab').classList.add('active');
            });
        });

        // Load specifications when category changes
        function loadSpecifications() {
            const categoryId = document.getElementById('category_id').value;
            const container = document.getElementById('specifications-container');

            if (!categoryId) {
                container.innerHTML = '<p style="color: #999; text-align: center; padding: 2rem;"><i class="fas fa-info-circle"></i> Select a category first to see available specifications</p>';
                return;
            }

            // Fetch specifications for this category
            fetch('pages/get-specifications.php?category_id=' + categoryId)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        container.innerHTML = '<p style="color: #999; text-align: center; padding: 2rem;"><i class="fas fa-info-circle"></i> No specifications available for this category</p>';
                    } else {
                        let html = '';
                        data.forEach(spec => {
                            html += '<div class="spec-item">';
                            html += '<label for="spec_' + spec.attribute_id + '">' + spec.attribute_name;
                            if (spec.unit) {
                                html += ' (' + spec.unit + ')';
                            }
                            html += '</label>';
                            html += '<input type="text" id="spec_' + spec.attribute_id + '" name="specifications[' + spec.attribute_id + ']" placeholder="Enter ' + spec.attribute_name.toLowerCase() + '">';
                            html += '</div>';
                        });
                        container.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error loading specifications:', error);
                    container.innerHTML = '<p style="color: #999; text-align: center; padding: 2rem;"><i class="fas fa-exclamation-circle"></i> Error loading specifications</p>';
                });
        }
    </script>
    <script src="js/sidebar.js"></script>
</body>
</html>

