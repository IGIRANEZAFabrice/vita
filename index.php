<?php
/**
 * Main Index File - Router
 * Routes requests to different pages based on 'page' parameter
 */

// Start session
session_start();

// Include database connection
require_once 'config/db.php';

// Get the page parameter from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Sanitize the page parameter to prevent directory traversal
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Define allowed pages
$allowed_pages = [
    'home',
    'about',
    'contact',
    'contactus',
    'products',
    'product',
    'training',
    'quote',
    'cart',
    'login',
    'register',
    'productOpen'
];

// Check if the requested page is allowed
if (!in_array($page, $allowed_pages)) {
    $page = 'home'; // Default to home if page not found
}

// Define the page file path
$page_file = 'pages/' . $page . '.php';

// Check if the page file exists
if (file_exists($page_file)) {
    include $page_file;
} else {
    // If file doesn't exist, show home page
    include 'pages/home.php';
}
?>
