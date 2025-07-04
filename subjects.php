<?php
session_start();
if (!isset($_SESSION['name']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'];
$initial = strtoupper($name[0]);

require 'db.php';

// ✅ Fetch active subjects that are scheduled and assigned directly to teachers
$sql = "
SELECT DISTINCT c.name, c.department, c.semester 
FROM courses c 
WHERE c.teacher_id IS NOT NULL
ORDER BY c.name ASC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Subjects Management | University of Messina</title>

  <!-- Bootstrap + Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #8D6E63;
      color: #fff;
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }
    .sidebar {
      width: 250px;
      background: #8D6E63;
      padding: 20px;
      position: fixed;
      height: 100vh;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
    }
    .sidebar img {
      width: 70px;
      margin: 0 auto 20px;
      display: block;
      animation: bounce 1s infinite alternate;
    }
    .sidebar h3 {
      text-align: center;
      font-size: 1.4rem;
      margin-bottom: 30px;
      color: #fff;
      font-weight: bold;
    }
    .sidebar a {
      display: block;
      color: #fff;
      padding: 15px;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
      font-weight: 600;
    }
    .sidebar a.active {
      background: #8D6E63;
      font-weight: bold;
    }
    .sidebar a:hover {
      background: #8D6E63;
      border-radius: 8px;
    }
    .sidebar a i {
      margin-right: 10px;
    }
    .main {
      margin-left: 270px;
      padding: 30px;
      flex: 1;
      min-height: 100vh;
      animation: fadeIn 0.8s forwards 0.9s;
    }
    .subject-card {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeIn 0.6s forwards;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .subject-card:hover {
      background: #8D6E63;
      transform: scale(1.05) rotate(1deg);
      color: #fff;
      box-shadow: 0 12px 25px rgba(0,0,0,0.3);
    }
    .subject-card h4 {
      margin-bottom: 5px;
      font-weight: bold;
      color: #fff;
      transition: all 0.3s ease;
    }
    .subject-card p {
      color: #ddd;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .subject-card:hover h4 {
      color: #fff;
    }
    .subject-card:hover p {
      color: #ccc;
    }
    .search-bar {
      margin-bottom: 30px;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .search-bar input {
      padding: 10px;
      border-radius: 8px;
      border: none;
      width: 300px;
      outline: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .search-bar input:focus {
      background: #f0f0f0;
      color: #333;
    }
    @keyframes fadeIn {
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes bounce {
      0% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }
    footer {
      margin-top: 20px;
      padding: 20px 0;
      background-color: #8D6E63;
      color: #ccc;
      text-align: center;
      box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.5);
    }
    footer a {
      color: #ccc;
      margin: 0 10px;
      text-decoration: none;
    }
    footer a:hover {
      color: #fff;
    }
  </style>
</head>

<body>

<!-- ✅ SIDEBAR -->
<div class="sidebar">

  <h3>University of Messina</h3>
  <a href="student-dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="students.php"><i class="fas fa-user-graduate"></i> Students</a>
  <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
  <a href="subjects.php" class="active"><i class="fas fa-book"></i> Subjects</a>
  <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
  <a href="attendance.php"><i class="fas fa-check-square"></i> Attendance</a>
</div>

<!-- ✅ MAIN CONTENT -->
<div class="main">
  <h2> Subjects Management</h2>

  <div class="search-bar">
    <input type="text" id="subjectSearch" placeholder="Search Subjects..." onkeyup="searchSubjects()" />
  </div>

  <div id="subjectsList">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($subject = $result->fetch_assoc()): ?>
        <div class="subject-card">
          <h4><?php echo htmlspecialchars($subject['name']); ?></h4>
          <p> <?php echo htmlspecialchars($subject['department']) . " | Semester: " . htmlspecialchars($subject['semester']); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No active subjects available yet. Please check back soon!</p>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; 2025 University of Messina</p>
    
  </footer>
</div>

<script>
function searchSubjects() {
  const searchValue = document.getElementById("subjectSearch").value.toLowerCase();
  const subjectCards = document.querySelectorAll(".subject-card");

  subjectCards.forEach(card => {
    const subjectName = card.querySelector("h4").textContent.toLowerCase();
    card.style.display = subjectName.includes(searchValue) ? "block" : "none";
  });
}
</script>

</body>
</html>