<?php
// register.php
session_start();
require_once 'db_connect.php'; // Include the database connection

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get and sanitize input data
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password']; // Get plain password for hashing
    
    $error = '';

    // 2. Validate basic fields
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    }

    // 3. Check if username or email already exists
    if (empty($error)) {
        $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email is already registered.";
        }
        $stmt->close();
    }

    // 4. Proceed with insertion if no errors
    if (empty($error)) {
        // Securely hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $insert_sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($insert_sql);
        // "sss" indicates three string parameters
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            // Success: Redirect to login page
            header("location: login.html?success=registered");
            exit;
        } else {
            // Failure
            $error = "Something went wrong. Please try again later. Error: " . $conn->error;
        }
        $stmt->close();
    }
    
    // 5. If there's an error, display it (or better: redirect with the error)
    if (!empty($error)) {
        // A simple way to show the error
        echo "<h2>Registration Error</h2>";
        echo "<p style='color:red;'>$error</p>";
        echo "<p><a href='register.html'>Go back to registration</a></p>";
    }
} else {
    // If someone tries to access this page directly without POST
    header("location: register.html");
    exit;
}

$conn->close();
?>