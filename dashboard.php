<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
<link href="dashboard.css" rel="stylesheet">
</head>
<body>
    <h1>Faculty Dashboard</h1>
    <p>Welcome! You are logged in.</p>
    <ul>
        <li><a href="enroll_student.php">Enroll Student</a></li>
        <li><a href="mark_attendance.php">Mark Attendance</a></li>
    
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>
