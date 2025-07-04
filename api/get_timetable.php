<?php
// Show any errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$db = 'class_timetable'; // use your actual DB name
$user = 'root';
$pass = ''; // if you have a password set, add it here

$conn = new mysqli($host, $user, $pass, $db);

// If connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query all timetables
$sql = "SELECT * FROM timetables";
$result = $conn->query($sql);

$timetables = [];

while ($row = $result->fetch_assoc()) {
    $timetables[] = $row;
}

$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($timetables);
?> 
