<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Cancelled - {{ config('app.name') }}</title>
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
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border: 1px solid #e9ecef;
    }
    .header {
      background-color: #dc3545;
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
    $package = $checkout->mealPackage->package ?? null;
  @endphp

  <div class="email-wrapper">
    <div class="header">
      <h2>Payment Cancelled</h2>
    </div>

    <div class="body">
      <p>Hi <strong>{{ $checkout->user->name ?? 'Customer' }}</strong>,</p>
      <p>
        It looks like you cancelled your payment for the <strong>{{ $checkout->meal->name }}</strong> meal plan.
        No charges have been made.
      </p>

      {{-- Order Summary --}}
      <div class="summary">
        <p><span>Base Price</span> <strong>AED {{ number_format($basePrice, 2) }}</strong></p>
        <p><span>Status</span> <strong class="text-danger">Cancelled</strong></p>
      </div>

      {{-- Details --}}
      <p><strong>Meal:</strong> {{ $checkout->meal->name }}</p>
      @if($package)
        <p><strong>Package:</strong> {{ $package->name }}<br>
        <span style="color:#6c757d;">{{ $package->tagline }}</span></p>
      @endif
      <p><strong>Duration:</strong> {{ $checkout->mealPackagePrice->duration }} Days</p>
      <p><strong>Calories:</strong> {{ $checkout->mealPackagePrice->calorie->label }} kcal</p>

      <p>
        You can retry the payment anytime from your customer portal or contact support if you need help completing your order.
      </p>

      <p style="margin-top:25px;">Warm regards,<br><strong>{{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
      <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
