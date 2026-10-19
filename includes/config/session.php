<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $appRoot = realpath(dirname(__DIR__, 2));
    $base = '';
    if ($documentRoot && $appRoot) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $appRoot = str_replace('\\', '/', $appRoot);
        if (strpos($appRoot, $documentRoot) === 0) {
            $base = substr($appRoot, strlen($documentRoot));
        }
    }
    define('BASE_URL', rtrim($base, '/'));
}

function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path === '' ? '/' : '/' . $path);
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current username
 * @return string The username or 'Guest' if not logged in
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'Guest';
}

/**
 * Set a flash message
 * @param string $type The type of message (success, error, info, warning)
 * @param string $message The message to display
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null The flash message or null if none exists
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Display flash message if exists
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = $flash['type'];
        $message = $flash['message'];
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
    }
}

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 300) {
    if (!session_regenerate_id(true)) {
        error_log("Failed to regenerate session ID");
    }
    $_SESSION['last_regeneration'] = time();
}
?> 