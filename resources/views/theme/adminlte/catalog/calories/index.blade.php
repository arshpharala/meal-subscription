@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Calorie'])</h1>
    </div>
    <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">
      @can('create', App\Models\Catalog\Calorie::class)
        <button data-url="{{ route('admin.catalog.calories.create') }}" type="button" class="btn btn-secondary"
          onclick="getAside()"> <i class="fa fa-plus"></i> @lang('crud.create')</button>
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
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Label</th>
                  <th>Min kcal</th>
                  <th>Max kcal</th>
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
          url: '{{ route('admin.catalog.calories.index') }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [
          {
            data: 'label',
            name: 'calories.label'
          },
          {
            data: 'min_kcal',
            name: 'calories.min_kcal'
          },
          {
            data: 'max_kcal',
            name: 'calories.max_kcal'
          },
          {
            data: 'status',
            name: 'calories.is_active'
          },
          {
            data: 'created_at',
            name: 'calories.created_at'
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

