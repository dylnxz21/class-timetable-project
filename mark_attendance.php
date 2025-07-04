<?php
// ✅ DEBUGGING ON
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start(); // capture ANY accidental output

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $student_name = $_SESSION['name'];
    $student_email = $_SESSION['email'];
    $timetable_id = intval($_POST['timetable_id']);
    $course_id = intval($_POST['course_id']); // ✅ also using this from POST
    $date_attended = date('Y-m-d');
    $status = 'Present';

    // ✅ Get student ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE name = ? AND email = ?");
    $stmt->bind_param("ss", $student_name, $student_email);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if (!$student_id) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit();
    }

    // ✅ Check if class is scheduled today
    $day_of_week = date('l');
    $stmt = $conn->prepare("
        SELECT id FROM timetables 
        WHERE id = ? AND course_id = ? AND day_of_week = ?
    ");
    $stmt->bind_param("iis", $timetable_id, $course_id, $day_of_week);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No class scheduled today']);
        exit();
    }
    $stmt->close();

    // ✅ Check if already marked
    $stmt = $conn->prepare("
        SELECT id FROM attendance 
        WHERE student_id = ? AND timetable_id = ? AND date_attended = ?
    ");
    $stmt->bind_param("iis", $student_id, $timetable_id, $date_attended);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Already marked']);
        exit();
    }
    $stmt->close();

    // ✅ Insert attendance
    $stmt = $conn->prepare("
        INSERT INTO attendance (student_id, timetable_id, date_attended, status)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $student_id, $timetable_id, $date_attended, $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// ✅ FINAL check for rogue output
$output = ob_get_clean();
if (!empty($output)) {
    echo json_encode(['success' => false, 'message' => 'Extra output: ' . $output]);
}
?>