<?php
session_start();
require 'db.php';

// ✅ Redirect if already logged in
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin-dashboard.php?login=success");
        exit();
    } elseif ($_SESSION["role"] === "teacher") {
        header("Location: teacher-dashboard.php?login=success");
        exit();
    } elseif ($_SESSION["role"] === "student") {
        header("Location: student-dashboard.php?login=success");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ✅ Wipe any previous session
    session_unset();
    session_destroy();
    session_start();

    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"]; // ✅ we’re now reading role from the form too

    if (empty($email) || empty($password) || empty($role)) {
        // Don't show form — just die with a message (handled better in production)
        die("❌ Please fill in all fields.");
    }

    // ✅ Match both email AND role in the DB
    $stmt = $conn->prepare("SELECT name, email, password, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($name, $email, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;

            // ✅ Redirect to correct dashboard
            if ($role === 'admin') {
                header("Location: admin-dashboard.php?login=success");
            } elseif ($role === 'teacher') {
                header("Location: teacher-dashboard.php?login=success");
            } elseif ($role === 'student') {
                header("Location: student-dashboard.php?login=success");
            } else {
                die("⚠️ Unknown role. Please contact support.");
            }
            exit();
        } else {
            die("❌ Incorrect password.");
        }
    } else {
        die("❌ User not found or incorrect role.");
    }

    $stmt->close();
    $conn->close();
}
?>