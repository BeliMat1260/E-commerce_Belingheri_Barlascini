<?php
require_once __DIR__ . "/../includes/config/session.php";
require_once __DIR__ . "/../includes/config/functions.php";

// Set flash message before destroying session
setFlashMessage('info', 'You have been successfully logged out.');

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: /E-commerce_Belingheri_Barlascini/index.php');
exit();