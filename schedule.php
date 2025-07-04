<?php
session_start();
require 'db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'teacher') {
  header("Location: login.php");
  exit();
}

$teacher_email = $_SESSION['email'];
$teacher_id = null;
$name = $_SESSION['name'];

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$stmt->bind_result($teacher_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT t.day_of_week, t.start_time, t.end_time, t.room_number, c.name AS course_name, c.department, c.semester FROM timetables t JOIN courses c ON t.course_id = c.id WHERE t.teacher_id = ? ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), t.start_time ASC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Schedule</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
    body {
      background: #8D6E63;
      color: white;
      padding: 40px;
      min-height: 100vh;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 2.5rem;
      color: gold;
    }
    .schedule-card {
      background: rgba(255,255,255,0.1);
      padding: 20px;
      border-radius: 20px;
      margin-bottom: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      animation: slideFade 0.6s ease-in-out;
    }
    .schedule-card h3 {
      margin-bottom: 10px;
      font-size: 1.5rem;
      color: #f0f0f0;
    }
    .info {
      font-size: 1rem;
      color: #ddd;
    }
    @keyframes slideFade {
      0% { transform: translateY(20px); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <h1><i class="fas fa-calendar-alt"></i> My Teaching Schedule</h1>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="schedule-card">
      <h3><?= htmlspecialchars($row['course_name']) ?> (<?= htmlspecialchars($row['department']) ?> - <?= htmlspecialchars($row['semester']) ?>)</h3>
      <p class="info">
        <strong>Day:</strong> <?= $row['day_of_week'] ?> | 
        <strong>Time:</strong> <?= substr($row['start_time'], 0, 5) ?> - <?= substr($row['end_time'], 0, 5) ?> | 
        <strong>Room:</strong> <?= htmlspecialchars($row['room_number']) ?>
      </p>
    </div>
  <?php endwhile; ?>

</body>
</html>