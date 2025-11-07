<form
  action="{{ route('admin.catalog.meal.package.prices.store', ['meal' => $mealPackage->meal, 'package' => $mealPackage->package]) }}"
  method="post" class="ajax-form" enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.create_title', ['name' => 'Pricing']),
  ])

  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="post clearfix">
        <div class="user-block"> <img class="img-sm img-bordered-sm"
            src="{{ asset('storage/' . $mealPackage->package->thumbnail) }}" alt="user image"> <span class="username">
            {{ $mealPackage->package->name }} </span> <span
            class="description">{{ $mealPackage->package->tagline }}</span> </div>
      </div>

      <div class="form-group">
        <label>Select Calorie</label>
        <select name="calorie_id" class="form-control" required>
          <option value="">Select Option</option>
          @foreach ($calories as $calorie)
            <option value="{{ $calorie->id }}">{{ $calorie->label }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label>Duration (in Days)</label>
        <input type="number" name="duration" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Price (AED)</label>
        <input type="number" name="price" class="form-control" step="0.01" required>
      </div>

      <div class="form-group">
        <label>Discount (%)</label>
        <input type="number" name="discount_percentage" class="form-control" value="0" min="0"
          max="100">
      </div>

      <div class="form-group">
        <div class="custom-control custom-switch mb-2">
          <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active_1">
          <label class="custom-control-label" for="is_active_1">Active</label>
        </div>
      </div>
    </div>
  </div>

  @include('theme.adminlte.components._aside-footer')
</form>

<script>
  $(document).ready(function() {
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
