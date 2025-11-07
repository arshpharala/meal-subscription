<form action="{{ route('admin.sales.subscription.freezes.store', $subscription) }}" method="post" class="ajax-form"
  enctype="multipart/form-data" onsubmit="handleFormSubmission(this)">
  @csrf
  @include('theme.adminlte.components._aside-header', [
      'moduleName' => 'Freeze Subscription',
  ])

  <!-- Scrollable Content -->
  <div class="flex-fill" style="overflow-y:auto; min-height:calc(100vh - 190px); max-height:calc(100vh - 190px);">
    <div class="p-3" id="aside-inner-content">

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <label class="form-label">From</label>
            <input type="date" name="freeze_start_date" class="form-control" min="{{ now()->toDateString() }}"
              required>
          </div>


          <div class="form-group">
            <label class="form-label">To</label>
            <input type="date" name="freeze_end_date" class="form-control" min="{{ now()->toDateString() }}"
              required>
          </div>

          <div class="form-group">
            <label class="form-label">Reason (optional)</label>
            <input type="text" name="reason" class="form-control" maxlength="255" placeholder="e.g., Travel">
          </div>

          <small class="text-danger d-block mt-2">
            Freezing will extend the subscription’s end
            date{{ $subscription->auto_charge ? ' and next charge date' : '' }} by the number of frozen days.
          </small>


        </div>
      </div>

    </div>
  </div>


  <!-- Fixed Buttons -->
  @include('theme.adminlte.components._aside-footer', ['submitButton' => 'Freeze'])

</form>
<script>
  $(document).ready(function() {
    $("form.ajax-form").each(function() {
      handleFormSubmission(this);
    });
  });
</script>
