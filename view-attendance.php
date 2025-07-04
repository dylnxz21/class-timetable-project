<?php
session_start();
require 'db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Unauthorized access'); window.location.href='login.php';</script>";
    exit();
}

$teacher_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$stmt->bind_result($teacher_id);
$stmt->fetch();
$stmt->close();

// 👇 Get all scheduled classes for this teacher
$scheduleQuery = "
  SELECT t.id AS timetable_id, t.day_of_week, t.start_time, t.end_time, t.room_number,
         c.name AS course_name, c.id AS course_id
  FROM timetables t
  JOIN courses c ON t.course_id = c.id
  WHERE t.teacher_id = ?
";
$scheduleStmt = $conn->prepare($scheduleQuery);
$scheduleStmt->bind_param("i", $teacher_id);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();

// 🔁 Loop through each timetable and collect attendance data
$allAttendance = [];

while ($row = $scheduleResult->fetch_assoc()) {
    $timetable_id = $row['timetable_id'];
    $course_name = $row['course_name'];

    // 👇 Get students for that course
    $studentQuery = "
      SELECT u.id, u.name
      FROM users u
      JOIN attendance a ON a.student_id = u.id
      WHERE a.timetable_id = ?
    ";
    $studentStmt = $conn->prepare($studentQuery);
    $studentStmt->bind_param("i", $timetable_id);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();

    // 👇 If attendance exists for that timetable, list it
    $attendanceQuery = "
      SELECT a.id, u.name AS student_name, a.date_attended, a.status
      FROM attendance a
      JOIN users u ON a.student_id = u.id
      WHERE a.timetable_id = ?
      ORDER BY a.date_attended DESC
    ";
    $attendanceStmt = $conn->prepare($attendanceQuery);
    $attendanceStmt->bind_param("i", $timetable_id);
    $attendanceStmt->execute();
    $attendanceResult = $attendanceStmt->get_result();

    while ($aRow = $attendanceResult->fetch_assoc()) {
        $allAttendance[] = [
            'id' => $aRow['id'],
            'student_name' => $aRow['student_name'],
            'course' => $course_name,
            'date' => $aRow['date_attended'],
            'status' => $aRow['status']
        ];
    }

    $attendanceStmt->close();
}

$scheduleStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📋 Attendance Records | Teacher Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #8D6E63;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      padding: 40px;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      animation: fadeInDown 0.7s ease-out;
    }
    .table-container {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 30px;
      animation: fadeInUp 0.8s ease-in-out;
      box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }
    table {
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      color: #333;
      animation: zoomIn 0.5s ease-out;
    }
    thead {
      background-color: #8D6E63;
      color: #fff;
    }
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes zoomIn {
      from { transform: scale(0.95); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="table-container">
    <h2>📋 Attendance Records</h2>
    <table class="table table-hover text-center">
      <thead>
        <tr>
          <th>ID</th>
          <th>Student Name</th>
          <th>Course</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($allAttendance) > 0): ?>
          <?php foreach ($allAttendance as $record): ?>
          <tr>
            <td><?= htmlspecialchars($record['id']) ?></td>
            <td><?= htmlspecialchars($record['student_name']) ?></td>
            <td><?= htmlspecialchars($record['course']) ?></td>
            <td><?= htmlspecialchars($record['date']) ?></td>
            <td class="fw-bold <?= $record['status'] === 'Present' ? 'text-success' : 'text-danger' ?>">
              <?= htmlspecialchars($record['status']) ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-muted">No attendance records found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>