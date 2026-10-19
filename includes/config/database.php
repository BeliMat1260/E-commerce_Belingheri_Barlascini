<?php

$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD');
if ($password === false) {
    $password = '';
}
$database = getenv('DB_NAME') ?: 'my_mattiabelingheri';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Database connection failed.');
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?> 