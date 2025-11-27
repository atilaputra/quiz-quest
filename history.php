<?php
// history.php
session_start();
require_once 'db_connect.php';

// 1. SECURITY CHECK - Ensure user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// 2. DATA FETCHING - Retrieve all quiz attempts for the logged-in user
$sql = "SELECT attempt_id, subject, score, total_questions, time_start, duration_seconds 
        FROM quiz_attempts 
        WHERE user_id = ? 
        ORDER BY time_start DESC"; // Show newest results first

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // 'i' for integer (user_id)
$stmt->execute();
$result = $stmt->get_result();
$attempts = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($username); ?> - History</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* History-specific styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .score-success {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="container" style="max-width: 900px;">
        <h2>📝 Your Quiz History</h2>
        <p>Viewing results for <?php echo htmlspecialchars($username); ?>.</p>
        
        <hr>

        <?php if (count($attempts) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Date & Time</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; foreach ($attempts as $attempt): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($attempt['subject']); ?></td>
                        <td class="score-success">
                            <?php echo htmlspecialchars($attempt['score']) . ' / ' . htmlspecialchars($attempt['total_questions']); ?>
                        </td>
                    <td>
                        <?php 
                        $timestamp = strtotime($attempt['time_start']);
                        // Check if strtotime failed (returns false) OR if the date is ridiculously old (like year 0000)
                        if ($timestamp === false || $timestamp < 0) {
                            echo "Data Invalid"; 
                        } else {
                            echo date('Y-m-d H:i:s', $timestamp);
                        }
                        ?>
                    </td>
                        <td><?php echo htmlspecialchars($attempt['duration_seconds']); ?> seconds</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="padding: 20px; border: 1px dashed #ccc; text-align: center;">
                <p>You haven't completed any quizzes yet!</p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <a href="quiz_dashboard.php" class="button primary">Back to Dashboard</a>
        </div>
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
