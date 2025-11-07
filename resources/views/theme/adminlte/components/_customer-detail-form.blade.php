<div class="row g-3">
  <div class="col-md-4">
    <label class="fw-semibold">Name</label>
    <input type="text" name="customer_name" class="form-control rounded-3" required>
  </div>
  <div class="col-md-4">
    <label class="fw-semibold">Email</label>
    <input type="email" name="customer_email" class="form-control rounded-3" required>
  </div>
  <div class="col-md-4">
    <label class="fw-semibold">Phone</label>
    <input type="text" name="customer_phone" class="form-control rounded-3" required>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Province</label>
      <select name="province_id" id="province-select" class="form-select theme-select" required>
        <option value="">Select your province</option>
        @foreach ($provinces ?? [] as $province)
          <option value="{{ $province->id }}">{{ $province->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">City</label>
      <select name="city_id" id="city-select" class="form-select theme-select" required>
        <option value="">Select your city</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Area</label>
      <select name="area_id" id="area-select" class="form-select theme-select" required>
        <option value="">Select your area</option>
      </select>
    </div>

  </div>

  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input type="text" name="address" class="form-control theme-input"
        placeholder="House no / building / street" required>
    </div>
  </div>

  <div class="col-md-4">
    <div class="mb-3">
      <label class="form-label">Landmark (Optional)</label>
      <textarea name="landmark" class="form-control theme-input" placeholder="E.g. beside train station"></textarea>
    </div>
  </div>
</div>
