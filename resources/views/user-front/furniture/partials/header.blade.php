<!-- Header Start -->


<style>
    
    .Shop_by{
font-size: 14px;
}
</style>

<header class="header header-fixed">
    <!-- Mobile Navbar -->
    <div class="mobile-navbar d-block d-xl-none">
        <div class="container">
            <div class="mobile-navbar-inner">
                <a href="https://store.monarchergo.com" class="logo">
                    <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                        data-src="{{ !empty($userBs->logo) ? asset('assets/front/img/user/' . $userBs->logo) : asset('assets/front/img/logo.png') }}"
                        alt="logo">
                </a>
                <button class="mobile-menu-toggler" type="button">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>

    <div class="header-top bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="header-left">
                        <ul>

                            <li><i class="{{ $header->header_logo ?? '' }}"></i>{{ $header->header_text ?? '' }}</li>
                            @php
                                $emails = !empty($userContact->contact_mails)
                                    ? explode(',', $userContact->contact_mails)
                                    : [];
                            @endphp
                            @if (count($emails) > 0)
                                <li>
                                    @foreach ($emails as $email)
                                        <i class="fal fa-envelope"></i>
                                        <a
                                            href="mailTo:{{ $email }}">{{ $email }}</a>{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <!-- <div class="col-xl-5 col-lg-4">
          <div class="header-center text-center">
           <p class="m-0">{{ $header->header_middle_text ?? '' }}</p>
          </div>
        </div> -->
                <div class="col-xl-6 col-lg-6 text-end">
                    <div class="header-right">
                        <ul class="menu justify-content-end ">
                            <!--<li class="menu-item">-->

                            <!--  @if ($userCurrentLang->id)
-->
                            <!--    <a href="javascript:void(0)"><i class="fal fa-globe"></i>{{ convertUtf8($userCurrentLang->name) }}</a>-->
                            <!--
@endif-->
                            <!--  <ul class="setting-dropdown">-->

                            <!--    @foreach ($userLangs as $userLang)
-->
                            <!--      <li>-->
                            <!--        <a href="{{ route('front.user.changeUserLanguage', ['code' => $userLang->code, getParam()]) }}"-->
                            <!--          class="menu-link" data-value="{{ $userLang->code }}">-->
                            <!--          {{ convertUtf8($userLang->name) }}-->
                            <!--        </a>-->
                            <!--      </li>-->
                            <!--
@endforeach-->
                            <!--  </ul>-->
                            <!--</li>-->

                            <!--<li class="menu-item">-->
                            <!--  @if ($userCurrentCurr->id)
-->
                            <!--    <a href="javascript:void(0)">{{ $userCurrentCurr->symbol }}-->
                            <!--      &nbsp;{{ convertUtf8($userCurrentCurr->text) }}</a>-->
                            <!--
@endif-->

                            <!--  <ul class="setting-dropdown">-->
                            <!--    @foreach ($userCurrency as $userCurr)
-->
                            <!--      <li>-->
                            <!--        <a href="{{ route('front.user.changeUserCurrency', ['id' => $userCurr->id, getParam()]) }}"-->
                            <!--          class="menu-link">{{ $userCurr->text }}</a>-->
                            <!--      </li>-->
                            <!--
@endforeach-->
                            <!--  </ul>-->

                            <!--</li>-->

                            <li class="menu-item ">
                                
                                
                                
                                        
                                            @guest('customer')
                                        <li class="me-5">
                                            <a class="menu-link"
                                                href="{{ route('customer.login') }}">{{ $keywords['Login'] ?? __('Login') }}</a>
                                        </li>
                                        <!--<li>-->
                                        <!--    <a class="menu-link"-->
                                        <!--        href="{{ route('customer.signup') }}">{{ $keywords['Signup'] ?? __('Signup') }}</a>-->
                                        <!--</li>-->
                                    @endguest
                                    @auth('customer')
                                        @php $authUserInfo = Auth::guard('customer')->user(); @endphp
                                        <a href="#"><i
                                        class="fal fa-user"></i>{{ $keywords['My Account'] ?? __('My Account') }}</a>
                                        
                                        
                                <ul class="setting-dropdown">
                                
                                        <li>
                                            <a href="{{ route('customer.dashboard', getParam()) }}"
                                                class="menu-link">{{ $keywords['Dashboard'] ?? __('Dashboard') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('customer.logout', getParam()) }}"
                                                class="menu-link">{{ $keywords['Logout'] ?? __('Logout') }}</a>
                                        </li>
                                    @endauth
                                </ul>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-middle sticky-header-2">

        <div class="container">
            <div class="header-left">
                <div class="brand-logo">
                    <a href="https://store.monarchergo.com" title="" target="_self">
                        <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                            data-src="{{ !empty($userBs->logo) ? asset('assets/front/img/user/' . $userBs->logo) : asset('assets/front/img/logo.png') }}"
                            alt="">
                    </a>
                </div>
            </div>
            <div class="header-center">
                <nav class="menu mobile-nav">
                    <ul class="menu-right me-auto">
                        <li class="nav-item">
                            <a class="nav-link " target="_self" href="https://store.monarchergo.com">Home</a>
                        </li>
                        <!-- <li class="nav-item">-->
                        <!--    <a class="nav-link " target="_self" href="{{ route('front.user.shop', 'office-chairs-2') }}">Seating</a>-->
                        <!--</li>-->
                        <!-- <li class="nav-item">-->
                        <!--    <a class="nav-link " target="_self" href="{{ route('front.user.shop', 'desk-tables') }}">Desks</a>-->
                        <!--</li>-->
                        
                        <!--                                                      <li class="nav-item">-->
                        <!--  <a class="nav-link " target="_self"-->
                        <!--    href="{{ route('front.user.shop') }}">Shop</a>-->
                        <!--</li>-->

                        <li class="nav-item has-submenu">
                            <a class="nav-link " target="_self" href="{{ route('front.user.shop') }}">Shop <i
                                    class="fal fa-plus"></i></a>
                            <ul class="submenu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('front.user.shop', 'office-chairs') }}"
                                        target="_self">
                                        Office Chairs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('front.user.shop', 'desk-tables') }}"
                                        target="_self">
                                        Height Adjustable Tables
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('front.user.shop', 'gaming-chair') }}"
                                        target="_self">
                                        Gaming Chair
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item d-none">
                            <a class="nav-link " target="_self" href="https://www.monarchergo.com/about-us">About Us</a>
                        </li>
                        <li class="nav-item d-none">
                            <a class="nav-link " target="_self" href="{{ route('front.user.blogs') }}">Blog</a>
                        </li>
                        <!--                                                      <li class="nav-item">-->
                        <!--  <a class="nav-link " target="_self"-->
                        <!--    href="https://store.seabluehost.com/store/faqs">FAQ</a>-->
                        <!--</li>-->
                        <li class="nav-item">
                            <a class="nav-link " target="_self" href="{{ route('front.user.contact') }}">Contact</a>
                        </li>
                        


                    </ul>
                </nav>
            </div>
            <div class="header-right">
                <ul class="menu">
                    
                    <li class="menu-item menu-wishlist border-end">
                        <a href="#elevate" class="menu-link Shop_by"
                            
                            title="">
                            Shop By | Inspiration + Resources
                        </a>
                    </li>
                    <li class="menu-item menu-wishlist border-end">
                        <a href="{{ route('customer.wishlist') }}" class="menu-link"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="{{ $keywords['Wishlist'] ?? __('Wishlist') }}">
                            <i class="fal fa-heart">
                                <span class="badge wishlist-count">{{ $wishListCount }}</span>
                            </i>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('front.user.compare') }}" class="menu-link"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="{{ $keywords['Compare'] ?? __('Compare') }}">
                            <i class="fal fa-random">
                                <span class="badge" id="compare-count">{{ $compareCount }}</span>
                            </i>
                        </a>
                    </li>
                    @if ($shop_settings->catalog_mode != 1)
                        <li class="menu-item">
                            <a href="javascript:void(0)" class="menu-link" data-bs-toggle="tooltip"
                                data-bs-placement="right" title="{{ $keywords['Cart'] ?? __('Cart') }}">
                                <i class="fal fa-shopping-cart">
                                    <span class="badge cart-dropdown-count">{{ $cartCount }}</span>
                                </i>

                            </a>
                            <div class="cart-dropdown" id="cart-dropdown-header">
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- Header End -->
