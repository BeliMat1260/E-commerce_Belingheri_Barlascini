<?php
require_once "config/database.php";
require_once "config/session.php";

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to your wishlist.']);
    exit();
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit();
}

$product_id = (int)$_POST['product_id'];

// Check if product exists and is active
$check_sql = "SELECT id FROM products WHERE id = ? AND is_active = 1";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $product_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Product not found or inactive.']);
    exit();
}

// Check if product is already in wishlist
$check_wishlist_sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
$check_wishlist_stmt = mysqli_prepare($conn, $check_wishlist_sql);
mysqli_stmt_bind_param($check_wishlist_stmt, "ii", $_SESSION['user_id'], $product_id);
mysqli_stmt_execute($check_wishlist_stmt);
$check_wishlist_result = mysqli_stmt_get_result($check_wishlist_stmt);

if (mysqli_num_rows($check_wishlist_result) > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Product is already in your wishlist.']);
    exit();
}

// Add to wishlist
$sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);

if (mysqli_stmt_execute($stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Product added to wishlist.']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist.']);
} 