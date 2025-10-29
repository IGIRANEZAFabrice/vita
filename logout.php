<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with success message
header('Location: index.php?page=login&logout=success');
exit;
?>

