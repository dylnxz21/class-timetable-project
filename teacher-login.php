<?php
session_start();
require 'db.php'; // This should point to your DB connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        die("❌ Please fill in all fields.");
    }

    // Prepare query to check for teacher login
    $stmt = $conn->prepare("SELECT name, email, password FROM users WHERE email = ? AND role = 'teacher'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($name, $email_db, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["email"] = $email_db;
            $_SESSION["name"] = $name;
            $_SESSION["role"] = "teacher";

            header("Location: teacher-dashboard.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ No teacher found with that email.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "⚠️ Invalid request method.";
}
?>
 
