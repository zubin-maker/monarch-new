@php
  // Default breadcrumb banner (set in "Breadcrumb" settings).
  $breadcrumbBannerUrl = (!is_null($userBe) && $userBe->breadcrumb)
    ? asset('assets/front/img/user/' . $userBe->breadcrumb)
    : null;

  $isShopPage = request()->routeIs('front.user.shop*');

  // Shop/category banner override:
  // /product-category/{categorySlug}  OR legacy /product-category?category=slug
  $shopCategorySlug = request()->route('category') ?? request()->input('category');
  $categoryBannerUrl = null;
  $categoryName = null;

  if (!empty($shopCategorySlug)) {
    // Prefer controller-provided $selected_category if present.
    if (isset($selected_category) && $selected_category && $selected_category->slug === $shopCategorySlug) {
      if (!empty($selected_category->category_background_image)) {
        $categoryBannerUrl = asset('assets/front/img/user/items/category_background/' . $selected_category->category_background_image);
      }
      $categoryName = $selected_category->name ?? null;
    } else {
      try {
        $user = app('user');
        $lang = app('userCurrentLang');
        $cat = \App\Models\User\UserItemCategory::where('slug', $shopCategorySlug)
          ->where('user_id', $user->id)
          ->where('language_id', $lang->id)
          ->where('status', 1)
          ->first();

        if ($cat && !empty($cat->category_background_image)) {
          $categoryBannerUrl = asset('assets/front/img/user/items/category_background/' . $cat->category_background_image);
        }
        $categoryName = $cat->name ?? null;
      } catch (\Throwable $e) {
        // Never break the page-title area if anything goes wrong here.
        $categoryBannerUrl = null;
      }
    }
  }

  $finalBannerUrl = $categoryBannerUrl ?? $breadcrumbBannerUrl;
@endphp

<div class="page-title-area header-next {{ $isShopPage ? 'shop-breadcrumb' : '' }}"
  data-default-banner="{{ $breadcrumbBannerUrl ?? '' }}"
  @if ($isShopPage)
    data-shop-url="{{ url('/product-category') }}"
  @endif
>
  @if (!empty($finalBannerUrl))
    <img id="pageTitleBanner" class="bg-img" src="{{ asset('assets/front/images/placeholder.png') }}"
      data-src="{{ $finalBannerUrl }}" alt="Banner">
  @endif
 <div class="container">
   <div class="content text-start">
     @if (!$isShopPage)
       <h2>@yield('breadcrumb_title') </h2>
     @endif
     <nav aria-label="breadcrumb">
       <ol class="breadcrumb justify-content-start">
         <li class="breadcrumb-item"><a
             href="{{ url('/') }}">{{ $keywords['Home'] ?? __('Home') }}</a></li>

         @if ($isShopPage)
           @php
             $isAllFilter = empty($shopCategorySlug) || $shopCategorySlug === 'all';
             $shopLabel = $pageHeading->shop_page ?? ($keywords['Shop'] ?? __('Shop'));
           @endphp

           @if ($isAllFilter)
             <li class="breadcrumb-item active" aria-current="page">{{ $shopLabel }}</li>
           @else
             <li class="breadcrumb-item">
               <a href="{{ url('/product-category') }}">{{ $shopLabel }}</a>
             </li>
             <li class="breadcrumb-item active" aria-current="page">{{ $categoryName ?? ucfirst($shopCategorySlug) }}</li>
           @endif
         @else
           <li class="breadcrumb-item active" aria-current="page">
             @if (
                 !request()->routeIs('customer.itemcheckout.offline.success') &&
                     !request()->routeIs('customer.success.page') &&
                     !request()->routeIs('front.user.productDetails') &&
                     !request()->routeIs('user-front.blog_details'))
               @yield('breadcrumb_title')
             @else
               @yield('breadcrumb_second_title')
             @endif
           </li>
         @endif
       </ol>
     </nav>
   </div>
 </div>
</div>
