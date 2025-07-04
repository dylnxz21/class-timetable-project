<?php
session_start();
if (!isset($_SESSION['name'], $_SESSION['email']) || $_SESSION['role'] !== 'student') {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'];
$initial = strtoupper($name[0]);
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile Settings | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #8D6E63;
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .profile-card {
      background: rgba(255, 255, 255, 0.1);
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 450px;
      text-align: center;
      color: #fff;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
      backdrop-filter: blur(15px);
      transition: all 0.3s ease;
      animation: slideIn 0.8s ease-in-out;
    }
    .profile-card:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: scale(1.02);
      box-shadow: 0 12px 35px rgba(0,0,0,0.3);
    }

    .profile-card h2 {
      font-size: 2.2rem;
      margin-bottom: 10px;
    }
    .profile-card p {
      font-size: 1.2rem;
      color: #ddd;
      margin-bottom: 20px;
    }
    .profile-card button {
      background-color: #fff;
      color: #8D6E63;
      padding: 12px 30px;
      border-radius: 10px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 5px 20px rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      margin: 5px 0;
    }
    .profile-card button:hover {
      background-color: #8D6E63;
      color: #fff;
      transform: scale(1.05);
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }
    .profile-card button.logout {
      background-color:rgb(163, 26, 23);
      color: #fff;
    }
    .profile-card button.logout:hover {
      background-color: #c62828;
    }
    .profile-card button i {
      margin-right: 8px;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="profile-card">
  
  <h2><?php echo htmlspecialchars($name); ?></h2>
  <p><?php echo htmlspecialchars($email); ?></p>
  <button onclick="window.location.href='student-dashboard.php'"><i class="fas fa-arrow-left"></i> Back to Dashboard</button>
  <button class="logout" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
</div>

<script>
  const studentName = <?php echo json_encode($name); ?>;

  function confirmLogout() {
    if (confirm(`Are you sure you want to logout?\nSee you soon, ${studentName} `)) {
      window.location.href = "logout.php";
    }
  }
</script>

</body>
</html>