
    <div class="overlay" onclick="toggleMobileSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="../logo/logo.png" alt="REDY-MED Logo" class="logo-icon">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="../client/index.php?page=dashboard" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../client/index.php?page=products" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'products') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box"></i>
                    <span>Browse Products</span>
                </a>
            </li>
            <li>
                <a href="../client/index.php?page=orders" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'orders') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shopping-bag"></i>
                    <span>My Orders</span>
                </a>
            </li>
            <li>
                <a href="../client/index.php?page=quotes" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'quotes') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>My Quotes</span>
                </a>
            </li>
            <li>
                <a href="../client/index.php?page=cart" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'cart') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <span>Shopping Cart</span>
                </a>
            </li>
            <li>
                <a href="../client/index.php?page=wishlist" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'wishlist') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-heart"></i>
                    <span>Wishlist</span>
                </a>
            </li>
            <li>
                <a href="../index.php?page=product" target="_blank">
                    <i class="fa-solid fa-external-link-alt"></i>
                    <span>View Catalog</span>
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
                        echo 'C';
                    }
                    ?>
                </div>
                <div class="dropdown">
                    <a href="../client/index.php?page=profile">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="../../logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
