<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to view wishlist status.']);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_ids']) || !is_array($input['product_ids'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid product IDs provided.']);
    exit();
}

try {
    // Convert product IDs to integers and create placeholders for the query
    $product_ids = array_map('intval', $input['product_ids']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    // Get wishlist items for the user
    $sql = "SELECT product_id FROM wishlist WHERE user_id = ? AND product_id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    // Bind parameters
    $types = 'i' . str_repeat('i', count($product_ids));
    $params = array_merge([$_SESSION['user_id']], $product_ids);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $wishlist_items = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $wishlist_items[] = (int)$row['product_id'];
    }
    
    echo json_encode([
        'success' => true,
        'wishlist_items' => $wishlist_items
    ]);

} catch (Exception $e) {
    error_log("Wishlist status check error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking wishlist status.',
        'debug' => $e->getMessage() // Only for development, remove in production
    ]);
} 