<div class="menu-action-item-area">
  <ul class="menu-action-item-wrapper">

    <li class="menu-action-item">
      <a href="javascript:void(0)"><span class="icon"><i class="fal fa-user"></i></span>
        {{ $keywords['My Account'] ?? __('My Account') }} <span class="plus-icon"><i class="fal fa-plus"></i></span></a>
      <ul class="setting-dropdown">
        @guest('customer')
          <li>
            <a class="menu-link"
              href="{{ route('customer.login', getParam()) }}">{{ $keywords['Login'] ?? __('Login') }}</a>
          </li>
          <li>
            <a class="menu-link"
              href="{{ route('customer.signup', getParam()) }}">{{ $keywords['Signup'] ?? __('Signup') }}</a>
          </li>
        @endguest
        @auth('customer')
          <li>
            <a class="menu-link"
              href="{{ route('customer.dashboard', getParam()) }}">{{ $keywords['Dashboard'] ?? __('Dashboard') }}</a>
          </li>
          <li>
            <a class="menu-link"
              href="{{ route('customer.logout', getParam()) }}">{{ $keywords['Logout'] ?? __('Logout') }}</a>
          </li>
        @endguest
      </ul>
    </li>
  </ul>
</div>
