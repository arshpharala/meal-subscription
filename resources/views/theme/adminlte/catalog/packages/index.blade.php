@extends('theme.adminlte.layouts.app')
@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">@lang('crud.list_title', ['name' => 'Package'])</h1>
    </div>
    <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">
      @can('create', App\Models\Catalog\Package::class)
        <button data-url="{{ route('admin.catalog.packages.create') }}" type="button" class="btn btn-secondary"
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
                  <th>Tagline</th>
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
          url: '{{ route('admin.catalog.packages.index') }}',
          data: d => $.extend(d, getFormFilters(filterForm, true))
        },
        columns: [{
            data: 'thumbnail',
            orderable: false,
            searchable: false
          },
          {
            data: 'name',
            name: 'packages.name'
          },
          {
            data: 'tagline',
            name: 'tagline'
          },
          {
            data: 'status',
            name: 'packages.is_active'
          },
          {
            data: 'created_at',
            name: 'packages.created_at'
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
