<?php
require_once "config/database.php";
require_once "config/session.php";

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your wishlist.']);
    exit();
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit();
}

$product_id = (int)$_POST['product_id'];

// Remove item from wishlist
$sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);

if (mysqli_stmt_execute($stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Item removed from wishlist.']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to remove item from wishlist.']);
} 