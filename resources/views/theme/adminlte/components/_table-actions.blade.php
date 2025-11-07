<div class="d-flex gap-2">

  @if (!empty($showUrl))
    <a href="{{ $showUrl }}" class="btn btn-sm btn-secondary">@lang('crud.show')</a>
  @endif

  @if (empty($row->deleted_at))
    @if (!empty($editUrl))
      @if (!empty($editSidebar))
        <button data-url="{{ $editUrl }}" type="button" class="btn btn-sm btn-secondary"
          onclick="getAside()">@lang('crud.edit')</button>
      @else
        <a href="{{ $editUrl }}" class="btn btn-sm btn-secondary">@lang('crud.edit')</a>
      @endif

    @endif
    @if (!empty($deleteUrl))
      <button type="button" class="btn btn-sm btn-danger btn-delete"
        data-url="{{ $deleteUrl }}">@lang('crud.delete')</button>
    @endif
  @elseif(!empty($restoreUrl))
    <button data-url="{{ $restoreUrl }}" class="btn btn-sm btn-success btn-delete">@lang('crud.restore')</button>
  @endif

</div>
