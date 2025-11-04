<?php
// db_connect.php

// Database configuration
define('DB_SERVER', 'localhost'); // Default for XAMPP
define('DB_USERNAME', 'root');    // Default for XAMPP
define('DB_PASSWORD', '');        // Default for XAMPP (blank)
define('DB_NAME', 'quiz_quest');     // Database name

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Stop execution and display a connection error
    die("ERROR: Could not connect to the database. " . $conn->connect_error);
}

// Optional: Set character set for proper data handling
$conn->set_charset("utf8mb4");

// Note: We don't close the connection here; it will be used by the calling script.
?>