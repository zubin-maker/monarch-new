@php
  $cart = Session::get('cart_' . $user->username);
  $user_id = getUser()->id ?? null;
  if (!is_null($cart) && is_array($cart) && $user_id) {
      $cart = array_filter($cart, function ($item) use ($user_id) {
          return $item['user_id'] == $user_id;
      });
  }
  $cartCount = is_array($cart) ? count($cart) : 0;
  $checkoutRoute = auth()->guard('customer')->check()
      ? route('front.user.checkout.final_step', getParam())
      : route('front.user.checkout.guest', getParam());
@endphp

<div class="mini-cart-layout">
  <div>
    <div class="cart-dropdown-header-bar">
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="cart-dropdown-close" aria-label="Close">&larr;</button>
        <h5 class="mb-0">
          {{ $keywords['My Cart'] ?? __('My Cart') }}
          @if ($cartCount > 0)
            ({{ $cartCount }})
          @endif
        </h5>
      </div>
    </div>

    @if (!empty($cart) && $cartCount > 0)
      <div class="mini-cart-items-wrapper">
        <ul class="cart-dropdown-list list-unstyled mb-0">
          @foreach ($cart as $key => $item)
            @php
              $id = $item['id'];
              $product = App\Models\User\UserItem::where('id', $item['id'])->first();
              if ($product) {
                  $content = $product
                      ->itemContents()
                      ->where('language_id', $userCurrentLang->id)
                      ->select('title', 'slug')
                      ->first();
              }
            @endphp
            @if ($product)
              <li class="cart-dropdown-list-item mb-3">
                <div class="d-flex">
                  <div class="cart-img radius-md me-3">
                    <a target="_blank"
                      href="{{ route('front.user.productDetails', [getParam(), 'slug' => $content->slug]) }}"><img
                        class="lazyload" alt="Nest" src="{{ asset('assets/front/images/placeholder.png') }}"
                        data-src="{{ asset('assets/front/img/user/items/thumbnail/' . $product->thumbnail) }}"></a>
                  </div>
                  <div class="cart-title flex-grow-1">
                    <h5 class="lc-1 product-title mb-1"><a target="_blank"
                        href="{{ route('front.user.productDetails', [getParam(), 'slug' => $content->slug]) }}">{{ convertUtf8($content->title) }}</a>
                    </h5>
                    <span class="mb-1 d-inline-block">
                      <span class="fw-medium me-1">{{ $keywords['Item Price'] ?? __('Item Price') }}:</span>
                      <span class="m-0">
                        {{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, $item['product_price']) }}
                      </span>
                    </span>
                    <br>
                    <span class="mb-1 d-inline-block">
                      <span class="fw-medium me-1">{{ $keywords['Quantity'] ?? __('Quantity') }}:</span>
                      <span class="m-0">
                        {{ $item['qty'] }}
                      </span>
                    </span>

                    @php
                      $variation_total = 0;
                    @endphp
                    @if (!empty($item['variations']))
                      @foreach ($item['variations'] as $k => $itm)
                        @php
                          $variation_total = $variation_total + $itm['price'];

                          //show variations name
                          $vNameId = App\Models\User\ProductVariationContent::where(
                              'product_variation_id',
                              $itm['variation_id'],
                          )
                              ->pluck('variation_name')
                              ->first();

                          $variant_id = App\Models\VariantContent::where('id', $vNameId)->pluck('variant_id')->first();
                          $variation_name = App\Models\VariantContent::where([
                              ['variant_id', $variant_id],
                              ['language_id', $userCurrentLang->id],
                          ])
                              ->pluck('name')
                              ->first();

                          //show variation options name
                          $vOptionId = App\Models\User\ProductVariantOptionContent::where([
                              ['language_id', $userCurrentLang->id],
                              ['product_variant_option_id', $itm['option_id']],
                          ])
                              ->pluck('option_name')
                              ->first();
                          $vOptionName = App\Models\VariantOptionContent::where([
                              ['language_id', $userCurrentLang->id],
                              ['id', $vOptionId],
                          ])
                              ->pluck('option_name')
                              ->first();
                        @endphp
                        <table class="variation-table mb-1">
                          <tr>
                            <td class="">
                              <span class="fw-medium me-1">{{ $variation_name }}:</span>
                            </td>
                            <td class="cart_variants_price">
                              <small>{{ $vOptionName }}</small>
                              -(<i
                                class="fas fa-plus"></i>{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, $itm['price']) }})
                            </td>
                          </tr>
                        </table>
                      @endforeach
                    @endif
                  </div>
                  <div class="cart-delete ms-2">
                    <span class="fas fa-times cursor-pointer item-remove btn-remove btn-icon" rel="{{ $id }}"
                      data-href="{{ route('front.cart.item.remove', ['uid' => $key, getParam()]) }}"></span>
                  </div>
                </div>
              </li>
            @endif
          @endforeach
        </ul>
      </div>

      <div class="mini-cart-order-summary">
        <h6 class="mb-3">{{ $keywords['Order Summary'] ?? __('Order Summary') }}</h6>
        <div class="mini-cart-order-summary-row">
          <span>{{ $keywords['Subtotal'] ?? __('Subtotal') }}</span>
          <span>{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, cartTotal()) }}</span>
        </div>
        <div class="mini-cart-order-summary-row">
          <span>{{ $keywords['Shipping'] ?? __('Shipping') }}</span>
          <span>{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, 0) }}</span>
        </div>
        <div class="mini-cart-order-summary-row est-total">
          <span>{{ $keywords['Est. Total'] ?? __('EST. TOTAL') }}</span>
          <span>{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, cartTotal()) }}</span>
        </div>
      </div>

      <div class="mini-cart-coupons-row">
        <div class="mini-cart-coupons-label">
          <span class="fas fa-tag"></span>
          <span>{{ $keywords['Coupons'] ?? __('Coupons') }}</span>
        </div>
        <span class="text-primary small">{{ $keywords['Apply'] ?? __('Apply') }}</span>
      </div>
    @else
      <div class="p-4 text-center">
        <h5 class="mb-0">{{ $keywords['Your Cart is Empty'] ?? __('Your Cart is Empty') }}</h5>
      </div>
    @endif
  </div>

  @if (!empty($cart) && $cartCount > 0)
    <div class="mini-cart-bottom-bar">
      <div class="mini-cart-bottom-main">
        <div>
          <div class="mini-cart-bottom-main-total">
            {{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, cartTotal()) }}
          </div>
          <div class="mini-cart-bottom-main-caption">
            {{ $keywords['Est. Total (Inc. Taxes if any)'] ?? __('Est. Total (Incl. taxes if any)') }}
          </div>
        </div>
        {{-- Placeholder: savings text can be wired later if you track MRP vs sale price --}}
      </div>
      <a href="{{ $checkoutRoute }}" class="mini-cart-cta-btn">
        {{ $keywords['Proceed to Pay'] ?? __('Proceed To Pay') }}
      </a>
    </div>
  @endif
</div>
