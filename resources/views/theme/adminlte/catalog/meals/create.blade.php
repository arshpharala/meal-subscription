<form action="{{ route('admin.catalog.meals.store') }}" method="post" class="ajax-form" enctype="multipart/form-data"
  onsubmit="handleFormSubmission(this)">
  @csrf
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => __('crud.create_title', ['name' => 'Meal']),
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
            <label>Slug</label>
            <input type="text" name="slug" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="tagline">Tagline</label>
            <textarea name="tagline" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label for="position">Position</label>
            <input type="number" name="position" class="form-control">
          </div>

          <div class="form-group">
            <div class="custom-control custom-switch mb-2">
              <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active">
              <label class="custom-control-label" for="is_active">Active</label>
            </div>
          </div>

          <div class="form-group">
            <label>Sample Menu</label>
            <input type="file" name="sample_menu_file" class="form-control" accept=".pdf">
          </div>
        </div>



        <div class="col-12">
          <div class="form-group">
            <label>Variant Images</label>
            <div class="upload__box">
              <div class="upload__btn-box">
                <label class="upload__btn btn btn-outline-primary">Upload images
                  <input type="file" name="attachments[]" multiple data-min-length="1" min="1"
                    data-max_length="10" class="upload__inputfile" accept="image/*" />
                </label>
              </div>
              <div class="upload__img-wrap"></div>
            </div>
          </div>


        </div>
      </div>

    </div>
  </div>


  <!-- Fixed Buttons -->
  @include('theme.adminlte.components._aside-footer')

</form>
<script>
  ImgUpload();

  $(document).ready(function() {
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });

  $(function() {
    let $slug = $("input[name='slug']");
    let $firstName = $("input[name^='name']").first();

    $firstName.on("input", function() {
      if (!$slug.val().trim()) {
        let slug = $(this).val()
          .toLowerCase()
          .replace(/\s+/g, "-") // spaces → dash
          .replace(/[^a-z0-9\-]/g, "") // remove invalid chars
          .replace(/-+/g, "-") // collapse multiple dashes
          .replace(/^-+|-+$/g, ""); // trim leading/trailing dashes

        $slug.val(slug);
      }
    });
  });
</script>
