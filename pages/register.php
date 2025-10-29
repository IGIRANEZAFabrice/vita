<?php
// Session is already started in index.php, no need to start again
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to appropriate page
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        // Redirect clients to main site home page
        header('Location: index.php?page=home');
    }
    exit;
}

$error_message = '';
$success_message = '';
$errors = [];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $company_name = trim($_POST['company_name']);

    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }

    if (empty($last_name)) {
        $errors[] = 'Last name is required.';
    }

    // Check if username already exists
    if (empty($errors)) {
        $check_sql = "SELECT user_id FROM user_credentials WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = 'Username already exists. Please choose another.';
        }
        $check_stmt->close();
    }

    // Check if email already exists
    if (empty($errors)) {
        $check_sql = "SELECT user_id FROM user_credentials WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = 'Email already exists. Please use another email or login.';
        }
        $check_stmt->close();
    }

    // If no errors, create the account
    if (empty($errors)) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert into user_credentials table (role is always 'client')
            $insert_sql = "INSERT INTO user_credentials (username, email, password_hash, role, is_active)
                          VALUES (?, ?, ?, 'client', 1)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sss", $username, $email, $password_hash);
            $insert_stmt->execute();

            // Get the new user ID
            $user_id = $conn->insert_id;

            // Insert into user_details table
            $details_sql = "INSERT INTO user_details (user_id, first_name, last_name, phone, company_name)
                           VALUES (?, ?, ?, ?, ?)";
            $details_stmt = $conn->prepare($details_sql);
            $details_stmt->bind_param("issss", $user_id, $first_name, $last_name, $phone, $company_name);
            $details_stmt->execute();

            // Commit transaction
            $conn->commit();

            // Set success message
            $success_message = 'Account created successfully! You can now login.';

            // Clear form data
            $_POST = [];

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errors[] = 'An error occurred while creating your account. Please try again.';
        }
    }

    // Combine errors into single message
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - REDY-MED</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        .register-section {
            max-width: 600px;
            margin: 3rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .register-section h1 {
            color: #000000;
            margin-bottom: 0.5rem;
            font-size: 2rem;
            text-align: center;
        }

        .register-section > p {
            color: #666;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #000000;
            font-weight: 500;
        }

        .form-group label .required {
            color: #ff0000;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00e600;
        }

        .register-btn {
            width: 100%;
            background: #00e600;
            color: #000000;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background: #00ff00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 230, 0, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
            color: #666;
        }

        .login-link a {
            color: #00e600;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 2.5rem;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .password-toggle-btn:hover {
            color: #00e600;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }



        .password-strength-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 0.25rem;
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #f44336; width: 33%; }
        .strength-medium { background: #ff9800; width: 66%; }
        .strength-strong { background: #4caf50; width: 100%; }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .register-section {
                margin: 2rem 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>

    <section class="register-section">
        <h1><i class="fas fa-user-plus"></i> Create Account</h1>
        <p>Join REDY-MED to access our medical equipment catalog</p>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success_message); ?>
            <br><br>
            <a href="index.php?page=login" style="color: #2e7d32; font-weight: 600;">
                <i class="fas fa-sign-in-alt"></i> Click here to login
            </a>
        </div>
        <?php endif; ?>

        <form method="POST" action="" id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i> First Name <span class="required">*</span>
                    </label>
                    <input type="text"
                           id="first_name"
                           name="first_name"
                           placeholder="Enter your first name"
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i> Last Name <span class="required">*</span>
                    </label>
                    <input type="text"
                           id="last_name"
                           name="last_name"
                           placeholder="Enter your last name"
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user-circle"></i> Username <span class="required">*</span>
                </label>
                <input type="text"
                       id="username"
                       name="username"
                       placeholder="Choose a username (letters, numbers, underscore only)"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       pattern="[a-zA-Z0-9_]+"
                       minlength="3"
                       required>
                <small style="color: #666; font-size: 0.85rem;">Minimum 3 characters, letters, numbers and underscore only</small>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Enter your email address"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           placeholder="Enter your phone number"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="company_name">
                        <i class="fas fa-building"></i> Company Name
                    </label>
                    <input type="text"
                           id="company_name"
                           name="company_name"
                           placeholder="Enter your company name"
                           value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
                </div>
            </div>

            <div class="form-group password-toggle">
                <label for="password">
                    <i class="fas fa-lock"></i> Password <span class="required">*</span>
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Create a password (minimum 6 characters)"
                       minlength="6"
                       oninput="checkPasswordStrength()"
                       required>
                <button type="button" class="password-toggle-btn" onclick="togglePassword('password', 'toggleIcon1')">
                    <i class="fas fa-eye" id="toggleIcon1"></i>
                </button>
                <div class="password-strength">
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="strengthBar"></div>
                    </div>
                    <small id="strengthText" style="color: #666;"></small>
                </div>
            </div>

            <div class="form-group password-toggle">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i> Confirm Password <span class="required">*</span>
                </label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       placeholder="Re-enter your password"
                       minlength="6"
                       oninput="checkPasswordMatch()"
                       required>
                <button type="button" class="password-toggle-btn" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                    <i class="fas fa-eye" id="toggleIcon2"></i>
                </button>
                <small id="matchText" style="display: block; margin-top: 0.5rem;"></small>
            </div>

            <button type="submit" class="register-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="index.php?page=login"><i class="fas fa-sign-in-alt"></i> Login here</a>
        </div>
    </section>

    <?php include 'include/footer.php'; ?>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-fill';

            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#f44336';
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Medium password';
                strengthText.style.color = '#ff9800';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#4caf50';
            }

            checkPasswordMatch();
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('matchText');

            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchText.textContent = '✓ Passwords match';
                matchText.style.color = '#4caf50';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.style.color = '#f44336';
            }
        }

        // Form validation before submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>

