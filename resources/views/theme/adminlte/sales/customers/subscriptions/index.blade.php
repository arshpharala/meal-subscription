<div class="d-flex justify-content-between align-items-center mb-3">

  {{-- <a class="btn btn-sm btn-primary"
    href="{{ route('admin.sales.customer.subscriptions.create', ['customer' => $customer->id]) }}">
    <i class="fas fa-plus"></i> New Subscription
  </a> --}}
</div>

<div class="row">
  @forelse($subscriptions as $subscription)
    @php
      $mealPackage = $subscription->mealPackage;
    @endphp
    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
      <div class="card bg-light d-flex flex-fill shadow-sm subscription-card" data-id="{{ $subscription->id }}">
        <div class="card-header text-muted border-bottom-0">
          <span>
            <i class="fas fa-box-open me-1"></i>
            {{ ucfirst($subscription->type) }}
          </span>

          @php
            $statusColor = match ($subscription->status) {
                'active' => 'success',
                'paused' => 'warning',
                'cancelled' => 'secondary',
                'payment_failed' => 'danger',
                default => 'dark',
            };
          @endphp

          <span class="badge bg-{{ $statusColor }} float-end">
            {{ ucfirst($subscription->status) }}
          </span>
        </div>

        <div class="card-body pt-3 pb-2">
          <div class="row">
            <div class="col-3 text-center">
              <div class="rounded-circle bg-gradient-primary d-flex justify-content-center align-items-center"
                style="width:60px;height:60px;margin:auto;">
                <i class="fas fa-utensils text-white fs-4"></i>
              </div>
            </div>

            <div class="col-9">
              <h2 class="lead mb-1"><b>{{ $mealPackage->meal->name ?? 'Meal Plan' }}</b></h2>
              <p class="text-muted text-sm mb-1">
                <b>Package:</b> {{ $mealPackage?->package->name ?? 'N/A' }} - ({{ $mealPackage?->package->tagline }})
              </p>
              <p class="text-muted text-sm mb-1">
                <b>Duration:</b>
                {{ $subscription->mealPackagePrice?->duration ?? '--' }} days
              </p>
              <p class="text-muted text-sm mb-1">
                <b>Start Date:</b>
                {{ $subscription->start_date->format('d-M-Y') ?? '--' }}
              </p>
              <p class="text-muted text-sm mb-1">
                <b>End Date:</b>
                {{ $subscription->end_date->format('d-M-Y') ?? '--' }}
              </p>
              <p class="text-muted text-sm mb-1">
                <b>Amount:</b> AED {{ number_format($mealPackagePrice?->price ?? 0, 2) }}
              </p>
              @if ($subscription->next_charge_date)
                <p class="text-muted text-sm mb-1">
                  <b>Next Charge:</b> {{ \Carbon\Carbon::parse($subscription->next_charge_date)->format('d M Y') }}
                </p>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="text-right">
              <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('admin.sales.subscriptions.show', ['subscription' => $subscription]) }}">
                <i class="fas fa-eye"></i> View Detail
              </a>


          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-light border text-center">
        No subscriptions found for this customer.
      </div>
    </div>
  @endforelse
</div>
