 <?php
session_start();
require 'db.php'; // assumes this connects you to your database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        die("❌ Please fill in all fields.");
    }

    $stmt = $conn->prepare("SELECT name, email, password FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($name, $email_db, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["email"] = $email_db;
            $_SESSION["name"] = $name;
            $_SESSION["role"] = "admin";

            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ No admin found with that email.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "⚠️ Invalid request method.";
}
?>

