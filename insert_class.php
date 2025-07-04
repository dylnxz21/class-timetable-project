<?php
// Enable all errors for debugging (remove in production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

// 🔐 Make sure it's a teacher
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('❌ Access denied. Only teachers can add classes.'); window.location.href='login.php';</script>";
    exit();
}

// 🌟 Grab logged-in teacher's info
$teacher_email = $_SESSION['email'];
$teacher_id = null;
$course_id = null;

// 🧠 Get the teacher's ID
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$stmt->bind_result($teacher_id);
$stmt->fetch();
$stmt->close();

// 🧠 Get the course assigned to the teacher (based on `courses` table)
$stmt = $conn->prepare("SELECT id FROM courses WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($course_id);
$stmt->fetch();
$stmt->close();

// ⛔ If no course assigned, stop here
if (!$course_id) {
    echo "<script>alert('⚠️ No course assigned to you yet. Please contact admin.'); window.location.href='teacher-dashboard.php';</script>";
    exit();
}

// 💾 When teacher submits class info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_number = trim($_POST['room_number']);

    // ✅ Basic validation
    if (empty($day_of_week) || empty($start_time) || empty($end_time) || empty($room_number)) {
        echo "<script>alert('❌ All fields are required!'); window.location.href='teacher-dashboard.php';</script>";
        exit();
    }

    // 🧠 Insert class into timetable
    $stmt = $conn->prepare("INSERT INTO timetables (course_id, teacher_id, day_of_week, start_time, end_time, room_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $course_id, $teacher_id, $day_of_week, $start_time, $end_time, $room_number);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Class added successfully!'); window.location.href='teacher-dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Failed to add class: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>