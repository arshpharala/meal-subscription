<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Cancelled - Meal Plan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .cancel-card {
      max-width: 520px;
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
      padding: 40px 30px;
      text-align: center;
    }
    .cancel-icon {
      font-size: 4rem;
      color: #dc3545;
    }
    .btn-home {
      margin-top: 25px;
      padding: 10px 30px;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <div class="cancel-card">
    <i class="fas fa-times-circle cancel-icon mb-3"></i>
    <h2 class="fw-bold text-danger mb-3">Payment Cancelled</h2>
    <p class="text-muted mb-4">
      Hello, <strong>{{ $paymentLink->user->name ?? 'Customer' }}</strong>.<br>
      You cancelled the payment process for your <strong>{{ $paymentLink->meal->name }}</strong> plan.
    </p>

    <div class="bg-light rounded p-3 mb-3 text-start small">
      <p class="mb-1"><strong>Meal:</strong> {{ $paymentLink->meal->name }}</p>
      <p class="mb-1"><strong>Duration:</strong> {{ $paymentLink->mealPackagePrice->duration }} Days</p>
      <p class="mb-1"><strong>Calories:</strong> {{ $paymentLink->mealPackagePrice->calorie->label }} kcal</p>
      <p class="mb-1"><strong>Amount:</strong> AED {{ number_format($paymentLink->mealPackagePrice->price, 2) }}</p>
      <p class="mb-0"><strong>Status:</strong> <span class="text-danger fw-semibold">Cancelled</span></p>
    </div>

    {{-- <p class="text-muted small">You can retry the payment anytime from your customer portal.</p>

    <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-home">
      <i class="fas fa-home me-2"></i> Back to Home
    </a> --}}
  </div>

</body>
</html>
