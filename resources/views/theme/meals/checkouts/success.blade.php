<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Successful - Meal Plan</title>
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

    .success-card {
      max-width: 520px;
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
      padding: 40px 30px;
      text-align: center;
    }

    .success-icon {
      font-size: 4rem;
      color: #28a745;
    }

    .btn-home {
      margin-top: 25px;
      padding: 10px 30px;
      font-weight: 500;
    }

    .price-summary {
      background-color: #f8f9fa;
      border-radius: 0.75rem;
      padding: 1rem 1.2rem;
      text-align: left;
    }

    .price-summary p {
      margin-bottom: 0.4rem;
    }

    .price-summary strong {
      float: right;
    }

    .divider {
      border-top: 1px dashed #ccc;
      margin: 0.8rem 0;
    }
  </style>
</head>

<body>

  @php
    // VAT % and label (fallback to 5%)
    $vatPercent = $paymentLink->tax->percentage ?? 5;
    $vatLabel = $paymentLink->tax->label ?? 'VAT';

    // Package info
    $package = $paymentLink->mealPackage->package ?? null;
  @endphp

  <div class="success-card">
    <i class="fas fa-check-circle success-icon mb-3"></i>
    <h2 class="fw-bold text-success mb-3">Payment Successful!</h2>
    <p class="text-muted mb-4">
      Thank you, <strong>{{ $paymentLink->user->name ?? 'Customer' }}</strong>.<br>
      Your payment for <strong>{{ $paymentLink->meal->name }}</strong> has been received successfully.
    </p>

    {{-- Price Summary --}}
    <div class="price-summary mb-3 small">
      <p><span>Base Price</span> <strong>AED {{ number_format($paymentLink->sub_total, 2) }}</strong></p>
      <p><span>{{ $vatLabel }} ({{ number_format($vatPercent, 2) }}%)</span> <strong>AED
          {{ number_format($paymentLink->tax_amount, 2) }}</strong></p>
      <div class="divider"></div>
      <p class="fw-semibold text-success"><span>Total (Incl. VAT)</span> <strong>AED
          {{ number_format($paymentLink->total, 2) }}</strong></p>
    </div>

    {{-- Meal & Package Details --}}
    <div class="bg-light rounded p-3 mb-3 text-start small">
      <p class="mb-1"><strong>Meal:</strong> {{ $paymentLink->meal->name }}</p>
      @if ($package)
        <p class="mb-1"><strong>Package:</strong> {{ $package->name }}
          @if ($package->tagline)
            (<span class="text-muted  small">{{ $package->tagline }}</span>)
          @endif
        </p>
      @endif
      <p class="mb-1"><strong>Duration:</strong> {{ $paymentLink->mealPackagePrice->duration }} Days</p>
      <p class="mb-1"><strong>Calories:</strong> {{ $paymentLink->mealPackagePrice->calorie->label }} kcal</p>
      <p class="mb-0"><strong>Status:</strong> <span class="text-success fw-semibold">Paid</span></p>
    </div>

    <p class="text-muted small mb-0">
      A confirmation email will be sent to <strong>{{ $paymentLink->user->email }}</strong>.
    </p>

    {{-- <a href="{{ url('/') }}" class="btn btn-success btn-home">
      <i class="fas fa-home me-2"></i> Back to Home
    </a> --}}
  </div>

</body>

</html>
