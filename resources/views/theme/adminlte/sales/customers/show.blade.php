@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="">Customer</h1>
    </div>
    <a href="{{ route('admin.sales.customers.index') }}" class="btn btn-secondary ">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
@endsection
@section('content')
  <div class="row">
    <div class="col-md-3">

      <!-- Customer Profile Card -->
      <div class="card card-outline card-primary shadow-sm">
        <div class="card-body box-profile">

          <!-- Profile Image -->
          <div class="text-center mb-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=random"
              alt="User Avatar" class="profile-user-img img-fluid img-circle shadow" width="64" height="64">
          </div>

          <h3 class="profile-username text-center mb-0">{{ $customer->name }}</h3>
          <p class="text-muted text-center mb-3">{{ $customer->email }}<br>{{ $customer->phone }}</p>

          <ul class="list-group list-group-unbordered mb-3">

            <!-- Account Status -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-user-circle text-primary me-1"></i> Account Status</span>
              @if ($customer->is_active)
                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Active</span>
              @else
                <span class="badge badge-secondary px-2 py-1"><i class="fas fa-user-slash me-1"></i> Inactive</span>
              @endif
            </li>

            <!-- Total Subscriptions -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-layer-group text-info me-1"></i> Total Subscriptions</span>
              <span class="badge badge-info px-2 py-1">
                {{ $customer->subscriptions->count() }}
              </span>
            </li>

            <!-- Active Subscriptions -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-play-circle text-success me-1"></i> Active Subscriptions</span>
              <span class="badge badge-success px-2 py-1">
                {{ $customer->subscriptions()->active()->count() }}
              </span>
            </li>

            <!-- Frozen Subscriptions -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-snowflake text-warning me-1"></i> Frozen Subscriptions</span>
              <span class="badge badge-warning px-2 py-1">
                {{ $customer->subscriptions()->where('status', 'paused')->count() }}
              </span>
            </li>
            <!-- Cancelled Subscriptions -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><i class="fas fa-times text-danger me-1"></i> Cancelled Subscriptions</span>
              <span class="badge badge-danger px-2 py-1">
                {{ $customer->subscriptions()->canceled()->count() }}
              </span>
            </li>

          </ul>

          {{-- Restrict Payment Link Creation if No Address --}}
          @if ($customer->addresses->count() === 0)
            <div class="alert alert-warning text-center py-2 mb-3">
              <i class="fas fa-exclamation-triangle me-1"></i>
              Please <strong>add at least one address</strong> before creating a payment link.
            </div>
            <button class="btn btn-secondary btn-block" disabled>
              <i class="fas fa-link me-1"></i> Create Payment Link
            </button>
          @else
            <a href="{{ route('admin.sales.customer.subscriptions.create', ['customer' => $customer->id]) }}"
              class="btn btn-primary btn-block">
              <i class="fas fa-link me-1"></i> Create Payment Link
            </a>
          @endif


        </div>
      </div>


      <!-- About Customer -->
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user me-2"></i> About Customer</h3>
        </div>
        <div class="card-body">

          <strong><i class="fas fa-calendar-check mr-1"></i> Joined On</strong>
          <p class="text-muted mb-2">
            {{ $customer->created_at?->format('d M Y') ?? 'N/A' }}
          </p>

          <hr>

          <strong><i class="fas fa-map-marker-alt mr-1"></i> Default Address</strong>
          <p class="text-muted mb-2">
            @if ($customer->default_address_id)
              {{ $customer->defaultAddress->render(true) }}
            @else
              No address available
            @endif
          </p>

          <hr>


          <strong><i class="fas fa-credit-card mr-1"></i> Payment Methods</strong>
          <p class="text-muted mb-2">
            @forelse($customer->paymentMethods() as $method)
              <i class="fab fa-cc-{{ strtolower($method->card->brand) }}"></i>
              **** {{ $method->card->last4 }}
              <small class="text-muted">EXP {{ $method->card->exp_month }}/{{ $method->card->exp_year }}</small><br>
            @empty
              <span class="text-muted">No saved payment methods</span>
            @endforelse
          </p>

        </div>
      </div>

    </div>
    <!-- /.col -->
    <!-- RIGHT CONTENT -->
    <div class="col-md-9">
      <div class="card shadow-sm">
        <div class="card-header p-2">
          <ul class="nav nav-pills gap-2">
            <li class="nav-item">
              <a class="nav-link active" href="#subscriptions" data-toggle="tab">
                <i class="fas fa-sync-alt"></i> Subscriptions
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#addresses" data-toggle="tab">
                <i class="fas fa-map-marker-alt"></i> Addresses
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body">
          <div class="tab-content">
            <!-- Subscriptions TAB -->
            <div class="active tab-pane" id="subscriptions">
              @include('theme.adminlte.sales.customers.subscriptions.index', [
                  'subscriptions' => $customer->subscriptions,
                  'customer' => $customer,
              ])
            </div>
            <!-- ADDRESSES TAB -->
            <div class="tab-pane" id="addresses">
              @include('theme.adminlte.sales.customers.addresses.index', [
                  'addresses' => $customer->addresses,
                  'customer' => $customer,
              ])
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- /.col -->
  </div>
@endsection
@push('scripts')
  <script></script>
@endpush
