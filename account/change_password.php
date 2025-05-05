<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to change your password.');
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    header("Location: /E-commerce_Belingheri_Barlascini/account/account.php");
    exit();
}

// Recupera e valida i dati
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Validazione dei dati
$errors = [];

// Verifica la password corrente
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!password_verify($current_password, $user['password'])) {
    $errors[] = 'Current password is incorrect.';
}

// Validazione nuova password
if (strlen($new_password) < 8) {
    $errors[] = 'New password must be at least 8 characters long.';
}

if (!preg_match('/[A-Z]/', $new_password)) {
    $errors[] = 'New password must contain at least one uppercase letter.';
}

if (!preg_match('/[a-z]/', $new_password)) {
    $errors[] = 'New password must contain at least one lowercase letter.';
}

if (!preg_match('/[0-9]/', $new_password)) {
    $errors[] = 'New password must contain at least one number.';
}

if (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
    $errors[] = 'New password must contain at least one special character.';
}

if ($new_password !== $confirm_password) {
    $errors[] = 'New passwords do not match.';
}

if (!empty($errors)) {
    setFlashMessage('error', implode('<br>', $errors));
    header("Location: /E-commerce_Belingheri_Barlascini/account/account.php");
    exit();
}

// Aggiorna la password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$update_sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt, "si", $hashed_password, $_SESSION['user_id']);

if (mysqli_stmt_execute($stmt)) {
    setFlashMessage('success', 'Password changed successfully.');
} else {
    setFlashMessage('error', 'Failed to change password. Please try again.');
    error_log("Password change error: " . mysqli_error($conn));
}

header("Location: /E-commerce_Belingheri_Barlascini/account/account.php");
exit(); 