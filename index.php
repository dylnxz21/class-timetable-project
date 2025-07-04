<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row["password"])) {
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $row["role"];

            // Redirect based on role
            if ($row["role"] == "admin") {
                header("Location: admin-dashboard.php");
            } elseif ($row["role"] == "teacher") {
                header("Location: teacher-dashboard.php");
            } else {
                header("Location: student-dashboard.php");
            }
            exit();
        } else {
            $error_message = "❌ Incorrect password.";
        }
    } else {
        $error_message = "❌ User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow-x: hidden;
    }
    .login-container {
      background: rgba(255, 255, 255, 0.1);
      padding: 40px 30px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      animation: slideIn 0.8s ease forwards;
    }
    .login-container h2 {
      font-weight: bold;
      margin-bottom: 20px;
      color: #fff;
      letter-spacing: 1px;
    }
    .login-container input {
      margin-bottom: 15px;
      border-radius: 10px;
      padding: 12px;
      width: 100%;
      border: none;
      transition: all 0.3s ease;
      outline: none;
    }
    .login-container input:focus {
      background: #f0f0f0;
      color: #333;
    }
    .login-container button {
      background-color: #fff;
      color: #2575fc;
      font-weight: bold;
      border-radius: 10px;
      width: 100%;
      padding: 12px;
      transition: all 0.3s ease;
    }
    .login-container button:hover {
      background-color: #341a8c;
      color: #fff;
    }
    .error-message {
      color: #ff5c5c;
      margin-bottom: 15px;
      font-weight: bold;
    }
    .footer-links {
      margin-top: 20px;
      font-size: 0.9rem;
      color: #ddd;
    }
    .footer-links a {
      color: #fff;
      text-decoration: underline;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🎓 Login</h2>

    <?php if (isset($error_message)): ?>
      <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <div class="footer-links">
      <p>New here? <a href="register.html">Register as Student</a> | <a href="teacher-register.html">Register as Teacher</a> | <a href="admin-register.html">Register as Admin</a></p>
    </div>
  </div>
</body>
</html>