@extends('theme.adminlte.layouts.app')
@push('head')
  <link rel="stylesheet" href="{{ asset('theme/adminlte/assets/css/image-upload.css') }}">
@endpush

@section('content-header')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>@lang('crud.edit_title', ['name' => 'Meal'])</h1>
    </div>
    <div class="col-sm-6 d-flex flex-row justify-content-end gap-2">

      <a href="{{ route('admin.catalog.meals.index') }}" class="btn btn-secondary">@lang('crud.back_to_list', ['name' => 'Meal'])</a>
    </div>
  </div>
@endsection

@section('content')
  <form method="POST" action="{{ route('admin.catalog.meals.update', $meal->id) }}" class="ajax-form"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
      <div class="col-md-7">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Meal Details</h3>
          </div>
          <div class="card-body">

            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" name="name" class="form-control" value="{{ $meal->name }}" required>
            </div>
            <div class="form-group">
              <label for="tagline">Tagline</label>
              <textarea name="tagline" class="form-control" rows="3">{{ $meal->tagline }}</textarea>
            </div>

            <div class="form-group">
              <label for="slug">Slug</label>
              <input type="text" name="slug" class="form-control" value="{{ $meal->slug }}" required>
            </div>

            <div class="form-group">
              <label for="position">Position</label>
              <input type="number" name="position" class="form-control" value="{{ $meal->position }}">

            </div>
            <div class="form-group">
              <div class="custom-control custom-switch mb-2">
                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active"
                  {{ old('is_active', $meal->is_active) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">Active</label>
              </div>
            </div>

            <div class="form-group">
              <label>Sample Menu</label>
              <input type="file" name="sample_menu_file" class="form-control-file" accept=".pdf">

              @if (!empty($meal->sample_menu_file))
                <div class="mt-2">
                  <a href="{{ asset('storage/' . $meal->sample_menu_file) }}" target="_blank"
                    class=" link-black text-sm"><i class="fas fa-link mr-1"></i> View Sample Menu</a>

                </div>
              @endif
            </div>
            <div class="form-group">
              <label>Images</label>
              {{-- <div class="upload__box">
                <div class="upload__btn-box">
                  <label class="upload__btn btn btn-outline-primary">Upload images
                    <input type="file" name="attachments[]" multiple data-min-length="1" min="1"
                      data-max_length="10" class="upload__inputfile" accept="image/*" />
                  </label>
                </div>
                <div class="upload__img-wrap"></div>
              </div> --}}

              <div class="upload__box">
                <div class="upload__btn-box">
                  <label class="upload__btn btn btn-outline-primary">Upload images
                    <input type="file" name="attachments[]" multiple data-max_length="5" class="upload__inputfile"
                      accept="image/*" />
                  </label>
                </div>
                <div class="upload__img-wrap uploaded-image-box"></div>
                <div class="form-group">
                  <label>Existing Images</label>
                  <div class="uploaded-image-box">
                    @foreach ($meal->attachments as $attachment)
                      <div class="uploaded-image" id="image_{{ $attachment->id }}">
                        <img src="{{ asset('storage/' . $attachment->file_path) }}" class="img-thumbnail">
                        <button type="button" class="delete-image-btn btn-delete" data-refresh="false"
                          data-remove="#image_{{ $attachment->id }}" data-id="{{ $attachment->id }}"
                          data-url="{{ route('admin.cms.attachments.destroy', $attachment->id) }}">
                          &times;
                        </button>
                      </div>
                    @endforeach
                  </div>
                </div>

              </div>
            </div>



          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary">@lang('crud.update')</button>
              </div>
            </div>
          </div>
        </div>


        {{-- @include('theme.adminlte.components._metas', ['model' => $meal]) --}}

      </div>
      <div class="col-md-5">

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Packages
            </h3>

            <div class="card-tools">
              {{-- <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
              </button> --}}
              <button type="button" class="btn btn-sm btn-secondary" onclick="getAside()"
                data-url="{{ route('admin.catalog.meal.packages.create', ['meal' => $meal]) }}">
                <i class="fa fa-plus"></i> Add Package
              </button>

            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 col-md-12 order-2 order-md-1">

                <div class="row">
                  <div class="col-12">
                    @if ($meal->mealPackages->isNotEmpty())
                      @foreach ($meal->mealPackages as $mealPackage)
                        @php
                          $package = $mealPackage->package;
                        @endphp
                        <div class="post {{ $loop->iteration > 1 }} ? 'clearfix' : ''">
                          <div class="user-block">
                            <span class="float-right">
                              @if (!is_null($mealPackage->deleted_at))
                                <a href="#"
                                  data-url="{{ route('admin.catalog.meal.packages.restore', ['meal' => $meal, 'package' => $package]) }}"
                                  class="btn btn-sm btn-outline-info text-sm btn-delete">
                                  <i class="fas fa-trash-restore mr-1"></i> Restore
                                </a>
                              @else
                                @if ($mealPackage->is_active)
                                  <a href="#"
                                    data-url="{{ route('admin.catalog.meal.packages.destroy', ['meal' => $meal, 'package' => $package, 'status' => 0]) }}"
                                    class="btn btn-sm btn-outline-warning text-sm btn-delete">
                                    <i class="far fa-eye-slash mr-1"></i> Mark Inactive
                                  </a>
                                @else
                                  <a href="#"
                                    data-url="{{ route('admin.catalog.meal.packages.destroy', ['meal' => $meal, 'package' => $package, 'status' => 1]) }}"
                                    class="btn btn-sm btn-outline-warning text-sm btn-delete">
                                    <i class="far fa-eye mr-1"></i> Activate
                                  </a>
                                @endif

                                <a href="#"
                                  data-url="{{ route('admin.catalog.meal.packages.destroy', ['meal' => $meal, 'package' => $package]) }}"
                                  class="btn btn-sm btn-outline-danger text-sm btn-delete">
                                  <i class="fas fa-trash-restore mr-1"></i> Delete
                                </a>
                              @endif

                            </span>
                            <img class="img-sm img-bordered-sm" src="{{ asset('storage/' . $package->thumbnail) }}"
                              alt="user image">
                            <span class="username {{ $mealPackage->stripe_product_id ? 'text-primary' : '' }}">
                              {{ $package->name }}
                              @if ($mealPackage->is_active)
                                <span class="badge badge-success">Active</span>
                              @else
                                <span class="badge badge-secondary">Inactive</span>
                              @endif
                            </span>
                            <span class="description">{{ $package->tagline }}</span>


                          </div>

                          @if ($mealPackage->prices->isNotEmpty())
                            <div class="table-responsive">
                              <table class="table">
                                <thead>

                                  <tr>
                                    <th>kcal</th>
                                    <th>Dur.</th>
                                    <th>Price</th>
                                    <th>Dis.</th>
                                    <th>Status</th>
                                    <th></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($mealPackage->prices as $price)
                                    <tr>
                                      <td class="{{ $price->stripe_price_id ? 'text-primary' : '' }}">
                                        {{ $price->calorie->label }}</td>
                                      <td>{{ $price->duration }}</td>
                                      <td>{{ $price->price }}</td>
                                      <td>{{ $price->discount_percent ?? 0 }}%</td>
                                      <td>
                                        @if ($price->is_active)
                                          <span class="badge badge-success">Active</span>
                                        @else
                                          <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                      </td>
                                      <td class="text-end">
                                        <div class="btn-group">
                                          <button type="button" id="btn_{{ $price->id }}" class="btn btn-default"
                                            onclick="getAside()"
                                            data-url="{{ route('admin.catalog.meal.package.prices.edit', ['meal' => $meal, 'package' => $package, 'price' => $price]) }}"><i
                                              class="fa fa-pen"></i></button>
                                        </div>
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          @else
                            <p class="text-muted">No Price added yet.</p>
                          @endif

                          <p>
                            <button type="button" onclick="getAside()"
                              data-url="{{ route('admin.catalog.meal.package.prices.create', ['meal' => $meal, 'package' => $package]) }}"
                              class="text-sm mr-2 btn btn-sm btn-outline-dark"><i class="fas fa-plus mr-1"></i> Add
                              Price</button>

                          </p>


                        </div>
                      @endforeach
                    @else
                      <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <p class="text-muted text-center my-3">No packages added yet</p>

                      </div>
                    @endif

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-body -->
        </div>



      </div>
    </div>
  </form>

  @push('scripts')
    <script src="{{ asset('theme/adminlte/assets/js/image-upload.js') }}"></script>
  @endpush

  @push('scripts')
    <script></script>
  @endpush
@endsection
