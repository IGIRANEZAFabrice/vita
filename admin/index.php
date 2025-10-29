<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header('Location: ../index.php?page=login');
    exit;
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    // Not an admin, redirect to client dashboard
    header('Location: ../client/index.php');
    exit;
}

// Include database connection
require_once '../config/db.php';

// Get the page parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Sanitize the page parameter
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Define allowed admin pages
$allowed_pages = [
    'dashboard',
    'hero-slides',
    'products',
    'add-product',
    'edit-product',
    'profile',
    'settings',
    'aboutus',
    'contactus',
    'users',
    'orders',
    'categories',
    'manufacturers',
    'reports'
];

// Check if the requested page is allowed
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard'; // Default to dashboard if page not found
}

// Map page names to file names
$page_files = [
    'dashboard' => 'index.php',
    'hero-slides' => 'hero-slides.php',
    'products' => 'products.php',
    'add-product' => 'add-product.php',
    'edit-product' => 'edit-product.php',
    'profile' => 'profile.php',
    'settings' => 'settings.php',
    'aboutus' => 'aboutus.php',
    'contactus' => 'contactus.php',
    'users' => 'users.php',
    'orders' => 'orders.php',
    'categories' => 'categories.php',
    'manufacturers' => 'manufacturers.php',
    'reports' => 'reports.php'
];

// Get the file name for the requested page
$page_file = isset($page_files[$page]) ? $page_files[$page] : 'index.php';

// Define the full page file path
$page_path = 'pages/' . $page_file;

// Check if the page file exists
if (file_exists($page_path)) {
    include $page_path;
} else {
    // Page file not found, show 404 or redirect to dashboard
    include 'pages/index.php';
}
?>

