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
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to your wishlist.']);
    exit();
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit();
}

$product_id = (int)$_POST['product_id'];

try {
    // Debug: Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Debug: Log user ID
    error_log("User ID: " . $_SESSION['user_id']);
    error_log("Product ID: " . $product_id);

    // Check if product exists and is active
    $check_sql = "SELECT id FROM products WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if (!$check_stmt) {
        throw new Exception("Error preparing product check statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($check_stmt, "i", $product_id);
    if (!mysqli_stmt_execute($check_stmt)) {
        throw new Exception("Error executing product check statement: " . mysqli_stmt_error($check_stmt));
    }
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit();
    }

    // Check if product is already in wishlist
    $check_wishlist_sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
    $check_wishlist_stmt = mysqli_prepare($conn, $check_wishlist_sql);
    if (!$check_wishlist_stmt) {
        throw new Exception("Error preparing wishlist check statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($check_wishlist_stmt, "ii", $_SESSION['user_id'], $product_id);
    if (!mysqli_stmt_execute($check_wishlist_stmt)) {
        throw new Exception("Error executing wishlist check statement: " . mysqli_stmt_error($check_wishlist_stmt));
    }
    $check_wishlist_result = mysqli_stmt_get_result($check_wishlist_stmt);

    if (mysqli_num_rows($check_wishlist_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Product is already in your wishlist.']);
        exit();
    }

    // Add to wishlist
    $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Error preparing insert statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing insert statement: " . mysqli_stmt_error($stmt));
    }

    echo json_encode(['success' => true, 'message' => 'Product added to wishlist.']);

} catch (Exception $e) {
    error_log("Wishlist error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while updating the wishlist.',
        'debug' => $e->getMessage() // Only for development, remove in production
    ]);
} 