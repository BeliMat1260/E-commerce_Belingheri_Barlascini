<?php
require_once "config/database.php";
require_once "config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to add addresses.');
    header("Location: login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    header("Location: addresses.php");
    exit();
}

// Verifica se tutti i parametri necessari sono presenti
$required_fields = ['address_name', 'address_line1', 'city', 'state', 'postal_code', 'country'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        setFlashMessage('error', 'All required fields must be filled.');
        header("Location: addresses.php");
        exit();
    }
}

// Recupera e valida i dati
$address_name = trim($_POST['address_name']);
$address_line1 = trim($_POST['address_line1']);
$address_line2 = trim($_POST['address_line2'] ?? '');
$city = trim($_POST['city']);
$state = trim($_POST['state']);
$postal_code = trim($_POST['postal_code']);
$country = trim($_POST['country']);
$is_default = isset($_POST['is_default']);

// Validazione dei dati
$errors = [];

// Validazione lunghezza campi
if (strlen($address_name) > 100) {
    $errors[] = 'Address name must be less than 100 characters.';
}

if (strlen($address_line1) > 255) {
    $errors[] = 'Address line 1 must be less than 255 characters.';
}

if (strlen($address_line2) > 255) {
    $errors[] = 'Address line 2 must be less than 255 characters.';
}

if (strlen($city) > 100) {
    $errors[] = 'City must be less than 100 characters.';
}

if (strlen($state) > 100) {
    $errors[] = 'State/Province must be less than 100 characters.';
}

if (strlen($postal_code) > 20) {
    $errors[] = 'Postal code must be less than 20 characters.';
}

if (strlen($country) > 100) {
    $errors[] = 'Country must be less than 100 characters.';
}

// Validazione caratteri speciali
if (!preg_match('/^[a-zA-Z0-9\s\-\.,]+$/', $address_name)) {
    $errors[] = 'Address name contains invalid characters.';
}

if (!preg_match('/^[a-zA-Z0-9\s\-\.,#]+$/', $address_line1)) {
    $errors[] = 'Address line 1 contains invalid characters.';
}

if ($address_line2 && !preg_match('/^[a-zA-Z0-9\s\-\.,#]+$/', $address_line2)) {
    $errors[] = 'Address line 2 contains invalid characters.';
}

if (!preg_match('/^[a-zA-Z\s\-]+$/', $city)) {
    $errors[] = 'City contains invalid characters.';
}

if (!preg_match('/^[a-zA-Z\s\-]+$/', $state)) {
    $errors[] = 'State/Province contains invalid characters.';
}

if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $postal_code)) {
    $errors[] = 'Postal code contains invalid characters.';
}

if (!preg_match('/^[a-zA-Z\s\-]+$/', $country)) {
    $errors[] = 'Country contains invalid characters.';
}

if (!empty($errors)) {
    setFlashMessage('error', implode('<br>', $errors));
    header("Location: addresses.php");
    exit();
}

// Inizia la transazione
mysqli_begin_transaction($conn);

try {
    // Se questo è l'indirizzo predefinito, rimuovi il flag predefinito da tutti gli altri indirizzi
    if ($is_default) {
        $update_sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
    }

    // Inserisci il nuovo indirizzo
    $insert_sql = "INSERT INTO addresses (user_id, address_name, address_line1, address_line2, city, state, postal_code, country, is_default) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "isssssssi", 
        $_SESSION['user_id'], 
        $address_name, 
        $address_line1, 
        $address_line2, 
        $city, 
        $state, 
        $postal_code, 
        $country, 
        $is_default
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to add address: " . mysqli_error($conn));
    }

    // Commit della transazione
    mysqli_commit($conn);

    setFlashMessage('success', 'Address added successfully.');
} catch (Exception $e) {
    // Rollback in caso di errore
    mysqli_rollback($conn);
    setFlashMessage('error', 'Failed to add address. Please try again.');
    error_log("Address addition error: " . $e->getMessage());
}

header("Location: addresses.php");
exit(); 