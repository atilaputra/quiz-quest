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
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

<script>
    /* 1. Define your Palette Colors */
    const colors = {
        pink: "%23F3C8DD",      // Queen Pink
        purple: "%23D183A9",    // Middle Purple
        lavender: "%2371557A",  // Old Lavender
        chocolate: "%234B1535"  // Brown Chocolate
    };

    /* 2. Create the Thicker SVGs */
    /* Note: stroke-width='6' makes them thick. We replace COLOR_HERE with specific hex codes. */
    const svgXBase = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='COLOR_HERE' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E";
    const svgOBase = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='COLOR_HERE' stroke-width='6'%3E%3Ccircle cx='12' cy='12' r='9'%3E%3C/circle%3E%3C/svg%3E";

    /* 3. Generate an image for every color */
    const coloredImages = [];
    for (const key in colors) {
        coloredImages.push({ src: svgXBase.replace('COLOR_HERE', colors[key]), width: 100, height: 100 });
        coloredImages.push({ src: svgOBase.replace('COLOR_HERE', colors[key]), width: 100, height: 100 });
    }

    tsParticles.load("particles-js", {
      background: {
        color: "#3A345B" /* Background: Jacarta */
      },
      particles: {
        number: {
          value: 30, 
          density: { enable: true, area: 800 }
        },
        shape: {
          type: "image",
          image: coloredImages /* Use the mixed colors */
        },
        opacity: {
          value: { min: 0.3, max: 0.7 }, 
          animation: { enable: true, speed: 1, sync: false }
        },
        size: {
          value: { min: 20, max: 50 }, /* Slightly larger to match the thickness */
          animation: {
            enable: true, 
            speed: 3,
            mode: "auto",
            sync: false
          }
        },
        rotate: {
          value: { min: 0, max: 360 },
          animation: {
            enable: true,
            speed: 4, /* Gentle rotation */
            sync: false
          }
        },
        move: {
          enable: true,
          speed: 2, /* Gentle floating */
          direction: "none",
          random: true,
          straight: false,
          outModes: "out"
        }
      },
      interactivity: {
        events: {
          onHover: { enable: true, mode: "repulse" },
          resize: true
        },
        modes: {
           repulse: { distance: 100, duration: 0.4 }
        }
      },
      detectRetina: true
    });
</script>
</body>
</html>
