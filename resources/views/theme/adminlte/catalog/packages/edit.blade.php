<form action="{{ route('admin.catalog.packages.update', $package->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Package']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $package->name }}" required>
          </div>

          <div class="form-group">
            <label for="tagline">Tagline</label>
            <textarea name="tagline" class="form-control" rows="3">{{ $package->tagline }}</textarea>
          </div>

          <div class="form-group">
            <div class="custom-control custom-switch mb-2">
              <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active"
                @checked($package->is_active)>
              <label class="custom-control-label" for="is_active">Active</label>
            </div>
          </div>

          <div class="form-group">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*">
            @if (isset($package) && $package->thumbnail)
              <div class="mt-2">
                <img src="{{ asset('storage/' . $package->thumbnail) }}" class="img-lg img-thumbnail">
              </div>
            @endif
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
