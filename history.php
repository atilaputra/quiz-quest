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
</body>
</html>