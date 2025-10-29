<?php
/**
 * Database Connection Configuration
 * Uses environment variables from .htaccess
 */

// Get database credentials from environment variables
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_username = getenv('DB_USERNAME') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'ecomercedb';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for better character support
$conn->set_charset("utf8mb4");

// Optional: Set timezone
// $conn->query("SET time_zone = '+00:00'");

?>

