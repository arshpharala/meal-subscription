<form action="{{ route('admin.auth.admins.update', $admin->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'User']),
  ])


  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ $admin->name }}" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $admin->email }}" class="form-control" required
              autocomplete="off">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ $admin->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$admin->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="form-group">
            <label>New Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>
        </div>


        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12 border-bottom mb-2">
              <h5>Roles</h5>
            </div>
            @foreach ($roles as $role)
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="role_{{ $role->id }}">
                    <input type="checkbox" id="role_{{ $role->id }}" value="{{ $role->id }}" name="roles[]"
                      @checked($role->checked)>
                    <i>
                      {{ $role->name }}
                    </i>
                  </label>
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
