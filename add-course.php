<?php
session_start();
require 'db.php';

// ✅ Proper session check for admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 🔍 Fetch teachers who haven't been assigned a course
$teacherQuery = "SELECT id, name FROM users WHERE role = 'teacher' AND id NOT IN (SELECT teacher_id FROM courses WHERE teacher_id IS NOT NULL)";
$teacherResult = $conn->query($teacherQuery);

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);  // course name
    $department = trim($_POST['department']);
    $semester = trim($_POST['semester']);
    $teacher_id = intval($_POST['teacher_id']);

    if (empty($name) || empty($department) || empty($semester) || empty($teacher_id)) {
        die("❌ Please fill in all fields.");
    }

    // Insert into courses table
    $stmt = $conn->prepare("INSERT INTO courses (name, department, semester, teacher_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $department, $semester, $teacher_id);

    if ($stmt->execute()) {
        // 💥 NEW: Insert into subjects table if it doesn’t already exist
        $checkSubject = $conn->prepare("SELECT id FROM subjects WHERE name = ?");
        $checkSubject->bind_param("s", $name);
        $checkSubject->execute();
        $checkSubject->store_result();

        if ($checkSubject->num_rows === 0) {
            $insertSubject = $conn->prepare("INSERT INTO subjects (name, department, semester) VALUES (?, ?, ?)");
            $insertSubject->bind_param("sss", $name, $department, $semester);
            $insertSubject->execute();
            $insertSubject->close();
        }
        $checkSubject->close();

        echo "<script>alert('✅ Course + Subject added successfully!'); window.location.href='manage-courses.php';</script>";
    } else {
        echo "<script>alert('❌ Error adding course: " . $stmt->error . "'); window.location.href='manage-courses.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- ✅ Simple HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Course</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Add New Course</h2>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Course Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Department</label>
        <input type="text" name="department" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Semester</label>
        <input type="text" name="semester" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Assign Teacher</label>
        <select name="teacher_id" class="form-select" required>
          <option value="">-- Select a Teacher --</option>
          <?php while ($row = $teacherResult->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
  </div>
</body>
</html>