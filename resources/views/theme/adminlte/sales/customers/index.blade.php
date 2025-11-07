@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Customers'])</h1>
    </div>
    <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">
      @can('create', App\Models\Catalog\Meal::class)
        <button data-url="{{ route('admin.sales.customers.create') }}" type="button" onclick="getAside()" class="btn btn-secondary"> <i
            class="fa fa-plus"></i> @lang('crud.create')</button>
      @endcan
    </div>
  </div>
@endsection
@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered data-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Subscription</th>
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
          url: '{{ route('admin.sales.customers.index') }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [{
            data: 'name',
            name: 'users.name'
          },
          {
            data: 'email',
            name: 'users.email'
          },
          {
            data: 'subscription_names',
            name: 'packages.name'

          },
          {
            data: 'created_at',
            name: 'users.created_at'
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
