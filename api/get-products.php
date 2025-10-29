<?php
/**
 * Get Products API
 * Fetch product details by IDs for guest cart
 */

require_once '../config/db.php';

header('Content-Type: application/json');

$ids = $_GET['ids'] ?? '';

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

// Sanitize IDs
$id_array = array_map('intval', explode(',', $ids));
$id_array = array_filter($id_array, function($id) { return $id > 0; });

if (empty($id_array)) {
    echo json_encode([]);
    exit;
}

// Create placeholders for prepared statement
$placeholders = implode(',', array_fill(0, count($id_array), '?'));

$sql = "SELECT p.product_id, p.product_name, p.price, p.sku, p.stock_quantity, p.short_description, c.category_name,
        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id IN ($placeholders) AND p.is_active = 1";

$stmt = $conn->prepare($sql);

// Bind parameters dynamically
$types = str_repeat('i', count($id_array));
$stmt->bind_param($types, ...$id_array);

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);

$stmt->close();
$conn->close();
?>

