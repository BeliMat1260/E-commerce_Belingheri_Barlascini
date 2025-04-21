<?php
$host = "localhost";
$username = "mattiabelingheri";
$password = "";
$database = "my_mattiabelingheri";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?> 