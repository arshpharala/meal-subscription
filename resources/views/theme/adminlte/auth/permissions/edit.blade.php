<form action="{{ route('admin.auth.permissions.update', $permission->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Permission']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Module</label>
            <select name="module_id" class="form-control" required>
              @foreach ($modules as $module)
                <option value="{{ $module->id }}" {{ $permission->module_id == $module->id ? 'selected' : '' }}>
                  {{ $module->name }}</option>
              @endforeach
            </select>
          </div>


          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ $permission->name }}" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Status</label>
                <select name="is_active" class="form-control">
                  <option value="1" {{ $permission->is_active ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ !$permission->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>
            </div>
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
