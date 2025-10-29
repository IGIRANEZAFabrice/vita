<?php
/**
 * Add to Wishlist Handler
 * This file handles AJAX requests to add/remove items from wishlist
 */

session_start();
require_once '../../config/db.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to wishlist',
        'redirect' => '../../index.php?page=login'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => 'Invalid request'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }
    
    // Check if product exists and is active
    $check_product = "SELECT product_id, product_name FROM products WHERE product_id = ? AND is_active = 1";
    $stmt = $conn->prepare($check_product);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($action === 'add') {
        // Add to wishlist
        $notes = $_POST['notes'] ?? '';
        
        // Check if already in wishlist
        $check_sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response = [
                'success' => false,
                'message' => 'This product is already in your wishlist',
                'already_exists' => true
            ];
        } else {
            // Insert into wishlist
            $insert_sql = "INSERT INTO wishlist (user_id, product_id, notes) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iis", $user_id, $product_id, $notes);
            
            if ($stmt->execute()) {
                // Get updated wishlist count
                $count_sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
                $count_stmt = $conn->prepare($count_sql);
                $count_stmt->bind_param("i", $user_id);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $wishlist_count = $count_result->fetch_assoc()['total'];
                $count_stmt->close();
                
                $response = [
                    'success' => true,
                    'message' => 'Added to wishlist successfully!',
                    'product_name' => $product['product_name'],
                    'wishlist_count' => $wishlist_count
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error adding to wishlist: ' . $conn->error
                ];
            }
        }
        $stmt->close();
        
    } elseif ($action === 'remove') {
        // Remove from wishlist
        $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        
        if ($stmt->execute()) {
            // Get updated wishlist count
            $count_sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bind_param("i", $user_id);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $wishlist_count = $count_result->fetch_assoc()['total'];
            $count_stmt->close();
            
            $response = [
                'success' => true,
                'message' => 'Removed from wishlist',
                'wishlist_count' => $wishlist_count
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error removing from wishlist'
            ];
        }
        $stmt->close();
        
    } elseif ($action === 'check') {
        // Check if product is in wishlist
        $check_sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $response = [
            'success' => true,
            'in_wishlist' => $result->num_rows > 0
        ];
        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>

