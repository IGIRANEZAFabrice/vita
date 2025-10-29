
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <li class="nav-dropdown">
                    <a href="index.php?page=product">Products <i class="fas fa-chevron-down"></i></a>
                    <div class="nav-dropdown-menu">
                       
                        <div class="nav-dropdown-content">
                            
                            <?php
                            // Fetch categories from database
                            $cat_query = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
                            $cat_result = $conn->query($cat_query);

                            if ($cat_result && $cat_result->num_rows > 0) {
                                while($category = $cat_result->fetch_assoc()) {
                                    $cat_id = $category['category_id'];
                                    $cat_name = htmlspecialchars($category['category_name']);

                                    echo '<a href="index.php?page=product&category=' . $cat_id . '" class="nav-dropdown-item">';
                                    echo $cat_name;
                                    echo '</a>';
                                }
                            } else {
                                echo '<div class="nav-dropdown-empty">';
                                echo '<p>No categories available</p>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </li>
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

                <!-- Search Icon -->
                <button class="icon-link search-toggle-btn" title="Search" onclick="toggleSearch()">
                    <i class="fa-solid fa-search"></i>
                </button>

                <div class="cart-dropdown-container">
                    <a href="index.php?page=cart" class="icon-link" title="Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-count" style="display: none;">0</span>
                    </a>

                    <!-- Cart Dropdown -->
                    <div class="cart-dropdown" id="cart-dropdown">
                        <div class="cart-dropdown-header">
                            <h3>Shopping Cart</h3>
                            <span class="cart-dropdown-count">0 items</span>
                        </div>

                        <div class="cart-dropdown-items" id="cart-dropdown-items">
                            <!-- Items will be inserted here by JavaScript -->
                        </div>

                        <div class="cart-dropdown-empty" id="cart-dropdown-empty" style="display: none;">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Your cart is empty</p>
                        </div>

                        <div class="cart-dropdown-footer" id="cart-dropdown-footer">
                            <div class="cart-dropdown-total">
                                <span>Total:</span>
                                <span class="cart-dropdown-total-amount">$0.00</span>
                            </div>
                            <a href="index.php?page=cart" class="cart-dropdown-view-btn">
                                <i class="fas fa-shopping-cart"></i> View Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Search Bar -->
        <div class="search-bar" id="search-bar">
            <button type="button" class="search-close-btn-top" onclick="toggleSearch()" title="Close Search">
                <i class="fas fa-times"></i>
            </button>
            <div class="search-bar-container">
                <form action="index.php" method="GET" class="search-form" id="search-form">
                    <input type="hidden" name="page" value="product">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            class="search-input"
                            placeholder="Search for products..."
                            autocomplete="off"
                        >
                        <button type="submit" class="search-submit-btn" title="Search">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <!-- Initialize Cart Count -->
    <script src="js/simple-cart.js"></script>
    <script src="js/cart-dropdown.js"></script>
    <script>
        // Update cart count and dropdown on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof simpleCart !== 'undefined') {
                simpleCart.updateCartCount();
            }
            if (typeof updateCartDropdown !== 'undefined') {
                updateCartDropdown();
            }
        });

        // Update dropdown when cart changes
        window.addEventListener('cartUpdated', function() {
            if (typeof updateCartDropdown !== 'undefined') {
                updateCartDropdown();
            }
        });
    </script>

    <!-- Search Bar Toggle Script -->
    <script>
        function toggleSearch() {
            const searchBar = document.getElementById('search-bar');
            const searchInput = document.getElementById('search-input');

            if (searchBar.classList.contains('active')) {
                // Close search bar
                searchBar.classList.remove('active');
                searchInput.value = '';
            } else {
                // Open search bar
                searchBar.classList.add('active');
                // Focus on input after animation
                setTimeout(() => {
                    searchInput.focus();
                }, 300);
            }
        }

        // Close search bar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const searchBar = document.getElementById('search-bar');
                if (searchBar.classList.contains('active')) {
                    toggleSearch();
                }
            }
        });

        // Handle search form submission
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');

            if (searchForm && searchInput) {
                // Submit on Enter key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (searchInput.value.trim() !== '') {
                            searchForm.submit();
                        } else {
                            alert('Please enter a search term');
                        }
                    }
                });

                // Validate on form submit
                searchForm.addEventListener('submit', function(e) {
                    if (searchInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please enter a search term');
                        searchInput.focus();
                    }
                });
            }
        });
    </script>