<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration - must be set before session_start()
if (session_status() === PHP_SESSION_NONE) {
    try {
        // Set session configuration before starting the session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set to 0 for local development
        ini_set('session.cookie_samesite', 'Lax'); // Changed to Lax for better compatibility
        ini_set('session.gc_maxlifetime', 1800); // 30 minutes
        ini_set('session.cookie_lifetime', 1800); // 30 minutes
        
        // Start session
        if (!session_start()) {
            throw new Exception("Failed to start session");
        }
    } catch (Exception $e) {
        // Log error
        error_log("Session error: " . $e->getMessage());
        
        // Show user-friendly error
        die("Sorry, there was a problem starting your session. Please try again later.");
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