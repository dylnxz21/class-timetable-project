<<?php
session_start();
if (!isset($_SESSION['name']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
  header("Location: login.html");
  exit();
}



$name = $_SESSION['name'];
$initial = strtoupper($name[0]);
$role = $_SESSION['role'];

// Time-based greeting
$hour = date("H");
if ($hour < 12) {
  $greeting = "Good Morning";
} elseif ($hour < 18) {
  $greeting = "Good Afternoon";
} else {
  $greeting = "Good Evening";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Merriweather:wght@300;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #8D6E63;
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
    }
    .sidebar {
      width: 250px;
      background:#8D6E63;
      padding: 20px;
      height: 100vh;
      position: fixed;
      color: #fff;
      animation: slideIn 0.5s forwards;
      overflow-y: auto;
    }
    .sidebar img {
      width: 70px;
      margin: 0 auto 20px;
      display: block;
      opacity: 0;
      animation: fadeIn 0.8s forwards 0.3s;
    }
    .sidebar h3 {
      text-align: center;
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #fff;
      opacity: 0;
      animation: fadeIn 0.8s forwards 0.5s;
    }
    .sidebar a {
      display: block;
      color: #fff;
      padding: 15px;
      text-decoration: none;
      margin-bottom: 10px;
      border-radius: 8px;
      transition: all 0.3s ease;
      opacity: 0;
      animation: fadeIn 0.8s forwards 0.7s;
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
      opacity: 0;
      animation: fadeIn 0.8s forwards 0.9s;
    }
    .profile {
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      margin-bottom: 20px;
    }
    .profile-circle {
      background-color: #fff;
      color: #8D6E63;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.4rem;
    }
    .profile-dropdown {
      display: none;
      position: absolute;
      top: 70px;
      right: 0;
      background: #fff;
      color: #333;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      width: 220px;
      text-align: left;
      z-index: 100;
      opacity: 0;
      transform: translateY(-10px);
      transition: all 0.3s ease;
    }
    .profile-dropdown a {
      display: block;
      color: #333;
      text-decoration: none;
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 6px;
    }
    .profile-dropdown a:hover {
      background: #f0f0f0;
    }
    .logout-btn {
      background-color:rgb(29, 27, 63);
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
      transition: all 0.3s ease;
    }
    .logout-btn:hover {
      background-color:rgb(120, 106, 171);
    }
    .quote-box {
      background: rgba(255, 255, 255, 0.15);
      padding: 30px;
      border-radius: 20px;
      text-align: center;
      margin-bottom: 30px;
      color: #fff;
      font-style: italic;
      font-size: 1.3rem;
      line-height: 1.6;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      font-family: 'Merriweather', serif;
    }
    .quick-link {
      flex: 1;
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      color: #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      font-family: 'Merriweather', serif;
      font-size: 1.1rem;
    }
    .quick-link:hover {
      background: #8D6E63;
      transform: scale(1.05);
    }
    footer {
      text-align: center;
      color: #ccc;
      margin-top: 40px;
      padding: 20px;
      background-color: #8D6E63;
    }
    @keyframes slideIn {
      to { transform: translateX(0); }
    }
    @keyframes fadeIn {
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  
  <h3>University of Messina</h3>
  <a href="student-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
  <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
  <a href="students.php"><i class="fas fa-user-graduate"></i> Students</a>
  <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
  <a href="subjects.php"><i class="fas fa-book"></i> Subjects</a>
  <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
  <a href="attendance.php"><i class="fas fa-check-square"></i> Attendance</a>
  
</div>

<!-- MAIN CONTENT -->
<div class="main">
  <div class="profile" onclick="toggleDropdown()">
    <div class="profile-circle"><?php echo $initial; ?></div>
    <span><?php echo htmlspecialchars($name); ?></span>
    <div class="profile-dropdown" id="profileDropdown">
      <a href="profile.php">Profile Settings</a>
      <form method="POST" action="logout.php" style="margin: 0;">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>
  </div>

  
</div>


</body>
</html>