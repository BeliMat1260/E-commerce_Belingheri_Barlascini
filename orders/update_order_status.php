<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

$check_sql = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$order = mysqli_fetch_assoc($check_result);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$allowed = ['pending', 'processing', 'shipped'];
if (!in_array($order['status'], $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Order cannot be marked delivered']);
    exit();
}

$update_sql = "UPDATE orders SET status = 'delivered' WHERE id = ? AND user_id = ? AND status IN ('pending', 'processing', 'shipped')";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "ii", $order_id, $_SESSION['user_id']);

if (mysqli_stmt_execute($update_stmt) && mysqli_stmt_affected_rows($update_stmt) === 1) {
    echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}
