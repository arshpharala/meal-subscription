@extends('theme.meals.layouts.app')

@section('content')
  <div class="container py-5">
    <div class="row">
      <!-- Meal Images -->
      <div class="col-md-6 mb-4">
        @if ($meal->attachments->isNotEmpty())
          <div id="mealCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              @foreach ($meal->attachments as $index => $attachment)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                  <img src="{{ asset('storage/' . $attachment->file_path) }}" class="d-block w-100"
                    alt="{{ $meal->name }}" style="height: 400px; object-fit: cover;">
                </div>
              @endforeach
            </div>
            @if ($meal->attachments->count() > 1)
              <a class="carousel-control-prev" href="#mealCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#mealCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            @endif
          </div>
        @else
          <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
            <i class="fas fa-image text-muted fa-3x"></i>
          </div>
        @endif
      </div>

      <!-- Meal Details -->
      <div class="col-md-6">
        <h1 class="mb-3">{{ $meal->name }}</h1>
        @if ($meal->tagline)
          <p class="lead text-muted">{{ $meal->tagline }}</p>
        @endif

        @if ($meal->sample_menu_file)
          <div class="mb-4">
            <a href="{{ asset('storage/' . $meal->sample_menu_file) }}" class="btn btn-outline-primary" target="_blank">
              <i class="fas fa-file-pdf mr-2"></i> Download Sample Menu
            </a>
          </div>
        @endif

        <!-- Available Packages -->
        @if ($meal->mealPackages->isNotEmpty())
          <div class="card mt-4">
            <div class="card-header">
              <h5 class="mb-0">Available Packages</h5>
            </div>
            <div class="card-body">
              <div class="row">
                @foreach ($meal->mealPackages as $mealPackage)
                @php
                    $package = $mealPackage->package;
                @endphp
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body">
                        <h6 class="card-title">{{ $package->name }}</h6>
                        @if ($mealPackage->prices->isNotEmpty())
                          <div class="small text-muted">
                            Starting from {{ number_format($mealPackage->prices->min('amount'), 2) }} AED
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .carousel-item img {
      border-radius: 8px;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 10%;
    }
  </style>
@endpush
