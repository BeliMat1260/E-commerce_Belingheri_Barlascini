<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to cancel orders.');
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    setFlashMessage('error', 'Invalid request.');
    header("Location: /E-commerce_Belingheri_Barlascini/orders/orders.php");
    exit();
}

$order_id = (int)$_POST['order_id'];

// Verifica se l'ordine esiste e appartiene all'utente
$check_sql = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    setFlashMessage('error', 'Order not found.');
    header("Location: /E-commerce_Belingheri_Barlascini/orders/orders.php");
    exit();
}

$order = mysqli_fetch_assoc($result);

// Verifica se l'ordine può essere annullato
if ($order['status'] !== 'pending') {
    setFlashMessage('error', 'Only pending orders can be cancelled.');
    header("Location: /E-commerce_Belingheri_Barlascini/orders/orders.php");
    exit();
}

// Inizia la transazione
mysqli_begin_transaction($conn);

try {
    // Aggiorna lo stato dell'ordine
    $update_sql = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);

    // Ripristina le quantità di stock
    $items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $items_sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $items_result = mysqli_stmt_get_result($stmt);

    while ($item = mysqli_fetch_assoc($items_result)) {
        $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_stock_sql);
        mysqli_stmt_bind_param($stmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($stmt);
    }

    // Commit della transazione
    mysqli_commit($conn);

    setFlashMessage('success', 'Order cancelled successfully.');
} catch (Exception $e) {
    // Rollback in caso di errore
    mysqli_rollback($conn);
    setFlashMessage('error', 'Failed to cancel order. Please try again.');
}

header("Location: /E-commerce_Belingheri_Barlascini/orders/orders.php");
exit(); 