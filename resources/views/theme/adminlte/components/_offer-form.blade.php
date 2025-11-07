@foreach (active_locals() as $locale)
  @php
    $translation = $offer?->translations->where('locale', $locale)->first() ?? null;
  @endphp
  <div class="form-group">
    <label for="title_{{ $locale }}">Title ({{ strtoupper($locale) }})</label>
    <input type="text" name="title[{{ $locale }}]" class="form-control" value="{{ $translation->title ?? '' }}"
      required>

  </div>

  <div class="form-group">
    <label for="description_{{ $locale }}">Description ({{ strtoupper($locale) }})</label>
    <textarea name="description[{{ $locale }}]" rows="3" class="form-control">{{ $translation->description ?? '' }}</textarea>

  </div>
@endforeach

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Discount Type</label>
    <select name="discount_type" class="form-control" required>
      <option value="fixed" {{ isset($offer) && $offer->discount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
      <option value="percent" {{ !isset($offer) || $offer->discount_type === 'percent' ? 'selected' : '' }}>Percent
      </option>
    </select>
  </div>

  <div class="form-group col-md-6">
    <label>Discount Value</label>
    <input type="number" name="discount_value" class="form-control" step="0.01"
      value="{{ $offer->discount_value ?? '' }}" required>
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label>Start Date</label>
    <input type="datetime-local" name="starts_at" class="form-control"
      value="{{ isset($offer->starts_at) ? $offer->starts_at->format('Y-m-d\TH:i') : '' }}">
  </div>
  <div class="form-group col-md-6">
    <label>End Date</label>
    <input type="datetime-local" name="ends_at" class="form-control"
      value="{{ isset($offer->ends_at) ? $offer->ends_at->format('Y-m-d\TH:i') : '' }}">
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
  <label>
    <input type="checkbox" name="is_active" value="1"
      {{ isset($offer) ? ($offer->is_active ? 'checked' : '') : 'checked' }}>
    Active
  </label>
</div>
  <div class="form-group col-md-6">
  <label>
    <input type="checkbox" name="show_in_slider" value="1"
      {{ isset($offer) ? ($offer->show_in_slider ? 'checked' : '') : 'checked' }}>
    Show in Slider
  </label>
</div>
</div>

{{-- Banner Image --}}
<div class="form-group">
  <label>Banner Image (PNG/JPG)</label>
  <input type="file" name="banner_image" class="form-control" accept="image/*">
  @if (!empty($offer->banner_image))
    <div class="mt-2">
      <img src="{{ asset('storage/' . $offer->banner_image) }}" alt="banner"
        style="max-height:90px;border:1px solid #eee;border-radius:6px;">
    </div>
  @endif
  <small class="form-text text-muted">Recommended: ~700×300 (transparent PNG works well).</small>
</div>

{{-- Background color --}}
<div class="form-group">
  <label>Background Color (hex or CSS color)</label>
  <input type="text" name="bg_color" class="form-control" placeholder="#fde7e8"
    value="{{ old('bg_color', $offer->bg_color ?? '') }}">
  <small class="form-text text-muted">Examples: #fde7e8, #e6f7fc, #ffe0c7.</small>
</div>

{{-- Tile Link URL (optional) --}}
<div class="form-group">
  <label>Tile Link URL (optional)</label>
  <input type="text" name="link_url" class="form-control"
    placeholder="https://… or /products?offer={{ $offer->id ?? '' }}"
    value="{{ old('link_url', $offer->link_url ?? '') }}">
  <small class="form-text text-muted">If empty, we’ll route to the default offer listing.</small>
</div>

{{-- Position --}}
<div class="form-group">
  <label>Position (sort order)</label>
  <input type="number" name="position" class="form-control" value="{{ old('position', $offer->position ?? 0) }}">
</div>
