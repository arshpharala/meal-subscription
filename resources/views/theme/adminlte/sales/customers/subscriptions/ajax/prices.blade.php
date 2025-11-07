<div class="alert alert-light border shadow-sm mb-4">
  <b>Selected Meal:</b> {{ $meal->name }}
</div>

<div class="alert alert-light border shadow-sm mb-4">
  <b>Selected Package:</b> {{ $package->name }} - {{ $package->tagline }}
</div>

<div class="d-flex flex-wrap gap-2">
  @foreach ($prices as $price)
    <div class="price-card border p-3 rounded shadow-sm text-center hover-scale" data-id="{{ $price->id }}"
      data-price="{{ $price->price }}" data-duration="{{ $price->duration }}"
      data-calorie="{{ $price->calorie->label ?? '' }}"
      data-query="?meal_id={{ $meal->id }}&package_id={{ $package->id }}&price_id={{ $price->id }}">

      <h5 class="fw-bold mb-1">{{ $price->duration }} Days</h5>
      <p class="small mb-1">{{ $price->calorie->label ?? '' }} kcal</p>
      <h6 class="fw-bold text-success">AED {{ $price->price }}</h6>
    </div>
  @endforeach
</div>
