<?php
session_start();
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

require 'db.php';
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$initial = strtoupper($name[0]);

// ✅ Get teacher's assigned course from 'courses' table via teacher_id
$stmt = $conn->prepare("SELECT id, name FROM courses WHERE teacher_id = (SELECT id FROM users WHERE email = ? AND role = 'teacher')");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$assignedCourse = $result->fetch_assoc(); // get one course (one-to-one mapping)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Dashboard | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body {
      margin: 0;
      background: #8D6E63;
      color: #fff;
    }
    .sidebar {
      width: 230px;
      height: 100vh;
      background-color: #8D6E63;
      position: fixed;
      top: 0;
      left: 0;
      padding: 30px 20px;
      box-shadow: 4px 0 20px rgba(0,0,0,0.2);
    }
    .sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
      color: #fff;
    }
    .sidebar a {
      display: block;
      color: #ccc;
      text-decoration: none;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: background 0.3s ease;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #8D6E63;
      color: #fff;
    }
    .main-content {
      margin-left: 250px;
      padding: 40px;
      min-height: 100vh;
    }
    .greeting {
      margin-bottom: 30px;
    }
    .card {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 15px;
      color: #fff;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .card h5 { font-weight: 600; }
    .card input, .card select {
      margin-bottom: 15px;
      border-radius: 10px;
    }
    table {
      background-color: #fff;
      color: #333;
      border-radius: 10px;
      overflow: hidden;
    }
    footer {
      background-color: #8D6E63;
      padding: 20px 0;
      text-align: center;
      color: #ccc;
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

<div class="sidebar">
  <h4><i class="fas fa-chalkboard-teacher"></i> Teacher Panel</h4>
  <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
  <a href="my-students.php"><i class="fas fa-user-graduate"></i> My Students</a>
  <a href="view-attendance.php"><i class="fas fa-check-square"></i> View Attendance</a>
  
  <a href="schedule.php"><i class="fas fa-calendar-alt"></i> My Schedule</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
  <div class="greeting">
    <h2> Welcome , <?php echo htmlspecialchars($name); ?>!</h2>
    

    <?php if ($assignedCourse): ?>
      <div class="alert alert-light text-dark mt-3">
        <strong>Assigned Course:</strong> <?= htmlspecialchars($assignedCourse['name']) ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning text-dark mt-3">
        ⚠️ You have not been assigned to any course yet.
      </div>
    <?php endif; ?>
  </div>

  <div class="card p-4 mb-5">
    <h5 class="mb-3"> Add New Class</h5>
    <form action="insert_class.php" method="POST">
      <select name="course_id" class="form-select" required>
        <option value="">Select Course</option>
        <?php if ($assignedCourse): ?>
          <option value="<?= $assignedCourse['id'] ?>"><?= htmlspecialchars($assignedCourse['name']) ?></option>
        <?php endif; ?>
      </select>
      <select name="day_of_week" class="form-select" required>
        <option value="">Select Day</option>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
      </select>
      <input type="time" name="start_time" class="form-control" required>
      <input type="time" name="end_time" class="form-control" required>
      <input type="text" name="room_number" class="form-control" placeholder="Room Number" required>
      <button type="submit" class="btn btn-light text-primary fw-bold">Add Class</button>
    </form>
  </div>
</div>

<footer>
  <p>&copy; 2025 University of Messina - Teacher Panel</p>
 
</footer>

</body>
</html>