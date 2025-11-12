<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | {{ config('app.name', 'Nutrify') }}</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at top right, #ff8a00, #ff4b2b);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow: hidden;
      color: #333;
    }
    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .leaf-bg {
      position: absolute;
      inset: 0;
      background: url('{{ asset('theme/meals/assets/images/pattern.png') }}') center/cover no-repeat;
      opacity: 0.08;
      z-index: 0;
    }

    .auth-card {
      position: relative;
      z-index: 10;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      backdrop-filter: blur(8px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
      width: 100%;
      max-width: 420px;
      padding: 40px 35px;
      text-align: center;
    }

    .auth-title {
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 8px;
      text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .auth-subtitle {
      font-size: 0.95rem;
      color: rgba(255,255,255,0.9);
      margin-bottom: 25px;
    }

    input, button {
      font-family: inherit;
      border: none;
      outline: none;
      border-radius: 8px;
      font-size: 0.95rem;
    }

    input {
      width: 100%;
      padding: 12px 14px;
      background: rgba(255, 255, 255, 0.85);
      color: #333;
      margin-bottom: 15px;
    }

    input:focus {
      background: #fff;
      box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
    }

    .btn {
      width: 100%;
      padding: 12px;
      background-color: #fff;
      color: #ff4b2b;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #f9b233;
      color: #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .extras {
      margin-top: 15px;
      font-size: 0.85rem;
      color: #fff;
      display: flex;
      justify-content: space-between;
    }

    .extras a {
      color: #fff;
      text-decoration: none;
      opacity: 0.9;
    }
    .extras a:hover {
      opacity: 1;
      text-decoration: underline;
    }

    @media(max-width: 480px) {
      .auth-card { padding: 30px 25px; }
      .auth-title { font-size: 1.8rem; }
    }
  </style>

  @stack('styles')
</head>
<body>
  <div class="leaf-bg"></div>

  <div class="auth-card">
    <h1 class="auth-title">{{ config('app.name', 'Nutrify') }}</h1>
    <p class="auth-subtitle">@yield('subtitle')</p>

    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
