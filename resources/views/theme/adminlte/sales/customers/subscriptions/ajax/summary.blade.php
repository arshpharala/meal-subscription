<div class="review-card border rounded-4 overflow-hidden">

  <form action="{{ route('admin.sales.customer.subscriptions.store', ['customer' => $customer]) }}"
        class="ajax-form" method="POST">
    @csrf
    <input type="hidden" name="meal_package_price_id" value="{{ $mealPackagePrice->id }}">

    <div class="p-4 bg-white">
      <div class="row">
        {{-- LEFT: Meal Plan --}}
        <div class="col-md-6 border-end">
          <h6 class="text-uppercase text-muted fw-bold mb-3">Selected Meal Plan</h6>
          <ul class="list-group list-group-flush small">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Meal</span><b>{{ $meal->name }}</b>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Package</span><b>{{ $package->name }} - {{ $package->tagline }}</b>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Duration</span><b>{{ $mealPackagePrice->duration }} Days</b>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Calories</span><b>{{ $mealPackagePrice->calorie->label }} kcal</b>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Base Price</span>
              <b class="text-success">AED {{ number_format($mealPackagePrice->price, 2) }}</b>
            </li>
          </ul>

          <hr class="my-4">

          {{-- Start Date --}}
          <div class="form-group mb-3">
            <label for="start_date" class="form-label fw-bold text-muted">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control"
                   min="{{ now()->addDay()->format('Y-m-d') }}" required>
          </div>

          {{-- Recurring Toggle --}}
          <div class="form-group">
            <div class="form-check form-switch">
              <input class="form-check-input ml-n3" type="checkbox" id="is_recurring" name="is_recurring" value="1">
              <label class="form-check-label ml-4 fw-bold text-muted" for="is_recurring">
                Enable Auto Recurring
              </label>
            </div>
          </div>
        </div>

        {{-- RIGHT: Customer Info & Addresses --}}
        <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
          <h6 class="text-uppercase text-muted fw-bold mb-3">Customer Information</h6>
          <ul class="list-group list-group-flush small mb-4">
            <li class="list-group-item"><b>Name:</b> {{ $customer->name }}</li>
            <li class="list-group-item"><b>Email:</b> {{ $customer->email }}</li>
            <li class="list-group-item"><b>Phone:</b> {{ $customer->phone }}</li>
          </ul>

          {{-- Address Selection --}}
          <h6 class="text-uppercase text-muted fw-bold mb-3">Select Delivery Address</h6>
          <div class="address-list">
            @forelse ($customer->addresses as $address)
              <div class="border rounded-3 p-3 mb-2 d-flex align-items-start address-card">
                <div class="form-check me-3 mt-1">
                  <input class="form-check-input address-radio" type="radio" name="address_id"
                         id="address_{{ $address->id }}" value="{{ $address->id }}"
                         data-country="{{ $address->country->code }}"
                         data-tax-id="{{ $address->country->tax_id }}"
                         {{ $loop->first ? 'checked' : '' }}>
                </div>
                <label class="form-check-label flex-grow-1" for="address_{{ $address->id }}">
                  {!! $address->render(true) !!}
                </label>
              </div>
            @empty
              <p class="text-muted">No saved addresses found for this customer.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="review-footer p-4 bg-light border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

      {{-- Summary --}}
      <div class="w-100 w-md-auto">
        <div class="bg-white border rounded-3 p-3 shadow-sm small">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted">Base Price</span>
            <strong id="summary-base">AED {{ number_format($mealPackagePrice->price, 2) }}</strong>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted"><span id="summary-vat-label">VAT 5%</span></span>
            <strong id="summary-vat-amount">AED 0.00</strong>
          </div>
          <hr class="my-2">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total (incl. VAT)</span>
            <h5 class="fw-bold text-success mb-0" id="summary-total">
              AED {{ number_format($mealPackagePrice->price, 2) }}
            </h5>
          </div>
        </div>
      </div>

      {{-- Button --}}
      <div class="text-end w-100 w-md-auto">
        <button type="submit" class="btn btn-success px-5 py-2 shadow-sm w-100 w-md-auto">
          <i class="fas fa-link me-2"></i> Generate Payment Link
        </button>
      </div>

    </div>
  </form>
</div>

<script>
  $(function() {
    const basePrice = {{ $mealPackagePrice->price }};
    const taxes = @json($taxes); // [{id, label, percentage}]

    const $summaryBase = $('#summary-base');
    const $summaryVatLabel = $('#summary-vat-label');
    const $summaryVatAmount = $('#summary-vat-amount');
    const $summaryTotal = $('#summary-total');

    function updateSummary(taxPercent = 0, taxLabel = '') {
      const vat = basePrice * (parseFloat(taxPercent) / 100);
      const total = basePrice + vat;

      $summaryBase.text(`AED ${basePrice.toFixed(2)}`);
      $summaryVatLabel.text(taxLabel ? `${taxLabel} (${taxPercent}%)` : '0%');
      $summaryVatAmount.text(`AED ${vat.toFixed(2)}`);
      $summaryTotal.text(`AED ${total.toFixed(2)}`);
    }

    function handleAddressChange() {
      const selected = $('.address-radio:checked');
      const taxId = selected.data('tax-id');

      if (taxId) {
        const tax = taxes.find(t => String(t.id) === String(taxId));
        if (tax) {
          updateSummary(tax.percentage, tax.label);
          return;
        }
      }

      // No tax for this country
      updateSummary(0);
    }

    // Bind event + init
    $('.address-radio').on('change', handleAddressChange);
    handleAddressChange();

    // Prevent past date
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('#start_date').attr('min', tomorrow.toISOString().split('T')[0]);

    // Ajax form handler
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
