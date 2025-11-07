@php
    $currentVariants = old('variants', $variants ?? []);
    $attributes = $categoryAttributes ?? [];
@endphp

<div class="card card-info mt-3" id="variant-manager-card">
    <div class="card-header">
        <h3 class="card-title">Product Variants</h3>
        <button type="button" class="btn btn-sm btn-success float-right" id="add-variant-row" onclick="getAside()" data-url="{{ route('admin.catalog.product.variants.create', $product->id) }}">Add Variant</button>
    </div>
    <div class="card-body p-2">
        <div id="variants-table">
            <div id="variants-header" class="row font-weight-bold mb-2">
                <div class="col-md-2">SKU</div>
                <div class="col-md-2">Price</div>
                <div class="col-md-2">Stock</div>
                @foreach ($attributes as $attribute)
                    <div class="col-md-2">{{ $attribute['name'] }}</div>
                @endforeach
                <div class="col-md-2"></div>
            </div>
            <div id="variants-list">
                @foreach ($currentVariants as $index => $variant)
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                    <div class="row align-items-center variant-row mb-1" data-index="{{ $index }}">
                        <div class="col-md-2">
                            <input type="text" name="variants[{{ $index }}][sku]" class="form-control"
                                   value="{{ $variant['sku'] ?? '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control"
                                   value="{{ $variant['price'] ?? '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control"
                                   value="{{ $variant['stock'] ?? '' }}" required>
                        </div>
                        @foreach ($attributes as $attr)
                            <div class="col-md-2">
                                <select name="variants[{{ $index }}][attributes][{{ $attr['id'] }}]" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($attr['values'] as $val)
                                        <option value="{{ $val['id'] }}"
                                            {{ (isset($variant['attributes'][$attr['id']]) && $variant['attributes'][$attr['id']] == $val['id']) ? 'selected' : '' }}>
                                            {{ $val['value'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                        <div class="col-md-2">
                            @php
                                $deleteUrl = route('admin.catalog.product.variants.destroy', ['product' => $variant['product_id'], 'variant' => $variant['id']]);
                            @endphp
                            <button type="button" class="btn btn-danger btn-sm remove-variant-row btn-delete" data-url="{{ $deleteUrl }}" data-refresh=false>×</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.currentAttributes = @json($attributes);
let variantIndex = {{ count($currentVariants) }};

function buildVariantRow(index, variant = null) {
    let row = `<div class="row align-items-center variant-row mb-1" data-index="${index}"><input type="hidden" name="variants[${index}][id]" class="form-control">`;
    row += `<div class="col-md-2"><input type="text" name="variants[${index}][sku]" class="form-control" value="${variant && variant.sku ? variant.sku : ''}" required></div>`;
    row += `<div class="col-md-2"><input type="number" name="variants[${index}][price]" class="form-control" value="${variant && variant.price ? variant.price : ''}" required></div>`;
    row += `<div class="col-md-2"><input type="number" name="variants[${index}][stock]" class="form-control" value="${variant && variant.stock ? variant.stock : ''}" required></div>`;
    (window.currentAttributes || []).forEach(function(attr) {
        row += `<div class="col-md-2">
            <select name="variants[${index}][attributes][${attr.id}]" class="form-control" required>
                <option value="">Select</option>`;
        (attr.values || []).forEach(function(val) {
            let selected = '';
            if (variant && variant.attributes && variant.attributes[attr.id] && variant.attributes[attr.id] == val.id) {
                selected = 'selected';
            }
            row += `<option value="${val.id}" ${selected}>${val.value}</option>`;
        });
        row += `</select></div>`;
    });
    row += `<div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-variant-row">×</button></div>`;
    row += `</div>`;
    return row;
}


$(document).on('click', '.remove-variant-row', function() {
    $(this).closest('.variant-row').remove();
});

window.setVariantAttributes = function(attrs, oldVariants = null) {
    window.currentAttributes = attrs;
    let headerHtml = `<div class="row font-weight-bold mb-2">
        <div class="col-md-2">SKU</div>
        <div class="col-md-2">Price</div>
        <div class="col-md-2">Stock</div>`;
    attrs.forEach(function(attr) {
        headerHtml += `<div class="col-md-2">${attr.name}</div>`;
    });
    headerHtml += `<div class="col-md-2"></div></div>`;
    $('#variants-header').replaceWith(headerHtml);

    $('#variants-list').html('');
    variantIndex = 0;
    if (oldVariants && Array.isArray(oldVariants) && oldVariants.length) {
        oldVariants.forEach(function(variant) {
            $('#variants-list').append(buildVariantRow(variantIndex, variant));
            variantIndex++;
        });
    } else {
        $('#variants-list').append(buildVariantRow(variantIndex));
        variantIndex++;
    }
};
</script>
@endpush
