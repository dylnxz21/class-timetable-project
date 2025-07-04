<?php
session_start();

// ✅ Check if the user is an admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

// ✅ Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // ✅ Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error_message = "❌ All fields are required.";
    } else {
        // ✅ Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // ✅ Check for duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "⚠️ This email is already registered. Try a different one.";
        } else {
            // ✅ Insert the new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                $success_message = "✅ User added successfully!";
            } else {
                $error_message = "❌ Failed to add user. Please try again.";
            }
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;700&family=Work+Sans:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Crimson Pro', serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .add-user-form {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 400px;
        }
        .add-user-form h2 {
            margin-bottom: 30px;
            text-align: center;
            color: #8D6E63; /* Harvard Crimson */
            font-family: 'Crimson Pro', serif;
            font-weight: bold;
        }
        .form-control, .form-select {
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: 'Work Sans', sans-serif;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #8D6E63;
            border: none;
            width: 100%;
            font-weight: 700;
            border-radius: 8px;
            font-family: 'Work Sans', sans-serif;
        }
        .btn-primary:hover {
            background-color: #8D6E63;
        }
        .feedback {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .error {
            color: #8D6E63;
        }
        .success {
            color: #218838;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #555;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            color: #8D6E63;
        }
    </style>
</head>
<body>
<div class="add-user-form">
    <h2>Add New User</h2>
    <?php if (isset($error_message)): ?>
        <div class="feedback error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
        <div class="feedback success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <select name="role" class="form-select" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>
    <a href="admin-dashboard.php" class="back-link">← Back to Admin Dashboard</a>
</div>
</body>
</html>