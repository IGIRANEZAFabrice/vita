<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - REDY-MED</title>
    <link rel="icon" type="image/png" href="../logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

 <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
            <p>Welcome back, <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Admin'; ?>! Here's what's happening today.</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <?php
                // Get total users count
                $user_sql = "SELECT COUNT(*) as total FROM user_credentials WHERE is_active = 1";
                $user_result = $conn->query($user_sql);
                $user_count = $user_result->fetch_assoc()['total'];
                ?>
                <div class="value"><?php echo number_format($user_count); ?></div>
            </div>
            <div class="card">
                <h3><i class="fas fa-box"></i> Products</h3>
                <?php
                // Get total products count
                $product_sql = "SELECT COUNT(*) as total FROM products WHERE is_active = 1";
                $product_result = $conn->query($product_sql);
                $product_count = $product_result->fetch_assoc()['total'];
                ?>
                <div class="value"><?php echo number_format($product_count); ?></div>
            </div>
            <div class="card">
                <h3><i class="fas fa-layer-group"></i> Categories</h3>
                <?php
                // Get total categories count
                $category_sql = "SELECT COUNT(*) as total FROM categories";
                $category_result = $conn->query($category_sql);
                $category_count = $category_result->fetch_assoc()['total'];
                ?>
                <div class="value"><?php echo number_format($category_count); ?></div>
            </div>
            <div class="card">
                <h3><i class="fas fa-user-shield"></i> Admins</h3>
                <?php
                // Get admin users count
                $admin_sql = "SELECT COUNT(*) as total FROM user_credentials WHERE role = 'admin' AND is_active = 1";
                $admin_result = $conn->query($admin_sql);
                $admin_count = $admin_result->fetch_assoc()['total'];
                ?>
                <div class="value"><?php echo number_format($admin_count); ?></div>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            <?php
            // Get recent user registrations
            $recent_sql = "SELECT uc.username, uc.email, uc.created_at, ud.first_name, ud.last_name
                          FROM user_credentials uc
                          LEFT JOIN user_details ud ON uc.user_id = ud.user_id
                          ORDER BY uc.created_at DESC
                          LIMIT 5";
            $recent_result = $conn->query($recent_sql);

            if ($recent_result->num_rows > 0) {
                echo '<table style="width: 100%; margin-top: 1rem; border-collapse: collapse;">';
                echo '<thead>';
                echo '<tr style="border-bottom: 2px solid #e0e0e0; text-align: left;">';
                echo '<th style="padding: 0.75rem;">User</th>';
                echo '<th style="padding: 0.75rem;">Email</th>';
                echo '<th style="padding: 0.75rem;">Registered</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $recent_result->fetch_assoc()) {
                    $full_name = trim($row['first_name'] . ' ' . $row['last_name']);
                    $display_name = !empty($full_name) ? $full_name : $row['username'];

                    echo '<tr style="border-bottom: 1px solid #f0f0f0;">';
                    echo '<td style="padding: 0.75rem;">' . htmlspecialchars($display_name) . '</td>';
                    echo '<td style="padding: 0.75rem;">' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td style="padding: 0.75rem;">' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p style="color: #666; margin-top: 1rem; font-size: 0.9rem;">No recent activity to display.</p>';
            }
            ?>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
</body>
</html>