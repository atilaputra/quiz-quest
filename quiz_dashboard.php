<?php
// quiz_dashboard.php
session_start();

// Check if the user is NOT logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // If not logged in, redirect to the login page
    header("location: login.html");
    exit;
}

// User is logged in, grab their username for a personalized welcome
$username = $_SESSION["username"]; 

// Define the subjects available for the quiz
$subjects = [
    "Science",
    "Math",
    "Geography",
    "English"
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional style for the dashboard specific layout */
        .subject-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .subject-card {
            background-color: #e9ecef;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .subject-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .subject-card a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            font-size: 1.1em;
            display: block; /* Make the whole card clickable */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Choose a subject to begin your quiz:</p>

        <div class="subject-list">
            <?php foreach ($subjects as $subject): ?>
                <div class="subject-card">
                    <a href="quiz.php?subject=<?php echo urlencode($subject); ?>">
                        Start <?php echo $subject; ?> Quiz
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <hr style="margin-top: 30px;">
        
        <div style="display: flex; justify-content: center; gap: 15px;">
            <a href="history.php" class="button" style="background-color: #17a2b8; color: white;">View History</a>
        <a href="logout.php" class="button" style="background-color: #dc3545; color: white;">Logout</a>

    </div>
</body>
</html>