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
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your wishlist.']);
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

    // Debug: Log user ID and product ID
    error_log("Removing from wishlist - User ID: " . $_SESSION['user_id']);
    error_log("Removing from wishlist - Product ID: " . $product_id);

    // Check if product exists in wishlist
    $check_sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if (!$check_stmt) {
        throw new Exception("Error preparing check statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($check_stmt, "ii", $_SESSION['user_id'], $product_id);
    if (!mysqli_stmt_execute($check_stmt)) {
        throw new Exception("Error executing check statement: " . mysqli_stmt_error($check_stmt));
    }
    
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Product is not in your wishlist.']);
        exit();
    }

    // Remove from wishlist
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Error preparing delete statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing delete statement: " . mysqli_stmt_error($stmt));
    }

    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Product removed from wishlist.']);
    } else {
        throw new Exception("No rows were affected by the delete operation.");
    }

} catch (Exception $e) {
    error_log("Wishlist removal error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while removing from wishlist.',
        'debug' => $e->getMessage() // Only for development, remove in production
    ]);
} 