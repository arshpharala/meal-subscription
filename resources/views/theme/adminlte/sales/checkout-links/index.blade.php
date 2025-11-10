@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Payment Link'])</h1>
    </div>
    {{-- <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">
      @can('create', App\Models\Catalog\Meal::class)
        <a href="{{ route('admin.sales.payment-links.create') }}" type="button" class="btn btn-secondary"> <i class="fa fa-plus"></i> @lang('crud.create')</a>
      @endcan
    </div> --}}
  </div>
@endsection
@section('content')
  {{-- <div class="mb-2">
    <button type="button" class="btn btn-danger btn-sm" id="bulk-delete">Delete Selected</button>
    <button type="button" class="btn btn-success btn-sm" id="bulk-restore">Restore Selected</button>
  </div> --}}
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
                  <th>Duration</th>
                  <th>Start Date</th>
                  <th>Is Recurring</th>
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
          url: '{{ route("admin.sales.payment-links.index") }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [
          {
            data: 'user_name',
            name: 'users.name'
          },
          {
            data: 'meal_name',
            name: 'meals.name'
          },
          {
            data: 'duration',
            name: 'meal_package_prices.duration'
          },
          {
            data: 'start_date',
            name: 'payment_links.start_date'
          },
          {
            data: 'recurring_status',
            name: 'payment_links.is_recurring'
          },
          {
            data: 'status',
            name: 'payment_links.status'
          },
          {
            data: 'created_at',
            name: 'payment_links.created_at'
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

@push('head')
  <link rel="stylesheet" href="{{ asset('theme/adminlte/assets/css/image-upload.css') }}">
@endpush
@push('scripts')
  <script src="{{ asset('theme/adminlte/assets/js/image-upload.js') }}"></script>
@endpush
