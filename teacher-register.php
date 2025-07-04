<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $role = 'teacher'; // Changed to 'teacher'

  // ✅ Validate fields
  if (empty($name) || empty($email) || empty($password)) {
    die("❌ Please fill out all fields.");
  }

  // ✅ Check if email already exists
  $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    die("⚠️ That email is already registered.");
  }
  $check->close();

  // ✅ Hash password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // ✅ Insert teacher into users table
  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

  if ($stmt->execute()) {
    // ✅ Start session + redirect
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;

    header("Location: teacher-dashboard.php"); // Redirect to teacher dashboard
    exit();
  } else {
    echo "❌ Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>