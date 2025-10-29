<?php
// Database connection is already established in the parent index.php
// No need to start session or check auth here as it's done in index.php

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hero_slides'])) {
    // Process each of the 3 slides
    for ($i = 1; $i <= 3; $i++) {
        $slide_id = isset($_POST['slide_id_' . $i]) ? intval($_POST['slide_id_' . $i]) : null;
        $small_title = trim($_POST['slide_' . $i . '_small_title'] ?? '');
        $main_title = trim($_POST['slide_' . $i . '_main_title'] ?? '');
        $image_path = trim($_POST['slide_' . $i . '_image_path'] ?? '');

        // Handle image upload if file is provided
        if (!empty($_FILES['slide_' . $i . '_image']['name'])) {
            $upload_dir = __DIR__ . '/../../images/hero/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = $_FILES['slide_' . $i . '_image']['name'];
            $file_tmp = $_FILES['slide_' . $i . '_image']['tmp_name'];
            $file_size = $_FILES['slide_' . $i . '_image']['size'];
            $file_error = $_FILES['slide_' . $i . '_image']['error'];

            // Validate file
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext) && $file_size <= 5242880 && $file_error === 0) {
                $new_filename = 'slide-' . $i . '-' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_path = 'images/hero/' . $new_filename;
                }
            }
        }

        if ($slide_id) {
            // Update existing slide
            $update_sql = "UPDATE hero_slides SET
                small_title = ?,
                main_title = ?,
                image_path = ?
                WHERE slide_id = ?";

            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $small_title, $main_title, $image_path, $slide_id);

            if (!$stmt->execute()) {
                $error_message = "Error updating slide " . $i . ": " . $conn->error;
                break;
            }
            $stmt->close();
        }
    }

    if (!$error_message) {
        $success_message = "✅ All hero slides updated successfully!";
    }
}

// Fetch all 3 hero slides
$slides_sql = "SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY slide_order ASC LIMIT 3";
$slides_result = $conn->query($slides_sql);
$slides = [];
while ($row = $slides_result->fetch_assoc()) {
    $slides[] = $row;
}

// Debug: Show what we got from database
// echo "<!-- DEBUG: Found " . count($slides) . " slides in database -->";
// foreach ($slides as $idx => $s) {
//     echo "<!-- DEBUG Slide " . ($idx+1) . ": image_path = '" . htmlspecialchars($s['image_path']) . "' -->";
// }

// Ensure we have exactly 3 slides
while (count($slides) < 3) {
    $slides[] = [
        'slide_id' => null,
        'slide_order' => count($slides) + 1,
        'small_title' => '',
        'main_title' => '',
        'image_path' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Slides Management - Admin</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-images"></i> Hero Slides Management</h1>
            <p>Edit the 3 hero slides displayed on your homepage</p>
        </div>

<style>
    .hero-slides-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }

    .slides-editor {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .slides-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .slide-tab {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        cursor: pointer;
        font-weight: 600;
        color: #999;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        position: relative;
        bottom: -2px;
    }

    .slide-tab.active {
        color: #00e600;
        border-bottom-color: #00e600;
    }

    .slide-tab:hover {
        color: #333;
    }

    .slide-content {
        display: none;
    }

    .slide-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
        font-size: 0.95rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #00e600;
        box-shadow: 0 0 0 3px rgba(0, 230, 0, 0.1);
    }

    .file-upload-area {
        border: 2px dashed #00e600;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9f9f9;
    }

    .file-upload-area:hover {
        background: #f0f0f0;
        border-color: #00cc00;
    }

    .file-upload-area i {
        font-size: 2.5rem;
        color: #00e600;
        margin-bottom: 1rem;
        display: block;
    }

    .file-upload-area p {
        margin: 0.5rem 0;
        color: #666;
        font-weight: 600;
    }

    .file-upload-area .help-text {
        font-size: 0.85rem;
        color: #999;
    }

    .file-input {
        display: none;
    }

    .preview-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 500px;
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        max-height: 600px;
    }

    .preview-placeholder {
        text-align: center;
        color: #999;
    }

    .preview-placeholder i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 1rem;
        display: block;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
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

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
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
        background: #00e600;
        color: white;
    }

    .btn-primary:hover {
        background: #00cc00;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 230, 0, 0.3);
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    @media (max-width: 1024px) {
        .hero-slides-wrapper {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?php echo $success_message; ?></span>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo $error_message; ?></span>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_hero_slides" value="1">

    <div class="hero-slides-wrapper">
        <!-- Left: Editor -->
        <div class="slides-editor">
            <div class="slides-tabs">
                <?php foreach ($slides as $index => $slide): ?>
                    <button type="button" class="slide-tab <?php echo $index === 0 ? 'active' : ''; ?>" onclick="switchSlide(<?php echo $index; ?>)">
                        Slide <?php echo $index + 1; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($slides as $index => $slide): ?>
                <div class="slide-content <?php echo $index === 0 ? 'active' : ''; ?>" id="slide-<?php echo $index; ?>">
                    <input type="hidden" name="slide_id_<?php echo $index + 1; ?>" value="<?php echo $slide['slide_id'] ?? ''; ?>">

                    <div class="form-group">
                        <label for="slide_<?php echo $index; ?>_small_title">Small Title</label>
                        <input
                            type="text"
                            id="slide_<?php echo $index; ?>_small_title"
                            name="slide_<?php echo $index + 1; ?>_small_title"
                            value="<?php echo htmlspecialchars($slide['small_title']); ?>"
                            placeholder="e.g., Medical Equipment"
                            onchange="updatePreview(<?php echo $index; ?>)"
                        >
                    </div>

                    <div class="form-group">
                        <label for="slide_<?php echo $index; ?>_main_title">Main Title</label>
                        <input
                            type="text"
                            id="slide_<?php echo $index; ?>_main_title"
                            name="slide_<?php echo $index + 1; ?>_main_title"
                            value="<?php echo htmlspecialchars($slide['main_title']); ?>"
                            placeholder="e.g., Professional Stethoscopes For Healthcare"
                            onchange="updatePreview(<?php echo $index; ?>)"
                        >
                    </div>

                    <div class="form-group">
                        <label for="slide_<?php echo $index; ?>_image">Upload Image</label>
                        <div class="file-upload-area" onclick="document.getElementById('slide_<?php echo $index; ?>_image').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload image</p>
                            <p class="help-text">JPG, PNG, GIF, WEBP (Max 5MB)</p>
                        </div>
                        <input
                            type="file"
                            id="slide_<?php echo $index; ?>_image"
                            name="slide_<?php echo $index + 1; ?>_image"
                            class="file-input"
                            accept="image/*"
                            onchange="handleImageUpload(<?php echo $index; ?>, this)"
                        >
                        <input
                            type="hidden"
                            id="slide_<?php echo $index; ?>_image_path"
                            name="slide_<?php echo $index + 1; ?>_image_path"
                            value="<?php echo htmlspecialchars($slide['image_path']); ?>"
                        >
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Right: Preview -->
        <div class="preview-section">
            <div id="preview-container">
                <div class="preview-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Select a slide to preview</p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="index.php?page=products" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save All Changes
        </button>
    </div>
</form>

<script>
    let currentSlide = 0;
    const slides = <?php echo json_encode($slides); ?>;

    function switchSlide(index) {
        // Hide all slides
        document.querySelectorAll('.slide-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.slide-tab').forEach(el => el.classList.remove('active'));

        // Show selected slide
        document.getElementById('slide-' + index).classList.add('active');
        document.querySelectorAll('.slide-tab')[index].classList.add('active');

        currentSlide = index;
        updatePreview(index);
    }

    function updatePreview(index) {
        const smallTitle = document.getElementById('slide_' + index + '_small_title').value;
        const mainTitle = document.getElementById('slide_' + index + '_main_title').value;
        const imagePath = document.getElementById('slide_' + index + '_image_path').value;

        let previewHTML = '';

        if (imagePath) {
            const fullPath = '../' + imagePath;
            console.log('Loading image from:', fullPath);
            console.log('Original path from DB:', imagePath);

            previewHTML = `
                <div style="width: 100%; text-align: center;">
                    <img src="${fullPath}" alt="Slide ${index + 1}" class="preview-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display:none; color: red; padding: 20px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to load image</p>
                        <p style="font-size: 0.85rem; color: #666;">Path: ${fullPath}</p>
                        <p style="font-size: 0.85rem; color: #666;">DB Path: ${imagePath}</p>
                    </div>
                </div>
            `;
        } else {
            previewHTML = `
                <div class="preview-placeholder">
                    <i class="fas fa-image"></i>
                    <p>No image selected</p>
                </div>
            `;
        }

        document.getElementById('preview-container').innerHTML = previewHTML;
    }

    function handleImageUpload(index, input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('preview-container').innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="preview-image">
                `;
            };

            reader.readAsDataURL(file);
        }
    }

    // Initialize preview on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview(0);
    });
</script>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
</body>
</html>

