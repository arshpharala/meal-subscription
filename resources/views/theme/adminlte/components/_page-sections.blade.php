@php
    $sectionLocales = active_locals();
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Page Sections</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-success" onclick="addSection()">+ Add Section</button>
        </div>
    </div>
    <div class="card-body" id="sections-container">
        @if (isset($model) && $model->sections)
            @foreach ($model->sections->sortBy('created_at') as $i => $section)
                <input type="hidden" name="sections[{{ $i }}][id]" value="{{ $section->id }}">
                <div class="section-block card mb-3 border">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Section Type</label>
                                <input type="text" name="sections[{{ $i }}][type]"
                                    value="{{ $section->type }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Section Image</label>
                                <input type="file" name="sections[{{ $i }}][image]" class="form-control">
                                @if ($section->image)
                                    <img src="{{ asset('storage/' . $section->image) }}" class="mt-2" width="120">
                                @endif
                            </div>
                        </div>

                        @foreach ($sectionLocales as $locale)
                            @php $trans = $section->translations->where('locale', $locale)->first(); @endphp
                            <div class="border p-3 mb-2">
                                <h6>{{ strtoupper($locale) }}</h6>
                                <div class="form-group">
                                    <label>Heading</label>
                                    <input type="text"
                                        name="sections[{{ $i }}][heading][{{ $locale }}]"
                                        value="{{ $trans?->heading }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea name="sections[{{ $i }}][content][{{ $locale }}]" class="form-control tinymce-editor" rows="4">{{ $trans?->content }}</textarea>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" class="btn btn-sm btn-danger"
                            onclick="this.closest('.section-block').remove()">Remove Section</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Dynamic template for JS --}}
<template id="section-template">
    <input type="hidden" name="sections[__index__][id]" value="">
    <div class="section-block card mb-3 border">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Section Type</label>
                    <input type="text" name="sections[__index__][type]" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Section Image</label>
                    <input type="file" name="sections[__index__][image]" class="form-control" accept="image/*">
                </div>
            </div>

            @foreach ($sectionLocales as $locale)
                <div class="border p-3 mb-2">
                    <h6>{{ strtoupper($locale) }}</h6>
                    <div class="form-group">
                        <label>Heading</label>
                        <input type="text" name="sections[__index__][heading][{{ $locale }}]"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="sections[__index__][content][{{ $locale }}]" class="form-control tinymce-editor" rows="4"></textarea>
                    </div>
                </div>
            @endforeach

            <button type="button" class="btn btn-sm btn-danger"
                onclick="this.closest('.section-block').remove()">Remove Section</button>
        </div>
    </div>
</template>

@push('scripts')
    <script>
        let sectionIndex = {{ isset($model) ? $model->sections->count() : 0 }};

        function addSection() {
            const template = document.getElementById('section-template').innerHTML;
            const html = template.replace(/__index__/g, sectionIndex++);
            document.getElementById('sections-container').insertAdjacentHTML('beforeend', html);
        }
    </script>
@endpush
