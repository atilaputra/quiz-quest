<?php
// quiz.php
session_start();
require_once 'db_connect.php';

// 1. SECURITY CHECK - Ensure user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// 2. SUBJECT VALIDATION - Get subject from URL
if (!isset($_GET['subject']) || empty($_GET['subject'])) {
    // If no subject is specified, go back to dashboard
    header("location: quiz_dashboard.php");
    exit;
}

$subject = $conn->real_escape_string($_GET['subject']);
$user_id = $_SESSION["user_id"]; 

// 3. DATA FETCHING - Select 5 random questions for the subject
// We use ORDER BY RAND() and LIMIT 5 to get a random set of 5 questions
$sql = "SELECT question_id, question_text, option_a, option_b, option_c, option_d 
        FROM questions 
        WHERE subject = ? 
        ORDER BY RAND() 
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $subject);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);

if (count($questions) < 5) {
    // Handle case where not enough questions are available
    $error_message = "Not enough questions for the '{$subject}' quiz yet!";
    // If you prefer to redirect: header("location: quiz_dashboard.php?error=noquestions"); exit;
}

$stmt->close();
$conn->close();

// --- Quiz settings ---
$quiz_duration_minutes = 5; // User will have 5 minutes to complete the quiz
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject); ?> Quiz</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Quiz-specific styling */
        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .quiz-timer {
            font-size: 1.5em;
            font-weight: bold;
            color: #dc3545;
        }
        .question-block {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .options label {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .options label:hover {
            background-color: #e9ecef;
        }
        /* Hide default radio button */
        .options input[type="radio"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="container" style="max-width: 800px;">
        
        <div class="quiz-header">
            <h2><?php echo htmlspecialchars($subject); ?> Quiz</h2>
            <div class="quiz-timer">Time Left: <span id="timer"></span></div>
        </div>

        <?php if (isset($error_message)): ?>
            <div style="color: red; padding: 15px; border: 1px solid red; border-radius: 4px;"><?php echo $error_message; ?></div>
            <p><a href="quiz_dashboard.php">Go back to dashboard</a></p>
        <?php else: ?>

            <form id="quizForm" action="submit_quiz.php" method="POST">
                <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                <input type="hidden" name="start_time" id="start_time" value="<?php echo time(); ?>">
                <input type="hidden" name="duration_seconds" id="duration_seconds">

                <?php $q_num = 1; foreach ($questions as $question): ?>
                    <div class="question-block">
                        <p><strong><?php echo $q_num; ?>. <?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                        
                        <div class="options">
                            <label>
                                <input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="A" required>
                                A. <?php echo htmlspecialchars($question['option_a']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="B">
                                B. <?php echo htmlspecialchars($question['option_b']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="C">
                                C. <?php echo htmlspecialchars($question['option_c']); ?>
                            </label>
                            <label>
                                <input type="radio" name="answer[<?php echo $question['question_id']; ?>]" value="D">
                                D. <?php echo htmlspecialchars($question['option_d']); ?>
                            </label>
                        </div>
                    </div>
                <?php $q_num++; endforeach; ?>

                <button type="submit" class="button primary" style="width: 100%; padding: 15px; margin-top: 20px;">Submit Quiz</button>
            </form>
        
        <?php endif; ?>
    </div>

    <script>
        const DURATION_MINUTES = <?php echo $quiz_duration_minutes; ?>;
        const totalTimeSeconds = DURATION_MINUTES * 60;
        let timeRemaining = totalTimeSeconds;
        const timerElement = document.getElementById('timer');
        const durationInput = document.getElementById('duration_seconds');
        const quizForm = document.getElementById('quizForm');

        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            
            // Format time as MM:SS
            timerElement.textContent = 
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (timeRemaining <= 0) {
                // Time's up!
                clearInterval(timerInterval);
                durationInput.value = totalTimeSeconds; // Record max duration
                alert("Time's up! The quiz will be submitted automatically.");
                quizForm.submit(); // Automatically submit the form
            } else {
                timeRemaining--;
            }
        }

        // Run the timer every second
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial call to display time immediately

        // When the user manually submits, calculate the actual duration taken
        quizForm.addEventListener('submit', function() {
            // Calculate time taken: total time - time remaining
            const timeTaken = totalTimeSeconds - timeRemaining;
            durationInput.value = timeTaken;
            clearInterval(timerInterval); // Stop the timer
        });

    </script>
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
