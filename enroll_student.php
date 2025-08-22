<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $class   = trim($_POST['class']);
    $section = trim($_POST['section']);

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir  = "uploads/students/";
        $fileName   = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        // Allow only jpg/png/jpeg
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($fileType, ["jpg", "jpeg", "png"])) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                // Insert into DB
                $stmt = $mysqli->prepare("
                    INSERT INTO students (name, email, class, section, photo) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssss", $name, $email, $class, $section, $fileName);

                if ($stmt->execute()) {
                    $message = "✅ Student enrolled successfully!";
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "❌ Failed to upload file.";
            }
        } else {
            $message = "❌ Only JPG/PNG files are allowed.";
        }
    } else {
        $message = "❌ Please upload a student photo.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="enroll.css">
</head>
<body>
    <div class="container">
        <h2>Enroll Student</h2>

        <form method="post" enctype="multipart/form-data" class="enroll-form">
            <div class="form-group">
                <input type="text" name="name" placeholder="Student Name" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Student Email" required>
            </div>

            <div class="form-group">
                <input type="text" name="class" placeholder="Class" required>
            </div>

            <div class="form-group">
                <input type="text" name="section" placeholder="Section" required>
            </div>

            <div class="form-group">
                <input type="file" name="photo" accept="image/*" required>
            </div>

            <button type="submit" class="btn">Enroll Student</button>
        </form>

        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="back-link">
            <a href="dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
