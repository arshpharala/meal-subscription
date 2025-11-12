@extends('theme.meals.layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
  <div class="max-w-4xl mx-auto px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fw-bold text-dark mb-0">My Delivery Addresses</h1>
        <small class="text-muted">Manage your delivery locations</small>
      </div>
      <button id="addAddressBtn" class="btn btn-sm btn-orange text-white shadow-sm">
        <i class="fas fa-plus me-1"></i> Add Address
      </button>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
      <div class="alert alert-success small mb-4">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
      </div>
    @elseif(session('error'))
      <div class="alert alert-danger small mb-4">
        <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
      </div>
    @endif

    {{-- ADDRESS LIST --}}
    @if($addresses->isEmpty())
      <div class="card border-0 shadow-sm text-center p-5">
        <i class="fas fa-map-marker-alt text-orange fs-2 mb-2"></i>
        <p class="text-muted mb-0">No saved addresses yet.</p>
      </div>
    @else
      <div class="row g-3">
        @foreach($addresses as $address)
          @php
            $hasSubscription = $address->subscriptions()->whereIn('status', ['active', 'pending'])->exists();
          @endphp
          <div class="col-md-6">
            <div class="card border-0 shadow-sm position-relative p-3 h-100">
              <div class="d-flex justify-content-between align-items-start">
                <h6 class="fw-semibold text-dark mb-1">
                  <i class="fas fa-home text-orange me-1"></i> {{ ucfirst($address->type) }}
                </h6>
                <div class="d-flex gap-2">
                  <button class="btn btn-link p-0 text-primary small editAddressBtn"
                          data-id="{{ $address->id }}" {{ $hasSubscription ? 'disabled' : '' }}>
                    <i class="fas fa-edit"></i>
                  </button>

                    <button type="button" data-url="{{ route('customer.addresses.destroy', $address->id) }}" class="btn btn-link p-0 text-danger small btn-delete"
                            {{ $hasSubscription ? 'disabled' : '' }}>
                      <i class="fas fa-trash-alt"></i>
                    </button>

                </div>
              </div>
              <p class="small text-muted mb-1">{{ $address->address }}</p>
              <p class="small text-muted mb-1">{{ optional($address->province)->name }}, {{ optional($address->city)->name }}, {{ optional($address->area)->name }}</p>
              @if($address->landmark)
                <p class="small text-secondary"><i class="fas fa-map-pin me-1"></i>{{ $address->landmark }}</p>
              @endif
              <p class="small text-secondary"><i class="fas fa-phone me-1"></i>{{ $address->phone }}</p>

              @if($hasSubscription)
                <div class="mt-2 text-warning small">
                  <i class="fas fa-lock me-1"></i> Linked to active subscription
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-body p-0" id="addressFormContainer">
        {{-- AJAX content will load here --}}
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(function() {
  // Add
  $('#addAddressBtn').on('click', function() {
    $.get("{{ route('customer.addresses.create') }}", function(response) {
      if (response.success) {
        $('#addressFormContainer').html(response.view);
        $('#addressModal').modal('show');
      }
    });
  });

  // Edit
  $(document).on('click', '.editAddressBtn', function() {
    const id = $(this).data('id');
    $.get(`/addresses/${id}/edit`, function(response) {
      if (response.success) {
        $('#addressFormContainer').html(response.view);
        $('#addressModal').modal('show');
      } else {
        alert(response.message);
      }
    });
  });
});
</script>
@endpush
@endsection
