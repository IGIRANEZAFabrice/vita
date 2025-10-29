<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header('Location: ../index.php?page=login');
    exit;
}

// Check if user is client (not admin)
if ($_SESSION['role'] !== 'client') {
    // Is an admin, redirect to admin dashboard
    header('Location: ../admin/index.php');
    exit;
}

// Include database connection
require_once '../config/db.php';

// Get the page parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Sanitize the page parameter
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Define allowed client pages
$allowed_pages = [
    'dashboard',
    'products',
    'profile',
    'orders',
    'cart',
    'wishlist',
    'quotes'
];

// Check if the requested page is allowed
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard'; // Default to dashboard if page not found
}

// Map page names to file names
$page_files = [
    'dashboard' => 'index.php',
    'products' => 'products.php',
    'profile' => 'profile.php',
    'orders' => 'orders.php',
    'cart' => 'cart.php',
    'wishlist' => 'wishlist.php',
    'quotes' => 'quotes.php'
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

