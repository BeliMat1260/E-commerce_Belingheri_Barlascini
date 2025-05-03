<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to add items to your cart.');
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $user_id = $_SESSION['user_id'];

    // Validazione base
    if ($product_id <= 0 || $quantity <= 0) {
        setFlashMessage('error', 'Invalid product or quantity.');
        header("Location: /E-commerce_Belingheri_Barlascini/products.php");
        exit();
    }

    // Verifica se il prodotto esiste e ha stock disponibile
    $check_sql = "SELECT stock_quantity FROM products WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $product_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $product = mysqli_fetch_assoc($result);

    if (!$product || $product['stock_quantity'] < $quantity) {
        setFlashMessage('error', 'Product is not available in the requested quantity.');
        header("Location: /E-commerce_Belingheri_Barlascini/products.php");
        exit();
    }

    // Verifica se il prodotto è già nel carrello
    $check_cart_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $check_cart_stmt = mysqli_prepare($conn, $check_cart_sql);
    mysqli_stmt_bind_param($check_cart_stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check_cart_stmt);
    $cart_result = mysqli_stmt_get_result($check_cart_stmt);
    $cart_item = mysqli_fetch_assoc($cart_result);

    if ($cart_item) {
        // Aggiorna la quantità se il prodotto è già nel carrello
        $new_quantity = $cart_item['quantity'] + $quantity;
        if ($new_quantity > $product['stock_quantity']) {
            setFlashMessage('error', 'Not enough stock available.');
            header("Location: /E-commerce_Belingheri_Barlascini/products.php");
            exit();
        }
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "iii", $new_quantity, $user_id, $product_id);
        mysqli_stmt_execute($update_stmt);
    } else {
        // Inserisci il nuovo prodotto nel carrello
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "iii", $user_id, $product_id, $quantity);
        mysqli_stmt_execute($insert_stmt);
    }

    setFlashMessage('success', 'Product added to cart successfully.');
    header("Location: /E-commerce_Belingheri_Barlascini/cart/cart.php");
    exit();
} else {
    // Se non è una richiesta POST, reindirizza alla pagina dei prodotti
    header("Location: /E-commerce_Belingheri_Barlascini/products.php");
    exit();
}
?> 