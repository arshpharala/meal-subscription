<form action="{{ route('admin.sales.customer.addresses.update', ['customer' => $customer, 'address' => $address]) }}"
  method="post" class="ajax-form" enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @method('PUT')

  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.edit_title', ['name' => 'Address']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $address->phone }}"
              required>
          </div>

          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" id="address-type" class="form-select theme-select">
              <option value="">Select Address Type</option>
              @foreach ($addressTypes ?? [] as $type)
                <option value="{{ $type->key }}" @selected($address->type == $type->key)>
                  {{ $type->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Province</label>
            <select name="province_id" id="province-select" class="form-select theme-select" required>
              <option value="">Select Province</option>
              @foreach ($provinces ?? [] as $province)
                <option value="{{ $province->id }}" @selected($address->province_id == $province->id)>
                  {{ $province->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">City</label>
            <select name="city_id" id="city-select" class="form-select theme-select" required>
              <option value="">Select City</option>
              @foreach ($cities ?? [] as $city)
                <option value="{{ $city->id }}" @selected($address->city_id == $city->id)>
                  {{ $city->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Area</label>
            <select name="area_id" id="area-select" class="form-select theme-select" required>
              <option value="">Select Area</option>
              @foreach ($areas ?? [] as $area)
                <option value="{{ $area->id }}" @selected($address->area_id == $area->id)>
                  {{ $area->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control theme-input"
              placeholder="House no / building / street" value="{{ $address->address }}" required>
          </div>

          <div class="form-group">
            <label class="form-label">Landmark</label>
            <textarea name="landmark" class="form-control theme-input" placeholder="E.g. beside train station">{{ $address->landmark }}</textarea>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Fixed Buttons -->
  @include('theme.adminlte.components._aside-footer')

</form>

<script>
  $(document).ready(function() {
    // Handle AJAX submission
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
