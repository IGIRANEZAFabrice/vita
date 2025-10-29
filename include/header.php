
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* User Profile Dropdown Styles */
        .user-profile-menu {
            position: relative;
            display: inline-block;
        }

        .user-profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00e600, #00b300);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 230, 0, 0.3);
            border: 2px solid #fff;
        }

        .user-profile-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 230, 0, 0.5);
        }

        .user-profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .user-profile-menu:hover .user-profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-profile-header {
            padding: 1.25rem;
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px 12px 0 0;
        }

        .user-profile-header .user-name {
            font-weight: 700;
            color: #000;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .user-profile-header .user-email {
            font-size: 0.8rem;
            color: #666;
        }

        .user-profile-header .user-role {
            display: inline-block;
            background: linear-gradient(135deg, #00e600, #00b300);
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            text-transform: uppercase;
        }

        .user-profile-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            color: #333;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .user-profile-dropdown a:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding-left: 1.5rem;
        }

        .user-profile-dropdown a i {
            width: 18px;
            font-size: 1rem;
            color: #00e600;
        }

        .user-profile-dropdown a.logout-link {
            border-top: 1px solid #f0f0f0;
            color: #dc3545;
            border-radius: 0 0 12px 12px;
        }

        .user-profile-dropdown a.logout-link i {
            color: #dc3545;
        }

        .user-profile-dropdown a.logout-link:hover {
            background: linear-gradient(135deg, #ffe5e5, #ffcccc);
        }

        .user-profile-dropdown a.dashboard-link {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        }

        .user-profile-dropdown a.dashboard-link:hover {
            background: linear-gradient(135deg, #c8e6c9, #a5d6a7);
        }
    </style>
    <header>
        <nav class="nav-container">
            <div class="logo">
                <img src="logo/logo.png" alt="REDY-MED Logo" class="logo-img">
                <img src="logo/favicon.png" alt="REDY-MED" class="logo-favicon">
            </div>

            <input type="checkbox" id="menuToggle">
            <label for="menuToggle" class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <ul class="nav-center">
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=about">About Us</a></li>
                <li><a href="index.php?page=contact">Contact Us</a></li>
                <li><a href="index.php?page=product">Products</a></li>
                <li><a href="index.php?page=training">Training</a></li>
                <li><a href="index.php?page=quote" class="quote-btn">Get Quote</a></li>
            </ul>

            <div class="nav-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in - Show profile dropdown -->
                    <div class="user-profile-menu">
                        <div class="user-profile-avatar">
                            <?php
                            if (isset($_SESSION['first_name'])) {
                                echo strtoupper(substr($_SESSION['first_name'], 0, 1));
                            } else {
                                echo 'U';
                            }
                            ?>
                        </div>
                        <div class="user-profile-dropdown">
                            <div class="user-profile-header">
                                <div class="user-name">
                                    <?php
                                    echo isset($_SESSION['first_name']) && isset($_SESSION['last_name'])
                                        ? htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name'])
                                        : htmlspecialchars($_SESSION['username']);
                                    ?>
                                </div>
                                <div class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
                                <span class="user-role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                            </div>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <a href="admin/index.php" class="dashboard-link">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Admin Dashboard</span>
                                </a>
                            <?php else: ?>
                                <a href="client/index.php" class="dashboard-link">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>My Dashboard</span>
                                </a>
                            <?php endif; ?>
                            <a href="index.php?page=product">
                                <i class="fas fa-box"></i>
                                <span>Browse Products</span>
                            </a>
                            <a href="logout.php" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- User is not logged in - Show login icon -->
                    <a href="index.php?page=login" class="icon-link" title="Login">
                        <i class="fa-solid fa-user"></i>
                    </a>
                <?php endif; ?>

                <a href="index.php?page=cart" class="icon-link" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-count">3</span>
                </a>
            </div>
        </nav>
    </header>