<?php
session_start();

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$initial = strtoupper($name[0]);

require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #ffffff;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      background: #8D6E63;
      color: #fff;
      position: fixed;
      padding: 30px 20px;
      box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    }
    .sidebar h3 {
      text-align: center;
      font-weight: 600;
      margin-bottom: 30px;
      font-size: 1.5rem;
      color: #fff;
    }
    .sidebar a {
      display: block;
      color: #ccc;
      padding: 15px;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }
    .sidebar a.active, .sidebar a:hover {
      background: #8D6E63;
      color: #fff;
    }
    .main {
      margin-left: 270px;
      padding: 30px;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
    .welcome-box {
      background: #ffffff;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .admin-tips {
      background: #ffffff;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .admin-tips h5 {
      margin-bottom: 20px;
      font-weight: 600;
    }
    .tips-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .tip-card {
      background: #f0f2f5;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: 0.3s;
    }
    .tip-card:hover {
      background: #e0e7ff;
      transform: translateY(-5px);
    }
    .tip-card i {
      font-size: 1.8rem;
      color: #8D6E63;
      margin-bottom: 10px;
    }
    footer {
      margin-top: 30px;
      background-color: #8D6E63;
      color: #fff;
      padding: 20px;
      text-align: center;
      border-radius: 15px;
    }
    footer a {
      color: #ccc;
      margin: 0 10px;
      text-decoration: none;
      font-weight: 500;
    }
    footer a:hover {
      color: #fff;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h3>Admin Panel</h3>
  <a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="manage-students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
  <a href="manage-teachers.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
  <a href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a>
  <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
 
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
  <div class="welcome-box">
    <h3> Hey <?php echo htmlspecialchars($name); ?>, welcome!</h3>
    <p>You’re logged in as <strong>Admin</strong>. Here's what you can do to keep the university system running smoothly.</p>
  </div>

  <div class="admin-tips">
    <h5> Your Admin powers</h5>
    <div class="tips-list">
      <div class="tip-card text-center">
        <i class="fas fa-user-plus"></i>
        <p>Add and manage student or teacher accounts effortlessly.</p>
      </div>
      <div class="tip-card text-center">
        <i class="fas fa-calendar-alt"></i>
        <p>Update and organize course timetables for better scheduling.</p>
      </div>
      
      
    </div>
  </div>

  <footer>
    <p>&copy; 2025 University of Messina</p>
    
  </footer>
</div>

</body>
</html>