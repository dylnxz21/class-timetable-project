<?php
session_start();
if (!isset($_SESSION['name']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'student')) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$initial = strtoupper($name[0]);

require 'db.php';

// Fetch updated timetable data with course & instructor details
$sql = "SELECT 
            c.name AS course_name,
            u.name AS instructor,
            c.semester,
            c.department,
            t.day_of_week,
            t.start_time,
            t.end_time,
            t.room_number
        FROM timetables t
        JOIN courses c ON t.course_id = c.id
        JOIN users u ON t.teacher_id = u.id
        ORDER BY t.day_of_week, t.start_time";
$result = $conn->query($sql);

// Organize timetable
$timetable = [];
$daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

while ($row = $result->fetch_assoc()) {
    $day = $row['day_of_week'];
    $timeSlot = date("H:i", strtotime($row['start_time'])) . " - " . date("H:i", strtotime($row['end_time']));
    $details = "<strong>" . $row['course_name'] . "</strong><br>" . $row['instructor'] . "<br>" . $row['room_number'];

    if (!isset($timetable[$day])) {
        $timetable[$day] = [];
    }

    $timetable[$day][$timeSlot] = $details;
}

// Sort each day's slots
foreach ($daysOfWeek as $day) {
    if (!isset($timetable[$day])) {
        $timetable[$day] = [];
    }
    ksort($timetable[$day]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title> Student Timetable | University of Messina</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

  <style>
    body {
      background: #8D6E63;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      padding: 30px;
    }

    .timetable-container {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    table {
      width: 100%;
      color: #fff;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 15px;
      text-align: center;
      border: 1px solid rgba(255,255,255,0.2);
      transition: all 0.3s ease;
    }

    th {
      background-color: rgba(0, 0, 0, 0.5);
      font-weight: 600;
    }

    td {
      background: rgba(255, 255, 255, 0.05);
    }

    td:hover {
      background: #8D6E63;
      transform: scale(1.03);
    }

    .search-bar {
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
    }

    .search-bar input {
      padding: 10px;
      border-radius: 10px;
      border: none;
      width: 280px;
    }

    .clear-btn {
      background-color: #ff5c5c;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: bold;
    }

    footer {
      margin-top: 40px;
      padding: 20px;
      text-align: center;
      background: rgba(0,0,0,0.4);
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <div class="timetable-container">
    <h2 class="text-center mb-4"> Weekly Timetable</h2>

    <div class="search-bar">
      <input type="text" id="timetableSearch" placeholder="Search courses or teachers..." onkeyup="searchTimetable()" />
      <button class="clear-btn" onclick="clearSearch()">Clear</button>
    </div>

    <table id="timetable">
      <thead>
        <tr>
          <th>Time</th>
          <?php foreach ($daysOfWeek as $day): ?>
            <th><?= $day ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php
        $timeSlots = [];
        foreach ($timetable as $day => $slots) {
            foreach ($slots as $time => $details) {
                $timeSlots[$time] = true;
            }
        }
        ksort($timeSlots);
        foreach ($timeSlots as $slot => $_):
        ?>
          <tr>
            <td><?= $slot ?></td>
            <?php foreach ($daysOfWeek as $day): ?>
              <td><?= $timetable[$day][$slot] ?? '' ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <footer>
    <p>&copy; 2025 University of Messina | Class Timetable Viewer</p>
    
    
  </footer>

  <script>
    function searchTimetable() {
      const searchValue = document.getElementById("timetableSearch").value.toLowerCase();
      const rows = document.querySelectorAll("#timetable tbody tr");

      rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        let match = false;
        cells.forEach(cell => {
          if (cell.textContent.toLowerCase().includes(searchValue)) match = true;
        });
        row.style.display = match ? "" : "none";
      });
    }

    function clearSearch() {
      document.getElementById("timetableSearch").value = "";
      searchTimetable();
    }
  </script>
</body>
</html>