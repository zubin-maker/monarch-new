@extends('user.layout')

@php
    $selLang = \App\Models\User\Language::where([
        ['code', 'en'],
        ['user_id', Auth::guard('web')->user()->id],
    ])->firstOrFail();

    $user_id = Auth::guard('web')->user()->id;

    // English language is fixed (id = 17)
    $englishLang = \App\Models\User\Language::where([
        ['user_id', $user_id],
        ['id', 17],
    ])->firstOrFail();

    $categories = \App\Models\User\UserItemCategory::where([
        ['user_id', $user_id],
        ['language_id', $englishLang->id],
        ['status', 1],
    ])->get();

    $variant_content = \App\Models\VariantContent::where([
        ['user_id', $user_id],
        ['language_id', $englishLang->id],
        ['variant_id', $variant->id],
    ])->first();

    $subcategories = $variant_content && $variant_content->category_id
        ? \App\Models\User\UserItemSubCategory::where([
            ['user_id', $user_id],
            ['category_id', $variant_content->category_id],
            ['status', 1],
        ])->get()
        : collect();
@endphp

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Edit Variant') }}</h4>
    <ul class="breadcrumbs">
        {{-- Breadcrumbs --}}
    </ul>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="row">
                <div class="col-lg-7">{{ __('Edit Variant') }}</div>
                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0 text-right">
                    <a class="btn btn-info text-white btn-sm"
                       href="{{ route('user.variant.index') . '?language=en' }}">
                        <i class="fas fa-backward"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="itemVariationForm"
              action="{{ route('user.variant.update', $variant->id) }}"
              method="post"
              enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="alert alert-danger pb-1 d-none" id="postErrors"><ul></ul></div>

                    <div id="variant-container">
                        <div class="row variant-box" data-index="1">
                            <div class="col-lg-12 p-0 variant-main">
                                <div class="row">

                                    {{-- Category --}}
                                    <div class="col-md-3 category_dropdown">
                                        <div class="form-group">
                                            <label>{{ __('Category') }} <span class="text-danger">**</span></label>
                                            <select class="form-control variation_category"
                                                    data-language_id="{{ $englishLang->id }}"
                                                    data-language_code="en"
                                                    name="category_id">
                                                <option value="">{{ __('Select Category') }}</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                            {{ $variant_content && $variant_content->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-warning">
                                                {{ __('Changing the category may affect your product variations.') }}
                                            </small>
                                            <p class="mb-0 text-danger em errcategory_id"></p>
                                        </div>
                                    </div>

                                    {{-- Subcategory --}}
                                    <!--<div class="col-md-3 subcategory_dropdown">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label>{{ __('Subcategory') }} <span class="text-danger">**</span></label>-->
                                    <!--        <select class="form-control variation_subcategory" name="sub_category_id">-->
                                    <!--            <option value="">{{ __('Select Subcategory') }}</option>-->
                                    <!--            @foreach ($subcategories as $sub)-->
                                    <!--                <option value="{{ $sub->id }}"-->
                                    <!--                        {{ $variant_content && $variant_content->sub_category_id == $sub->id ? 'selected' : '' }}>-->
                                    <!--                    {{ $sub->name }}-->
                                    <!--                </option>-->
                                    <!--            @endforeach-->
                                    <!--        </select>-->
                                    <!--        <p class="mb-0 text-danger em errsub_category_id"></p>-->
                                    <!--    </div>-->
                                    <!--</div>-->

                                    {{-- Variant Name (English only) --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('Variant Name') }} (en) <span class="text-danger">**</span></label>
                                            <input name="variant_names[en][]"
                                                   type="text"
                                                   class="form-control"
                                                   value="{{ $variant_content->name ?? '' }}"
                                                   placeholder="{{ __('e.g., size, color etc.') }}">
                                            <p class="mb-0 text-danger em errvariant_names_en"></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Add Option Button --}}
                            <div class="col-lg-12 pl-0 mt-2">
                                <button type="button" class="btn btn-secondary btn-sm add-option">
                                    <i class="fas fa-plus"></i> {{ __('Add Option') }}
                                </button>
                            </div>

                            {{-- Options Container --}}
                            <div class="col-lg-12 options-container">
                                @php
                                    $optionContents = \App\Models\VariantOptionContent::where([
                                        ['variant_id', $variant->id],
                                        ['language_id', $englishLang->id],
                                    ])->orderBy('index_key')->get();
                                @endphp

                                @foreach ($optionContents as $opt)
                                    @php $uniq = $opt->index_key; @endphp
                                    <div class="row option-box mb-3" data-vindex="1" data-oindex="{{ $uniq }}">
                                        <div class="col-lg-11">
                                            <div class="row">
                                                {{-- Option Name (en) --}}
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Option Name') }} (en) <span class="text-danger">**</span></label>
                                                        <input name="option_names[en][{{ $uniq }}][]"
                                                               type="text"
                                                               class="form-control"
                                                               value="{{ $opt->option_name ?? '' }}"
                                                               placeholder="{{ __('e.g., m, xl, black, red etc.') }}">
                                                        <p class="mb-0 text-danger em erroption_names_en_{{ $uniq }}"></p>
                                                    </div>
                                                </div>

                                                {{-- Image (en) --}}
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Image') }} (en) <span class="text-danger">**</span></label>
                                                        <input name="images[en][{{ $uniq }}][]"
                                                               type="file"
                                                               class="form-control">
                                                        @if ($opt->image)
                                                            <small class="d-block mt-1">
                                                                <img src="{{ asset($opt->image) }}" alt="" width="50">
                                                            </small>
                                                        @endif
                                                        <p class="mb-0 text-danger em errimages_en_{{ $uniq }}"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-1 text-right">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm text-white delete-option"
                                                    data-oindex="{{ $uniq }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <button type="submit" class="btn btn-success">{{ __('Update Variation') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vuescripts')
<script>
    'use strict';

    function uniqId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
    }

    function renderOption(vIndex, uniq) {
        let html = `
            <div class="row option-box mb-3" data-vindex="${vIndex}" data-oindex="${uniq}">
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Option Name') }} (en) <span class="text-danger">**</span></label>
                                <input name="option_names[en][${uniq}][]"
                                       type="text"
                                       class="form-control"
                                       placeholder="{{ __('e.g., m, xl, black, red etc.') }}">
                                <p class="mb-0 text-danger em erroption_names_en_${uniq}"></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Image') }} (en) <span class="text-danger">**</span></label>
                                <input name="images[en][${uniq}][]"
                                       type="file"
                                       class="form-control">
                                <p class="mb-0 text-danger em errimages_en_${uniq}"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 text-right">
                    <button type="button"
                            class="btn btn-danger btn-sm text-white delete-option"
                            data-oindex="${uniq}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        $(`[data-index="${vIndex}"] .options-container`).append(html);
    }

    $(document).on('click', '.add-option', function () {
        const vIndex = $(this).closest('.variant-box').data('index');
        const uniq = uniqId();
        renderOption(vIndex, uniq);
    });

    $(document).on('click', '.delete-option', function () {
        const $row = $(this).closest('.option-box');
        const oindex = $row.data('oindex');

        if (!isNaN(oindex) && oindex > 0) {
            if (!confirm('{{ __("Are you sure you want to delete this option?") }}')) return;
            $.post('{{ route('user.variant.delete-option') }}', {
                _token: '{{ csrf_token() }}',
                index_key: oindex
            }).then(() => $row.remove())
              .catch(() => alert('{{ __("Failed to delete option.") }}'));
        } else {
            $row.remove();
        }
    });

    $(document).on('change', '.variation_category', function () {
        const $catSelect = $(this);
        const langId = $catSelect.data('language_id');
        const $subSelect = $catSelect.closest('.variant-main').find('.variation_subcategory');
        $subSelect.html('<option value="">{{ __("Select Subcategory") }}</option>');
        const catId = $catSelect.val();
        if (!catId) return;

        $.get('{{ route('user.variant.get-subcategory') }}', {
            category_id: catId,
            language_id: langId
        }).then(html => $subSelect.append(html));
    });

    $('#itemVariationForm').on('submit', function (e) {
        $('.em').text('');
        let valid = true;

        if (!$('[name="category_id"]').val()) {
            $('.errcategory_id').text('{{ __("Category is required.") }}');
            valid = false;
        }
        // if (!$('[name="sub_category_id"]').val()) {
        //     $('.errsub_category_id').text('{{ __("Subcategory is required.") }}');
        //     valid = false;
        // }
        if (!$('[name="variant_names[en][]"]').val().trim()) {
            $('.errvariant_names_en').text('{{ __("Variant name is required.") }}');
            valid = false;
        }
        if ($('.option-box').length === 0) {
            alert('{{ __("You must add at least one option.") }}');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            $('#postErrors ul').empty().append('<li>{{ __("Please fix the errors below.") }}</li>');
            $('#postErrors').removeClass('d-none');
        }
    });
</script>
@endsection