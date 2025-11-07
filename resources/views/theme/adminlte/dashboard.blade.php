@extends('theme.adminlte.layouts.app')
@push('header')
  <style>
    .skeleton-loader {
      background: linear-gradient(-90deg, #f0f0f0 0%, #f4f4f4 50%, #f0f0f0 100%);
      background-size: 400% 400%;
      animation: shimmer 1.2s ease-in-out infinite;
      border-radius: 8px;
    }

    @keyframes shimmer {
      0% {
        background-position: 100% 0;
      }

      100% {
        background-position: -100% 0;
      }
    }
  </style>
@endpush
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Dashboard</h1>
    </div>
    <div class="col-sm-6">
      <div class="input-group w-50 float-end">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
          </span>
        </div>
        <input type="text" class="form-control float-right" id="dashboardDateRange" style="width: 250px;" />
      </div>
    </div>
  </div>
@endsection
@section('content')
  {{-- <div class="row text-center">
    <div class="col-md-3">
      <div class="card p-3">
        <h5>Total Sales</h5>
        <h2 id="total-sales" class="skeleton-loader">...</h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h5>Total Orders</h5>
        <h2 id="total-orders" class="skeleton-loader">...</h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h5>Total Customers</h5>
        <h2 id="total-customers" class="skeleton-loader">...</h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h5>Total Products</h5>
        <h2 id="total-products" class="skeleton-loader">...</h2>
      </div>
    </div>
  </div> --}}

  {{-- <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Sales Overview</h5>
          <canvas id="salesChart" height="100"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title">Recent Orders</h5>
        </div>
        <div class="card-body">
          <div id="ordersLoader" class="skeleton-loader" style="height: 160px;"></div>
          <table class="table table-hover d-none" id="recentOrdersTable">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title">Top Selling Products</h5>
        </div>
        <div class="card-body">
          <div id="topProductsLoader" class="skeleton-loader" style="height: 100px;"></div>
          <canvas id="topProductsChart" class="d-none" height="100"></canvas>
        </div>
      </div>
    </div>


    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title">New Customers</h5>
        </div>
        <div class="card-body">
          <div id="newCustomersLoader" class="skeleton-loader" style="height: 100px;"></div>
          <canvas id="newCustomersChart" class="d-none" height="100"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title">Payment Method Breakdown</h5>
        </div>
        <div class="card-body">
          <div id="paymentMethodLoader" class="skeleton-loader" style="height: 100px;"></div>
          <canvas id="paymentMethodChart" class="d-none" height="100"></canvas>
        </div>
      </div>
    </div>

  </div> --}}
@endsection

@push('scripts')
  <script src="{{ asset('theme/adminlte/plugins/moment/moment.min.js') }}"></script>
  <!-- date-range-picker -->
  <script src="{{ asset('theme/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  {{-- <script src="{{ asset('assets/js/dashboard.js') }}"></script> --}}

  <script>
    $('#reservation').daterangepicker();
  </script>
@endpush
