<?php
session_start();
if (isset($_SESSION['faculty_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	
    <title>Face Attendance System</title>
    <link rel="stylesheet" href="index.css">


    
</head>
<body>
    <div class="container">
        <h1>Welcome to Face Attendance System</h1>
        <a href="login.php" class="button">Login</a>
        <a href="register.php" class="button">Register</a>
    </div>
</body>
</html>
