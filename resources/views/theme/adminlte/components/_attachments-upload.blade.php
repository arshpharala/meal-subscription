<div class="card card-secondary">
  <div class="card-header"><h3 class="card-title">Images & Attachments</h3></div>
  <div class="card-body">
    <input type="file" name="attachments[]" multiple class="form-control">
    <small class="form-text text-muted">You can upload multiple images or files.</small>
    @if(isset($attachments) && count($attachments))
      <div class="mt-2">
        <label>Current Attachments:</label>
        <ul>
          @foreach($attachments as $att)
            <li>
              <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank">{{ $att->file_name }}</a>
            </li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</div>
