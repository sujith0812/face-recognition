<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class   = $_POST['class'];
    $section = $_POST['section'];
    $topic   = $_POST['topic'];

    // Save these to session so Python can use later
    $_SESSION['class']   = $class;
    $_SESSION['section'] = $section;
    $_SESSION['topic']   = $topic;

    // Call Python script
    $command = escapeshellcmd("python attendance.py \"$class\" \"$section\" \"$topic\"");
    $output  = shell_exec($command);

    echo "<h2>Attendance Completed</h2>";
    echo "<pre>$output</pre>";
    echo "<a href='dashboard.php'>⬅ Back to Dashboard</a>";
}
?>
