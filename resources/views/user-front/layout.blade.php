<!DOCTYPE html>
<html lang="{{ $userCurrentLang->code }}" dir="{{ $userCurrentLang->rtl == 1 ? 'rtl' : '' }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

  <title>@yield('page-title') | {{ $user->username }} </title>
  <link rel="icon" href="{{ !empty($userBs->favicon) ? asset('assets/front/img/user/' . $userBs->favicon) : '' }}">
 <meta name="description" content="@yield('meta-description', 'Default description')">
  <meta name="keywords" content="@yield('meta-keywords', 'default, keywords')">
  <meta name="title" content="@yield('meta-title')">


  <link rel="canonical" href="{{ canonicalUrl() }}">
  @yield('og-meta')
  @includeif('user-front.styles')
  @php
    $selLang = App\Models\Language::where('code', request()->input('language'))->first();
  @endphp

  <style>
    :root {
      --color-primary: #{{ $userBs->base_color }};
      --color-primary-rgb: {{ hexToRgba($userBs->base_color) }}
    }
  </style>

  @yield('styles')

  @if ($userBs->is_analytics == 1 && in_array('Google Analytics', $packagePermissions))
    <script async src="//www.googletagmanager.com/gtag/js?id={{ $userBs->measurement_id }}"></script>
    <script>
      "use strict";
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());

      gtag('config', '{{ $userBs->measurement_id }}');
    </script>
  @endif
<!-- Google tag (gtag.js) -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-741835309"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-741835309');
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-N45DJJZHGS"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-N45DJJZHGS');
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PWCBQP7W');</script>
<!-- End Google Tag Manager -->




<!-- Event snippet for Add to cart New conversion page
In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
<script>
function gtag_report_conversion(url) {
  var callback = function () {
    if (typeof(url) != 'undefined') {
      window.location = url;
    }
  };
  gtag('event', 'conversion', {
      'send_to': 'AW-664327569/RT-JCKr7moMYEJGr47wC',
      'event_callback': callback
  });
  return false;
}
</script>
<!-- Event snippet for Order-Received_Monarch_3DM conversion page
In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
<script>
function gtag_report_conversion(url) {
  var callback = function () {
    if (typeof(url) != 'undefined') {
      window.location = url;
    }
  };
  gtag('event', 'conversion', {
      'send_to': 'AW-664327569/yJ46CJPu99wDEJGr47wC',
      'transaction_id': '',
      'event_callback': callback
  });
  return false;
}
</script>

<!-- Event snippet for Purchase New conversion page
In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
<script>
function gtag_report_conversion(url) {
  var callback = function () {
    if (typeof(url) != 'undefined') {
      window.location = url;
    }
  };
  gtag('event', 'conversion', {
      'send_to': 'AW-664327569/6I_fCI6Dm4MYEJGr47wC',
      'transaction_id': '',
      'event_callback': callback
  });
  return false;
}
</script>
</head>

<body @if (request()->cookie('user-theme') == 'dark') data-background-color="dark" @endif>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PWCBQP7W"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript)-->



  {{-- Loader --}}
  <div class="request-loader">
    <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
      data-src="{{ asset('assets/admin/img/loader.gif') }}" alt="">
  </div>
  {{-- Loader --}}

  <!-- Preloader Start -->
  @if ($userBs->preloader_status == 1)
    <div class="preloader">
      <div class="preloader-wrapper">
        <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
          data-src="{{ !is_null($userBs->preloader) ? asset('assets/front/img/user/' . $userBs->preloader) : asset('assets/user-front/images/preloader.gif') }}"
          alt="preloder-image">
      </div>
    </div>
  @endif
  <!-- Preloader End -->

  <div class="wrapper">
    {{-- top navbar area start --}}
    @if ($userBs->theme == 'electronics')
      @includeif('user-front.electronics.partials.header')
    @elseif($userBs->theme == 'vegetables')
      @includeif('user-front.grocery.partials.header')
    @elseif($userBs->theme == 'fashion')
      @includeif('user-front.fashion.partials.header')
    @elseif($userBs->theme == 'furniture')
      @includeif('user-front.furniture.partials.header')
    @elseif($userBs->theme == 'kids')
      @includeif('user-front.kids.partials.header')
    @elseif($userBs->theme == 'manti')
      @includeif('user-front.manti.partials.header')
    @elseif($userBs->theme == 'pet')
      @includeif('user-front.pet.partials.header')
    @elseif($userBs->theme == 'skinflow')
      @includeif('user-front.skinflow.partials.header')
    @elseif($userBs->theme == 'jewellery')
      @includeif('user-front.jewellery.partials.header')
    @endif


    @if (!request()->routeIs('front.user.detail.view'))
      @includeIf('user-front.partials.breadcrumb')
    @endif

    <div class="main-panel">
      <div class="content">
        <div class="page-inner">
          @yield('content')
        </div>
      </div>

      @if ($userBs->footer_section == 1)
        @if ($userBs->theme == 'electronics')
          @includeif('user-front.electronics.partials.footer')
        @elseif($userBs->theme == 'vegetables')
          @includeif('user-front.grocery.partials.footer')
        @elseif($userBs->theme == 'fashion')
          @includeif('user-front.fashion.partials.footer')
        @elseif($userBs->theme == 'furniture')
          @includeif('user-front.furniture.partials.footer')
        @elseif($userBs->theme == 'kids')
          @includeif('user-front.kids.partials.footer')
        @elseif($userBs->theme == 'manti')
          @includeif('user-front.manti.partials.footer')
        @elseif($userBs->theme == 'pet')
          @includeif('user-front.pet.partials.footer')
        @elseif($userBs->theme == 'skinflow')
          @includeif('user-front.skinflow.partials.footer')
        @elseif($userBs->theme == 'jewellery')
          @includeif('user-front.jewellery.partials.footer')
        @endif
      @endif
    </div>
  </div>

  <div class="go-top active"><i class="fal fa-angle-double-up"></i></div>
  @if (@$userBe->cookie_alert_status == 1)
    <div class="cookie">
      @include('cookie-consent::index')
    </div>
  @endif

  @if ($userBs->theme == 'pet')
    @includeIf('user-front.pet.partials.mobile-menu')
  @elseif ($userBs->theme == 'skinflow')
    @includeIf('user-front.skinflow.partials.mobile-menu')
  @else
    @includeIf('user-front.partials.mobile-footer-menu')
  @endif

  <!-- WhatsApp Chat Button -->
  <div id="WAButton"></div>

  @includeif('user-front.scripts')
  @yield('scripts')
  @includeIf('user-front.partials.plugins')
</body>

</html>
