@extends('theme.adminlte.layouts.app')

@push('header')
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="{{ asset('theme/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet"
    href="{{ asset('theme/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

  <style>
    .small-box {
      border-radius: 0.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: all .2s;
    }

    .small-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card {
      border-radius: .5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .card-header {
      background-color: #f8f9fa;
      font-weight: 600;
    }

    .dataTables_wrapper .dataTables_filter input {
      border-radius: 4px;
      border: 1px solid #ddd;
      padding: 3px 8px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border-radius: 4px !important;
    }
  </style>
@endpush

@section('content-header')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="m-0 text-dark"><i class="fas fa-tachometer-alt mr-2 text-primary"></i>Subscription Dashboard</h1>
  </div>
@endsection

@section('content')
  <div class="row">
    {{-- KPI BOXES --}}
    <div class="col-lg-2 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="stat-active">...</h3>
          <p>Active</p>
        </div>
        <div class="icon"><i class="fas fa-play-circle"></i></div>
      </div>
    </div>
    <div class="col-lg-2 col-6">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3 id="stat-subscribed">...</h3>
          <p>Upcoming (Subscribed)</p>
        </div>
        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
      </div>
    </div>
    <div class="col-lg-2 col-6">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3 id="stat-cancelled">...</h3>
          <p>Cancelled</p>
        </div>
        <div class="icon"><i class="fas fa-times-circle"></i></div>
      </div>
    </div>
    <div class="col-lg-2 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="stat-freezed">...</h3>
          <p>Freezed</p>
        </div>
        <div class="icon"><i class="fas fa-pause-circle"></i></div>
      </div>
    </div>
    <div class="col-lg-2 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3 id="stat-unpaid">...</h3>
          <p>Unpaid</p>
        </div>
        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
      </div>
    </div>
    <div class="col-lg-2 col-6">
      <div class="small-box bg-secondary">
        <div class="inner">
          <h3 id="total-revenue">AED ...</h3>
          <p>Total Revenue</p>
        </div>
        <div class="icon"><i class="fas fa-coins"></i></div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- CHART --}}
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0"><i class="fas fa-chart-line text-primary mr-2"></i>Revenue Trends</h3>
          <div class="card-tools">

            <div class="btn-group btn-group-sm chart-switch">
              <button class="btn btn-outline-primary active" data-type="month">Monthly</button>
              <button class="btn btn-outline-primary" data-type="week">Weekly</button>
              <button class="btn btn-outline-primary" data-type="day">Daily</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <canvas id="revenueChart" height="200"></canvas>
        </div>
      </div>
    </div>

    {{-- NEW CUSTOMERS --}}
    <div class="col-md-7">
      <div class="card">
        <div class="card-header"><i class="fas fa-users mr-2 text-primary"></i>New Customers (Last 7 Days)</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0 w-100" id="newCustomersTable">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- SUBSCRIPTION TABLES --}}
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><i class="far fa-calendar-check mr-2 text-success"></i>Ending in Next 7 Days</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0 w-100" id="endingSoonTable">
            <thead class="table-light">
              <tr>
                <th>Customer</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><i class="far fa-star mr-2 text-info"></i>New Subscriptions (7 Days)</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0 w-100" id="newSubsTable">
            <thead class="table-light">
              <tr>
                <th>Customer</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><i class="fas fa-snowflake mr-2 text-secondary"></i>Currently Freezed</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0 w-100" id="freezedTable">
            <thead class="table-light">
              <tr>
                <th>Customer</th>
                <th>Updated</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- DataTables JS -->
  <script src="{{ asset('theme/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('theme/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('theme/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('theme/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

  <script>
    $(function() {
      let chartInstance;

      // === KPI BOXES ===
      $.get("{{ route('admin.dashboard.stats') }}", res => {
        $('#stat-active').text(res.active);
        $('#stat-subscribed').text(res.subscribed);
        $('#stat-cancelled').text(res.cancelled);
        $('#stat-freezed').text(res.freezed);
        $('#stat-unpaid').text(res.unpaid);
        $('#total-revenue').text('AED ' + res.revenue);
      });

      // === CHART ===
      loadChart('month');
      $('.chart-switch button').click(function() {
        $('.chart-switch button').removeClass('active');
        $(this).addClass('active');
        loadChart($(this).data('type'));
      });

      function loadChart(type) {
        const url = "{{ route('admin.dashboard.chart', ['type' => '__TYPE__']) }}".replace('__TYPE__', type);
        $.get(url, res => {
          if (chartInstance) chartInstance.destroy();
          const ctx = document.getElementById('revenueChart');
          const labels = type === 'month' ? res.map(i => moment(i.period).format('MMM')) :
            type === 'week' ? res.map(i => 'W' + String(i.period).slice(-2)) :
            res.map(i => moment(i.period).format('DD MMM'));
          const totals = res.map(i => i.total);
          chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
              labels,
              datasets: [{
                data: totals,
                label: 'Revenue (AED)',
                fill: true,
                backgroundColor: 'rgba(60,141,188,0.2)',
                borderColor: '#3c8dbc',
                pointBackgroundColor: '#3c8dbc',
                tension: 0.3
              }]
            },
            options: {
              plugins: {
                legend: {
                  display: false
                }
              },
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        });
      }

      // === DATATABLES ===
      const tableConfigs = {
        responsive: true,
        autoWidth: false,
        searching: false,
        paging: false,
        info: false,
        pageLength: 5,
        language: {
          searchPlaceholder: "Search...",
          search: ""
        },
        ajax: "",
        columns: []
      };

      $('#endingSoonTable').DataTable({
        ...tableConfigs,
        ajax: "{{ route('admin.dashboard.table', 'ending') }}",
        columns: [{
            data: 'user.name',
            defaultContent: 'N/A'
          },
          {
            data: 'ends_at',
            render: d => d ? moment(d).format('DD-MMM') : '-'
          },
          {
            data: 'status',
            render: s =>
              `<span class="badge badge-${s === 'active' ? 'success' : s === 'cancelled' ? 'danger' : 'secondary'}">${s}</span>`
          },
          {
            data: 'total',
            render: t => t ? 'AED ' + parseFloat(t).toFixed(2) : '-'
          }
        ]
      });

      $('#newSubsTable').DataTable({
        ...tableConfigs,
        ajax: "{{ route('admin.dashboard.table', 'new') }}",
        columns: [{
            data: 'user.name',
            defaultContent: 'N/A'
          },
          {
            data: 'created_at',
            render: d => moment(d).format('DD-MMM')
          },
          {
            data: 'status',
            render: s =>
              `<span class="badge badge-${s === 'active' ? 'success' : s === 'cancelled' ? 'danger' : 'secondary'}">${s}</span>`
          },
          {
            data: 'total',
            render: t => t ? 'AED ' + parseFloat(t).toFixed(2) : '-'
          }
        ]
      });

      $('#freezedTable').DataTable({
        ...tableConfigs,
        ajax: "{{ route('admin.dashboard.table', 'freezed') }}",
        columns: [{
            data: 'user.name',
            defaultContent: 'N/A'
          },
          {
            data: 'updated_at',
            render: d => moment(d).format('DD-MMM')
          },
          {
            data: 'status',
            render: s => `<span class="badge badge-info">${s}</span>`
          },
          {
            data: 'total',
            render: t => t ? 'AED ' + parseFloat(t).toFixed(2) : '-'
          }
        ]
      });

      $('#newCustomersTable').DataTable({
        ...tableConfigs,
        ajax: "{{ route('admin.dashboard.table', 'customers') }}",
        columns: [{
            data: 'name'
          },
          {
            data: 'email'
          },
          {
            data: 'created_at',
            render: d => moment(d).format('DD-MMM')
          }
        ]
      });

      // === AUTO REFRESH (Every 60s) ===
      setInterval(() => {
        $('#endingSoonTable').DataTable().ajax.reload(null, false);
        $('#newSubsTable').DataTable().ajax.reload(null, false);
        $('#freezedTable').DataTable().ajax.reload(null, false);
        $('#newCustomersTable').DataTable().ajax.reload(null, false);
      }, 60000);
    });
  </script>
@endpush
