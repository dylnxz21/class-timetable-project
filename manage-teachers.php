<?php
session_start();

// ✅ Proper session check for admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$initial = strtoupper($name[0]);

require 'db.php';

// Fetch all teachers
$sql = "SELECT id, name, email FROM users WHERE role = 'teacher'";
$result = $conn->query($sql);
$totalTeachers = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Teachers | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f9;
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
      color: #ddd;
      padding: 15px;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .sidebar a.active {
      background: #8D6E63;
      color: #fff;
    }

    .sidebar a:hover {
      background: #8D6E63;
      color: #fff;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .main {
      margin-left: 270px;
      padding: 30px;
      background-color: #f4f4f9;
      min-height: 100vh;
    }

    .card {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .card h4 {
      font-weight: 600;
      margin-bottom: 20px;
    }

    .teacher-list {
      margin-bottom: 30px;
    }

    .teacher-card {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .teacher-card:hover {
      background: #e9ecef;
    }

    .teacher-info {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .teacher-info img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: #ddd;
    }

    .teacher-info div {
      font-weight: 500;
    }

    .remove-btn {
      background-color: #dc3545;
      color: #fff;
      border: none;
      padding: 8px 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
   }

    .remove-btn:hover {
      background-color: #c82333;
      transform: scale(1.05);
      box-shadow: 0 8px 15px rgba(0,0,0,0.3);
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
    }
    
  </style>
</head>
<body>

<!-- ✅ SIDEBAR -->
<div class="sidebar">
  <h3>Admin Panel</h3>
  <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="manage-students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
  <a href="manage-teachers.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</a>
  <a href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a>
  <a href="manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a>
 
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- ✅ MAIN CONTENT -->
<div class="main">
  <h4>Manage Teachers (Total: <?php echo $totalTeachers; ?>)</h4>

  <div class="teacher-list">
    <?php while ($teacher = $result->fetch_assoc()): ?>
      <div class="teacher-card">
        <div class="teacher-info">
          
          <div>
            <strong><?php echo htmlspecialchars($teacher['name']); ?></strong>
            <p><?php echo htmlspecialchars($teacher['email']); ?></p>
          </div>
        </div>
        <form action="remove_user.php" method="POST" style="margin: 0;">
  <input type="hidden" name="user_id" value="<?php echo $teacher['id']; ?>">
  <button type="submit" class="remove-btn" onclick="return confirm('Are you sure you want to remove this teacher?')">Remove</button>
</form>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- ✅ FOOTER -->
<footer>
  <p>&copy; 2025 University of Messina - Admin Panel</p>
</footer>

</body>
</html>