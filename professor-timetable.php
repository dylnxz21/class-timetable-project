<?php
session_start();
require 'db.php';

// ✅ Role Check
if (!isset($_SESSION['name']) || !in_array($_SESSION['role'], ['teacher', 'student', 'admin'])) {
  header("Location: login.html");
  exit();
}

$profName = isset($_GET['prof']) ? trim($_GET['prof']) : '';

if ($profName === '') {
  echo "No professor name provided.";
  exit();
}

$stmt = $conn->prepare("SELECT * FROM timetables WHERE instructor LIKE ?");
$searchTerm = "%" . $profName . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Professor Timetable | <?php echo htmlspecialchars($profName); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .header {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      padding: 40px 20px;
      text-align: center;
      margin-bottom: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      backdrop-filter: blur(10px);
    }

    .header h1 {
      margin-bottom: 10px;
      font-size: 2.2rem;
      font-weight: 700;
      color: #fff;
    }

    .btn-back {
      margin: 20px auto;
      display: block;
      width: fit-content;
      background-color: #fff;
      color: #2575fc;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-back:hover {
      background-color: #341a8c;
      color: #fff;
      transform: scale(1.05);
    }

    .container {
      padding: 30px;
      max-width: 1200px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      backdrop-filter: blur(15px);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    th, td {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
      font-weight: 500;
      color: #333;
    }

    th {
      background-color: #341a8c;
      color: #fff;
      font-weight: 700;
    }

    tr:hover {
      background-color: rgba(100, 100, 255, 0.1);
    }

    footer {
      text-align: center;
      padding: 20px;
      background: rgba(255, 255, 255, 0.1);
      color: #ccc;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      backdrop-filter: blur(10px);
      margin-top: 20px;
    }

    footer a {
      color: #fff;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }

    footer a:hover {
      color: #ddd;
    }
  </style>
</head>
<body>

<div class="header">
  <h1>🧑‍🏫 Timetable for <?php echo htmlspecialchars($profName); ?></h1>
  <p>Explore the full teaching schedule for this professor.</p>
</div>

<a href="student-dashboard.php" class="btn btn-back">← Back to Dashboard</a>

<div class="container">
  <table>
    <thead>
      <tr>
        <th>Course</th>
        <th>Department</th>
        <th>Semester</th>
        <th>Day</th>
        <th>Start</th>
        <th>End</th>
        <th>Room</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>" . htmlspecialchars($row['course_name']) . "</td>
                  <td>" . htmlspecialchars($row['department']) . "</td>
                  <td>" . htmlspecialchars($row['semester']) . "</td>
                  <td>" . htmlspecialchars($row['day_of_week']) . "</td>
                  <td>" . htmlspecialchars($row['start_time']) . "</td>
                  <td>" . htmlspecialchars($row['end_time']) . "</td>
                  <td>" . htmlspecialchars($row['room_number']) . "</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No classes found for this professor.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<footer>
  <p>&copy; 2025 University of Messina</p>
  <a href="https://instagram.com" target="_blank">Instagram</a> |
  <a href="https://www.unime.it" target="_blank">Unime Website</a>
</footer>

</body>
</html>