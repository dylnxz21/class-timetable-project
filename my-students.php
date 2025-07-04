<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
$name = $_SESSION['name'];

require 'db.php';

// ✅ Only select the columns that actually exist in your users table
$result = $conn->query("SELECT name, email FROM users WHERE role = 'student'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Students | Teacher Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #8D6E63;
      color: #fff;
      padding: 30px;
    }
    .container {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h2 {
      margin-bottom: 30px;
      font-weight: bold;
    }
    table {
      background-color: #fff;
      color: #333;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      vertical-align: middle;
    }
    .btn-email {
      background-color: #8D6E63;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 6px 12px;
      font-weight: 500;
    }
    .btn-email:hover {
      background-color: #8D6E63;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-user-graduate"></i> My Students</h2>
    <div class="table-responsive">
      <table class="table table-bordered text-center">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($student = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($student['name']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td>
                <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="btn btn-email">
                  <i class="fas fa-envelope"></i> Email
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>