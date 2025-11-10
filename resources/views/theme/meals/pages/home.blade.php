<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ env('APP_NAME') }} | Your Daily Dose of Nutrition</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: #fff;
      background: radial-gradient(circle at top right, #ff8a00, #ff4b2b);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
      overflow: hidden;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .logo {
      font-size: 4.5rem;
      font-weight: 700;
      letter-spacing: 2px;
      color: #fff;
      text-shadow: 0 3px 10px rgba(0,0,0,0.3);
      position: relative;
      display: inline-block;
    }

    .logo::after {
      content: '';
      position: absolute;
      bottom: -12px;
      left: 50%;
      transform: translateX(-50%);
      width: 60%;
      height: 4px;
      background: linear-gradient(90deg, #fff, #f9b233, #fff);
      border-radius: 2px;
      animation: shimmer 2s infinite linear;
    }

    @keyframes shimmer {
      0% { opacity: 0.3; width: 20%; }
      50% { opacity: 1; width: 60%; }
      100% { opacity: 0.3; width: 20%; }
    }

    p.tagline {
      margin-top: 20px;
      font-size: 1.4rem;
      font-weight: 500;
      color: #fff;
      opacity: 0.9;
    }

    .buttons {
      margin-top: 40px;
    }

    a.btn {
      display: inline-block;
      margin: 0 10px;
      padding: 12px 30px;
      border-radius: 50px;
      background-color: rgba(255,255,255,0.15);
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.25);
      transition: all 0.3s ease;
    }

    a.btn:hover {
      background-color: #fff;
      color: #ff4b2b;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .leaf-bg {
      position: absolute;
      inset: 0;
      background: url('{{ asset('theme/meals/assets/images/pattern.png') }}') center/cover no-repeat;
      opacity: 0.1;
      z-index: 0;
    }

    .content {
      position: relative;
      z-index: 10;
      padding: 20px;
    }

    @media (max-width: 600px) {
      .logo { font-size: 2.5rem; }
      p.tagline { font-size: 1.1rem; }
      a.btn { padding: 10px 20px; font-size: 0.9rem; }
    }
  </style>
</head>

<body>
  <div class="leaf-bg"></div>
  <div class="content">
    <h1 class="logo">{{ env('APP_NAME', 'Nutrify') }}</h1>
    <p class="tagline">Your Daily Dose of Nutrition.</p>
    <div class="buttons">
      <a href="tel:+97100000000" class="btn">Contact Us</a>
      <a href="{{ route('admin.login') }}" class="btn">Admin Login</a>
    </div>
  </div>
</body>
</html>
