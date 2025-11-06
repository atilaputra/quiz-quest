<?php
// Database configuration for Docker
$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE') ?: 'quiz_quest';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

// Create mysqli instance
$conn = mysqli_init();

if (!$conn) {
    die('mysqli_init failed');
}

// Set SSL options for Azure MySQL (required)
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

// Disable SSL certificate verification (for Azure)
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Connect to database with SSL
if (!$conn->real_connect($host, $username, $password, $database, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
$conn->set_charset("utf8mb4");
