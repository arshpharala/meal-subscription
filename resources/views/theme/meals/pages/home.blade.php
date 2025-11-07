@extends('theme.meals.layouts.app')

@section('content')
  <div class="container py-5">
    <div class="row">
      <div class="col-12">
        <h2 class="text-center mb-5">Our Delicious Meals</h2>
      </div>
    </div>

    <div class="row">
      @forelse($meals as $meal)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card h-100 shadow-sm">
            @if ($meal->thumbnail_file_path)
              <img src="{{ asset('storage/' . $meal->thumbnail_file_path) }}" class="card-img-top" alt="{{ $meal->name }}"
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

              <div class="mt-3">
                @if ($meal->packages->isNotEmpty())
                  <div class="small text-muted mb-2">Available in packages:</div>
                  @foreach ($meal->packages->take(3) as $package)
                    <span class="badge badge-pill badge-primary mr-1">
                      {{ $package->name }}
                    </span>
                  @endforeach
                  @if ($meal->packages->count() > 3)
                    <span class="badge badge-pill badge-light">
                      +{{ $meal->packages->count() - 3 }} more
                    </span>
                  @endif
                @endif
              </div>
            </div>

            <div class="card-footer bg-white border-top-0">
              <a href="{{ route('meals.show', $meal->slug) }}" class="btn btn-outline-primary btn-sm">
                View Details
              </a>
              @if ($meal->sample_menu_file)
                <a href="{{ asset('storage/' . $meal->sample_menu_file) }}" class="btn btn-link btn-sm text-muted"
                  target="_blank">
                  <i class="fas fa-file-pdf"></i> Sample Menu
                </a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info text-center">
            No meals available at the moment.
          </div>
        </div>
      @endforelse
    </div>

    @if ($meals->hasPages())
      <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
          {{ $meals->links() }}
        </div>
      </div>
    @endif
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

    .pagination {
      margin-bottom: 0;
    }
  </style>
@endpush
