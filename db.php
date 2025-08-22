<?php
// db.php - database connection (include this in other files)
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'class_attendance_system';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
