@php
  use App\Http\Helpers\LimitCheck as LimitCheck;

  $userId = Auth::guard('web')->user()->id;
  $currPackage = LimitCheck::current_package($userId);
  $featureCount = LimitCheck::packageFeaturesCount($userId);
  $infoIcon = false;

  //product category limit
  $catLimit = LimitCheck::catLimit($userId);
  $totalCat = $featureCount['categories'];
  $canAddCat = $catLimit - $totalCat;
  //product subcategory limit
  $subcatLimit = LimitCheck::subcatLimit($userId);
  $totalSubcat = $featureCount['subcategories'];
  $canAddSubcat = $subcatLimit - $totalSubcat;
  //product limit
  $itemLimit = LimitCheck::itemLimit($userId);
  $totalItem = $featureCount['items'];
  $canAddItem = $itemLimit - $totalItem;
  //blog limit
  $blogLimit = LimitCheck::blogLimit($userId);
  $totalBlog = $featureCount['blogs'];
  $canAddBlog = $blogLimit - $totalBlog;
  //language limit
  $langLimit = LimitCheck::langLimit($userId);
  $totalLang = $featureCount['languages'];
  $canAddLang = $langLimit - $totalLang;
  //custompage limit
  $pageLimit = LimitCheck::pageLimit($userId);
  $totalCustomPage = $featureCount['custome_page'];
  $canAddPage = $pageLimit - $totalCustomPage;
  //order limit
  $orderLimit = LimitCheck::orderLimit($userId);
  $totalOrder = $featureCount['items'];
  $canAddOrder = $orderLimit - $totalOrder;

  if ($canAddCat < 0 || $canAddSubcat < 0 || $canAddItem < 0 || $canAddBlog < 0 || $canAddLang < 0 || $canAddPage < 0) {
      $infoIcon = true;
  }
@endphp

<div class="main-header">
  <!-- Logo Header -->
  <div class="logo-header" @if (request()->cookie('user-theme') == 'dark') data-background-color="dark2" @endif>
    <a href="{{ route('front.index') }}" class="logo" target="_blank">
      <img
        src="{{ !empty($userBs->logo) ? asset('assets/front/img/user/' . $userBs->logo) : asset('assets/front/img/logo.png') }}"
        alt="Logo" class="navbar-brand">
    </a>
    <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
        <i class="icon-menu"></i>
      </span>
    </button>
    <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
    <div class="nav-toggle">
      <button class="btn btn-toggle toggle-sidebar">
        <i class="icon-menu"></i>
      </button>
    </div>
  </div>
  <!-- End Logo Header -->
  <!-- Navbar Header -->
  <nav class="navbar navbar-header navbar-expand-lg"
    @if (request()->cookie('user-theme') == 'dark') data-background-color="dark" @endif>
    <div class="container-fluid">
      <ul class="navbar-nav topbar-nav ml-md-auto align-items-centere">
       
        <form action="{{ route('user.theme.change') }}" class="form-inline" id="adminThemeForm">
          <div class="form-group">
            <div class="selectgroup selectgroup-secondary selectgroup-pills">
              <label class="selectgroup-item">
                <input type="radio" name="theme" value="light" class="selectgroup-input"
                  {{ empty(request()->cookie('user-theme')) || request()->cookie('user-theme') == 'light' ? 'checked' : '' }}
                  onchange="document.getElementById('adminThemeForm').submit();">
                <span class="selectgroup-button selectgroup-button-icon"><i class="fa fa-sun"></i></span>
              </label>
              <label class="selectgroup-item">
                <input type="radio" name="theme" value="dark" class="selectgroup-input"
                  {{ request()->cookie('user-theme') == 'dark' ? 'checked' : '' }}
                  onchange="document.getElementById('adminThemeForm').submit();">
                <span class="selectgroup-button selectgroup-button-icon"><i class="fa fa-moon"></i></span>
              </label>
            </div>
          </div>
        </form>
        <li>
        <li class="mr-2 d-none">
          @php
            if (Auth::user()->custom_domain_status == 1 && !empty(Auth::user()->custom_domain)) {
                $domain = Auth::user()->custom_domain;
            } else {
                $domain = Auth::user()->username . '.' . env('WEBSITE_HOST');
            }
          @endphp
          <a class="btn btn-primary btn-sm btn-round" target="_blank"
            href="{{ route('front.user.detail.view', Auth::user()->username) }}" title="View Profile">
            <i class="fas fa-eye"></i>
          </a>
        </li>
        </li>
       
        <li class="nav-item dropdown hidden-caret">
          <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
            <div class="avatar-sm">
              @if (!empty(Auth::user()->photo))
                <img src="{{ asset('assets/front/img/user/' . Auth::user()->photo) }}" alt="..."
                  class="avatar-img rounded-circle">
              @else
                <img src="{{ asset('assets/admin/img/propics/blank_user.jpg') }}" alt="..."
                  class="avatar-img rounded-circle">
              @endif
            </div>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    @if (!empty(Auth::user()->photo))
                      <img src="{{ asset('assets/front/img/user/' . Auth::user()->photo) }}" alt="..."
                        class="avatar-img rounded">
                    @else
                      <img src="{{ asset('assets/admin/img/propics/blank_user.jpg') }}" alt="..."
                        class="avatar-img rounded">
                    @endif
                  </div>
                  <div class="u-text">
                    <h4>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                  
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                <!--<a class="dropdown-item" href="{{ route('user-profile-update') }}">{{ __('Edit Profile') }}</a>-->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('user.changePass') }}">{{ __('Change Password') }}</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('user-logout') }}">{{ __('Logout') }}</a>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
</div>
@includeIf('user.partials.limit-modal')
