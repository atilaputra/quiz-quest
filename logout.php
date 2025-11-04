<?php
// logout.php
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Destroy the session
session_destroy();

// 3. Redirect to the login page or the home page
header("location: index.html");
exit;
?>