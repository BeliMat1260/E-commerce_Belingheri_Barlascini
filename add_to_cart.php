<?php
session_start();
require_once "config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add or update product quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    
    // Redirect back to products page with success message
    $_SESSION['message'] = 'Product added to cart successfully!';
    header('Location: products.php');
    exit();
} else {
    // Invalid request
    header('Location: products.php');
    exit();
} 