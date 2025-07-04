<?php
session_start();
require 'db.php';

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Handle course addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $semester = trim($_POST['semester']);
    $teacher_id = $_POST['teacher_id'];

    if (empty($name) || empty($department) || empty($semester)) {
        $error = "❌ Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (name, department, semester, teacher_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $department, $semester, $teacher_id);
        if ($stmt->execute()) {
            // ✅ Also insert into teacher_courses to keep relationship synced
            $course_id = $stmt->insert_id;
            $linkStmt = $conn->prepare("INSERT INTO teacher_courses (teacher_id, course_id) VALUES (?, ?)");
            $linkStmt->bind_param("ii", $teacher_id, $course_id);
            $linkStmt->execute();
            $linkStmt->close();

            $success = "✅ Course and teacher assignment added successfully!";
        } else {
            $error = "❌ Error adding course. Please try again.";
        }
        $stmt->close();
    }
}

$teacherQuery = "SELECT id, name FROM users WHERE role = 'teacher' AND id NOT IN (SELECT teacher_id FROM courses WHERE teacher_id IS NOT NULL)";
$teacherResult = $conn->query($teacherQuery);

$courses = $conn->query("
  SELECT c.id, c.name AS course_name, c.department, c.semester, u.name AS teacher_name
  FROM courses c
  LEFT JOIN users u ON c.teacher_id = u.id AND u.role = 'teacher'
  ORDER BY c.name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f9;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }
    .container {
      background-color: #ffffff;
      border-radius: 20px;
      padding: 40px;
      margin: 40px auto;
      max-width: 900px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }
    .navbar {
      background-color: #8D6E63;
    }
    .navbar-brand, .nav-link, .navbar-text {
      color: #fff !important;
    }
    .btn-primary {
      background-color: #8D6E63;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #8D6E63;
    }
    .remove-btn {
      background-color: #ff4757;
      color: #fff;
      border: none;
      padding: 8px 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .remove-btn:hover {
      background-color: #d90429;
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    .form-container {
      margin-bottom: 40px;
    }
    .form-control, .form-select {
      margin-bottom: 15px;
      border-radius: 10px;
      border: 1px solid #ddd;
    }
    .table {
      background-color: #ffffff;
      color: #333;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 40px;
    }
    .table thead {
      background: #8D6E63;
      color: #fff;
      font-weight: bold;
    }
    .table tbody tr {
      transition: all 0.3s ease;
    }
    .table tbody tr:hover {
      background-color: #f1f1f1;
    }
    .alert {
      margin-bottom: 20px;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    footer {
      background-color: #8D6E63;
      color: #ccc;
      padding: 20px 0;
      text-align: center;
      margin-top: 40px;
      border-radius: 10px;
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

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <i class="fas fa-university me-2"></i> University of Messina - Admin Panel
    </a>
    <div class="ms-auto d-flex align-items-center">
      <span class="navbar-text me-3">
        Logged in as: <strong><?php echo htmlspecialchars($_SESSION['name']); ?> (Admin)</strong>
      </span>
      <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="text-center mb-4"> Manage Courses</h2>

  <div class="form-container">
    <h4>Add New Course</h4>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (isset($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Course Name" class="form-control" required>
      <input type="text" name="department" placeholder="Department" class="form-control" required>
      <input type="text" name="semester" placeholder="Semester" class="form-control" required>
      <select name="teacher_id" class="form-select" required>
        <option value="">👩‍🏫 Select Teacher</option>
        <?php while ($teacher = $teacherResult->fetch_assoc()): ?>
          <option value="<?php echo $teacher['id']; ?>">
            <?php echo htmlspecialchars($teacher['name']); ?>
          </option>
        <?php endwhile; ?>
      </select>
      <button type="submit" class="btn btn-primary w-100">Add Course</button>
    </form>
  </div>

  <table class="table table-striped text-center">
    <thead>
      <tr>
        <th>Course Name</th>
        <th>Department</th>
        <th>Semester</th>
        <th>Teacher</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($course = $courses->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($course['course_name']); ?></td>
          <td><?php echo htmlspecialchars($course['department']); ?></td>
          <td><?php echo htmlspecialchars($course['semester']); ?></td>
          <td>
            <?php echo $course['teacher_name'] ? htmlspecialchars($course['teacher_name']) : '<span style="color: red;">Not Assigned</span>'; ?>
          </td>
          <td>
            <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm me-2" onclick="return confirm('Are you sure you want to modify this course?')">Modify</a>
            <a href="remove-course.php?id=<?php echo $course['id']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to remove this course?')">Remove</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer>
  <p>&copy; 2025 University of Messina - Admin Panel</p>
 
</footer>

</body>
</html>