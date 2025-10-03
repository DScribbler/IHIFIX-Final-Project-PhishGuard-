<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Organization Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #2c3e50, #34495e, #e67e22);
      color: #fff;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(6px);
    }
    header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
    }
    nav a {
      margin-left: 1.5rem;
      text-decoration: none;
      color: #fff;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #e67e22;
    }
    .login-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .login-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.5);
      width: 100%;
      max-width: 400px;
      animation: fadeIn 0.8s ease-in-out;
      box-sizing: border-box;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-weight: 600;
    }
    .input-group {
      position: relative;
      margin-bottom: 15px;
      width: 100%;
    }
    .input-group i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #e67e22;
      font-size: 1rem;
      pointer-events: none;
    }
    .input-group input {
      width: 100%;
      box-sizing: border-box;
      padding: 12px 12px 12px 40px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }
    .input-group input::placeholder {
      color: #eee;
    }
    .login-box button {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #e67e22, #d35400);
      color: #fff;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      margin-top: 10px;
    }
    .login-box button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(230,126,34,0.5);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 600px) {
      header {
        flex-direction: column;
        text-align: center;
      }
      nav {
        margin-top: 0.5rem;
      }
      .login-box {
        padding: 1.5rem;
      }
      .input-group input {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Organization Portal</h1>
    <nav>
      <a href="index.php">Home</a>
    </nav>
  </header>

  <div class="login-container">
    <div class="login-box">
      <h2>Login</h2>

      <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
        <p style="color: #ffdddd; background-color: #c0392b; padding: 10px; border-radius: 6px; text-align: center;">
          Invalid username or password.
        </p>
      <?php endif; ?>

      <form method="post" action="includes/auth.php">
        <div class="input-group">
          <i class="fa fa-user"></i>
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
          <i class="fa fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit">Log In</button>
      </form>
    </div>
  </div>
</body>
</html>
