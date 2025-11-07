@extends('theme.adminlte.layouts.app')
@push('head')
  <link rel="stylesheet" href="{{ asset('theme/adminlte/assets/css/subscription.css') }}">
@endpush

@section('content-header')
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="">Create Subscription</h1>
    </div>
    <a href="{{ route('admin.sales.customers.show', ['customer' => $customer]) }}" class="btn btn-secondary ">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
@endsection

@section('content')
  <div class="card border-0">
    <div class="card-body p-0">
      <form id="checkoutLinkForm" method="POST"
        action="{{ route('admin.sales.customer.subscriptions.store', ['customer' => $customer]) }}">
        @csrf
        <div class="row g-0">

          {{-- LEFT SIDEBAR --}}
          <div class="col-md-3 bg-light border-end p-4">
            <h6 class="text-uppercase text-muted fw-bold mb-3">Steps</h6>
            <ul id="wizardSteps" class="list-unstyled">

              <li data-step="meal " class="mb-3 fw-bold text-primary">
                <span class="step-index">1</span> Select Meal
              </li>
              <li data-step="package" class="mb-3">
                <span class="step-index">2</span> Choose Package
              </li>
              <li data-step="price" class="mb-3">
                <span class="step-index">3</span> Set Price
              </li>
              <li data-step="summary">
                <span class="step-index">4</span> Review Summary
              </li>
            </ul>
          </div>

          {{-- MAIN CONTENT --}}
          <div class="col-md-9 p-4" id="wizardContent">


            {{-- STEP 1 — MEAL --}}
            <section id="step-meal" class="d-none">
              <h4 class="fw-bold mb-4">1. Select a Meal</h4>
              <div id="mealGrid">
                @include('theme.adminlte.sales.customers.subscriptions.ajax.meals', ['meals' => $meals])
              </div>
            </section>

            {{-- STEP 2 — PACKAGE --}}
            <section id="step-package" class="d-none">
              <h4 class="fw-bold mb-4">3. Choose a Package</h4>
              <div class="" id='packages-content'>

              </div>
              <div class="mt-3">
                <button type="button" class="btn btn-outline-secondary" id="backToMeal">
                  <i class="fas fa-arrow-left me-1"></i> Back
                </button>
              </div>
            </section>

            {{-- STEP 3 — PRICE --}}
            <section id="step-price" class="d-none">
              <h4 class="fw-bold mb-4">4. Select Duration / Calories</h4>
              <div id="selectedPackageDisplay" class="alert alert-light border shadow-sm mb-4 d-none"></div>
              <div id="priceGrid"></div>
              <div class="mt-3">
                <button type="button" class="btn btn-outline-secondary" id="backToPackage">
                  <i class="fas fa-arrow-left me-1"></i> Back
                </button>
              </div>
            </section>

            {{-- STEP 4 — REVIEW --}}
            <section id="step-summary" class="d-none">
              <h4 class="fw-bold mb-4">5. Review & Confirm</h4>
              <div id="review-content"></div>
              <div class="mt-4">
                <button type="button" class="btn btn-outline-secondary px-4" id="backToPrice">
                  <i class="fas fa-arrow-left me-1"></i> Back
                </button>
              </div>
            </section>

          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('theme/adminlte/assets/js/subscription.js') }}"></script>
  <script>
    const CUSTOMER_ID = "{{ $customer->id }}";
  </script>
@endpush
