<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - REDY-MED Admin</title>
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
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-header h2 {
            color: #000;
            font-size: 1.5rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #dc3545;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00e600;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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

        .category-status {
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
    </style>
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-layer-group"></i> Manage Categories</h1>
            <p>Create, edit, and manage product categories</p>
        </div>

        <?php
        // Handle form submissions
        $success_message = '';
        $error_message = '';

        // Add Category
        if (isset($_POST['add_category'])) {
            $category_name = trim($_POST['category_name']);
            $description = trim($_POST['description']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (!empty($category_name)) {
                $insert_sql = "INSERT INTO categories (category_name, description, is_active) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("ssi", $category_name, $description, $is_active);
                
                if ($stmt->execute()) {
                    $success_message = "Category added successfully!";
                } else {
                    $error_message = "Error adding category: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error_message = "Category name is required!";
            }
        }

        // Update Category
        if (isset($_POST['update_category'])) {
            $category_id = intval($_POST['category_id']);
            $category_name = trim($_POST['category_name']);
            $description = trim($_POST['description']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (!empty($category_name)) {
                $update_sql = "UPDATE categories SET category_name = ?, description = ?, is_active = ? WHERE category_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ssii", $category_name, $description, $is_active, $category_id);
                
                if ($stmt->execute()) {
                    $success_message = "Category updated successfully!";
                } else {
                    $error_message = "Error updating category: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error_message = "Category name is required!";
            }
        }

        // Delete Category
        if (isset($_GET['delete'])) {
            $category_id = intval($_GET['delete']);
            
            // Check if category has products
            $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                $error_message = "Cannot delete category! It has {$row['count']} product(s) associated with it.";
            } else {
                $delete_sql = "DELETE FROM categories WHERE category_id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param("i", $category_id);
                
                if ($stmt->execute()) {
                    $success_message = "Category deleted successfully!";
                } else {
                    $error_message = "Error deleting category: " . $conn->error;
                }
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

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Category
            </button>
        </div>

        <div class="card">
            <h3><i class="fas fa-list"></i> All Categories</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.*, COUNT(p.product_id) as product_count 
                            FROM categories c 
                            LEFT JOIN products p ON c.category_id = p.category_id 
                            GROUP BY c.category_id 
                            ORDER BY c.category_name ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $is_active = isset($row['is_active']) ? $row['is_active'] : 1;
                            echo '<tr>';
                            echo '<td>' . $row['category_id'] . '</td>';
                            echo '<td><strong>' . htmlspecialchars($row['category_name']) . '</strong></td>';
                            echo '<td>' . htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : '') . '</td>';
                            echo '<td>' . $row['product_count'] . '</td>';
                            echo '<td><span class="category-status status-' . ($is_active ? 'active' : 'inactive') . '">' . ($is_active ? 'Active' : 'Inactive') . '</span></td>';
                            echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                            echo '<td>';
                            echo '<button class="btn btn-edit" onclick=\'openEditModal(' . json_encode($row) . ')\'><i class="fas fa-edit"></i> Edit</button> ';
                            echo '<button class="btn btn-delete" onclick="deleteCategory(' . $row['category_id'] . ', \'' . htmlspecialchars($row['category_name']) . '\')"><i class="fas fa-trash"></i> Delete</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #666;">No categories found. Add your first category!</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Category Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Add New Category</h2>
                <button class="close-modal" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="category_name">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" checked> Active
                    </label>
                </div>
                <button type="submit" name="add_category" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Category</h2>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" id="edit_category_id" name="category_id">
                <div class="form-group">
                    <label for="edit_category_name">Category Name *</label>
                    <input type="text" id="edit_category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="edit_is_active" name="is_active"> Active
                    </label>
                </div>
                <button type="submit" name="update_category" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
            </form>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(category) {
            document.getElementById('edit_category_id').value = category.category_id;
            document.getElementById('edit_category_name').value = category.category_name;
            document.getElementById('edit_description').value = category.description;
            document.getElementById('edit_is_active').checked = (category.is_active !== undefined && category.is_active == 1) || category.is_active === undefined;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        function deleteCategory(id, name) {
            if (confirm('Are you sure you want to delete the category "' + name + '"?')) {
                window.location.href = 'index.php?page=categories&delete=' + id;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>

