<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form id="existing-address-form">
        <div class="modal-header">
          <h5 class="modal-title">Add Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="x"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Province</label>
              <select name="province_id" class="form-select" required></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <select name="city_id" class="form-select" required></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Area</label>
              <select name="area_id" class="form-select" required></select>
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" required>
            </div>
            <div class="col-md-12">
              <label class="form-label">Landmark</label>
              <input type="text" name="landmark" class="form-control">
            </div>
            <div class="col-md-12">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control">
            </div>
          </div>
          <div class="text-danger small mt-2 d-none" id="addressModalErrors"></div>
        </div>
        <div class="modal-footer">
          <button type="button" data-bs-dismiss="modal" class="btn btn-light">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Address</button>
        </div>
      </form>
    </div>
  </div>
</div>
