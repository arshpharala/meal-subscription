<form
  action="{{ route('admin.catalog.meal.package.prices.update', ['meal' => $price->mealPackage->meal_id, 'package' => $price->mealPackage->package_id, 'price' => $price->id]) }}"
  method="post" class="ajax-form" enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Pricing']),
  ])

  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="post clearfix">
        <div class="user-block"> <img class="img-sm img-bordered-sm"
            src="{{ asset('storage/' . $price->mealPackage->package->thumbnail) }}" alt="user image"> <span class="username">
            {{ $price->mealPackage->package->name }} </span> <span
            class="description">{{ $price->mealPackage->package->tagline }}</span> </div>
      </div>

      <div class="form-group">
        <label>Select Calorie</label>
        <select name="calorie_id" class="form-control" required>
          @foreach ($calories as $calorie)
            <option value="{{ $calorie->id }}" {{ $price->calorie_id == $calorie->id ? 'selected' : '' }}>
              {{ $calorie->label }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label>Duration (Days)</label>
        <input type="number" name="duration" value="{{ $price->duration }}" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Price (AED)</label>
        <input type="number" step="0.01" name="price" value="{{ $price->price }}" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Discount (%)</label>
        <input type="number" name="discount_percentage" value="{{ $price->discount_percentage }}" class="form-control"
          min="0" max="100">
      </div>

      <div class="form-group">
        <div class="custom-control custom-switch mb-2">
          <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active_1"
            {{ $price->is_active ? 'checked' : '' }}>
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
