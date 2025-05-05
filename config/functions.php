<?php
// Database connection
require_once 'database.php';

// Session management
require_once 'session.php';

/**
 * Get current username
 */
function getCurrentUsername() {
    if (!isLoggedIn()) {
        return '';
    }
    
    global $conn;
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    return $user ? $user['username'] : '';
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

/**
 * Format price
 */
function formatPrice($price) {
    return number_format($price, 2, '.', ',');
}

/**
 * Redirect with flash message
 */
function redirectWithMessage($url, $type, $message) {
    setFlashMessage($type, $message);
    header("Location: $url");
    exit();
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
} 