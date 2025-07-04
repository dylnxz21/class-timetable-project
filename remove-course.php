<?php
session_start();
require 'db.php';

// ✅ Proper session check for admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Handle course removal
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // ✅ Delete the course from the database
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        // ✅ Redirect back with a success message
        header("Location: manage-courses.php?success=Course removed successfully!");
        exit();
    } else {
        // ✅ Show error if delete fails
        header("Location: manage-courses.php?error=Error removing course. Please try again.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // ✅ Redirect back if no course ID is provided
    header("Location: manage-courses.php");
    exit();
}
?>