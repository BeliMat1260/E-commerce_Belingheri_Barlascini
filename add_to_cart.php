<?php
require_once "config/database.php";
require_once "config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to add items to your cart.');
    header("Location: login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    header("Location: products.php");
    exit();
}

// Verifica se i parametri necessari sono presenti
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    setFlashMessage('error', 'Missing required parameters.');
    header("Location: products.php");
    exit();
}

$product_id = (int)$_POST['product_id'];
$quantity = (int)$_POST['quantity'];

// Verifica se la quantità è valida
if ($quantity <= 0) {
    setFlashMessage('error', 'Invalid quantity.');
    header("Location: products.php");
    exit();
}

// Verifica se il prodotto esiste
$check_product_sql = "SELECT id, stock_quantity FROM products WHERE id = ? AND is_active = 1";
$check_product_stmt = mysqli_prepare($conn, $check_product_sql);
mysqli_stmt_bind_param($check_product_stmt, "i", $product_id);
mysqli_stmt_execute($check_product_stmt);
$product_result = mysqli_stmt_get_result($check_product_stmt);

if (mysqli_num_rows($product_result) === 0) {
    setFlashMessage('error', 'Product not found.');
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($product_result);

// Verifica se c'è abbastanza stock
if ($quantity > $product['stock_quantity']) {
    setFlashMessage('error', 'Not enough stock available.');
    header("Location: products.php");
    exit();
}

// Verifica se il prodotto è già nel carrello
$check_cart_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$check_cart_stmt = mysqli_prepare($conn, $check_cart_sql);
mysqli_stmt_bind_param($check_cart_stmt, "ii", $_SESSION['user_id'], $product_id);
mysqli_stmt_execute($check_cart_stmt);
$cart_result = mysqli_stmt_get_result($check_cart_stmt);

error_log("Debug - User ID: " . $_SESSION['user_id']);
error_log("Debug - Product ID: " . $product_id);
error_log("Debug - Quantity: " . $quantity);

if (mysqli_num_rows($cart_result) > 0) {
    // Aggiorna la quantità se il prodotto è già nel carrello
    $cart_item = mysqli_fetch_assoc($cart_result);
    $new_quantity = $cart_item['quantity'] + $quantity;
    
    error_log("Debug - Updating existing cart item. Current quantity: " . $cart_item['quantity'] . ", New quantity: " . $new_quantity);
    
    // Verifica se la nuova quantità totale non supera lo stock
    if ($new_quantity > $product['stock_quantity']) {
        error_log("Debug - Not enough stock. Requested: " . $new_quantity . ", Available: " . $product['stock_quantity']);
        setFlashMessage('error', 'Not enough stock available for the requested quantity.');
        header("Location: products.php");
        exit();
    }
    
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_item['id']);
    $update_result = mysqli_stmt_execute($update_stmt);
    error_log("Debug - Update result: " . ($update_result ? "Success" : "Failed - " . mysqli_error($conn)));
    
    setFlashMessage('success', 'Cart updated successfully.');
} else {
    // Inserisci il nuovo prodotto nel carrello
    error_log("Debug - Inserting new cart item");
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "iii", $_SESSION['user_id'], $product_id, $quantity);
    $insert_result = mysqli_stmt_execute($insert_stmt);
    error_log("Debug - Insert result: " . ($insert_result ? "Success" : "Failed - " . mysqli_error($conn)));
    
    setFlashMessage('success', 'Product added to cart successfully.');
}

// Redirect alla pagina del carrello
header("Location: cart.php");
exit(); 