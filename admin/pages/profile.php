<?php
// Handle form submission
$success_message = '';
$error_message = '';

// Get current user data
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT uc.username, uc.email, ud.* FROM user_credentials uc
             LEFT JOIN user_details ud ON uc.user_id = ud.user_id
             WHERE uc.user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state_province = trim($_POST['state_province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'USA');
    $bio = trim($_POST['bio'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    }

    // Validate password if provided
    if (!$error_message && !empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error_message = "Password must be at least 6 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        }
    }

    // Handle profile image upload
    $profile_image = $user['profile_image'] ?? '';
    if (!empty($_FILES['profile_image']['name'])) {
        $upload_dir = __DIR__ . '/../../images/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['profile_image']['name'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_error = $_FILES['profile_image']['error'];

        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $error_message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($file_size > 5242880) {
            $error_message = "File size exceeds 5MB limit.";
        } elseif ($file_error !== 0) {
            $error_message = "Error uploading file. Error code: " . $file_error;
        } else {
            $new_filename = 'profile-' . $user_id . '-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $profile_image = 'images/profiles/' . $new_filename;
            } else {
                $error_message = "Failed to move uploaded file. Check folder permissions.";
            }
        }
    }

    // Check if user details record exists
    $check_sql = "SELECT detail_id FROM user_details WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $detail_exists = $check_result->num_rows > 0;
    $check_stmt->close();

    if (!$error_message) {
        if ($detail_exists) {
            // Update existing user details
            $update_sql = "UPDATE user_details SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                company_name = ?,
                address_line1 = ?,
                address_line2 = ?,
                city = ?,
                state_province = ?,
                postal_code = ?,
                country = ?,
                profile_image = ?,
                bio = ?
                WHERE user_id = ?";

            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssssssssssi", $first_name, $last_name, $phone, $company_name,
                                    $address_line1, $address_line2, $city, $state_province,
                                    $postal_code, $country, $profile_image, $bio, $user_id);

            if ($update_stmt->execute()) {
                // Update email and password in user_credentials table
                if (!empty($new_password)) {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $cred_sql = "UPDATE user_credentials SET email = ?, password_hash = ? WHERE user_id = ?";
                    $cred_stmt = $conn->prepare($cred_sql);
                    $cred_stmt->bind_param("ssi", $email, $password_hash, $user_id);
                } else {
                    $cred_sql = "UPDATE user_credentials SET email = ? WHERE user_id = ?";
                    $cred_stmt = $conn->prepare($cred_sql);
                    $cred_stmt->bind_param("si", $email, $user_id);
                }

                if ($cred_stmt->execute()) {
                    $success_message = "✅ Profile updated successfully!";
                } else {
                    $error_message = "Error updating credentials: " . $conn->error;
                }
                $cred_stmt->close();
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }
            $update_stmt->close();
        } else {
            // Insert new user details record
            $insert_sql = "INSERT INTO user_details (user_id, first_name, last_name, phone, company_name,
                          address_line1, address_line2, city, state_province, postal_code, country, profile_image, bio)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("isssssssssss", $user_id, $first_name, $last_name, $phone, $company_name,
                                    $address_line1, $address_line2, $city, $state_province,
                                    $postal_code, $country, $profile_image, $bio);

            if ($insert_stmt->execute()) {
                // Update email and password in user_credentials table
                if (!empty($new_password)) {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $cred_sql = "UPDATE user_credentials SET email = ?, password_hash = ? WHERE user_id = ?";
                    $cred_stmt = $conn->prepare($cred_sql);
                    $cred_stmt->bind_param("ssi", $email, $password_hash, $user_id);
                } else {
                    $cred_sql = "UPDATE user_credentials SET email = ? WHERE user_id = ?";
                    $cred_stmt = $conn->prepare($cred_sql);
                    $cred_stmt->bind_param("si", $email, $user_id);
                }

                if ($cred_stmt->execute()) {
                    $success_message = "✅ Profile created successfully!";
                } else {
                    $error_message = "Error updating credentials: " . $conn->error;
                }
                $cred_stmt->close();
            } else {
                $error_message = "Error creating profile: " . $conn->error;
            }
            $insert_stmt->close();
        }

        // Update session
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        // Refresh user data
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - REDY-MED</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <style>
        .profile-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .profile-sidebar {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            height: fit-content;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00e600, #00b300);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1.5rem;
            overflow: hidden;
            border: 4px solid #f0f0f0;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            color: #00e600;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .profile-info {
            text-align: left;
            border-top: 1px solid #f0f0f0;
            padding-top: 1.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-label {
            font-size: 0.8rem;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .profile-form {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00e600;
            box-shadow: 0 0 0 3px rgba(0, 230, 0, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
            font-size: 2rem;
            color: #00e600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .file-upload-area p {
            margin: 0.5rem 0;
            color: #666;
            font-weight: 600;
        }

        .file-input {
            display: none;
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
            padding-top: 2rem;
            border-top: 1px solid #f0f0f0;
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

        @media (max-width: 768px) {
            .profile-wrapper {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <div class="content-header">
            <h1><i class="fas fa-user-circle"></i> Admin Profile</h1>
            <p>Manage your profile information and settings</p>
        </div>

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

        <div class="profile-wrapper">
            <!-- Profile Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="../../<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['first_name'] ?? 'A', 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <div class="profile-name">
                    <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                </div>
                <div class="profile-role">
                    <i class="fas fa-shield-alt"></i> Administrator
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Member Since</div>
                        <div class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_profile" value="1">

                    <!-- Profile Picture Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-image"></i> Profile Picture
                        </div>
                        <div class="form-group">
                            <div class="file-upload-area" onclick="document.getElementById('profile_image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload profile picture</p>
                                <p style="font-size: 0.85rem; color: #999;">JPG, PNG, GIF (Max 5MB)</p>
                            </div>
                            <input
                                type="file"
                                id="profile_image"
                                name="profile_image"
                                class="file-input"
                                accept="image/*"
                            >
                        </div>
                    </div>

                    <!-- Account Credentials Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-lock"></i> Account Credentials
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                    placeholder="Enter email address"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input
                                    type="password"
                                    id="new_password"
                                    name="new_password"
                                    placeholder="Leave blank to keep current password"
                                >
                                <small style="color: #999; margin-top: 0.3rem; display: block;">Minimum 6 characters</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    placeholder="Confirm new password"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user"></i> Personal Information
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"
                                    placeholder="Enter first name"
                                >
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"
                                    placeholder="Enter last name"
                                >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                    placeholder="Enter phone number"
                                >
                            </div>
                            <div class="form-group">
                                <label for="company_name">Company Name</label>
                                <input
                                    type="text"
                                    id="company_name"
                                    name="company_name"
                                    value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>"
                                    placeholder="Enter company name"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="address_line1">Address Line 1</label>
                                <input
                                    type="text"
                                    id="address_line1"
                                    name="address_line1"
                                    value="<?php echo htmlspecialchars($user['address_line1'] ?? ''); ?>"
                                    placeholder="Street address"
                                >
                            </div>
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="address_line2">Address Line 2</label>
                                <input
                                    type="text"
                                    id="address_line2"
                                    name="address_line2"
                                    value="<?php echo htmlspecialchars($user['address_line2'] ?? ''); ?>"
                                    placeholder="Apartment, suite, etc."
                                >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                                    placeholder="City"
                                >
                            </div>
                            <div class="form-group">
                                <label for="state_province">State/Province</label>
                                <input
                                    type="text"
                                    id="state_province"
                                    name="state_province"
                                    value="<?php echo htmlspecialchars($user['state_province'] ?? ''); ?>"
                                    placeholder="State or Province"
                                >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input
                                    type="text"
                                    id="postal_code"
                                    name="postal_code"
                                    value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>"
                                    placeholder="Postal code"
                                >
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input
                                    type="text"
                                    id="country"
                                    name="country"
                                    value="<?php echo htmlspecialchars($user['country'] ?? 'USA'); ?>"
                                    placeholder="Country"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-pen"></i> Bio
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="bio">About You</label>
                                <textarea
                                    id="bio"
                                    name="bio"
                                    placeholder="Tell us about yourself..."
                                ><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="index.php?page=dashboard" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include 'include/footer.php'; ?>
    <script src="js/sidebar.js"></script>
</body>
</html>