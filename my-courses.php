<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Courses | University of Messina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      min-height: 100vh;
      overflow: hidden;
    }

    .container {
      margin-top: 80px;
      max-width: 900px;
      background: rgba(255, 255, 255, 0.15);
      padding: 40px;
      border-radius: 20px;
      backdrop-filter: blur(15px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.2);
      color: #fff;
      text-align: center;
      animation: slideIn 0.8s ease forwards;
    }

    .container:hover {
      transform: scale(1.02);
      box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .course-card {
      background: rgba(255, 255, 255, 0.2);
      margin-bottom: 20px;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      color: #fff;
      cursor: pointer;
    }

    .course-card:hover {
      background: #5e49d8;
      transform: scale(1.05);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }

    .back-btn {
      background-color: #fff;
      color: #2575fc;
      font-weight: bold;
      width: 100%;
      border-radius: 10px;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      background-color: #341a8c;
      color: #fff;
      transform: scale(1.05);
    }

    .background-circles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: -1;
    }

    .circle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      animation: float 15s ease-in-out infinite alternate;
      backdrop-filter: blur(15px);
    }

    .circle:nth-child(1) {
      width: 300px;
      height: 300px;
      top: 10%;
      left: 20%;
      animation-delay: 0s;
    }

    .circle:nth-child(2) {
      width: 200px;
      height: 200px;
      bottom: 20%;
      right: 15%;
      animation-delay: 4s;
    }

    .circle:nth-child(3) {
      width: 400px;
      height: 400px;
      bottom: 5%;
      left: 35%;
      animation-delay: 8s;
    }

    @keyframes float {
      to {
        transform: translateY(-50px) translateX(50px);
      }
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="background-circles">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
  </div>

  <div class="container">
    <h2>📚 My Courses</h2>

    <div class="course-card">
      <h4>Web Programming</h4>
      <p>Instructor: Prof. Maria Fazio</p>
    </div>

    <div class="course-card">
      <h4>System Security</h4>
      <p>Instructor: Prof. Massimo Villari</p>
    </div>

    <div class="course-card">
      <h4>Data Analysis</h4>
      <p>Instructor: Prof. Giacomo Fiumara</p>
    </div>

    <button onclick="window.location.href='student-dashboard.php'" class="btn back-btn">Back to Dashboard</button>
  </div>
</body>
</html>