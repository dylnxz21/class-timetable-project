<?php
// ✅ Show all errors (helpful for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Connect to the DB
require 'db.php';
session_start();

// ✅ Handle POST request only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Grab and sanitize inputs
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $role = $_POST['role']; // student, teacher, or admin

  // ✅ Validate fields
  if (empty($name) || empty($email) || empty($password) || empty($role)) {
    echo "<script>
      alert('❌ Please fill in all fields, including role.');
      history.back();
    </script>";
    exit();
  }

  // ✅ Hash the password safely
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // ✅ Check for duplicate email
  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }
  
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    echo "<script>
      alert('⚠️ This email is already registered. Try logging in.');
      history.back();
    </script>";
    exit();
  }

  $stmt->close();

  // ✅ Insert new user
  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

  if ($stmt->execute()) {
    echo "<script>
      document.body.innerHTML = `<div class='success-popup'>
        <h1>✅ Registration Successful!</h1>
        <p>Welcome to the University of Messina!</p>
        <button onclick=\"window.location.href='login.html'\">Go to Login</button>
      </div>`;
      document.body.style.backgroundColor = '#6a11cb';
    </script>";
  } else {
    echo "❌ Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>

<!-- ✅ Success Popup Styles -->
<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #8D6E63;
  color: #fff;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  overflow: hidden;
}
.success-popup {
  background: rgba(255, 255, 255, 0.15);
  padding: 40px 30px;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
  animation: fadeIn 0.8s ease-in-out;
}
.success-popup h1 {
  margin-bottom: 20px;
}
.success-popup p {
  margin-bottom: 30px;
}
.success-popup button {
  background-color: #fff;
  color: #8D6E63;
  padding: 10px 30px;
  border-radius: 10px;
  font-weight: bold;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}
.success-popup button:hover {
  background-color: #8D6E63;
  color: #fff;
  transform: scale(1.05);
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>