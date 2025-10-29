<?php
/**
 * Shopping Cart API
 * Handles all cart operations: add, remove, update, get, sync
 */

session_start();
require_once '../../config/db.php';

// Set JSON header
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

// Get request data
$action = $_POST['action'] ?? $_GET['action'] ?? '';

/**
 * Get or create cart ID for current user/session
 */
function getCartId($conn, $user_id = null, $session_id = null) {
    if ($user_id) {
        // Check if user has a cart
        $sql = "SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['cart_id'];
        } else {
            // Create new cart for user
            $sql = "INSERT INTO cart (user_id) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            return $conn->insert_id;
        }
    } elseif ($session_id) {
        // Check if session has a cart
        $sql = "SELECT cart_id FROM cart WHERE session_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['cart_id'];
        } else {
            // Create new cart for session
            $sql = "INSERT INTO cart (session_id) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $session_id);
            $stmt->execute();
            return $conn->insert_id;
        }
    }
    
    return null;
}

/**
 * Get cart items with product details
 */
function getCartItems($conn, $cart_id) {
    $sql = "SELECT ci.*, p.product_name, p.price, p.sku, p.stock_quantity, p.short_description,
            (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url,
            (p.price * ci.quantity) as subtotal
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_id = ? AND p.is_active = 1
            ORDER BY ci.added_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    $total_items = 0;
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total += $row['subtotal'];
        $total_items += $row['quantity'];
    }
    
    return [
        'items' => $items,
        'total' => $total,
        'total_items' => $total_items
    ];
}

// Handle different actions
switch ($action) {
    
    case 'get':
        // Get cart items
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = $_POST['session_id'] ?? null;
        
        $cart_id = getCartId($conn, $user_id, $session_id);
        
        if ($cart_id) {
            $cart_data = getCartItems($conn, $cart_id);
            $response = [
                'success' => true,
                'cart' => $cart_data['items'],
                'total' => $cart_data['total'],
                'total_items' => $cart_data['total_items'],
                'cart_id' => $cart_id
            ];
        } else {
            $response = [
                'success' => true,
                'cart' => [],
                'total' => 0,
                'total_items' => 0
            ];
        }
        break;
    
    case 'add':
        // Add item to cart
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = $_POST['session_id'] ?? null;
        
        if ($product_id <= 0 || $quantity <= 0) {
            $response = ['success' => false, 'message' => 'Invalid product or quantity'];
            break;
        }
        
        // Check if product exists and is active
        $check_sql = "SELECT product_id, product_name, stock_quantity FROM products WHERE product_id = ? AND is_active = 1";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $response = ['success' => false, 'message' => 'Product not found'];
            break;
        }
        
        $product = $result->fetch_assoc();
        
        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            $response = ['success' => false, 'message' => 'Insufficient stock'];
            break;
        }
        
        // Get or create cart
        $cart_id = getCartId($conn, $user_id, $session_id);
        
        // Check if product already in cart
        $check_item_sql = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $stmt = $conn->prepare($check_item_sql);
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
        $item_result = $stmt->get_result();
        
        if ($item_result->num_rows > 0) {
            // Update quantity
            $item = $item_result->fetch_assoc();
            $new_quantity = $item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock_quantity']) {
                $response = ['success' => false, 'message' => 'Insufficient stock'];
                break;
            }
            
            $update_sql = "UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_item_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ii", $new_quantity, $item['cart_item_id']);
            $stmt->execute();
            
            $message = 'Cart updated successfully!';
        } else {
            // Insert new item
            $insert_sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
            $stmt->execute();
            
            $message = 'Added to cart successfully!';
        }
        
        // Get updated cart data
        $cart_data = getCartItems($conn, $cart_id);
        
        $response = [
            'success' => true,
            'message' => $message,
            'product_name' => $product['product_name'],
            'cart_count' => $cart_data['total_items'],
            'cart_total' => $cart_data['total']
        ];
        break;
    
    case 'update':
        // Update item quantity
        $cart_item_id = intval($_POST['cart_item_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($cart_item_id <= 0 || $quantity < 0) {
            $response = ['success' => false, 'message' => 'Invalid data'];
            break;
        }
        
        if ($quantity === 0) {
            // Remove item
            $delete_sql = "DELETE FROM cart_items WHERE cart_item_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $cart_item_id);
            $stmt->execute();
            
            $response = ['success' => true, 'message' => 'Item removed from cart'];
        } else {
            // Check stock
            $check_sql = "SELECT p.stock_quantity FROM cart_items ci 
                         JOIN products p ON ci.product_id = p.product_id 
                         WHERE ci.cart_item_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("i", $cart_item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $response = ['success' => false, 'message' => 'Item not found'];
                break;
            }
            
            $product = $result->fetch_assoc();
            
            if ($quantity > $product['stock_quantity']) {
                $response = ['success' => false, 'message' => 'Insufficient stock'];
                break;
            }
            
            // Update quantity
            $update_sql = "UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_item_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ii", $quantity, $cart_item_id);
            $stmt->execute();
            
            $response = ['success' => true, 'message' => 'Cart updated'];
        }
        break;
    
    case 'remove':
        // Remove item from cart
        $cart_item_id = intval($_POST['cart_item_id'] ?? 0);
        
        if ($cart_item_id <= 0) {
            $response = ['success' => false, 'message' => 'Invalid item'];
            break;
        }
        
        $delete_sql = "DELETE FROM cart_items WHERE cart_item_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $cart_item_id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Item removed from cart'];
        } else {
            $response = ['success' => false, 'message' => 'Error removing item'];
        }
        break;
    
    case 'sync':
        // Sync localStorage cart to database (on login)
        $user_id = $_SESSION['user_id'] ?? null;
        $cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
        
        if (!$user_id) {
            $response = ['success' => false, 'message' => 'User not logged in'];
            break;
        }
        
        // Get or create user cart
        $cart_id = getCartId($conn, $user_id);
        
        $synced = 0;
        $errors = 0;
        
        foreach ($cart_items as $item) {
            $product_id = intval($item['product_id'] ?? 0);
            $quantity = intval($item['quantity'] ?? 1);
            
            if ($product_id <= 0 || $quantity <= 0) continue;
            
            // Check if product exists
            $check_sql = "SELECT stock_quantity FROM products WHERE product_id = ? AND is_active = 1";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $errors++;
                continue;
            }
            
            $product = $result->fetch_assoc();
            
            // Check if already in cart
            $check_item_sql = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
            $stmt = $conn->prepare($check_item_sql);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
            $item_result = $stmt->get_result();
            
            if ($item_result->num_rows > 0) {
                // Update quantity (add to existing)
                $existing = $item_result->fetch_assoc();
                $new_quantity = min($existing['quantity'] + $quantity, $product['stock_quantity']);
                
                $update_sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ii", $new_quantity, $existing['cart_item_id']);
                $stmt->execute();
            } else {
                // Insert new item
                $quantity = min($quantity, $product['stock_quantity']);
                $insert_sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
                $stmt->execute();
            }
            
            $synced++;
        }
        
        // Get updated cart data
        $cart_data = getCartItems($conn, $cart_id);
        
        $response = [
            'success' => true,
            'message' => "Cart synced! $synced items added.",
            'synced' => $synced,
            'errors' => $errors,
            'cart' => $cart_data['items'],
            'total' => $cart_data['total'],
            'total_items' => $cart_data['total_items']
        ];
        break;
    
    case 'clear':
        // Clear cart
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = $_POST['session_id'] ?? null;
        
        $cart_id = getCartId($conn, $user_id, $session_id);
        
        if ($cart_id) {
            $delete_sql = "DELETE FROM cart_items WHERE cart_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();
            
            $response = ['success' => true, 'message' => 'Cart cleared'];
        } else {
            $response = ['success' => true, 'message' => 'Cart already empty'];
        }
        break;
    
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

echo json_encode($response);
$conn->close();
?>

