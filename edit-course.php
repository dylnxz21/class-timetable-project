<?php
session_start();
require 'db.php';

// Check session and access
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Handle update after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = trim($_POST['name']);
  $department = trim($_POST['department']);
  $semester = trim($_POST['semester']);
  $teacher_id = isset($_POST['teacher_id']) ? $_POST['teacher_id'] : null;

  $stmt = $conn->prepare("UPDATE courses SET name = ?, department = ?, semester = ?, teacher_id = ? WHERE id = ?");
  $stmt->bind_param("ssssi", $name, $department, $semester, $teacher_id, $id);

  if ($stmt->execute()) {
    header("Location: manage-courses.php?success=Course updated successfully!");
  } else {
    echo "<script>alert('Error updating course.');</script>";
  }
  $stmt->close();
  $conn->close();
  exit();
}

// Load course info to pre-fill the form
if (!isset($_GET['id'])) {
  echo "<h2 style='color:red'>Course not found.</h2>";
  exit();
}

$id = $_GET['id'];
$courseQuery = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$courseQuery->bind_param("i", $id);
$courseQuery->execute();
$courseResult = $courseQuery->get_result();

if ($courseResult->num_rows === 0) {
  echo "<h2 style='color:red'>Course not found.</h2>";
  exit();
}

$course = $courseResult->fetch_assoc();

// Fetch teacher list for dropdown
$teachers = $conn->query("SELECT id, name FROM users WHERE role = 'teacher'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Course</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #8D6E63;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      padding: 50px;
    }
    .form-container {
      max-width: 600px;
      margin: auto;
      background: rgba(255,255,255,0.1);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .btn-warning {
      background-color: #ffc107;
      border: none;
      font-weight: bold;
    }
    .btn-secondary {
      background-color: #6c757d;
      border: none;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2> Edit Course</h2>
    <form method="POST">
      <input type="hidden" name="id" value="<?php echo $course['id']; ?>">

      <div class="mb-3">
        <label class="form-label">Course Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Department</label>
        <input type="text" name="department" value="<?php echo htmlspecialchars($course['department']); ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Semester</label>
        <input type="text" name="semester" value="<?php echo htmlspecialchars($course['semester']); ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Assign Teacher</label>
        <select name="teacher_id" class="form-select">
          <option value="">-- Select Teacher --</option>
          <?php while($t = $teachers->fetch_assoc()): ?>
            <option value="<?php echo $t['id']; ?>" <?php if ($course['teacher_id'] == $t['id']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($t['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" name="update" class="btn btn-warning">✅ Update Course</button>
      <a href="manage-courses.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>