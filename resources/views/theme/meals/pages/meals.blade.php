@extends('theme.meals.layouts.app')

@section('content')
  <div class="container py-5">
    <div class="row mb-4">
      <div class="col-12">
        <h2>All Meals</h2>
      </div>
    </div>

    <div class="row">
      <!-- Filters -->
      <div class="col-md-3 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Filters</h5>
            <form action="{{ route('meals.index') }}" method="GET">
              <!-- Package Filter -->
              @if ($packages ?? null)
                <div class="form-group">
                  <label>Packages</label>
                  @foreach ($packages as $package)
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="package_{{ $package->id }}"
                        name="packages[]" value="{{ $package->id }}"
                        {{ in_array($package->id, request('packages', [])) ? 'checked' : '' }}>
                      <label class="custom-control-label" for="package_{{ $package->id }}">
                        {{ $package->name }}
                      </label>
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Sort Order -->
              <div class="form-group">
                <label for="sort">Sort By</label>
                <select class="form-control" id="sort" name="sort">
                  <option value="position" {{ request('sort') === 'position' ? 'selected' : '' }}>
                    Featured
                  </option>
                  <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>
                    Name
                  </option>
                  <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>
                    Newest
                  </option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Meals Grid -->
      <div class="col-md-9">
        <div class="row">
          @forelse($meals as $meal)
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="card h-100 shadow-sm">
                @if ($meal->thumbnail)
                  <img src="{{ asset('storage/' . $meal->thumbnail) }}" class="card-img-top" alt="{{ $meal->name }}"
                    style="height: 200px; object-fit: cover;">
                @else
                  <div class="bg-light" style="height: 200px;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                      <i class="fas fa-image text-muted fa-3x"></i>
                    </div>
                  </div>
                @endif

                <div class="card-body">
                  <h5 class="card-title">{{ $meal->name }}</h5>
                  @if ($meal->tagline)
                    <p class="card-text text-muted">{{ $meal->tagline }}</p>
                  @endif

                  @if ($meal->packages->isNotEmpty())
                    <div class="mt-2">
                      @foreach ($meal->packages->take(3) as $package)
                        <span class="badge badge-pill badge-primary mr-1">
                          {{ $package->name }}
                        </span>
                      @endforeach
                    </div>
                  @endif
                </div>

                <div class="card-footer bg-white border-top-0">
                  <a href="{{ route('meals.show', $meal->slug) }}" class="btn btn-outline-primary btn-sm">
                    View Details
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="alert alert-info">
                No meals found matching your criteria.
              </div>
            </div>
          @endforelse
        </div>

        <!-- Pagination -->
        @if ($meals->hasPages())
          <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
              {{ $meals->appends(request()->query())->links() }}
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .card {
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .badge-primary {
      background-color: #007bff;
    }
  </style>
@endpush
