<?php
session_start();
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'student') {
  header("Location: login.php");
  exit();
}

$name = $_SESSION['name'];
$initial = strtoupper($name[0]);

require 'db.php';

$sql = "
SELECT 
  c.id AS course_id,
  c.name AS course_name, 
  u.name AS instructor, 
  t.day_of_week, 
  t.start_time, 
  t.end_time, 
  t.room_number,
  t.id AS timetable_id
FROM timetables t
JOIN courses c ON t.course_id = c.id
JOIN users u ON t.teacher_id = u.id
ORDER BY t.day_of_week, t.start_time;
";

$result = $conn->query($sql);
$todayName = strtolower(date('l'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Attendance Management | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #8D6E63;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      padding: 20px;
    }
    .attendance-card {
      background: rgba(255, 255, 255, 0.15);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .attendance-card h4 { margin-bottom: 5px; }
    .attendance-card p { color: #ddd; }
    .attendance-card button {
      background-color: #00c853;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }
    .attendance-card button:disabled {
      background-color: #777;
      cursor: not-allowed;
    }
    footer {
      margin-top: 30px;
      background: #8D6E63;
      text-align: center;
      padding: 15px;
      color: #ccc;
    }
    footer a { color: #ccc; margin: 0 10px; }

    #message-box {
      position: fixed;
      top: 15px;
      left: 50%;
      transform: translateX(-50%);
      background: #00c853;
      color: white;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: bold;
      display: none;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>

<h2>📅 Check In for Your Classes</h2>

<!-- Placeholder for success message -->
<div id="message-box"></div>

<?php while ($row = $result->fetch_assoc()): 
  $classDay = strtolower($row['day_of_week']);
  $courseId = $row['course_id'];
  $timetableId = $row['timetable_id'];
  $encodedId = base64_encode("{$courseId}_{$timetableId}");
?>
  <div class="attendance-card" id="attendance-<?php echo $encodedId; ?>">
    <h4><?php echo htmlspecialchars($row['course_name']); ?></h4>
    <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['instructor']); ?></p>
    <p><strong>Day:</strong> <?php echo htmlspecialchars($row['day_of_week']); ?></p>
    <p><strong>Time:</strong> <?php echo date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])); ?></p>
    <p><strong>Room:</strong> <?php echo htmlspecialchars($row['room_number']); ?></p>

    <?php if ($todayName === $classDay): ?>
      <button onclick="markAttendance(<?php echo $courseId; ?>, <?php echo $timetableId; ?>)">Mark Attendance</button>
    <?php else: ?>
      <button disabled>🚫 Not Today</button>
    <?php endif; ?>
  </div>
<?php endwhile; ?>

<footer>
  <p>&copy; 2025 University of Messina - Student Attendance Panel</p>
  
</footer>

<script>
function showMessage(msg, isError = false) {
  const box = document.getElementById("message-box");
  box.textContent = msg;
  box.style.backgroundColor = isError ? "#e53935" : "#00c853";
  box.style.display = "block";
  setTimeout(() => {
    box.style.display = "none";
  }, 2500);
}

function markAttendance(courseId, timetableId) {
  const encodedId = btoa(`${courseId}_${timetableId}`);
  fetch("mark_attendance.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `course_id=${encodeURIComponent(courseId)}&timetable_id=${encodeURIComponent(timetableId)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const card = document.getElementById(`attendance-${encodedId}`);
      const button = card.querySelector("button");
      button.textContent = "✅ Marked Present";
      button.disabled = true;
      button.style.backgroundColor = "#00897b";
      showMessage("✅ You’ve successfully marked your attendance!");
    } else {
      const msg = data.message || "Something went wrong.";
      showMessage("❌ " + msg, true);
    }
  })
  .catch((error) => {
    console.error("Network ERROR:", error);
    showMessage("⚠️ Network error. Please try again.", true);
  });
}
</script>

</body>
</html>