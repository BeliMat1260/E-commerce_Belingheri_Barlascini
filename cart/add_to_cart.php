<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to add items to your cart.');
    header("Location: /E-commerce_Belingheri_Barlascini/login.php");
    exit();
}

// Verifica se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Verifica se il prodotto esiste e ha abbastanza stock
    $check_sql = "SELECT stock_quantity FROM products WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $product_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['stock_quantity'] >= $quantity) {
            // Verifica se il prodotto è già nel carrello
            $cart_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $cart_stmt = mysqli_prepare($conn, $cart_sql);
            mysqli_stmt_bind_param($cart_stmt, "ii", $_SESSION['user_id'], $product_id);
            mysqli_stmt_execute($cart_stmt);
            $cart_result = mysqli_stmt_get_result($cart_stmt);
            
            if ($cart_row = mysqli_fetch_assoc($cart_result)) {
                // Aggiorna la quantità se il prodotto è già nel carrello
                $new_quantity = $cart_row['quantity'] + $quantity;
                if ($new_quantity <= $row['stock_quantity']) {
                    $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "iii", $new_quantity, $_SESSION['user_id'], $product_id);
                    mysqli_stmt_execute($update_stmt);
                    setFlashMessage('success', 'Cart updated successfully.');
                } else {
                    setFlashMessage('error', 'Not enough stock available.');
                }
            } else {
                // Inserisci il nuovo prodotto nel carrello
                $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($insert_stmt, "iii", $_SESSION['user_id'], $product_id, $quantity);
                mysqli_stmt_execute($insert_stmt);
                setFlashMessage('success', 'Item added to cart successfully.');
            }
        } else {
            setFlashMessage('error', 'Not enough stock available.');
        }
    } else {
        setFlashMessage('error', 'Product not found.');
    }
    
    // Redirect alla pagina del prodotto
    header("Location: /E-commerce_Belingheri_Barlascini/pages/product.php?id=" . $product_id);
    exit();
} else {
    // Se il form non è stato inviato correttamente, redirect alla home
    header("Location: /E-commerce_Belingheri_Barlascini/index.php");
    exit();
}
?> 