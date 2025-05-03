<?php
require_once "config/session.php";

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
header("Location: index.php");
exit();