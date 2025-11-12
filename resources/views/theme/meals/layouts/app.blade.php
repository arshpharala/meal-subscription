<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? env('APP_NAME') }} | Nutrify</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}">

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Font Awesome Free CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>


  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
      {{-- SweetAlert2 --}}
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">


  {{-- Tailwind / Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script>
    const appUrl = '{{ env('APP_URL') }}';
  </script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9fafb;
      color: #333;
    }

    header {
      background: linear-gradient(90deg, #ff8a00, #ff4b2b);
      color: #fff;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .nav-link:hover {
      color: #fff;
    }

    .btn-orange {
      background-color: #ff4b2b;
      border: none;
    }

    .btn-orange:hover {
      background-color: #ff8a00;
    }
  </style>
</head>

<body class="min-h-screen d-flex flex-column">

  {{-- Header --}}
  <header class="shadow">
    <div class="container py-3 d-flex justify-content-between align-items-center">
      <div class="fs-3 fw-bold">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
          {{ env('APP_NAME', 'Nutrify') }}
        </a>
      </div>
      <nav class="d-flex align-items-center gap-3">
        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        <a href="{{ route('customer.addresses.index') }}" class="nav-link">Addresses</a>
        <a href="{{ route('customer.subscriptions.index') }}" class="nav-link">Subscriptions</a>

        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-light text-danger fw-semibold d-flex align-items-center gap-1">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </nav>
    </div>
  </header>

  {{-- Content --}}
  <main class="flex-grow-1 py-5">
    <div class="container">
      @yield('content')
    </div>
  </main>

  {{-- Footer --}}
  <footer class="bg-light text-center py-3 border-top small text-muted">
    © {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
  </footer>

  {{-- jQuery & Bootstrap JS --}}
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

  <script src="{{ asset('theme/adminlte/assets/js/form.js') }}"></script>
  <script src="{{ asset('theme/adminlte/assets/js/address.js') }}"></script>

  {{-- Page-Specific Scripts --}}
  @stack('scripts')
</body>

</html>
