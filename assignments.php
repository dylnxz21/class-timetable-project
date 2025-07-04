<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Assignments | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      min-height: 100vh;
    }
    .container {
      margin-top: 50px;
      max-width: 900px;
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 15px;
      color: #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s forwards 0.2s;
    }
    h2 {
      margin-bottom: 20px;
    }
    .assignment-card {
      background: rgba(255, 255, 255, 0.1);
      margin-bottom: 20px;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .assignment-card:hover {
      background: #5e49d8;
      transform: scale(1.05);
      cursor: pointer;
    }
    @keyframes fadeIn {
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📝 My Assignments</h2>
  <div class="assignment-card">
    <h4>Assignment 1</h4>
    <p>Due: May 20, 2025</p>
  </div>
  <div class="assignment-card">
    <h4>Assignment 2</h4>
    <p>Due: May 25, 2025</p>
  </div>
  <button onclick="window.location.href='student-dashboard.php'" class="btn btn-light">Back to Dashboard</button>
</div>
</body>
</html>