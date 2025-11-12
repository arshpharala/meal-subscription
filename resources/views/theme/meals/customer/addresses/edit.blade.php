<form action="{{ route('customer.addresses.update', $address) }}" method="POST" class="p-4 ajax-form">
  @csrf
  @method('PUT')

  <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
    <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-plus-circle text-orange me-2"></i>Add New Address</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Address Type</label>
    <select name="type" class="form-select" required>
      <option value="">Select Type</option>
      @foreach ($addressTypes as $type)
        <option value="{{ $type->key }}" @selected($address->type == $type->key)>{{ $type->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Province</label>
    <select name="province_id" id="province-select" class="form-select" required>
      <option value="">Select Province</option>
      @foreach ($provinces as $province)
        <option value="{{ $province->id }}" @selected($address->province_id == $province->id)>{{ $province->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">City</label>
    <select name="city_id" id="city-select" class="form-select" required>
      @foreach ($cities ?? [] as $city)
        <option value="{{ $city->id }}" @selected($address->city_id == $city->id)>
          {{ $city->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Area</label>
    <select name="area_id" id="area-select" class="form-select" required>
      @foreach ($areas ?? [] as $area)
        <option value="{{ $area->id }}" @selected($address->area_id == $area->id)>
          {{ $area->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Full Address</label>
    <input type="text" name="address" class="form-control" placeholder="House no / street / building"
      value="{{ $address->address }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Landmark (optional)</label>
    <textarea name="landmark" class="form-control" placeholder="E.g. near metro station">{{ $address->landmark }}</textarea>
  </div>

  <div class="text-end">
    <button type="submit" class="btn btn-orange text-white px-4"><i class="fas fa-save me-1"></i>Save</button>
  </div>
</form>

<script>
  $(document).ready(function() {
    // Handle AJAX submission
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
