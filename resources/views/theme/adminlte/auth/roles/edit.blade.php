<form action="{{ route('admin.auth.roles.update', $role->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Role']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ $role->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$role->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            @foreach ($permissions->groupBy('module.name') as $moduleName => $modulePermissions)
              <div class="col-md-12 bg-gray-light mb-2`">
                <div class="border">
                  <h5>{{ $moduleName }}</h5>
                </div>
                <div class="row">
                  @foreach ($modulePermissions as $permission)
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="permission_{{ $permission->id }}">
                          <input type="checkbox" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                            name="permissions[]" @checked($permission->checked)>
                          <i>
                            {{ $permission->name }}
                          </i>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
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
