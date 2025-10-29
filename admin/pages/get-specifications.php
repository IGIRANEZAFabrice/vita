<?php
// Get specifications for a category via AJAX
header('Content-Type: application/json');

// Include database connection
require_once '../../config/db.php';

// Get category ID from query parameter
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id <= 0) {
    echo json_encode([]);
    exit;
}

// Fetch specifications for this category
$sql = "SELECT * FROM specification_attributes WHERE category_id = ? OR category_id IS NULL ORDER BY display_order ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

$specifications = [];
while ($row = $result->fetch_assoc()) {
    $specifications[] = $row;
}

$stmt->close();

// Return as JSON
echo json_encode($specifications);
?>

