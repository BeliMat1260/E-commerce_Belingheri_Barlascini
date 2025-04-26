<?php
// Session configuration - must be set before session_start()
if (session_status() === PHP_SESSION_NONE) {
    // Set session configuration before starting the session
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    
    // Start session
    session_start();
}

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

// Function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Function to get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
?> 