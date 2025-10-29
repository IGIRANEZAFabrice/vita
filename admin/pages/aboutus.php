<?php
// Handle form submissions
$success_message = '';
$error_message = '';

// Function to handle image upload
function uploadAboutImage($file) {
    $upload_dir = '../../images/about/';

    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No file uploaded
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Upload error: " . $file['error']);
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.");
    }

    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        throw new Exception("File size exceeds 5MB limit.");
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'about_' . time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Return relative path from root
        return 'images/about/' . $new_filename;
    } else {
        throw new Exception("Failed to move uploaded file.");
    }
}

// Add New Section
if (isset($_POST['add_section'])) {
    $section_name = trim($_POST['section_name']);
    $section_title = trim($_POST['section_title']);
    $section_content = trim($_POST['section_content']);
    $section_order = intval($_POST['section_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle image upload
    $section_image = '';
    try {
        if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $section_image = uploadAboutImage($_FILES['section_image']);
        }
    } catch (Exception $e) {
        $error_message = "Image upload error: " . $e->getMessage();
    }

    // Only proceed if no image upload error
    if (empty($error_message)) {
        $insert_sql = "INSERT INTO about_us_content (section_name, section_title, section_content, section_image, section_order, is_active)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssii", $section_name, $section_title, $section_content, $section_image, $section_order, $is_active);

        if ($stmt->execute()) {
            $success_message = "Section added successfully!";
        } else {
            $error_message = "Error adding section: " . $conn->error;
        }
        $stmt->close();
    }
}

// Update Section
if (isset($_POST['update_section'])) {
    $content_id = intval($_POST['content_id']);
    $section_name = trim($_POST['section_name']);
    $section_title = trim($_POST['section_title']);
    $section_content = trim($_POST['section_content']);
    $section_order = intval($_POST['section_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Get existing image path
    $existing_image_sql = "SELECT section_image FROM about_us_content WHERE content_id = ?";
    $stmt = $conn->prepare($existing_image_sql);
    $stmt->bind_param("i", $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_data = $result->fetch_assoc();
    $section_image = $existing_data['section_image'];
    $stmt->close();

    // Handle image upload (if new image is uploaded)
    try {
        if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $new_image = uploadAboutImage($_FILES['section_image']);

            // Delete old image if exists and new upload successful
            if ($new_image && $section_image && file_exists('../../' . $section_image)) {
                unlink('../../' . $section_image);
            }

            $section_image = $new_image;
        }
    } catch (Exception $e) {
        $error_message = "Image upload error: " . $e->getMessage();
    }

    // Only proceed if no image upload error
    if (empty($error_message)) {
        $update_sql = "UPDATE about_us_content
                       SET section_name = ?, section_title = ?, section_content = ?, section_image = ?, section_order = ?, is_active = ?
                       WHERE content_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssiii", $section_name, $section_title, $section_content, $section_image, $section_order, $is_active, $content_id);

        if ($stmt->execute()) {
            $success_message = "Section updated successfully!";
        } else {
            $error_message = "Error updating section: " . $conn->error;
        }
        $stmt->close();
    }
}

// Delete Section
if (isset($_GET['delete'])) {
    $content_id = intval($_GET['delete']);

    // Get image path before deleting
    $image_sql = "SELECT section_image FROM about_us_content WHERE content_id = ?";
    $stmt = $conn->prepare($image_sql);
    $stmt->bind_param("i", $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image_data = $result->fetch_assoc();
    $stmt->close();

    $delete_sql = "DELETE FROM about_us_content WHERE content_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $content_id);

    if ($stmt->execute()) {
        // Delete image file if exists
        if ($image_data && $image_data['section_image'] && file_exists('../../' . $image_data['section_image'])) {
            unlink('../../' . $image_data['section_image']);
        }
        $success_message = "Section deleted successfully!";
    } else {
        $error_message = "Error deleting section: " . $conn->error;
    }
    $stmt->close();
}

// Toggle Active Status
if (isset($_GET['toggle'])) {
    $content_id = intval($_GET['toggle']);

    $toggle_sql = "UPDATE about_us_content SET is_active = NOT is_active WHERE content_id = ?";
    $stmt = $conn->prepare($toggle_sql);
    $stmt->bind_param("i", $content_id);

    if ($stmt->execute()) {
        $success_message = "Section status updated successfully!";
    } else {
        $error_message = "Error updating status: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Us - REDY-MED Admin</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: space-between;
            align-items: center;
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

        .btn-secondary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-edit:hover {
            transform: scale(1.05);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-delete:hover {
            transform: scale(1.05);
        }

        .btn-toggle {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #000;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-toggle:hover {
            transform: scale(1.05);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #dc3545;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input[type="file"]:hover {
            border-color: #00e600;
            background: #f0fff0;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00e600;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            font-family: inherit;
        }

        .form-group .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #e0e0e0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table thead {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .content-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #666;
            font-size: 0.9rem;
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
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .section-order-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .action-cell {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }

        .preview-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .preview-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'include/sidebar.php'; ?>


    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-info-circle"></i> Manage About Us Content</h1>
            <p>Edit and manage all sections of the About Us page</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="../../index.php?page=about" class="preview-link" target="_blank">
                <i class="fas fa-eye"></i> Preview About Us Page
            </a>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Section
            </button>
        </div>

        <div class="card">
            <h3><i class="fas fa-list"></i> All About Us Sections</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Section Name</th>
                        <th>Title</th>
                        <th>Content Preview</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM about_us_content ORDER BY section_order ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $content_id = $row['content_id'];
                            $section_name = htmlspecialchars($row['section_name']);
                            $section_title = htmlspecialchars($row['section_title']);
                            $section_content = htmlspecialchars($row['section_content']);
                            $section_image = htmlspecialchars($row['section_image']);
                            $section_order = $row['section_order'];
                            $is_active = $row['is_active'];
                            $updated_at = date('M d, Y', strtotime($row['updated_at']));

                            $content_preview = strlen($section_content) > 50
                                ? substr($section_content, 0, 50) . '...'
                                : $section_content;

                            $status_class = $is_active ? 'status-active' : 'status-inactive';
                            $status_text = $is_active ? 'Active' : 'Inactive';

                            echo "<tr>";
                            echo "<td><span class='section-order-badge'>$section_order</span></td>";
                            echo "<td><strong>$section_name</strong></td>";
                            echo "<td>$section_title</td>";
                            echo "<td><div class='content-preview'>$content_preview</div></td>";
                            echo "<td>" . ($section_image ? '<i class="fas fa-image" style="color: #00e600;"></i>' : '<i class="fas fa-times" style="color: #ccc;"></i>') . "</td>";
                            echo "<td><span class='status-badge $status_class'>$status_text</span></td>";
                            echo "<td>$updated_at</td>";
                            echo "<td>";
                            echo "<div class='action-cell'>";
                            echo "<button class='btn btn-edit' onclick='openEditModal($content_id)'><i class='fas fa-edit'></i> Edit</button>";
                            echo "<a href='?page=aboutus&toggle=$content_id' class='btn btn-toggle' onclick='return confirm(\"Toggle section status?\")'><i class='fas fa-toggle-on'></i></a>";
                            echo "<a href='?page=aboutus&delete=$content_id' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this section?\")'><i class='fas fa-trash'></i></a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center; padding: 2rem; color: #666;'>";
                        echo "<i class='fas fa-inbox' style='font-size: 3rem; margin-bottom: 1rem; display: block;'></i>";
                        echo "No sections found. Click 'Add New Section' to get started.";
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>


    <!-- Add Section Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Add New Section</h2>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="?page=aboutus" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="add_section_name">Section Name <span style="color: red;">*</span></label>
                    <input type="text" id="add_section_name" name="section_name" required
                           placeholder="e.g., hero, mission, vision">
                    <div class="help-text">Unique identifier for the section (lowercase, no spaces)</div>
                </div>

                <div class="form-group">
                    <label for="add_section_title">Section Title <span style="color: red;">*</span></label>
                    <input type="text" id="add_section_title" name="section_title" required
                           placeholder="e.g., Our Mission">
                    <div class="help-text">Display title shown on the page</div>
                </div>

                <div class="form-group">
                    <label for="add_section_content">Section Content <span style="color: red;">*</span></label>
                    <textarea id="add_section_content" name="section_content" required
                              placeholder="Enter the section content here...&#10;&#10;Use line breaks to separate paragraphs.&#10;For lists, use format: Title: Description"></textarea>
                    <div class="help-text">Main content of the section. Use line breaks for paragraphs.</div>
                </div>

                <div class="form-group">
                    <label for="add_section_image">Section Image</label>
                    <input type="file" id="add_section_image" name="section_image" accept="image/*" onchange="previewAddImage(event)">
                    <div class="help-text">Upload an image (JPG, PNG, GIF, WEBP - Max 5MB)</div>
                    <div id="add_image_preview" style="margin-top: 10px; display: none;">
                        <img id="add_preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="add_section_order">Display Order <span style="color: red;">*</span></label>
                    <input type="number" id="add_section_order" name="section_order" required
                           value="1" min="0">
                    <div class="help-text">Order in which the section appears (lower numbers first)</div>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="add_is_active" name="is_active" checked>
                        <label for="add_is_active" style="margin: 0;">Active (Show on page)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" name="add_section" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Section
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Section Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Section</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="?page=aboutus" enctype="multipart/form-data">
                <input type="hidden" id="edit_content_id" name="content_id">
                <input type="hidden" id="edit_existing_image" name="existing_image">

                <div class="form-group">
                    <label for="edit_section_name">Section Name <span style="color: red;">*</span></label>
                    <input type="text" id="edit_section_name" name="section_name" required>
                    <div class="help-text">Unique identifier for the section (lowercase, no spaces)</div>
                </div>

                <div class="form-group">
                    <label for="edit_section_title">Section Title <span style="color: red;">*</span></label>
                    <input type="text" id="edit_section_title" name="section_title" required>
                    <div class="help-text">Display title shown on the page</div>
                </div>

                <div class="form-group">
                    <label for="edit_section_content">Section Content <span style="color: red;">*</span></label>
                    <textarea id="edit_section_content" name="section_content" required></textarea>
                    <div class="help-text">Main content of the section. Use line breaks for paragraphs.</div>
                </div>

                <div class="form-group">
                    <label for="edit_section_image">Section Image</label>
                    <div id="edit_current_image" style="margin-bottom: 10px; display: none;">
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Current Image:</p>
                        <img id="edit_current_img" src="" alt="Current Image" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                    </div>
                    <input type="file" id="edit_section_image" name="section_image" accept="image/*" onchange="previewEditImage(event)">
                    <div class="help-text">Upload a new image to replace the current one (JPG, PNG, GIF, WEBP - Max 5MB)</div>
                    <div id="edit_image_preview" style="margin-top: 10px; display: none;">
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">New Image Preview:</p>
                        <img id="edit_preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #00e600;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_section_order">Display Order <span style="color: red;">*</span></label>
                    <input type="number" id="edit_section_order" name="section_order" required min="0">
                    <div class="help-text">Order in which the section appears (lower numbers first)</div>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="edit_is_active" name="is_active">
                        <label for="edit_is_active" style="margin: 0;">Active (Show on page)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" name="update_section" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Section
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
    <script>
        // Section data for editing
        const sections = <?php
            $sections_data = [];
            $sql = "SELECT * FROM about_us_content";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $sections_data[$row['content_id']] = $row;
            }
            echo json_encode($sections_data);
        ?>;

        // Image Preview Functions
        function previewAddImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('add_image_preview');
            const previewImg = document.getElementById('add_preview_img');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        function previewEditImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('edit_image_preview');
            const previewImg = document.getElementById('edit_preview_img');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        // Add Modal Functions
        function openAddModal() {
            // Reset form and preview
            document.getElementById('add_image_preview').style.display = 'none';
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        // Edit Modal Functions
        function openEditModal(contentId) {
            const section = sections[contentId];
            if (!section) return;

            document.getElementById('edit_content_id').value = section.content_id;
            document.getElementById('edit_section_name').value = section.section_name;
            document.getElementById('edit_section_title').value = section.section_title;
            document.getElementById('edit_section_content').value = section.section_content;
            document.getElementById('edit_existing_image').value = section.section_image || '';
            document.getElementById('edit_section_order').value = section.section_order;
            document.getElementById('edit_is_active').checked = section.is_active == 1;

            // Show current image if exists
            const currentImageDiv = document.getElementById('edit_current_image');
            const currentImg = document.getElementById('edit_current_img');
            if (section.section_image) {
                currentImg.src = '../../' + section.section_image;
                currentImageDiv.style.display = 'block';
            } else {
                currentImageDiv.style.display = 'none';
            }

            // Hide new image preview
            document.getElementById('edit_image_preview').style.display = 'none';

            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');

            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>
</body>
</html>