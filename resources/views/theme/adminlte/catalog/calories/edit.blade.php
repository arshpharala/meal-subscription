<form action="{{ route('admin.catalog.calories.update', $calorie->id) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Calorie']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label for="name">Label</label>
            <input type="text" name="label" class="form-control" value="{{ $calorie->label }}" required>
          </div>

          <div class="form-group">
            <label>Min kcal</label>
            <input type="number" name="min_kcal" class="form-control" value="{{ $calorie->min_kcal }}" required>
          </div>

          <div class="form-group">
            <label>Max kcal</label>
            <input type="number" name="max_kcal" class="form-control" value="{{ $calorie->max_kcal }}" required>
          </div>

          <div class="form-group">
            <div class="custom-control custom-switch mb-2">
              <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active"
                @checked($calorie->is_active)>
              <label class="custom-control-label" for="is_active">Active</label>
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
