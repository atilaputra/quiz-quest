<?php
//test
// login.php
session_start();
require_once 'db_connect.php'; // Include the database connection

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get and sanitize input data (username can be username or email)
    $login_input = $conn->real_escape_string(trim($_POST['username'])); 
    $password = $_POST['password']; 
    
    $error = '';

    // 2. Prepare the SQL to find the user by username OR email
    $sql = "SELECT user_id, username, password_hash FROM users WHERE username = ? OR email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Bind the result variables
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();
        
        // 3. Verify the password
        if (password_verify($password, $hashed_password)) {
            
            // Password is correct, start a new session
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;
            
            // Redirect user to the main quiz selection page
            header("location: quiz_dashboard.php"); // *** NEXT STEP: We'll create this file! ***
            exit;

        } else {
            // Password is not valid
            $error = "Invalid username or password.";
        }
    } else {
        // User not found
        $error = "Invalid username or password.";
    }
    
    $stmt->close();

    // 4. If there's an error, handle it
    if (!empty($error)) {
        // A simple way to show the error
        echo "<h2>Login Error</h2>";
        echo "<p style='color:red;'>$error</p>";
        echo "<p><a href='login.html'>Go back to login</a></p>";
    }
} else {
    // If someone tries to access this page directly without POST
    header("location: login.html");
    exit;
}

$conn->close();
?>
