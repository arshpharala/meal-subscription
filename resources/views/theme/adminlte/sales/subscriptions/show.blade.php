@extends('theme.adminlte.layouts.app')

@section('content-header')
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="fw-bold mb-1">
        <i class="fas fa-receipt me-2"></i> Subscription {!! $subscription->status_badge !!}
      </h1>
    </div>
    <a href="{{ route('admin.sales.subscriptions.index') }}" class="btn btn-outline-secondary">
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
              <img src="https://ui-avatars.com/api/?name={{ urlencode($subscription->user->name) }}&background=random"
                alt="User Avatar" class="rounded-circle border" width="64" height="64">
            </div>
            <div>
              <h5 class="fw-bold mb-1">{{ $subscription->user->name }}</h5>
              <span class="text-muted small">{{ $subscription->user->email }}</span><br>
              <span class="text-muted small">{{ $subscription->user->phone }}</span>
            </div>
          </div>
          <hr>
          <h6 class="text-uppercase text-muted fw-bold small mb-2">Delivery Address</h6>
          @if ($subscription->address_id)
            <p class="text-secondary mb-0">{!! $subscription->address->render(true) !!}</p>
          @else
            <p class="text-muted mb-0">No address available</p>
          @endif
        </div>
      </div>

      {{-- Payment Summary --}}
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-credit-card text-primary me-2"></i> Payment Summary
        </div>
        @php
          $base = $subscription->mealPackagePrice->price ?? 0;
          $vatPercent = $subscription->address->country->tax->percentage ?? 5;
          $vatLabel = $subscription->address->country->tax->label ?? 'VAT';
          $vat = round(($base * $vatPercent) / 100, 2);
          $total = $base + $vat;
        @endphp
        <div class="card-body">
          <p class="mb-2"><strong>Payment Reference:</strong> {{ $subscription->reference ?? 'N/A' }}</p>
          <p class="mb-2"><strong>Status:</strong>
            @if ($subscription->stripe_status == 'paid')
              <span class="badge bg-success">Paid</span>
            @else
              <span class="badge bg-warning text-dark">{{ ucfirst($subscription->stripe_status) }}</span>
            @endif
          </p>
          <hr>
          <p class="mb-1"><span>Base Price</span>
            <strong class="float-end">AED {{ number_format($base, 2) }}</strong>
          </p>
          <p class="mb-1"><span>{{ $vatLabel }} ({{ $vatPercent }}%)</span>
            <strong class="float-end">AED {{ number_format($vat, 2) }}</strong>
          </p>
          <hr class="my-2">
          <p class="fw-bold text-success mb-0">Total (Incl. VAT)
            <strong class="float-end">AED {{ number_format($total, 2) }}</strong>
          </p>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-xl-8 col-lg-7">

      {{-- Meal Plan --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-utensils text-primary me-2"></i> Meal Plan Information
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-8">
              <h5 class="fw-bold mb-1">{{ $subscription->mealPackage->meal->name ?? 'N/A' }}</h5>
              <p class="text-muted small mb-0">{{ $subscription->mealPackage->package->tagline ?? '' }}</p>
            </div>
            <div class="col-md-4 text-end">
              <span class="badge bg-primary">{{ $subscription->mealPackagePrice->calorie->label ?? 'N/A' }} kcal</span>
            </div>
          </div>

          <div class="row border-top pt-3">
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Package</p>
              <p class="fw-semibold mb-0">{{ $subscription->mealPackage->package->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Duration</p>
              <p class="fw-semibold mb-0">{{ $subscription->mealPackagePrice->duration ?? '-' }} Days</p>
            </div>
            <div class="col-md-4">
              <p class="mb-1 text-muted small">Amount</p>
              <p class="fw-semibold mb-0">AED {{ number_format($subscription->mealPackagePrice->price ?? 0, 2) }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Schedule --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-calendar-alt text-primary me-2"></i> Subscription Schedule
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <p class="mb-1 text-muted small">Start Date</p>
                <h6 class="fw-bold mb-0">{{ $subscription->start_date?->format('d M Y') ?? 'N/A' }}</h6>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <p class="mb-1 text-muted small">End Date</p>
                <h6 class="fw-bold mb-0">{{ $subscription->end_date?->format('d M Y') ?? 'N/A' }}</h6>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-light rounded">
                <p class="mb-1 text-muted small">Next Charge</p>
                <h6 class="fw-bold mb-0">{{ $subscription->next_charge_date?->format('d M Y') ?? 'N/A' }}</h6>
              </div>
            </div>
          </div>
          <hr>
          <p class="mb-1"><strong>Auto Charge:</strong>
            @if ($subscription->auto_charge)
              <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> Enabled</span>
            @else
              <span class="text-muted fw-semibold"><i class="fas fa-times-circle me-1"></i> Disabled</span>
            @endif
          </p>
        </div>
      </div>

      {{-- Freeze History --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold">
          <i class="fas fa-snowflake text-primary me-2"></i> Freeze History
        </div>
        <div class="card-body">
          @if ($subscription->freezes->isEmpty())
            <div class="text-center py-4 text-muted">
              <i class="fas fa-snowflake fa-2x mb-2"></i>
              <p class="mb-0">No freezes recorded.</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead>
                  <tr>
                    <th>Period</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($subscription->freezes as $freeze)
                    @php
                      $badge =
                          [
                              'scheduled' => 'secondary',
                              'active' => 'warning',
                              'completed' => 'success',
                              'cancelled' => 'dark',
                          ][$freeze->status] ?? 'secondary';
                    @endphp
                    <tr>
                      <td>{{ $freeze->freeze_start_date->format('d M Y') }} →
                        {{ $freeze->freeze_end_date->format('d M Y') }}</td>
                      <td>{{ $freeze->frozen_days }}</td>
                      <td><span class="badge bg-{{ $badge }}">{{ ucfirst($freeze->status) }}</span></td>
                      <td>{{ $freeze->reason ?? '—' }}</td>
                      <td>
                        @if ($freeze->status === 'scheduled')
                          <button class="btn btn-sm btn-outline-danger btn-delete" type="button"
                            data-url="{{ route('admin.sales.subscription.freezes.destroy', [$subscription, $freeze]) }}">
                            <i class="fas fa-times me-1"></i> Cancel
                          </button>
                        @else
                          —
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Renewal History --}}
      <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom fw-bold d-flex align-items-center justify-content-between">
          <div><i class="fas fa-redo-alt text-primary me-2"></i> Renewal History</div>

        </div>

        <div class="card-body">
          @if ($subscription->renewalLogs->isEmpty())
            <div class="text-center py-4 text-muted">
              <i class="fas fa-history fa-2x mb-2"></i>
              <p class="mb-0">No renewal records found for this subscription yet.</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="bg-light text-muted small text-uppercase">
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Gateway</th>
                    <th>Amount</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Reference</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($subscription->renewalLogs->sortByDesc('charged_at') as $log)
                    @php
                      $statusBadge = match ($log->status) {
                          'success' => 'success',
                          'failed' => 'danger',
                          'pending' => 'warning',
                          default => 'secondary',
                      };
                      $gatewayName = match ($log->gateway_id) {
                          1 => ['label' => 'Stripe', 'icon' => 'fab fa-cc-stripe text-primary'],
                          2 => ['label' => 'PayPal', 'icon' => 'fab fa-cc-paypal text-info'],
                          3 => ['label' => 'Cash', 'icon' => 'fas fa-money-bill text-muted'],
                          default => ['label' => 'Other', 'icon' => 'fas fa-credit-card text-muted'],
                      };
                    @endphp
                    <tr>
                      <td class="text-muted small">{{ $loop->iteration }}</td>
                      <td>{{ $log->charged_at?->format('d M Y, h:i A') ?? 'N/A' }}<br>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                      </td>
                      <td><i class="{{ $gatewayName['icon'] }} me-1"></i>{{ $gatewayName['label'] }}</td>
                      <td class="">AED {{ number_format($log->amount, 2) }}</td>
                      <td class="">AED {{ number_format($log->tax_amount, 2) }}</td>
                      <td class="fw-bold text-success ">AED {{ number_format($log->total_amount, 2) }}</td>
                      <td><span class="badge bg-{{ $statusBadge }}">{{ ucfirst($log->status) }}</span></td>
                      <td class="text-monospace small">{{ Str::limit($log->reference, 20) ?? 'N/A' }}</td>
                      <td class="text-center">
                        @if ($log->receipt_url)
                          <a href="{{ $log->receipt_url }}" target="_blank"
                            class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="View Receipt">
                            <i class="fas fa-receipt"></i>
                          </a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Action Buttons --}}
      <div class="text-end mt-3">
        @if ($subscription->status == 'active')
          <button class="btn btn-danger btn-delete" type="button"
            data-url="{{ route('admin.sales.subscriptions.destroy', ['subscription' => $subscription]) }}">
            <i class="fas fa-ban me-1"></i> Cancel Subscription
          </button>
          <button class="btn btn-warning"
            data-url="{{ route('admin.sales.subscription.freezes.create', $subscription) }}" onclick="getAside()">
            <i class="fas fa-snowflake me-1"></i> Freeze Subscription
          </button>
        @endif

        {{-- Manual Renew --}}
        @if ($subscription->end_date?->isPast() || !$subscription->auto_charge)
          <button class="btn btn-primary btn-renew"
            data-url="{{ route('admin.sales.subscription.manualRenew', $subscription->id) }}">
            <i class="fas fa-redo me-1"></i> Manual Renew
          </button>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(function() {
      $('[data-bs-toggle="tooltip"]').tooltip();

      $(document).on('click', '.btn-retry', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        const btn = $(this);
        Swal.fire({
          title: 'Retry Payment?',
          text: 'This will reattempt the renewal charge using saved card details.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Retry',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        }).then(result => {
          if (result.isConfirmed) {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
              url: url,
              type: 'POST',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(res) {
                Swal.fire(res.title, res.message, 'success');
                setTimeout(() => window.location.reload(), 1200);
              },
              error: function(err) {
                const msg = err.responseJSON?.message || 'Retry failed.';
                Swal.fire('Error', msg, 'error');
              },
              complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-redo"></i>');
              }
            });
          }
        });
      });

      $(document).on('click', '.btn-renew', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        const btn = $(this);
        Swal.fire({
          title: 'Renew Subscription?',
          text: 'This will extend the subscription and charge the customer again.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, Renew Now',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        }).then(result => {
          if (result.isConfirmed) {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Renewing...');
            $.ajax({
              url: url,
              type: 'POST',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(res) {
                Swal.fire(res.title, res.message, 'success');
                setTimeout(() => window.location.reload(), 1200);
              },
              error: function(err) {
                const msg = err.responseJSON?.message || 'Renewal failed';
                Swal.fire('Error', msg, 'error');
              },
              complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-redo me-1"></i> Manual Renew');
              }
            });
          }
        });
      });
    });
  </script>
@endpush
