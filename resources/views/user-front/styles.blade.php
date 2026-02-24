<!--====== Favicon Icon ======-->
<link rel="shortcut icon" href="{{ !empty($userBs->favicon) ? asset('assets/front/img/user/' . $userBs->favicon) : '' }}"
  type="img/png" />


<link rel="stylesheet" href="{{ asset('assets/user-front/css/plugins.css') }}">
<link rel="stylesheet" href="{{ asset('assets/user-front/css/aos.min.css') }}">


<link rel="stylesheet" href="{{ asset('assets/user-front/fonts/fontawesome/css/all.min.css') }}">
<!-- Main Style CSS -->
<link rel="stylesheet" href="{{ asset('assets/user-front/css/common/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/user-front/css/common/header-1.css') }}">
<link rel="stylesheet" href="{{ asset('assets/user-front/css/tinymce-content.css') }}">

@if ($userBs->theme == 'vegetables')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/grocery/home-1.css') }}">
@elseif ($userBs->theme == 'furniture')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/furniture/home-2.css') }}">
@elseif ($userBs->theme == 'fashion')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/fashion/home-3.css') }}">
@elseif ($userBs->theme == 'electronics')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/electronics/home-4.css') }}">
@elseif ($userBs->theme == 'kids')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/kids/home-5.css') }}">
@elseif ($userBs->theme == 'manti')
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/manti/home-6.css') }}">
@elseif ($userBs->theme == 'pet')
  <style>
    :root {
      --font-family-base: "Nunito", sans-serif !important;
      --font-family-body: 'Nunito', sans-serif !important;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/pet/home-7.css') }}">
@elseif ($userBs->theme == 'skinflow')
  <style>
    :root {
      --font-family-base: "Jost", sans-serif;
      --font-family-body: "Jost", sans-serif;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/skinflow/home-8.css') }}">
@elseif ($userBs->theme == 'jewellery')

  <style>
    :root {
      --font-family-base: "Merriweather", serif !important;
      --font-family-body: "Jost", sans-serif !important;
    }
  </style>
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/jewellery/jewellery.css') }}">
@endif
<!--====== Style css ======-->

<!--====== RTL css ======-->
@if ($userCurrentLang->rtl == 1)
  <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
@endif
@if ($userCurrentLang->rtl == 1 & ($userBs->theme == 'pet'))
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/pet/home-7-rtl.css') }}">
@endif
@if ($userCurrentLang->rtl == 1 & ($userBs->theme == 'skinflow'))
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/skinflow/home-8-rtl.css') }}">
@endif
@if ($userCurrentLang->rtl == 1 & ($userBs->theme == 'jewellery'))
  <link rel="stylesheet" href="{{ asset('assets/user-front/css/jewellery/jewellery-rtl.css') }}">
@endif

@yield('styles')
