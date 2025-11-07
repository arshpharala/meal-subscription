<div id="selectedMealDisplay" class="alert alert-light border shadow-sm mb-4">
  <b>Selected Meal:</b> {{ $meal->name }}
</div>

<div class="row g-3" id="packageGrid">
  @foreach ($packages as $p)
    <div class="col-md-3">
      <div class="card pkg-card border-0 shadow-sm hover-scale">
        <img src="{{ asset('storage/' . $p->package->thumbnail) }}" class="card-img-top"
          style="height:200px;object-fit:cover;">
        <div class="card-body text-center">
          <h6 class="fw-bold">{{ $p->package->name }}</h6>
          <p class="small text-muted mb-2">{{ $p->package->tagline }}</p>
          <button type="button" class="btn btn-sm btn-outline-primary selectPackage" data-meal-id="{{ $meal->id }}"
            data-id="{{ $p->package->id }}" data-query="?meal_id={{ $meal->id }}&package_id={{ $p->package->id }}">
            Select
          </button>
        </div>
      </div>
    </div>
  @endforeach
</div>
