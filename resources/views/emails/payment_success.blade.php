<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Confirmation - {{ config('app.name') }}</title>
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Inter', Arial, sans-serif;
      color: #212529;
      margin: 0;
      padding: 40px 0;
    }

    .email-wrapper {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
    }

    .header {
      background-color: #28a745;
      color: #fff;
      text-align: center;
      padding: 25px;
    }

    .header h2 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    .body {
      padding: 30px;
    }

    .body p {
      margin-bottom: 12px;
      line-height: 1.6;
    }

    .summary {
      background-color: #f8f9fa;
      padding: 15px 20px;
      border-radius: 8px;
      margin: 20px 0;
      font-size: 14px;
    }

    .summary p {
      margin: 5px 0;
    }

    .summary strong {
      float: right;
    }

    .footer {
      background-color: #f1f3f5;
      text-align: center;
      padding: 15px;
      font-size: 13px;
      color: #6c757d;
    }
  </style>
</head>

<body>
  @php
    $basePrice = $checkout->mealPackagePrice->price;
    $vatPercent = $checkout->tax->percentage ?? 5;
    $vatLabel = $checkout->tax->label ?? 'VAT';
    $vatAmount = round($basePrice * ($vatPercent / 100), 2);
    $total = $basePrice + $vatAmount;
    $package = $checkout->mealPackage->package ?? null;
  @endphp

  <div class="email-wrapper">
    <div class="header">
      <h2>Payment Successful!</h2>
    </div>

    <div class="body">
      <p>Hi <strong>{{ $checkout->user->name ?? 'Customer' }}</strong>,</p>
      <p>
        We’ve received your payment for the <strong>{{ $checkout->meal->name }}</strong> meal plan.
        Your subscription has been successfully activated.
      </p>

      {{-- Price Summary --}}
      <div class="summary">
        <p><span>Base Price</span> <strong>AED {{ number_format($basePrice, 2) }}</strong></p>
        <p><span>{{ $vatLabel }} ({{ $vatPercent }}%)</span> <strong>AED
            {{ number_format($vatAmount, 2) }}</strong></p>
        <hr style="border:none;border-top:1px dashed #ccc;">
        <p><strong>Total (Incl. VAT)</strong> <strong>AED {{ number_format($total, 2) }}</strong></p>
      </div>

      {{-- Details --}}
      <p><strong>Meal:</strong> {{ $checkout->meal->name }}</p>
      @if ($package)
        <p><strong>Package:</strong> {{ $package->name }}<br>
          <span style="color:#6c757d;">{{ $package->tagline }}</span>
        </p>
      @endif
      <p><strong>Duration:</strong> {{ $checkout->mealPackagePrice->duration }} Days</p>
      <p><strong>Calories:</strong> {{ $checkout->mealPackagePrice->calorie->label }} kcal</p>

      <p>We hope you enjoy your meal plan experience! 🍽️</p>

      <p style="margin-top:25px;">Warm regards,<br><strong>{{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
      <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
  </div>
</body>

</html>
