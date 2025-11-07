@php $locales = active_locals(); @endphp

<div id="metaAccordion" class="row">
  @foreach ($locales as $locale)
    @php $meta = $model?->metaForLocale($locale); @endphp

    <div class="{{ $grid ?? 'col-md-6 col-12' }}">
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">Meta ({{ strtoupper($locale) }})</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Meta Title</label>
            <input type="text" name="metas[{{ $locale }}][meta_title]" class="form-control"
              value="{{ old("metas.$locale.meta_title", $meta->meta_title ?? '') }}">
          </div>
          <div class="form-group">
            <label>Meta Description</label>
            <textarea name="metas[{{ $locale }}][meta_description]" class="form-control" rows="2">{{ old("metas.$locale.meta_description", $meta->meta_description ?? '') }}</textarea>
          </div>
          <div class="form-group">
            <label>Meta Keywords</label>
            <select name="metas[{{ $locale }}][meta_keywords][]" multiple="multiple"
              class="form-control meta-keywords-select" data-locale="{{ $locale }}">
              @php
                $selectedKeywords = old(
                    "metas.$locale.meta_keywords",
                    isset($meta) ? $meta->keywords->pluck('keyword')->toArray() : [],
                );
              @endphp
              @foreach ($selectedKeywords as $tag)
                @if ($tag)
                  <option value="{{ $tag }}" selected>{{ $tag }}</option>
                @endif
              @endforeach
            </select>
            <small class="form-text text-muted">Enter keywords and press Enter. Duplicates are not allowed.</small>
          </div>

        </div>
      </div>
    </div>
  @endforeach
</div>

@push('scripts')
  <script>
    $('.meta-keywords-select').select2({
      tags: true,
      tokenSeparators: [','],
      maximumSelectionLength: 20,
      placeholder: 'Add keywords',
      width: '100%'
    });
  </script>
@endpush
