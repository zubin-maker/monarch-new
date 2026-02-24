
 <?php
    if(last(request()->segments()) =='desk-tables')
    {
        ?>
        <div class="page-title-area header-next" style="background-image:url('https://store.monarchergo.com/assets/front/img/user/modern-office-interior-design.jpg')">
        <?php
    }

   else{ ?>
   <div class="page-title-area header-next">
  @if (!is_null($userBe) && $userBe->breadcrumb)
    <img class="bg-img" src="{{ asset('assets/front/images/placeholder.png') }}"
      data-src="{{ asset('assets/front/img/user/' . $userBe->breadcrumb) }}" alt="Banner">
  @endif
  <?php } ?>
  <div class="container">
    <div class="content text-start">
      <h2>@yield('breadcrumb_title') </h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-start">
          <li class="breadcrumb-item"><a
              href="{{ route('front.user.detail.view', getParam()) }}">{{ $keywords['Home'] ?? __('Home') }}</a></li>
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
        </ol>
      </nav>
    </div>
  </div>
</div>
