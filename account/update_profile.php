<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to update your profile.');
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
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone'] ?? '');

// Validazione dei dati
$errors = [];

// Validazione nome e cognome
if (empty($first_name) || strlen($first_name) > 50) {
    $errors[] = 'First name must be between 1 and 50 characters.';
}

if (empty($last_name) || strlen($last_name) > 50) {
    $errors[] = 'Last name must be between 1 and 50 characters.';
}

// Validazione email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
} else {
    // Verifica se l'email è già in uso da un altro utente
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "si", $email, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $errors[] = 'This email is already in use by another account.';
    }
}

// Validazione telefono
if (!empty($phone) && !preg_match('/^\+?[0-9\s-]{6,20}$/', $phone)) {
    $errors[] = 'Please enter a valid phone number.';
}

if (!empty($errors)) {
    setFlashMessage('error', implode('<br>', $errors));
    header("Location: /E-commerce_Belingheri_Barlascini/account/account.php");
    exit();
}

// Aggiorna il profilo
$update_sql = "UPDATE users SET 
               first_name = ?, 
               last_name = ?, 
               email = ?, 
               phone = ? 
               WHERE id = ?";
$stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt, "ssssi", 
    $first_name, 
    $last_name, 
    $email, 
    $phone,
    $_SESSION['user_id']
);

if (mysqli_stmt_execute($stmt)) {
    setFlashMessage('success', 'Profile updated successfully.');
} else {
    setFlashMessage('error', 'Failed to update profile. Please try again.');
    error_log("Profile update error: " . mysqli_error($conn));
}

header("Location: /E-commerce_Belingheri_Barlascini/account/account.php");
exit(); 