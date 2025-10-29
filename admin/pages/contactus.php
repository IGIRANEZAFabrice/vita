<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css"> 
    <link rel="stylesheet" href="../css/sidebar.css"> 
</head>
<body>
    <?php include '../include/sidebar.php'; ?>

 <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1>Dashboard Overview</h1>
            <p>Welcome back! Here's what's happening today.</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Users</h3>
                <div class="value">1,245</div>
            </div>
            <div class="card">
                <h3>Products</h3>
                <div class="value">89</div>
            </div>
            <div class="card">
                <h3>Messages</h3>
                <div class="value">32</div>
            </div>
            <div class="card">
                <h3>Revenue</h3>
                <div class="value">$12.5K</div>
            </div>
        </div>

        <div class="card">
            <h3>Recent Activity</h3>
            <p style="color: #666; margin-top: 1rem; font-size: 0.9rem;">Your recent activity will appear here...</p>
        </div>
    </main>

    <?php include '../include/footer.php'; ?>
    <script src="../js/sidebar.js"></script>
</body>
</html>