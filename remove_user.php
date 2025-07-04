<?php
session_start();
require 'db.php';

// Make sure the user is an admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the user_id is provided
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect back to the admin dashboard after deleting
        header("Location: admin-dashboard.php?success=User+removed+successfully");
        exit();
    } else {
        echo "❌ Error removing user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ No user ID provided.";
}
?>