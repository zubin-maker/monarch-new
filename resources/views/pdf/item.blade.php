@php
  $language = App\Models\User\Language::where([
      ['user_id', $user->id],
      ['code', session()->get('user_lang_' . $user->username)],
  ])->first();
  if (empty($language)) {
      $language = App\Models\User\Language::where([['user_id', $user->id], ['is_default', 1]])->first();
  }
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $language->rtl == 1 ? 'rtl' : '' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ __('Invoice') }}</title>
  <link rel="stylesheet" href="{{ asset('assets/front/css/design-pdf.css') }}">
  @php
    $font_family = 'DejaVu Sans, serif';

    $color = '#333542';
    $rtl = 'rtl';
    $unicode_bidi = 'bidi-override';
    $di_block = 'inline-block';
    $w_60 = '60%';
    $w_10 = '10%';
    $w_30 = '30%';
    $w_80 = '80%';
    $w_20 = '20%';
    $w_45 = '45%';
    if (!is_null(getUserNullCheck())) {
        $keywords = App\Http\Helpers\Common::get_keywords($user->id);
    }
  @endphp
  <style>
    body {
      font-family: {{ $font_family }} !important;
    }

    .rtl {
      text-align: right;
      unicode-bidi: {{ $unicode_bidi }} !important;
      direction: {{ $rtl }} !important;
    }

    span {
      display: {{ $di_block }};
    }

    .w_50 {
      width: {{ $w_60 }} !important;
    }

    .w_10 {
      width: {{ $w_10 }} !important;
    }

    .w_40 {
      width: {{ $w_30 }} !important;
    }

    .w_80 {
      width: {{ $w_80 }};
    }

    .w-20 {
      width: {{ $w_20 }};
    }

    .w_45 {
      width: {{ $w_45 }};
    }

    .invoice-header {
      background: rgba({{ hexToRgba($userBs->base_color) }}, 0.2);
      padding: 10px 25px;
    }
    
    .col-4{
        width:32%;
        float:left;
        position:relative;
        padding:0px 10px;
    }

    .tm_invoice_info_table {
      background: rgba({{ hexToRgba($userBs->base_color) }}, 0.2);
    }

    .package-info-table thead {
      background: #{{ $userBs->base_color }};
    }

    .bg-primary {
      background: #{{ $userBs->base_color }};
    }
  </style>

</head>

<body dir="{{ $language->rtl == 1 ? 'rtl' : '' }}">
  <div class="main">
    <div class="invoice-container">
      <div class="invoice-wrapper">
        <div class="invoice-area pb-30">
          <div class="invoice-header clearfix mb-15 px-25">
            <div class="float-left">
              @if ($userBs->logo)
                <img src="{{ asset('/assets/front/img/user/' . $userBs->logo) }}" height="40"
                  class="d-inline-block ">
              @else
                <img src="{{ asset('assets/admin/img/noimage.jpg') }}" height="40" class="d-inline-block">
              @endif
            </div>
            <div class="text-right strong invoice-heading float-right"
              class="{{ detectTextDirection($keywords['INVOICE'] ?? __('INVOICE')) }}"
              dir="{{ detectTextDirection($keywords['INVOICE'] ?? __('INVOICE')) }}">
              <span style="font-size:20px;" >{{ $keywords['PROFORMA INVOICE'] ?? __('PROFORMA INVOICE') }}</span>
            </div>
          </div>

          <div class="px-25 mb-15 clearfix tm_invoice_info_table">
            <table>
              <tbody>
                <tr>
                  <td>
                    <span class="{{ detectTextDirection($keywords['Payment Method'] ?? __('Payment Method')) }}"
                      dir="{{ detectTextDirection($keywords['Payment Method'] ?? __('Payment Method')) }}">
                      {{ $keywords['Payment Method'] ?? __('Payment Method') }}:
                      {{ $keywords[$order->method] ?? $order->method }}</span>
                  </td>
                  <td>
                    <span class="{{ detectTextDirection($keywords['Invoice No'] ?? __('Invoice No')) }}"
                      dir="{{ detectTextDirection($keywords['Invoice No'] ?? __('Invoice No')) }}">
                      {{ $keywords['Invoice No'] ?? __('P Invoice No') }}: #{{ $order->order_number }}
                    </span>
                  </td>
                  <td class="text-right">
                    <span class="{{ detectTextDirection($keywords['Date'] ?? __('Date')) }}"
                      dir="{{ detectTextDirection($keywords['Date'] ?? __('Date')) }}">
                      {{ $keywords['Date'] ?? __('Date') }}:
                      {{ \Carbon\Carbon::parse($order->created_at)->locale('en')->isoFormat('Do, MMMM YYYY') }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="header clearfix px-25 mb-15">
            <div class="text-left float-left col-4">
              <div class="strong" class="{{ detectTextDirection($keywords['Bill to'] ?? __('Bill to')) }}"
                dir="{{ detectTextDirection($keywords['Bill to'] ?? __('Bill to')) }}">
                {{ $keywords['Bill to'] ?? __('Bill to') }}:</div>
              <div class="small">
                <span class="{{ detectTextDirection($keywords['Name'] ?? __('Name')) }}"
                  dir="{{ detectTextDirection($keywords['Name'] ?? __('Name')) }}">
                  {{ ucfirst($order->billing_fname) }}
                  {{ ucfirst($order->billing_lname) }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($keywords['Address'] ?? __('Address')) }}"
                  dir="{{ detectTextDirection($keywords['Address'] ?? __('Address')) }}">
                  {{ $order->billing_address }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($keywords['City'] ?? __('City')) }}"
                  dir="{{ detectTextDirection($keywords['City'] ?? __('City')) }}">
                  {{ $order->billing_city }},
                  {{ $order->billing_state }}
                </span>
              </div>
              @if (!is_null($order->billing_state))
                <div class="small">
                  <span class="{{ detectTextDirection($keywords['State'] ?? __('State')) }}"
                    dir="{{ detectTextDirection($keywords['State'] ?? __('State')) }}">
                    {{ $order->billing_state }}
                  </span>
                </div>
              @endif

              <div class="small">
                <span class="{{ detectTextDirection($keywords['Country'] ?? __('Country')) }}"
                  dir="{{ detectTextDirection($keywords['Country'] ?? __('Country')) }}">
                  {{ $order->billing_country }}
                </span>
              </div>
              
              <div class="small">
                <span class="{{ detectTextDirection($keywords['Email'] ?? __('Email')) }}"
                  dir="{{ detectTextDirection($keywords['Email'] ?? __('Email')) }}">
                  {{ $order->billing_email }}
                </span>
              </div>
               <div class="small">
                <span class="{{ detectTextDirection($keywords['Phone Number'] ?? __('Phone Number')) }}"
                  dir="{{ detectTextDirection($keywords['Phone Number'] ?? __('Phone Number')) }}">
                  {{ $order->billing_number }}
                </span>
              </div>
            @if (!is_null($order->billing_company))
               <div class="small">
                <span class="{{ detectTextDirection($keywords['Company'] ?? __('Company')) }}"
                  dir="{{ detectTextDirection($keywords['Company'] ?? __('Company')) }}">{{ $keywords['Company'] ?? __('Company') }}:
                  {{ $order->billing_company }}
                </span>
              </div>
               @endif
               @if (!is_null($order->billing_gst))
               <div class="small">
                <span class="{{ detectTextDirection($keywords['GST Number'] ?? __('GST Number')) }}"
                  dir="{{ detectTextDirection($keywords['GST Number'] ?? __('GST Number')) }}">{{ $keywords['GST Number'] ?? __('GST Number') }}:
                  {{ $order->billing_gst }}
                </span>
              </div>
                @endif
            </div>
              <div class="text-left float-left col-4">
              <div class="strong" class="{{ detectTextDirection($keywords['Ship to'] ?? __('Ship to')) }}"
                dir="{{ detectTextDirection($keywords['Ship to'] ?? __('Ship to')) }}">
                {{ $keywords['Ship to'] ?? __('Ship to') }}:</div>
              <div class="small">
                <span class="{{ detectTextDirection($keywords['Name'] ?? __('Name')) }}"
                  dir="{{ detectTextDirection($keywords['Name'] ?? __('Name')) }}">
                  {{ ucfirst($order->shipping_fname) }}
                  {{ ucfirst($order->shipping_lname) }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($keywords['Address'] ?? __('Address')) }}"
                  dir="{{ detectTextDirection($keywords['Address'] ?? __('Address')) }}">
                  {{ $order->shipping_address }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($keywords['City'] ?? __('City')) }}"
                  dir="{{ detectTextDirection($keywords['City'] ?? __('City')) }}">
                  {{ $order->shipping_city }},
                  {{ $order->shipping_state }}
                </span>
              </div>
              @if (!is_null($order->shipping_state))
                <div class="small">
                  <span class="{{ detectTextDirection($keywords['State'] ?? __('State')) }}"
                    dir="{{ detectTextDirection($keywords['State'] ?? __('State')) }}">
                    {{ $order->shipping_state }}
                  </span>
                </div>
              @endif

              <div class="small">
                <span class="{{ detectTextDirection($keywords['Country'] ?? __('Country')) }}"
                  dir="{{ detectTextDirection($keywords['Country'] ?? __('Country')) }}">
                  {{ $order->shipping_country }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($keywords['Email'] ?? __('Email')) }}"
                  dir="{{ detectTextDirection($keywords['Email'] ?? __('Email')) }}">
                  {{ $order->shipping_email }}
                </span>
              </div>
              <div class="small">
                <span class="{{ detectTextDirection($keywords['Phone Number'] ?? __('Phone Number')) }}"
                  dir="{{ detectTextDirection($keywords['Phone Number'] ?? __('Phone Number')) }}">
                  {{ $order->shipping_number }}
                </span>
              </div>
                @if (!is_null($order->shipping_company))
               <div class="small">
                <span class="{{ detectTextDirection($keywords['Company'] ?? __('Company')) }}"
                  dir="{{ detectTextDirection($keywords['Company'] ?? __('Company')) }}">{{ $keywords['Company'] ?? __('Company') }}:
                  {{ $order->shipping_company }}
                </span>
              </div>
               @endif
               @if (!is_null($order->shipping_gst))
               <div class="small">
                <span class="{{ detectTextDirection($keywords['GST Number'] ?? __('GST Number')) }}"
                  dir="{{ detectTextDirection($keywords['GST Number'] ?? __('GST Number')) }}">{{ $keywords['GST Number'] ?? __('GST Number') }}:
                  {{ $order->shipping_gst }}
                </span>
              </div>
                @endif
            </div>
            <div class="order-details float-right col-4">
              <div class="text-right">
                <div class="strong"
                  class="{{ detectTextDirection($keywords['Order Details'] ?? __('Order Details')) }}"
                  dir="{{ detectTextDirection($keywords['Order Details'] ?? __('Order Details')) }}">
                  {{ $keywords['Order Details'] ?? __('Order Details') }}:</div>
                @if (!is_null($order->discount))
                  <div class="small">
                    <span class="{{ detectTextDirection($keywords['Discount'] ?? __('Discount')) }}"
                      dir="{{ detectTextDirection($keywords['Discount'] ?? __('Discount')) }}">{{ $keywords['Discount'] ?? __('Discount') }}:
                      {{ currencyTextPrice($order->currency_id, $order->discount) }}
                    </span>
                  </div>
                @endif

                <!--<div class="small">-->
                <!--  <span class="{{ detectTextDirection($keywords['Tax'] ?? __('Tax')) }}"-->
                <!--    dir="{{ detectTextDirection($keywords['Tax'] ?? __('Tax')) }}">{{ $keywords['Tax'] ?? __('Tax') }}:-->
                <!--    {{ currencyTextPrice($order->currency_id, $order->tax) }}-->
                <!--  </span>-->
                <!--</div>-->

                <div class="small"><span
                    class="{{ detectTextDirection($keywords['Paid Amount'] ?? __('Paid Amount')) }}"
                    dir="{{ detectTextDirection($keywords['Paid Amount'] ?? __('Paid Amount')) }}">{{ $keywords['Paid Amount'] ?? __('Paid Amount') }}:
                    {{ currencyTextPrice($order->currency_id, $order->total) }}
                  </span>
                </div>

                <div class="small">
                  <span class="{{ detectTextDirection($keywords['Payment Status'] ?? __('Payment Status')) }}"
                    dir="{{ detectTextDirection($keywords['Payment Status'] ?? __('Payment Status')) }}">{{ $keywords['Payment Status'] ?? __('Payment Status') }}:
                    {{ $keywords[$order->payment_status] ?? $order->payment_status }}
                  </span>
                </div>

                <div class="small">
                  <span class="{{ detectTextDirection($keywords['Order Status'] ?? __('Order Status')) }}"
                    dir="{{ detectTextDirection($keywords['Order Status'] ?? __('Order Status')) }}">{{ $keywords['Order Status'] ?? __('Order Status') }}:
                    {{ $keywords[ucfirst($order->order_status)] ?? ucfirst($order->order_status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="package-info px-25">
            <table class="text-left package-info-table">
              <thead>
                <tr>
                  <td class="text-center text-white small">
                    <strong class="text-white" class="{{ detectTextDirection($keywords['Title'] ?? __('Title')) }}"
                      dir="{{ detectTextDirection($keywords['Title'] ?? __('Title')) }}">{{ $keywords['Title'] ?? __('Title') }}</strong>
                  </td>
                  <td class="tm_border_left text-center text-white small">
                    <strong class="text-white"
                      class="{{ detectTextDirection($keywords['Quantity'] ?? __('Quantity')) }}"
                      dir="{{ detectTextDirection($keywords['Quantity'] ?? __('Quantity')) }}">
                      {{ $keywords['Quantity'] ?? __('Quantity') }}</strong>
                  </td>
                  <td class="tm_border_left text-center small">
                    <strong class="text-white" class="{{ detectTextDirection($keywords['Price'] ?? __('Price')) }}"
                      dir="{{ detectTextDirection($keywords['Price'] ?? __('Price')) }}">
                      {{ $keywords['Price'] ?? __('Price') }}</strong>
                  </td>
                </tr>
              </thead>
              <tbody>
                @foreach ($order->orderitems as $item)
                  <tr>
                    <td>
                      <p class="{{ detectTextDirection($item->title) }}"
                        dir="{{ detectTextDirection($item->title) }}">{{ $item->title }}</p>
                    </td>
                    <td class="tm_border_left text-center">{{ $item->qty }}</td>
                    <td class="tm_border_left text-right">
                      {{ currencyTextPrice($order->currency_id, round($item->price, 2)) }}
                      <br>
                      @php
                        $variations = json_decode($item->variations);
                      @endphp
                      @if (!is_null($variations))
                        @foreach ($variations as $k => $vitm)
                          @php
                            $name = isset($vitm->name) ? $vitm->name : '';
                            $key = is_string($k) ? $k : '';
                          @endphp
                          <span class="{{ detectTextDirection($name) }}" dir="{{ detectTextDirection($name) }}">
                            {{ $name }} <small class="{{ detectTextDirection($key) }}"
                              dir="{{ detectTextDirection($key) }}">({{ $key }})</small>
                              <?php if($vitm->price > 0){ ?>
                            <span class="ltr"
                              dir="ltr">{{ currencyTextPrice($order->currency_id, round($vitm->price, 2)) }}</span>
                          </span>
                          <?php } ?>
                        @endforeach
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="tm_invoice_footer clearfix px-25">
            <div class="tm_right_footer float-right">
              <table>
                <tbody>
                  <tr>
                    <td class="fw-bold"><span
                        class="{{ detectTextDirection($keywords['Subtotal'] ?? __('Subtotal')) }}"
                        dir="{{ detectTextDirection($keywords['Subtotal'] ?? __('Subtotal')) }}">{{ $keywords['Subtotal'] ?? __('Subtotal') }}</span>
                    </td>
                    <td class="text-right fw-bold"> {{ currencyTextPrice($order->currency_id, $order->cart_total) }}
                    </td>
                    
                    
                   
                    
                    
                    
                  </tr>
                  <!--<tr>-->
                  <td class="fw-bold"><span
                        class="{{ detectTextDirection($keywords['Discount'] ?? __('Discount')) }}"
                        dir="{{ detectTextDirection($keywords['Discount'] ?? __('Discount')) }}"> {{ $keywords['Discount'] ?? __('Discount') }}</span>
                    </td>
                    <td class="text-right fw-bold"> {{ currencyTextPrice($order->currency_id, $order->discount) }}
                    </td>
                  <tr>
                    <td class="fw-bold">
                      <span class="{{ detectTextDirection($keywords['Shipping Charge'] ?? __('Shipping Charge')) }}"
                        dir="{{ detectTextDirection($keywords['Shipping Charge'] ?? __('Shipping Charge')) }}">{{ $keywords['Shipping Charge'] ?? __('Shipping Charge') }}</span>
                    </td>
                    
                    <td class="text-right fw-bold">
                      Flat Rate<!--{{ currencyTextPrice($order->currency_id, $order->shipping_charge) }}-->
                    </td>
                    
                  </tr>
                  <tr class="bg-primary paid-tr">
                    <td class="fw-bold ">
                      <span
                        class="text-white {{ detectTextDirection($keywords['Total'] ?? __('Total')) }}"
                        dir="{{ detectTextDirection($keywords['Total'] ?? __('Total')) }}">
                        {{ $keywords['Total'] ?? __('Total') }}
                      </span>
                    </td>
                    <td class="text-right fw-bold text-white">
                      {{ currencyTextPrice($order->currency_id, $order->total) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="mt-50">
            <div class="text-right regards"
              class="{{ detectTextDirection($keywords['Thanks & Regards'] ?? __('Thanks & Regards')) }}"
              dir="{{ detectTextDirection($keywords['Thanks & Regards'] ?? __('Thanks & Regards')) }}">
              {{ $keywords['Thanks & Regards'] ?? __('Thanks & Regards') }},</div>
            <div class="text-right strong regards">
              @php
                $website_title = $user->shop_name ?? $user->username;
              @endphp
              <span class="{{ detectTextDirection($website_title) }}"
                dir="{{ detectTextDirection($website_title) }}">{{ $website_title }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>
