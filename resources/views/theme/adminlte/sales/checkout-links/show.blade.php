@extends('theme.adminlte.layouts.app')

@section('content-header')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="fw-bold mb-1">
        <i class="fas fa-link me-2"></i> Payment Link Details
        @if ($paymentLink->status === 'paid')
          <span class="badge bg-success ms-2">Paid</span>
        @elseif ($paymentLink->status === 'pending')
          <span class="badge bg-warning text-dark ms-2">Pending</span>
        @else
          <span class="badge bg-danger ms-2">{{ ucfirst($paymentLink->status) }}</span>
        @endif
      </h1>
      <p class="text-muted mb-0 small">ID: {{ $paymentLink->id }}</p>
    </div>
    <a href="{{ route('admin.sales.payment-links.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Back to List
    </a>
  </div>
@endsection

@section('content')
  <div class="row g-4">

    {{-- LEFT COLUMN --}}
    <div class="col-xl-4 col-lg-5">
      {{-- Customer Card --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-user-circle text-primary me-2"></i> Customer
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3">
              <img src="https://ui-avatars.com/api/?name={{ urlencode($paymentLink->user->name) }}&background=random"
                class="rounded-circle border" width="64" height="64">
            </div>
            <div>
              <h5 class="fw-bold mb-1">{{ $paymentLink->user->name ?? 'N/A' }}</h5>
              <span class="text-muted small">{{ $paymentLink->user->email }}</span><br>
              <span class="text-muted small">{{ $paymentLink->user->phone }}</span>
            </div>
          </div>
          <hr>
          <h6 class="text-uppercase text-muted fw-bold small mb-2">Delivery Address</h6>
          @if ($paymentLink->address_id)
            <p class="text-secondary mb-0">{!! $paymentLink->address->render(true) !!}</p>
          @else
            <p class="text-muted mb-0">No address available.</p>
          @endif
        </div>
      </div>

      {{-- Payment Summary --}}
      @php
        $vatPercent = $paymentLink->tax->percentage ?? 5;
        $vatLabel = $paymentLink->tax->label ?? 'VAT';
      @endphp

      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-credit-card text-primary me-2"></i> Payment Summary
        </div>
        <div class="card-body small">
          <p class="mb-2"><strong>Stripe Session:</strong> {{ $paymentLink->stripe_session_id ?? 'N/A' }}</p>
          <p class="mb-2"><strong>Stripe Status:</strong>
            @if ($paymentLink->status === 'paid')
              <span class="badge bg-success">Paid</span>
            @elseif ($paymentLink->status === 'pending')
              <span class="badge bg-warning text-dark">Pending</span>
            @else
              <span class="badge bg-danger">{{ ucfirst($paymentLink->status) }}</span>
            @endif
          </p>
          <hr>
          <p class="mb-1"><span>Base Price</span>
            <strong class="float-end">AED {{ number_format($paymentLink->sub_total, 2) }}</strong>
          </p>
          <p class="mb-1"><span>{{ $vatLabel }} ({{ $vatPercent }}%)</span>
            <strong class="float-end">AED {{ number_format($paymentLink->tax_amount, 2) }}</strong>
          </p>
          <hr class="my-2">
          <p class="fw-bold text-success mb-0">Total (Incl. VAT)
            <strong class="float-end">AED {{ number_format($paymentLink->total, 2) }}</strong>
          </p>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-xl-8 col-lg-7">

      {{-- Meal Plan --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-utensils text-primary me-2"></i> Meal Plan Details
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-8">
              <h5 class="fw-bold mb-1">{{ $paymentLink->meal->name ?? 'N/A' }}</h5>
              <p class="text-muted small mb-0">
                {{ $paymentLink->mealPackage->package->tagline ?? '' }}
              </p>
            </div>
            <div class="col-md-4 text-end">
              <span class="badge bg-primary">
                {{ $paymentLink->mealPackagePrice->calorie->label ?? 'N/A' }} kcal
              </span>
            </div>
          </div>
          <div class="row border-top pt-3">
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Package</p>
              <p class="fw-semibold mb-0">{{ $paymentLink->mealPackage->package->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Duration</p>
              <p class="fw-semibold mb-0">{{ $paymentLink->mealPackagePrice->getDurationLabel() }}</p>
            </div>
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Start Date</p>
              <p class="fw-semibold mb-0">
                {{ $paymentLink->start_date ? \Carbon\Carbon::parse($paymentLink->start_date)->format('d M Y') : 'N/A' }}
              </p>
            </div>
          </div>
          <hr>
          <p class="mb-0"><strong>Recurring:</strong>
            @if ($paymentLink->is_recurring)
              <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> Enabled</span>
            @else
              <span class="text-muted fw-semibold"><i class="fas fa-times-circle me-1"></i> Disabled</span>
            @endif
          </p>
        </div>
      </div>

      {{-- Checkout Link Info --}}
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-link text-primary me-2"></i> Checkout Portal Link
        </div>
        <div class="card-body">
          @if ($paymentLink->portal_url)
            <div class="input-group mb-3">
              <input type="text" readonly class="form-control" value="{{ $paymentLink->portal_url }}">
              <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $paymentLink->portal_url }}')">
                <i class="fas fa-copy"></i>
              </button>
            </div>
            <a href="{{ $paymentLink->portal_url }}" target="_blank" class="btn btn-primary w-100">
              <i class="fas fa-external-link-alt me-1"></i> Open Checkout Page
            </a>
          @else
            <p class="text-muted mb-0">No portal link generated.</p>
          @endif
        </div>
      </div>

      {{-- Action --}}
      <div class="text-end mt-3">
        @if ($paymentLink->status === 'pending')
          <a href="{{ $paymentLink->portal_url }}" class="btn btn-success">
            <i class="fas fa-redo me-1"></i> Retry Payment
          </a>
        @elseif ($paymentLink->status === 'paid')
          <button class="btn btn-secondary" disabled>
            <i class="fas fa-check-circle me-1"></i> Already Paid
          </button>
        @endif
      </div>
    </div>
  </div>

  <script>
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text);
      alert('Checkout link copied to clipboard!');
    }
  </script>
@endsection
