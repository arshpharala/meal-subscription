@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Subscriptions'])</h1>
    </div>
  </div>
@endsection
@section('content')
  <form id="filter-form" action="{{ route('admin.sales.subscriptions.index') }}" method="get">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Filters</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="row g-3">

              {{-- Meal --}}
              <div class="col-md-3 col-xl-2">
                <label for="meal_id" class="form-label">Meal Plan</label>
                <select name="meal_id" id="meal_id" class="form-control">
                  <option value="">All Meals</option>
                  @foreach ($meals as $meal)
                    <option value="{{ $meal->id }}" @selected($meal->id == request()->meal_id)>{{ $meal->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Package --}}
              <div class="col-md-3 col-xl-2">
                <label for="package_id" class="form-label">Package</label>
                <select name="package_id" id="package_id" class="form-control">
                  <option value="">All Packages</option>
                  @foreach ($packages as $package)
                    <option value="{{ $package->id }}" @selected($package->id == request()->package_id)>{{ $package->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Duration --}}
              <div class="col-md-2">
                <label for="duration" class="form-label">Duration</label>
                <select name="duration" id="duration" class="form-control">
                  <option value="">All</option>
                  @foreach ($durations as $item)
                    <option value="{{ $item->duration }}" @selected($item->duration == request()->duration)>
                      {{ $item->duration }} Days
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- Status --}}
              <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                  <option value="">All</option>
                  <option value="active" @selected(request()->status == 'active')>Active</option>
                  <option value="paused" @selected(request()->status == 'paused')>Paused</option>
                  <option value="cancelled" @selected(request()->status == 'cancelled')>Cancelled</option>
                  <option value="payment_failed" @selected(request()->status == 'payment_failed')>Payment Failed</option>
                </select>
              </div>

              {{-- Auto Charge --}}
              <div class="col-md-2">
                <label for="auto_charge" class="form-label">Auto Charge</label>
                <select name="auto_charge" id="auto_charge" class="form-control">
                  <option value="">All</option>
                  <option value="1" @selected(request()->auto_charge == '1')>Yes</option>
                  <option value="0" @selected(request()->auto_charge == '0')>No</option>
                </select>
              </div>

            </div>
          </div>

          <div class="card-footer text-end">
            <button type="submit" class="btn btn-dark"><i class="fas fa-filter me-1"></i> Filter</button>
            <a href="{{ route('admin.sales.subscriptions.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-sync me-1"></i> Clear
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Meal Plan</th>
                  <th>Address</th>
                  <th>Duration</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Auto Charge</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  <script>
    $(function() {
      let filterForm = $('#filter-form');

      let table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.sales.subscriptions.index') }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [{
            data: 'user_name',
            name: 'users.name'
          },
          {
            data: 'meal_name',
            name: 'meals.name'
          },
          {
            data: 'delivery_address',
            name: 'delivery_address',
            orderable: false,
            searchable: false
          },
          {
            data: 'duration',
            name: 'meal_package_prices.duration'
          },
          {
            data: 'start_date',
            name: 'subscriptions.start_date'
          },
          {
            data: 'end_date',
            name: 'subscriptions.end_date'
          },
          {
            data: 'auto_charge_status',
            name: 'subscriptions.auto_charge'
          },
          {
            data: 'status',
            name: 'subscriptions.status'
          },
          {
            data: 'created_at',
            name: 'subscriptions.created_at'
          },
          {
            data: 'action',
            orderable: false,
            searchable: false
          }
        ]
      });


      filterForm.on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
      });

    });
  </script>
@endpush

