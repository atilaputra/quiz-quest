<?php
// submit_quiz.php
session_start();
require_once 'db_connect.php';

// 1. SECURITY CHECK - Ensure user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// Check if form data was submitted via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: quiz_dashboard.php");
    exit;
}

// 2. DATA RETRIEVAL
$user_id = $_SESSION["user_id"];
$subject = $conn->real_escape_string($_POST['subject']);
$start_time_unix = (int)$_POST['start_time'];
$duration_seconds = (int)$_POST['duration_seconds'];
$user_answers = $_POST['answer'] ?? []; // Array of user's answers (question_id => answer)

// Convert UNIX timestamp back to MySQL DATETIME format
$time_start_db = date('Y-m-d H:i:s', $start_time_unix);

$question_ids = array_keys($user_answers);
$total_questions = count($question_ids);

if (empty($question_ids)) {
    // Should not happen if all questions were required, but good safety check
    $score_message = "You didn't answer any questions!";
    $final_score = 0;
} else {
    
    // 3. CORRECT ANSWERS FETCH
    // Create placeholders for the question IDs (?, ?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    
    // SQL to fetch the correct answers for only the questions the user was asked
    $sql = "SELECT question_id, correct_answer FROM questions WHERE question_id IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    
    // Dynamically bind parameters (all are integers)
    $types = str_repeat('i', count($question_ids)); // 'iiiii' for 5 questions
    $stmt->bind_param($types, ...$question_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $correct_answers_db = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $correct_answers_map = [];
    foreach ($correct_answers_db as $row) {
        $correct_answers_map[$row['question_id']] = $row['correct_answer'];
    }

    // 4. SCORING
    $correct_count = 0;
    foreach ($user_answers as $q_id => $u_answer) {
        // Check if the user's submitted answer matches the correct answer from the DB
        if (isset($correct_answers_map[$q_id]) && $u_answer === $correct_answers_map[$q_id]) {
            $correct_count++;
        }
    }
    
    $final_score = $correct_count;
    $score_message = "You scored {$final_score} out of {$total_questions}!";
    // 5. DATABASE RECORDING (Insert attempt into quiz_attempts table)
    $insert_sql = "INSERT INTO quiz_attempts (user_id, subject, score, total_questions, time_start, duration_seconds) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $conn->prepare($insert_sql);
    // "sisisi" -> user_id (int), subject (string), score (int), total_questions (int), time_start (string/datetime), duration_seconds (int)
    $stmt_insert->bind_param("isiisi", $user_id, $subject, $final_score, $total_questions, $time_start_db, $duration_seconds);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="particles-js"></div>
    <div class="container">
        <h2>🎉 Quiz Completed!</h2>
        <h1 style="color: #007bff;"><?php echo htmlspecialchars($subject); ?> Results</h1>
        
        <div style="font-size: 1.5em; margin: 30px 0; padding: 20px; border: 2px solid #007bff; border-radius: 8px;">
            <?php echo $score_message; ?>
        </div>

        <p>Your attempt has been recorded.</p>
        <p>Time taken: <?php echo $duration_seconds; ?> seconds.</p>
        
        <hr>
        
        <a href="quiz_dashboard.php" class="button primary">Take Another Quiz</a>
        <a href="logout.php" class="button" style="background-color: #6c757d; color: white;">Logout</a>

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
