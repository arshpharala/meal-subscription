<form action="{{ route('admin.sales.customers.store') }}" method="post" class="ajax-form" enctype="multipart/form-data"
  onsubmit="handleFormSubmission(this)">
  @csrf
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.create_title', ['name' => 'Customer']),
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>


          <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <br>
          <h5 class="text-center">Address</h5>

          <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" id="address-type" class="form-select theme-select">
              <option value="">Select Address Type</option>
              @foreach ($addressTypes ?? [] as $type)
                <option value="{{ $type->key }}">{{ $type->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Province</label>
            <select name="province_id" id="province-select" class="form-select theme-select">
              <option value="">Select your province</option>
              @foreach ($provinces ?? [] as $province)
                <option value="{{ $province->id }}">{{ $province->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <select name="city_id" id="city-select" class="form-select theme-select">
              <option value="">Select your city</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Area</label>
            <select name="area_id" id="area-select" class="form-select theme-select">
              <option value="">Select your area</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control theme-input"
              placeholder="House no / building / street">
          </div>
          <div class="form-group">
            <label class="form-label">Landmark</label>
            <textarea name="landmark" class="form-control theme-input" placeholder="E.g. beside train station"></textarea>
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
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
