@php
  if (session()->has('lang')) {
      $language = App\Models\Language::where('code', session()->get('lang'))->first();
  } else {
      $language = App\Models\Language::where('is_default', 1)->first();
  }
  $language_keywords = file_get_contents(resource_path() . '/lang/' . $language->code . '.json');
  $language_keywords = json_decode($language_keywords, true);
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $language->rtl == 1 ? 'rtl' : '' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $language_keywords['INVOICE'] ?? __('INVOICE') }}</title>
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
      display: {{ $di_block }}
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
      background: rgba({{ hexToRgba($bs->base_color) }}, 0.2);
      padding: 20px 25px;
    }

    .tm_invoice_info_table {
      background: rgba({{ hexToRgba($bs->base_color) }}, 0.2);
    }

    .package-info-table thead {
      background: #{{ $bs->base_color }};
    }

    .bg-primary {
      background: #{{ $bs->base_color }};
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
              @if ($bs->logo)
                <img src="{{ asset('assets/front/img/' . $bs->logo) }}" height="40" class="d-inline-block ">
              @else
                <img src="{{ asset('assets/admin/img/noimage.jpg') }}" height="40" class="d-inline-block">
              @endif
            </div>
            <div
              class="text-right strong invoice-heading float-right {{ detectTextDirection($language_keywords['INVOICE'] ?? __('INVOICE')) }}"
              dir="{{ detectTextDirection($language_keywords['INVOICE'] ?? __('INVOICE')) }}">
              {{ $language_keywords['INVOICE'] ?? __('INVOICE') }}</div>

          </div>

          <div class="px-25 mb-15 clearfix tm_invoice_info_table">
            <table class=" ">
              <tbody>
                <tr>
                  <td>
                    <span
                      class="{{ detectTextDirection($language_keywords['Payment Method'] ?? __('Payment Method')) }}"
                      dir="{{ detectTextDirection($language_keywords['Payment Method'] ?? __('Payment Method')) }}">
                      {{ $language_keywords['Payment Method'] ?? __('Payment Method') }}:
                      {{ $request['payment_method'] }}</span>
                  </td>
                  <td>
                    <span class="{{ detectTextDirection($language_keywords['Order No'] ?? __('Order No')) }}"
                      dir="{{ detectTextDirection($language_keywords['Order No'] ?? __('Order No')) }}">
                      {{ $language_keywords['Order No'] ?? __('Order No') }}: #{{ $order_id }}</span>
                  </td>
                  <td class="text-right">
                    <span class="{{ detectTextDirection($language_keywords['Date'] ?? __('Date')) }}"
                      dir="{{ detectTextDirection($language_keywords['Date'] ?? __('Date')) }}">
                      {{ $language_keywords['Date'] ?? __('Date') }}:
                      {{ \Carbon\Carbon::now()->locale('en')->isoFormat('Do, MMMM YYYY') }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="header clearfix px-25 mb-15">
            <div class="text-left  float-left">
              <div class="strong" class="{{ detectTextDirection($language_keywords['Bill to'] ?? __('Bill to')) }}"
                dir="{{ detectTextDirection($language_keywords['Bill to'] ?? __('Bill to')) }}">
                {{ $language_keywords['Bill to'] ?? __('Bill to') }}:</div>
              <div class="small">
                <span style="" class="{{ detectTextDirection($language_keywords['Name'] ?? __('Name')) }}"
                  dir="{{ detectTextDirection($language_keywords['Name'] ?? __('Name')) }}">{{ $language_keywords['Name'] ?? __('Name') }}:
                  {{ $member['shop_name'] }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($language_keywords['Username'] ?? __('Username')) }}"
                  dir="{{ detectTextDirection($language_keywords['Username'] ?? __('Username')) }}">{{ $language_keywords['Username'] ?? __('Username') }}:
                  {{ $member['username'] }}
                </span>
              </div>

              <div class="small">
                <span class="{{ detectTextDirection($language_keywords['Email'] ?? __('Email')) }}"
                  dir="{{ detectTextDirection($language_keywords['Email'] ?? __('Email')) }}">{{ $language_keywords['Email'] ?? __('Email') }}:
                  {{ $member['email'] }}
                </span>
              </div>

              @if ($phone)
                <div class="small">
                  <span class="{{ detectTextDirection($language_keywords['Phone'] ?? __('Phone')) }}"
                    dir="{{ detectTextDirection($language_keywords['Phone'] ?? __('Phone')) }}">{{ $language_keywords['Phone'] ?? __('Phone') }}:
                    {{ $phone }}
                  </span>
                </div>
              @endif

            </div>
            <div class="order-details float-right">
              <div class="text-right">

                <div class="strong"
                  class="{{ detectTextDirection($language_keywords['Order Details'] ?? __('Order Details')) }}"
                  dir="{{ detectTextDirection($language_keywords['Order Details'] ?? __('Order Details')) }}">
                  {{ $language_keywords['Order Details'] ?? __('Order Details') }}:</div>

                <div class="small">
                  <span class="{{ detectTextDirection($language_keywords['Order Id'] ?? __('Order Id')) }}"
                    dir="{{ detectTextDirection($language_keywords['Order Id'] ?? __('Order Id')) }}">{{ $language_keywords['Order Id'] ?? __('Order Id') }}:
                    #{{ $order_id }}
                  </span>
                </div>

                <div class=" small">
                  <span class="{{ detectTextDirection($language_keywords['Order Price'] ?? __('Order Price')) }}"
                    dir="{{ detectTextDirection($language_keywords['Order Price'] ?? __('Order Price')) }}">{{ $language_keywords['Order Price'] ?? __('Order Price') }}:
                    {{ $amount == 0 ? $language_keywords['Free'] ?? __('Free') : textPrice($base_currency_text_position, $base_currency_text, $amount) }}
                  </span>
                </div>

                <div class="small">
                  <span class="{{ detectTextDirection($language_keywords['Payment Status'] ?? __('Payment Status')) }}"
                    dir="{{ detectTextDirection($language_keywords['Payment Status'] ?? __('Payment Status')) }}">{{ $language_keywords['Payment Status'] ?? __('Payment Status') }}:
                    {{ $status == 2 ? $language_keywords['Rejected'] ?? __('Rejected') : $language_keywords['Completed'] ?? __('Completed') }}
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
                    <span class="{{ detectTextDirection($language_keywords['Package Title'] ?? __('Package Title')) }}"
                      dir="{{ detectTextDirection($language_keywords['Package Title'] ?? __('Package Title')) }}">{{ $language_keywords['Package Title'] ?? __('Package Title') }}</span>
                  </td>
                  <td class="tm_border_left text-white small text-center">
                    <span class="{{ detectTextDirection($language_keywords['Start Date'] ?? __('Start Date')) }}"
                      dir="{{ detectTextDirection($language_keywords['Start Date'] ?? __('Start Date')) }}">{{ $language_keywords['Start Date'] ?? __('Start Date') }}</span>

                  </td>
                  <td class="tm_border_left text-white small text-center">
                    <span class="{{ detectTextDirection($language_keywords['Expire Date'] ?? __('Expire Date')) }}"
                      dir="{{ detectTextDirection($language_keywords['Expire Date'] ?? __('Expire Date')) }}">{{ $language_keywords['Expire Date'] ?? __('Expire Date') }}</span>
                  </td>
                  <td class="tm_border_left text-white small text-center">
                    <span class="{{ detectTextDirection($language_keywords['Currency'] ?? __('Currency')) }}"
                      dir="{{ detectTextDirection($language_keywords['Currency'] ?? __('Currency')) }}">{{ $language_keywords['Currency'] ?? __('Currency') }}</span>
                  </td>
                  <td class="tm_border_left text-white small text-center">
                    <span class="{{ detectTextDirection($language_keywords['Price'] ?? __('Price')) }}"
                      dir="{{ detectTextDirection($language_keywords['Price'] ?? __('Price')) }}">{{ $language_keywords['Price'] ?? __('Price') }}</span>
                  </td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <span class="{{ detectTextDirection($package_title) }}"
                      dir="{{ detectTextDirection($package_title) }}">{{ $package_title }}</span>
                  </td>

                  <td class="tm_border_left text-center">{{ $request['start_date'] }}</td>

                  <td class="tm_border_left text-center">
                    @if (\Carbon\Carbon::parse($request['expire_date'])->format('Y') == '9999')
                      <span class="{{ detectTextDirection($language_keywords['Lifetime'] ?? __('Lifetime')) }}"
                        dir="{{ detectTextDirection($language_keywords['Lifetime'] ?? __('Lifetime')) }}">{{ $language_keywords['Lifetime'] ?? __('Lifetime') }}</span>
                    @else
                      {{ $request['expire_date'] }}
                    @endif
                  </td>

                  <td class="tm_border_left text-center">{{ $base_currency_text }}</td>
                  <td class="tm_border_left text-center">
                    @if ($amount == 0)
                      <span class="{{ detectTextDirection($language_keywords['Free'] ?? __('Free')) }}"
                        dir="{{ detectTextDirection($language_keywords['Free'] ?? __('Free')) }}">{{ $language_keywords['Free'] ?? __('Free') }}</span>
                    @else
                      {{ textPrice($base_currency_text_position, $base_currency_text, $amount) }}
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-50">
            <div class="text-right regards"
              class="{{ detectTextDirection($language_keywords['Thanks & Regards'] ?? __('Thanks & Regards')) }}"
              dir="{{ detectTextDirection($language_keywords['Thanks & Regards'] ?? __('Thanks & Regards')) }}">
              {{ $language_keywords['Thanks & Regards'] ?? __('Thanks & Regards') }},
            </div>
            <div class="text-right strong regards">
              <span class="{{ detectTextDirection($bs->website_title) }}"
                dir="{{ detectTextDirection($bs->website_title) }}">{{ $bs->website_title }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>

</html>
