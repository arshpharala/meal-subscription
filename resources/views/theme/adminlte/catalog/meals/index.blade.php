@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Meal'])</h1>
    </div>
    <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">
      @can('create', App\Models\Catalog\Meal::class)
        <button data-url="{{ route('admin.catalog.meals.create') }}" type="button" class="btn btn-secondary"
          onclick="getAside()"> <i class="fa fa-plus"></i> @lang('crud.create')</button>
      @endcan
    </div>
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
                  <th>Thumbnail</th>
                  <th>Name</th>
                  <th>Slug</th>
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
          url: '{{ route('admin.catalog.meals.index') }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [{
            data: 'thumbnail',
            orderable: false,
            searchable: false
          },
          {
            data: 'name',
            name: 'meals.name'
          },
          {
            data: 'slug',
            name: 'meals.slug'
          },
          {
            data: 'status',
            name: 'meals.is_active'
          },
          {
            data: 'created_at',
            name: 'meals.created_at'
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
