<div class="d-flex justify-content-between align-items-center mb-3">
  <button data-url="{{ route('admin.sales.customer.addresses.create', ['customer' => $customer]) }}" onclick="getAside()"
    class="btn btn-sm btn-primary">
    <i class="fas fa-plus"></i> Add New Address
  </button>
</div>


<div class="row">
  @forelse($addresses as $address)
    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
      <div class="card bg-light d-flex flex-fill shadow-sm address-card" data-id="{{ $address->id }}">
        <div class="card-header text-muted border-bottom-0">
          <span><i class="fas fa-map-marked-alt me-1"></i> {{ ucfirst($address->type ?? 'Home') }}</span>
          @if ($address->id == $customer->default_address_id)
            <span class="badge bg-success float-end"><i class="fas fa-star me-1"></i> Default</span>
          @endif
        </div>

        <div class="card-body pt-3 pb-2">
          <div class="row">
            <div class="col-3 text-center">
              <div class="rounded-circle bg-gradient-secondary d-flex justify-content-center align-items-center"
                style="width:60px;height:60px;margin:auto;">
                <i class="fas fa-map-marker-alt text-white fs-4"></i>
              </div>
            </div>
            <div class="col-9">
              <h2 class="lead mb-1"><b>{{ $customer->name }}</b></h2>
              <p class="text-muted text-sm mb-1">
                <b>Address: </b>
                {!! $address->render(true) !!}
              </p>

              <p class="text-muted text-sm mb-1"><b>Phone:</b> {{ $address->phone }}</p>
              <p class="text-muted text-sm mb-1"><b>Added:</b> {{ $address->created_at?->format('d M, Y') }}</p>


            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="text-right">
            <button class="btn btn-sm btn-outline-secondary me-1"
              data-url="{{ route('admin.sales.customer.addresses.edit', ['customer' => $customer, 'address' => $address]) }}"
              onclick="getAside()">
              <i class="fas fa-edit"></i> Edit
            </button>

          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-light border text-center">No addresses added yet.</div>
    </div>
  @endforelse
</div>
