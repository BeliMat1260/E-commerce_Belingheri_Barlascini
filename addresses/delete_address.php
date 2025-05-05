<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to manage addresses.');
    header("Location: /E-commerce_Belingheri_Barlascini/login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

// Verifica se l'ID dell'indirizzo è presente
if (!isset($_POST['address_id']) || !is_numeric($_POST['address_id'])) {
    setFlashMessage('error', 'Invalid address ID.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

$address_id = (int)$_POST['address_id'];

// Verifica se l'indirizzo esiste e appartiene all'utente
$check_sql = "SELECT id, is_default FROM addresses WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "ii", $address_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    setFlashMessage('error', 'Address not found or you do not have permission to delete it.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

$address = mysqli_fetch_assoc($result);

// Non permettere l'eliminazione dell'indirizzo predefinito
if ($address['is_default']) {
    setFlashMessage('error', 'Cannot delete the default address. Please set another address as default first.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

// Verifica se ci sono altri indirizzi disponibili
$count_sql = "SELECT COUNT(*) as total FROM addresses WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$count = mysqli_fetch_assoc($count_result)['total'];

if ($count <= 1) {
    setFlashMessage('error', 'Cannot delete the last address. Please add another address first.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

// Inizia la transazione
mysqli_begin_transaction($conn);

try {
    // Elimina l'indirizzo
    $delete_sql = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "ii", $address_id, $_SESSION['user_id']);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to delete address: " . mysqli_error($conn));
    }

    // Verifica se l'eliminazione è avvenuta con successo
    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception("No address was deleted.");
    }

    // Commit della transazione
    mysqli_commit($conn);

    setFlashMessage('success', 'Address deleted successfully.');
} catch (Exception $e) {
    // Rollback in caso di errore
    mysqli_rollback($conn);
    setFlashMessage('error', 'Failed to delete address. Please try again.');
    error_log("Address deletion error: " . $e->getMessage());
}

header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
exit(); 