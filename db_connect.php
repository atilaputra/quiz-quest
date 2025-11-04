<?php
// Database configuration for Docker
$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE') ?: 'quiz_quest';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset
$conn->set_charset("utf8mb4");
?>
