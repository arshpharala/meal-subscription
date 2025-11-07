<form action="{{ route('admin.catalog.meal.packages.store', ['meal' => $meal]) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.create_title', ['name' => 'Meal Package']),
  ])

  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">
          @if ($packages->isNotEmpty())
            <ul class="todo-list" data-widget="todo-list">
              @foreach ($packages as $package)
                <li>

                  <div class="icheck-primary d-inline ml-2">
                    <input type="checkbox" id="{{ $package->id }}" value="{{ $package->id }}"
                      name="packages[{{ $package->id }}]" value="{{ $package->id }}">
                    <label for="{{ $package->id }}">

                      <span class="text">{{ $package->name }}</span>
                    </label>
                  </div>
                  <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="img"
                    class="img-md img-bordered float-end">
                  <br>
                  <small class="ms-4">{{ $package->tagline }}</small>

                </li>
              @endforeach
            </ul>
          @else
            <p class="text-center">
              You've selected all packages or create new package.
            </p>

          @endif


          {{-- <div class="form-group">
            <label>Internal Code <small>(Optional)</small></label>
            <input type="text" name="code" class="form-control">
          </div> --}}

          {{-- <div class="form-group">
            <div class="custom-control custom-switch mb-2">
              <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active">
              <label class="custom-control-label" for="is_active">Active</label>
            </div>
          </div> --}}
        </div>

      </div>
    </div>
  </div>

  @include('theme.adminlte.components._aside-footer')
</form>

<script>
  $(document).ready(function() {
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
