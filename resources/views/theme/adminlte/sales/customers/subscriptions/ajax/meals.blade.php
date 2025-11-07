@foreach ($meals ?? [] as $meal)
  <div class="meal-card position-relative">
    <img src="{{ asset('storage/' . $meal->thumbnail_file_path) }}" alt="{{ $meal->name }}">
    <div class="meal-body p-3 text-center">
      <h6 class="fw-bold mb-1">{{ $meal->name }}</h6>
      <p class="text-muted mb-2">{{ $meal->tagline }}</p>
      <button type="button" class="btn btn-sm btn-outline-primary selectMeal" data-meal-id="{{ $meal->id }}"
        data-customer-id="{{ $customer->id }}" data-query="?meal_id={{ $meal->id }}">
        Select
      </button>
    </div>
  </div>
@endforeach
