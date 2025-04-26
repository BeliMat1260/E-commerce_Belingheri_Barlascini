<?php
require_once "config/database.php";

function getCartId($user_id) {
    global $conn;
    $sql = "SELECT id FROM carts WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result)['id'];
    }
    return null;
}

function createCart($user_id) {
    global $conn;
    $sql = "INSERT INTO carts (user_id) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    return null;
}

function deleteEmptyCart($user_id) {
    global $conn;
    // Verifica se il carrello è vuoto
    $check_sql = "SELECT COUNT(*) as count FROM cart_items WHERE cart_id = (SELECT id FROM carts WHERE user_id = ?)";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $user_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count == 0) {
        // Se il carrello è vuoto, eliminalo
        $delete_sql = "DELETE FROM carts WHERE user_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $user_id);
        return mysqli_stmt_execute($delete_stmt);
    }
    return false;
}

function getCartItems($user_id) {
    global $conn;
    $sql = "SELECT ci.*, p.name, p.price, p.stock_quantity, p.discount_price,
            (SELECT image_url FROM product_images WHERE product_id = p.id AND image_order = 1 LIMIT 1) as product_image
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            JOIN products p ON ci.product_id = p.id
            WHERE c.user_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
?> 