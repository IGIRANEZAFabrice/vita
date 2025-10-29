
    <div class="overlay" onclick="toggleMobileSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="../logo/logo.png" alt="REDY-MED Logo" class="logo-icon">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="../admin/index.php?page=dashboard" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=hero-slides" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'hero-slides') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-images"></i>
                    <span>Hero Slides</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=products" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'products') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=categories" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'categories') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=manufacturers" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'manufacturers') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-industry"></i>
                    <span>Manufacturers</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=users" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'users') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=orders" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'orders') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=contactus" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'contactus') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contact Messages</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=aboutus" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'aboutus') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>About Us</span>
                </a>
            </li>
            <li>
                <a href="../admin/index.php?page=settings" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'settings') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <div class="header-right">
            <div class="user-menu">
                <div class="user-avatar">
                    <?php
                    if (isset($_SESSION['first_name'])) {
                        echo strtoupper(substr($_SESSION['first_name'], 0, 1));
                    } else {
                        echo 'A';
                    }
                    ?>
                </div>
                <div class="dropdown">
                    <a href="../admin/index.php?page=profile">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="../logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
