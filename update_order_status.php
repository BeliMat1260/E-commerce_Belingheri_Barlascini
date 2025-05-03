<?php
require_once "config/database.php";
require_once "config/session.php";

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get order ID from request
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order ID']);
    exit();
}

// Update order status to delivered
$update_sql = "UPDATE orders SET status = 'delivered' WHERE id = ? AND user_id = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "ii", $order_id, $_SESSION['user_id']);

if (mysqli_stmt_execute($update_stmt)) {
    echo json_encode(['success' => true, 'status' => 'delivered']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update order status']);
} 