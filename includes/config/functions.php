<?php
// Cart functions
function getCartCount($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['count'];
}

// Product functions
function getProductPrice($price) {
    return number_format($price, 2);
}

function getProductImage($image_url) {
    return $image_url ?: 'assets/images/no-image.jpg';
}

// Security functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// URL functions
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'];
}

function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

// Format functions
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

// Error handling
function handleError($message, $type = 'error') {
    setFlashMessage($type, $message);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Database helper functions
function executeQuery($conn, $sql, $params = [], $types = '') {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }

    return $stmt;
}

function fetchAll($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Error getting result: " . mysqli_stmt_error($stmt));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchOne($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Error getting result: " . mysqli_stmt_error($stmt));
    }
    return mysqli_fetch_assoc($result);
} 