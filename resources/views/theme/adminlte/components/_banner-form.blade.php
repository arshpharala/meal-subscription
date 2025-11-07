@foreach (active_locals() as $locale)
  @php
    $translation = $banner?->translations->where('locale', $locale)->first() ?? null;
  @endphp
  <div class="form-group">
    <label for="title_{{ $locale }}">Title ({{ strtoupper($locale) }})</label>
    <input type="text" name="title[{{ $locale }}]" class="form-control" value="{{ $translation->title ?? '' }}">
  </div>
  <div class="form-group">
    <label for="subtitle_{{ $locale }}">Subtitle ({{ strtoupper($locale) }})</label>
    <input type="text" name="subtitle[{{ $locale }}]" class="form-control" value="{{ $translation->subtitle ?? '' }}">
  </div>
  <div class="form-group">
    <label for="description_{{ $locale }}">Description ({{ strtoupper($locale) }})</label>
    <textarea name="description[{{ $locale }}]" rows="3" class="form-control">{{ $translation->description ?? '' }}</textarea>
  </div>
@endforeach

<div class="form-group">
  <label>Image (PNG/JPG)</label>
  <input type="file" name="image" class="form-control" accept="image/*">
  @if (!empty($banner->image))
    <img src="{{ asset('storage/' . $banner->image) }}" alt="image" class="mt-2" style="max-height:90px;border:1px solid #eee;">
  @endif
</div>

<div class="form-group">
  <label>Background (PNG/JPG)</label>
  <input type="file" name="background" class="form-control" accept="image/*">
  @if (!empty($banner->background))
    <img src="{{ asset('storage/' . $banner->background) }}" alt="background" class="mt-2" style="max-height:90px;border:1px solid #eee;">
  @endif
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Text Color</label>
    <input type="text" name="text_color" class="form-control" value="{{ old('text_color', $banner->text_color ?? '#000000') }}">
  </div>
  <div class="form-group col-md-6">
    <label>Button Color</label>
    <input type="text" name="btn_color" class="form-control" value="{{ old('btn_color', $banner->btn_color ?? '#ffffff') }}">
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Button Text</label>
    <input type="text" name="btn_text" class="form-control" value="{{ old('btn_text', $banner->btn_text ?? '') }}">
  </div>
  <div class="form-group col-md-6">
    <label>Button Link</label>
    <input type="text" name="btn_link" class="form-control" value="{{ old('btn_link', $banner->btn_link ?? '') }}">
  </div>
</div>

<div class="form-group">
  <label>Position</label>
  <input type="number" name="position" class="form-control" value="{{ old('position', $banner->position ?? 0) }}">
</div>

<div class="form-group">
  <label>
    <input type="checkbox" name="is_active" value="1" {{ isset($banner) ? ($banner->is_active ? 'checked' : '') : 'checked' }}>
    Active
  </label>
</div>
