<form action="{{ route('admin.auth.permissions.store') }}" method="post" class="ajax-form" enctype="multipart/form-data"
  onsubmit="handleFormSubmission(this)">
  @csrf

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.create_title', ['name' => 'Permission']),
  ])


  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label>Module</label>
            <select name="module_id" class="form-control">
              <option value="">Select Module</option>
              @foreach ($modules as $module)
                <option value="{{ $module->id }}">{{ $module->name }}</option>
              @endforeach
            </select>
          </div>


          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- Fixed Buttons -->
  @include('theme.adminlte.components._aside-footer')
</form>
<script>
  $(document).ready(function() {
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
