<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="mark_attendance.css">
</head>
<body>
    <div class="container">
        <h2>Mark Attendance</h2>

        <form method="post" action="start_attendance.php" class="attendance-form">
            <div class="form-group">
                <label for="class">Class:</label>
                <input type="text" id="class" name="class" required>
            </div>

            <div class="form-group">
                <label for="section">Section:</label>
                <input type="text" id="section" name="section" required>
            </div>

            <div class="form-group">
                <label for="topic">Topic:</label>
                <input type="text" id="topic" name="topic" required>
            </div>

            <button type="submit" class="btn">Start Attendance</button>
        </form>

        <div class="back-link">
            <a href="dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
