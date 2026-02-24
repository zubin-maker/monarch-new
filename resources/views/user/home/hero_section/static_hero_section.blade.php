@extends('user.layout')

@includeIf('user.partials.rtl-style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Hero Section') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('user-dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Pages') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Home Page') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Hero Section') }}</a>
      </li>
    </ul>
  </div>
  <form id="ajaxForm" action="{{ route('user.home_page.static_hero_section.update') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="card">
      <div class="card-header text-center">
        <div class="row">
          <div class="col-lg-10">
            <div class="card-title text-left"> {{ __('Hero Section') }}
            </div>
          </div>
          <div class="col-lg-2">
            @if (!empty($u_langs))
              <select name="language" class="form-control"
                onchange="window.location='{{ url()->current() . '?language=' }}'+this.value">
                <option value="" selected disabled>
                  {{ __('Select a Language') }}
                </option>
                @foreach ($u_langs as $lang)
                  <option value="{{ $lang->code }}"
                    {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                    {{ $lang->name }}
                  </option>
                @endforeach
              </select>
            @endif
          </div>
        </div>
      </div>
      <input type="hidden" name="language_id" value="{{ $language->id }}">
      <div class="card-body">
        <div class="row">
          @php
            $not_allow_hero_image = ['jewellery'];
          @endphp
          <div class="{{ in_array($userBs->theme, $not_allow_hero_image) ? 'col-lg-12' : 'col-lg-6' }}">
            <div class="form-group">
              <label for="image"><strong>
                  {{ __('Background Image') }}</strong></label>
              <div class="showImage2 ">
                <img
                  src="{{ !is_null(@$data->background_image) ? asset('assets/front/img/hero-section/' . @$data->background_image) : asset('assets/admin/img/noimage.jpg') }}"
                  alt="..." class="img-thumbnail">
                @if (!is_null(@$ubs->hero_section_background_image))
                  <x-remove-button url="{{ route('user.remove_image', ['language_id' => $language->id]) }}"
                    name="hero_section_background_image" type="image" />
                @endif
              </div>
              <br>
              <div role="button" class="btn btn-primary btn-sm upload-btn" id="image2">
                {{ __('Choose Image') }}
                <input type="file" class="img-input" name="hero_section_background_image">
              </div>
              <p id="errhero_section_background_image" class="mb-0 text-danger em"></p>
            </div>
          </div>

          @if (!in_array($userBs->theme, $not_allow_hero_image))
            <div class="col-lg-6">
              <div class="form-group">
                <label for="image"><strong>
                    {{ __('Hero Image') }}</strong></label>
                <div class="showImage4">
                  <img
                    src="{{ !is_null(@$data->hero_image) ? asset('assets/front/img/hero-section/' . @$data->hero_image) : asset('assets/admin/img/noimage.jpg') }}"
                    alt="..." class="img-thumbnail">
                  @if (!is_null(@$ubs->hero_image))
                    <x-remove-button url="{{ route('user.remove_image', ['language_id' => $language->id]) }}"
                      name="hero_section_hero_image" type="image" />
                  @endif
                </div>
                <br>
                <div role="button" class="btn btn-primary btn-sm upload-btn" id="image4">
                  {{ __('Choose Image') }}
                  <input type="file" class="img-input" name="hero_image">
                </div>
                <p id="errhero_image" class="mb-0 text-danger em"></p>
              </div>
            </div>
          @endif
          <div class="col-lg-6">
            <div class="form-group">
              <label for="">{{ __('Title') }}</label>

              <input class="form-control" name="title" value="{{ @$data->title }}">
              <p id="errtitle" class="mb-0 text-danger em"></p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label for="">{{ __('Subtitle') }}</label>
              <input class="form-control" name="subtitle" value="{{ @$data->subtitle }}">
              <p id="errsubtitle" class="mb-0 text-danger em"></p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label for="">{{ __('Button Text') }}</label>
              <input class="form-control" name="button_text" value="{{ @$data->button_text }}">
              <p id="errbutton_text" class="mb-0 text-danger em"></p>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group">
              <label for="">{{ __('Button URL') }}</label>
              <input type="url" class="form-control" name="button_url" value="{{ @$data->button_url }}">
              <p id="errbutton_url" class="mb-0 text-danger em"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-center">
        <button type="submit" id="submitBtn" class="btn btn-success">{{ __('Update') }}</button>
      </div>
    </div>
  </form>
@endsection
@section('scripts')
  <script src="{{ asset('assets/user/js/image-text.js') }}"></script>
@endsection
