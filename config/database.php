<?php
// Database configuration
$host = "localhost";
$username = "mattiabelingheri";  // Your XAMPP username
$password = "";                  // Your XAMPP password
$database = "my_mattiabelingheri"; // Your database name

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);

    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    // Set charset to utf8mb4
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        throw new Exception("Error setting charset: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    // Log error
    error_log("Database connection error: " . $e->getMessage());
    
    // Show user-friendly error
    die("Sorry, there was a problem connecting to the database. Please try again later.");
}
?> 