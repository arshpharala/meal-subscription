<form action="{{ route('admin.auth.modules.update', $module->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Module']),
  ])


  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ $module->name }}" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ $module->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$module->is_active ? 'selected' : '' }}>Inactive</option>
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
