<?php
require_once __DIR__ . "/../includes/config/database.php";
require_once __DIR__ . "/../includes/config/session.php";

// Verifica se l'utente è loggato
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to manage addresses.');
    header("Location: /E-commerce_Belingheri_Barlascini/auth/login.php");
    exit();
}

// Verifica se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['address_id'])) {
    setFlashMessage('error', 'Invalid request.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

$address_id = (int)$_POST['address_id'];

// Verifica se l'indirizzo esiste e appartiene all'utente
$check_sql = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "ii", $address_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    setFlashMessage('error', 'Address not found.');
    header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
    exit();
}

// Inizia la transazione
mysqli_begin_transaction($conn);

try {
    // Un solo update con CASE per rispettare il vincolo UNIQUE
    $update_sql = "
        UPDATE addresses
        SET is_default = CASE WHEN id = ? THEN 1 ELSE 0 END
        WHERE user_id = ?
    ";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "ii", $address_id, $_SESSION['user_id']);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update default address: " . mysqli_error($conn));
    }

    // Commit della transazione
    mysqli_commit($conn);

    setFlashMessage('success', 'Default address updated successfully.');
} catch (Exception $e) {
    // Rollback in caso di errore
    mysqli_rollback($conn);
    setFlashMessage('error', 'Failed to update default address. Please try again.');
    error_log("Set default address error: " . $e->getMessage());
}

header("Location: /E-commerce_Belingheri_Barlascini/addresses/addresses.php");
exit(); 