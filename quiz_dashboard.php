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
    <div id="particles-js"></div>
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
            <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
          "particles": {
            "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": "#ffffff" },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.5, "random": false },
            "size": { "value": 3, "random": true },
            "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
            "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
          },
          "interactivity": {
            "detect_on": "canvas",
            "events": {
              "onhover": { "enable": true, "mode": "repulse" },
              "onclick": { "enable": true, "mode": "push" },
              "resize": true
            }
          },
          "retina_detect": true
        });
    </script>
</body>
</html>
