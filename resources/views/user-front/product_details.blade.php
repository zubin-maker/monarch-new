@extends('user-front.layout')

@section('page-title', $product->title ?? ($keywords['Product_Details'] ?? __('Product Details')))
@section('breadcrumb_title', $product->title ?? ($keywords['Product_Details'] ?? __('Product Details')))
@section('breadcrumb_second_title', $keywords['Product_Details'] ?? __('Product Details'))
@section('meta-description')
    {{ $product->meta_description ?? 'Default description' }}
@endsection

@section('meta-keywords')
    {{ $product->meta_keywords ?? 'default, keywords' }}
@endsection

@section('meta-title')
    {{ $product->meta_title ?? $product->title ?? __('Product Details') }}
@endsection

@section('og-meta')
    <meta property="og:title" content="{{ $product->title . ' | ' . $user->username }}">
    <meta property="og:description" content="{{ $product->summary }}">
    <meta property="og:image" content="{{ asset('assets/front/img/user/items/thumbnail/' . $product->item->thumbnail) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
    <meta name="twitter:card" content="summary_large_image">
    <style>
        .btns_3d {
            position: absolute;
            top: 2px;
            right: 40px;
            z-index: 9999;
            background-color: #ffffff;
            padding: 3px 10px;
            border-radius: 5px;
            display: flex;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btns_3d button {
            color: #000;
            border: none;
            background: transparent;
            padding: 8px 12px;
            margin: 0 5px;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #101010;
        }
        .btns_3d button:hover {
            background: #00c8fa;
            color: #fff;
            transform: scale(1.1);
        }

        /* Out of stock variants */
        .out-of-stock-label {
            opacity: 0.5;
            cursor: not-allowed !important;
            position: relative;
            pointer-events: none;
        }
        .out-of-stock-label .variant-swatch-text {
            color: red !important;
        }
        .out-of-stock-label::after {
            content: "Out of stock";
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: #dc3545;
            white-space: nowrap;
        }

        /* Variant swatches */
        .variant-swatch {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        .variant-swatch-text {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: all 0.2s;
            color: #000;
            font-weight: 600;
        }
        .variant-swatch-label input:checked + .variant-swatch .variant-swatch-text {
            border-color: #00c8fa !important;
            background-color: #00c8fa;
            color: #fff;
            box-shadow: 0 0 8px rgba(0,200,250,0.5);
        }

        /* Active thumbnail highlight */
        .slider-thumbnails2 .thumbnail-img.slick-current {
            border: 3px solid #00c8fa;
            box-shadow: 0 0 12px rgba(0,200,250,0.6);
        }
        .slider-thumbnails2 .thumbnail-img {
            cursor: pointer;
            transition: all 0.3s;
        }
        .slider-thumbnails2 .thumbnail-img:hover {
            opacity: 0.9;
        }
        
        .page-title-area{
            display:none !important;
        }
    </style>
@endsection

@section('content')
<div class="product-single pt-100 pb-70 overflow-hidden">
    <div class="container">
        <div class="product-single-default">
            <div class="row">
                <div class="col-lg-6 position-relative">
                    <div class="btns_3d">
                        @isset($product->item->three_d_image)
                            <button data-type="popup" title="View 3D Model" class="featured_3d_video-button" data-video="{{ $product->item->three_d_image }}">
                                3D
                            </button>
                        @endisset
                        @isset($product->item->you_tube)
                            <button data-type="popup" title="View Video" class="fa fa-video-camera" data-video="{{ $product->item->you_tube }}">
                                <i class="fa-solid fa-video"></i>
                            </button>
                        @endisset
                    </div>

                    @if ($product->item->sliders)
                        <input type="hidden" id="details_item_id" value="{{ $product->item->id }}">
                        <div class="d-flex gap-4">
                        <!-- Thumbnails (Vertical) -->
                        <div class="slider-thumbnails2">
                            @foreach ($product->item->sliders as $slide)
                                <div class="thumbnail-img radius-md lazy-container ratio ratio-1-1">
                                    <img class="lazyload" 
                                         src="{{ asset('assets/front/images/placeholder.png') }}"
                                         data-src="{{ asset('assets/front/img/user/items/slider-images/' . $slide->image) }}"
                                         alt="thumbnail" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Main Slider -->
                        <div class="product-single-slider2">
                            @foreach ($product->item->sliders as $slide)
                                <div class="product-single-single-item">
                                    <figure class="radius-lg lazy-container ratio ratio-1-1">
                                        <a href="{{ asset('assets/front/img/user/items/slider-images/' . $slide->image) }}" class="zoom">
                                            <img class="lazyload main-thumbnail"
                                                 src="{{ asset('assets/front/images/placeholder.png') }}"
                                                 data-src="{{ asset('assets/front/img/user/items/slider-images/' . $slide->image) }}"
                                                 data-zoom-image="{{ asset('assets/front/img/user/items/slider-images/' . $slide->image) }}"
                                                 alt="product image" />
                                        </a>
                                    </figure>
                                </div>
                            @endforeach
                        </div>
                        </div>
                    @endif
                </div>

                <!-- Product Details Column -->
                <div class="col-lg-6">
                    <div class="product-single-details">
                        <!-- Title & Label -->
                        @php
                            $item_label = DB::table('labels')->where('id', $product->label_id)->first();
                        @endphp
                        <h2 class="product-title align-items-start">
                            {{ $product->title }}
                            <!--@if($item_label)-->
                            <!--    <span class="label label-2" style="background-color: #{{ $item_label->color }}">{{ $item_label->name }}</span>-->
                            <!--@endif-->
                        </h2>

                        <!-- Rating & Stock -->
                        <div class="d-flex align-items-center gap-10 mb-2">
                            @php $avgreview = \App\Models\User\UserItemReview::where('item_id', $product->item->id)->avg('review'); @endphp
                            @if ($shop_settings->item_rating_system == 1)
                                <div class="rating-wrapper d-flex gap-2 align-items-center">
                                    <div class="rate-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none">
                                        <path d="M12 0.5L14.6942 8.7918H23.4127L16.3593 13.9164L19.0534 22.2082L12 17.0836L4.94658 22.2082L7.64074 13.9164L0.587322 8.7918H9.30583L12 0.5Z" fill="#EEAE0B"/>
                                    </svg></div>
                                    @if(trim(strtoupper($product->title)) == 'NEXA MEDIUM BACK CHAIR')
                                    <span>{{ number_format($avgreview, 1) }} ({{ reviewCount($product->item->id)+8000 }} {{ Str::plural('Review', reviewCount($product->item->id)) }})</span>
                                       
                                    @else
                                  <span>{{ number_format($avgreview, 1) }} ({{ reviewCount($product->item->id) }} {{ Str::plural('Review', reviewCount($product->item->id)) }})</span>
                                
                                   @endif
                                </div>
                            @endif

                            <div class="stock-status">
                                @php $varitaion_stock = VariationStock($product->item->id); @endphp
                                @if ($varitaion_stock['has_variation'] == 'yes')
                                    @if ($varitaion_stock['stock'] == 'yes')
                                        <span class="badge bg-success"><i class="fa fa-check"></i> {{ __('In Stock') }}</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fa fa-times"></i> {{ __('Out of Stock') }}</span>
                                    @endif
                                @else
                                <?php echo $product->item->stock > 0
    ? '<span class="badge bg-success"><i class="fa fa-check"></i> ' . __('In Stock') . '</span>'
    : '<span class="badge bg-danger"><i class="fa fa-times"></i> ' . __('Out of Stock') . '</span>';
?>
                                @endif
                            </div>
                        </div>

                        <!-- Price -->
                          @php
                                $flash_info = flashAmountStatus($product->item_id, $product->item->current_price);
                                $product_current_price = $flash_info['amount'];
                                $flash_status = $flash_info['status'];
                            @endphp

                            <div class="product-price">
                                @if ($flash_status == true)
                                    <div class="new-price-area d-flex color-primary">
                                        <span class="new-price-currency">{{ $userCurrentCurr->symbol_position == 'left' ? $userCurrentCurr->symbol : '' }}</span>
                                        <span class="new-price" id="details_new-price" data-base_price="{{ currency_converter($product_current_price) }}">{{ currency_converter($product_current_price) }}</span>
                                        {{ $userCurrentCurr->symbol_position == 'right' ? $userCurrentCurr->symbol : '' }}
                                    </div>
                                    <div class="old-price-area d-flex">
                                        {{ $userCurrentCurr->symbol_position == 'left' ? $userCurrentCurr->symbol : '' }}
                                        <span class="old-price" id="details_old-price" data-old_price="{{ currency_converter($product->item->current_price) }}">
                                            {{ currency_converter($product->item->current_price) }}
                                        </span>
                                        {{ $userCurrentCurr->symbol_position == 'right' ? $userCurrentCurr->symbol : '' }}
                                    </div>
                                    <span class="discountoff">{{ $product->item->flash_amount }}% {{ $keywords['OFF'] ?? __('OFF') }}</span>
                                @else
                                    <div class="new-price-area d-flex color-primary ">
                                        {{ $userCurrentCurr->symbol_position == 'left' ? $userCurrentCurr->symbol : '' }}
                                        <span class="new-price" id="details_new-price" data-base_price="{{ currency_converter($product->item->current_price) }}">{{ currency_converter($product->item->current_price) }}</span>
                                        {{ $userCurrentCurr->symbol_position == 'right' ? $userCurrentCurr->symbol : '' }}
                                    </div>
                                    <div class="old-price-area d-flex">
                                        @if ($product->item->previous_price > 0)
                                            {{ $userCurrentCurr->symbol_position == 'left' ? $userCurrentCurr->symbol : '' }}
                                            <span class="old-price" id="details_old-price" data-old_price="{{ currency_converter($product->item->previous_price) }}">
                                                {{ currency_converter($product->item->previous_price) }}
                                            </span>
                                            {{ $userCurrentCurr->symbol_position == 'right' ? $userCurrentCurr->symbol : '' }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                        <p class="product-text mb-3">{{ $product->summary }}</p>

                        @if ($product->item->sku)
                            <div class="sku-code mb-3">
                                <span class="text-dark fw-semibold">{{ __('SKU') }} :</span>
                                <span class="text-dark">{{ $product->item->sku }}</span>
                            </div>
                        @endif

                        <!-- Flash Sale Countdown -->
                        @if ($flash_status)
                            <div class="product-countdown mt-3" data-end_time="{{ $product->item->end_time }}" data-end_date="{{ $product->item->end_date }}" data-item_id="{{ $product->id }}">
                                <div class="count radius-sm days"><span class="count-value_{{ $product->id }}"></span><span class="count-period">{{ __('Days') }}</span></div>
                                <div class="count radius-sm hours"><span class="count-value_{{ $product->id }}"></span><span class="count-period">{{ __('Hours') }}</span></div>
                                <div class="count radius-sm minutes"><span class="count-value_{{ $product->id }}"></span><span class="count-period">{{ __('Mins') }}</span></div>
                                <div class="count radius-sm seconds"><span class="count-value_{{ $product->id }}"></span><span class="count-period">{{ __('Sec') }}</span></div>
                                <div class="details-label-discount-percentage"><div class="percentage-text"><x-flash-icon></x-flash-icon><span>{{ $product->item->flash_amount }}%</span></div></div>
                            </div>
                        @endif

                        <!-- Variants -->
                        <span id="variantStockDisplay" class="fw-bold" style="color:green"></span>

                        @if (count($product_variations) > 0)
                            <ul class="product-list-group mb-20" id="variantListULDetails">
                                @foreach ($product_variations as $product_variation)
                                    @php
                                        $product_variation_contents = App\Models\User\ProductVariationContent::where([['product_variation_id', $product_variation->id], ['language_id', $uLang]])->get();
                                        $variant_content_options = App\Models\User\ProductVariantOption::where('product_variation_id', $product_variation->id)->get();
                                    @endphp
                                    @foreach ($product_variation_contents as $content)
                                        @php $variant_content = App\Models\VariantContent::find($content->variation_name); @endphp
                                        <li class="list-item" data-variant_name="{{ $variant_content->name ?? '' }}">
                                            <h4 class="list-item-title color-primary mb-2">{{ $variant_content->name ?? '' }}:</h4>
                                            <ul class="custom-radio variantUL d-flex flex-wrap gap-2">
                                                @foreach ($variant_content_options as $option)
                                                    @php
                                                        $opt_content = App\Models\User\ProductVariantOptionContent::where([['product_variant_option_id', $option->id], ['language_id', $uLang]])->first();
                                                        $opt = $opt_content->option_content ?? null;
                                                        $imagePath = $opt?->image ? asset($opt->image) : null;
                                                        $input_name = make_input_name($variant_content->name ?? 'variant');
                                                        $id_name = make_input_name($opt_content->option_content->option_name ?? 'option');
                                                        $main_id = 'detail_' . $input_name . '_' . $id_name;
                                                    @endphp
                                                    <li>
                                                        <input id="radio_{{ $main_id }}" type="radio" name="{{ $input_name }}[]"
                                                               class="{{ $main_id }} product-variant input-radio"
                                                               value="{{ $opt_content->option_content->option_name }}:{{ currency_converter($option->price) }}:{{ $option->stock }}:{{ $option->id }}:{{ $product_variation->id }}"
                                                               data-variant-image="{{ $imagePath }}">
                                                        <label for="radio_{{ $main_id }}" class="form-radio-label variant-swatch-label">
                                                            <span class="variant-swatch">
                                                                <span class="variant-swatch-text">{{ $opt?->option_name }}</span>
                                                                @if($option->price > 0)
                                                                    <small class="variant-price text-success ms-1">(+{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, currency_converter($option->price)) }})</small>
                                                                @endif
                                                            </span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif

                        <!-- Add to Cart & Actions -->
                        @if ($shop_settings->catalog_mode != 1)
                            <div class="d-flex flex-wrap align-items-center gap-10 mb-20">
                                <div class="quantity-input d-flex item_quantity_details">
                                    <div class="quantity-down quantity-btn minus" data-item_id="{{ $product->id }}"><i class="fal fa-minus"></i></div>
                                    <input class="quantity_field" type="number" name="cart-amount" value="1" min="1">
                                    <div class="quantity-up quantity-btn plus" data-item_id="{{ $product->id }}"><i class="fal fa-plus"></i></div>
                                </div>
                                <div class="btn-icon-group btn-inline">
                                    <a class="btn btn-icon radius-sm" onclick="addToCompare('{{ route('front.user.add.compare', ['id' => $product->item_id, getParam()]) }}')" title="{{ __('Compare') }}"><i class="fal fa-random"></i></a>
                                    @php $checkWishList = Auth::guard('customer')->check() ? checkWishList($product->item_id, Auth::guard('customer')->user()->id) : false; @endphp
                                    <a href="#" class="btn btn-icon radius-sm {{ $checkWishList ? 'remove-wish active' : 'add-to-wish' }}"
                                       data-url="{{ route('front.user.add.wishlist', ['id' => $product->item_id, getParam()]) }}"
                                       data-removeUrl="{{ route('front.user.remove.wishlist', ['id' => $product->item_id, getParam()]) }}"
                                       title="{{ __('Add to wishlist') }}"><i class="fal fa-heart"></i></a>
                                </div>
                            </div>

                            <input type="hidden" id="details_final-price">
                            <button class="btn btn-lg btn-primary radius-md" type="button" onclick="addToCartDetails2()">
                                <i class="fas fa-cart-plus"></i> {{ __('Shop Now') }}
                            </button>
                        @endif

                        <!-- Share Buttons -->
                        <div class="d-flex align-items-center flex-wrap gap-10 mt-20">
                            <span class="text-dark fw-semibold">{{ __('Share Now') }} :</span>
                            <div class="social-link">
                                <a href="https://www.instagram.com/stories/create/?text={{ urlencode('Check out this product: '.url()->current()) }}" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="//x.com/intent/tweet?text={{ urlencode('Check this out! '.url()->current()) }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="//www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://wa.me/?text={{ urlencode('Check this out: '.url()->current()) }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                          <div class="product-single-tab pt-70">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc" type="button">
                                {{ $keywords['Description'] ?? __('Description') }}
                            </button>
                        </li>
                        @if ($shop_settings->item_rating_system == 1)
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                    {{ $keywords['Reviews'] ?? __('Reviews') }}
                                </button>
                            </li>
                        @endif
                        @if ($shop_settings->disqus_comment_system == 1)
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#Disqus" type="button">
                                    {{ $keywords['Comments'] ?? __('Comments') }}
                                </button>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content radius-lg">
                        <div class="tab-pane fade active show" id="desc">
                            <div class="tab-description">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="content mb-30 tinymce-content">
                                            {!! replaceBaseUrl($product->description ?? null) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($shop_settings->item_rating_system == 1)
                            <div class="tab-pane fade" id="reviews">
                                <div class="tav-review mb-30">
                                    <h4 class="mb-15">{{ $keywords['Customer Reviews'] ?? __('Customer Reviews') }}</h4>
                                    <ul class="comment-list">
                                        @foreach ($reviews as $review)
                                            <li class="comment mb-20">
                                                <div class="comment-body">
                                                    <div class="author">
                                                        <img class="radius-sm lazyload"
                                                             src="{{ asset('assets/front/images/placeholder.png') }}"
                                                             data-src="{{ is_null(@$review->customer->image) ? asset('assets/user-front/images/avatar-1.jpg') : asset('assets/user-front/images/users/' . @$review->customer->image) }}"
                                                             alt="">
                                                    </div>
                                                    <div class="content">
                                                        <h6>
                                                            {{ !empty(convertUtf8(@$review->customer)) ? convertUtf8(@$review->customer->username) : '' }}
                                                            <span>( {{ @$review->created_at->format('F j, Y') }} )</span>
                                                        </h6>
                                                        <div class="rate text-xsm">
                                                            <div class="rating" style="width:{{ @$review->review * 20 }}%"></div>
                                                        </div>
                                                        <p>{{ nl2br(convertUtf8(@$review->comment)) }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    @if (Auth::guard('customer')->user())
                                        @if (App\Models\User\UserOrderItem::where('customer_id', Auth::guard('customer')->user()->id)->where('item_id', $product->item->id)->exists())
                                            <div class="comment-form">
                                                @error('error')
                                                    <p class="text-danger my-2">{{ Session::get('error') }}</p>
                                                @enderror
                                                <h4 class="mb-10">{{ $keywords['Add Your review'] ?? __('Add Your review') }}</h4>
                                                <form action="{{ route('item.review.submit', getParam()) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" value="" id="reviewValue" name="review">
                                                    <input type="hidden" value="{{ $product->item->id }}" name="item_id">
                                                    <div class="input-box mb-3">
                                                        <div class="review-content mt-3">
                                                            <ul class="review-value review-1"><li><a class="cursor-pointer" data-href="1"><i class="far fa-star"></i></a></li></ul>
                                                            <ul class="review-value review-2"><li><a class="cursor-pointer" data-href="2"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="2"><i class="far fa-star"></i></a></li></ul>
                                                            <ul class="review-value review-3"><li><a class="cursor-pointer" data-href="3"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="3"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="3"><i class="far fa-star"></i></a></li></ul>
                                                            <ul class="review-value review-4"><li><a class="cursor-pointer" data-href="4"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="4"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="4"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="4"><i class="far fa-star"></i></a></li></ul>
                                                            <ul class="review-value review-5"><li><a class="cursor-pointer" data-href="5"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="5"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="5"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="5"><i class="far fa-star"></i></a></li><li><a class="cursor-pointer" data-href="5"><i class="far fa-star"></i></a></li></ul>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group mb-30">
                                                                <textarea class="form-control" name="comment" cols="30" rows="9" placeholder="{{ $keywords['Comment'] ?? __('Comment') }} *"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-lg btn-primary radius-md">{{ $keywords['Submit'] ?? __('Submit') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <div class="review-login">
                                            <a class="boxed-btn d-inline-block mr-2" href="{{ route('customer.login', getParam()) }}">{{ $keywords['Login'] ?? __('Login') }}</a>
                                            {{ $keywords['to leave a rating'] ?? __('to leave a rating') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($shop_settings->disqus_comment_system == 1)
                            <div class="tab-pane fade" id="Disqus">
                                <div id="disqus_thread"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Products -->
                <div class="pt-100">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-title title-inline mb-10">
                                <h2 class="title fs-1 mb-20">{{ $keywords['Related Products'] ?? __('Related Products') }}</h2>
                                @if (count($related_product) > 0)
                                    <a href="{{ route('front.user.shop', [getParam(), 'category' => $category_slug]) }}"
                                       class="btn btn-md btn-primary radius-sm mb-20">{{ $keywords['More_Items'] ?? __('More Items') }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            @if (count($related_product) > 0)
                                <div class="product-slider mb-30 pb-10" id="product-details-slider" data-slick='{"dots": true, "slidesToShow": 4}'>
                                    @foreach ($related_product as $item)
                                        <!-- Related product card -->
                                        <div class="product-default product-center radius-xl">
                                            <figure class="product-img">
                                                <a href="{{ route('front.user.productDetails', ['slug' => $item->slug]) }}" class="lazy-container ratio ratio-1-1">
                                                    <img class="lazyload default-img" src="{{ asset('assets/front/images/placeholder.png') }}"
                                                         data-src="{{ asset('assets/front/img/user/items/thumbnail/' . $item->item->thumbnail) }}" alt="Product">
                                                    <img class="lazyload hover-img" src="{{ asset('assets/front/images/placeholder.png') }}"
                                                         data-src="{{ asset('assets/front/img/user/items/thumbnail/' . $item->item->thumbnail) }}" alt="Product">
                                                </a>
                                            </figure>
                                            <div class="product-details">
                                                <a href="{{ route('front.user.shop', ['category' => $item->category->slug]) }}">
                                                    <span class="product-category text-sm">{{ $item->category->name }}</span>
                                                </a>
                                                <h3 class="product-title">
                                                    <a href="{{ route('front.user.productDetails', ['slug' => $item->slug]) }}">{{ truncateString($item->title, 30) }}</a>
                                                </h3>
                                                @if ($shop_settings->item_rating_system == 1)
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="product-ratings rate text-xsm">
                                                            <div class="rating" style="width:{{ $item->item->rating * 20 }}%"></div>
                                                        </div>
                                                        <span class="ratings-total">({{ reviewCount($item->item_id) }})</span>
                                                    </div>
                                                @endif
                                                @php
                                                    $flash_info = flashAmountStatus($item->item->id, $item->item->current_price);
                                                    $product_current_price = $flash_info['amount'];
                                                    $flash_status = $flash_info['status'];
                                                @endphp
                                                <div class="product-price">
                                                    @if ($flash_status == true)
                                                        <span class="new-price">{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, currency_converter($product_current_price)) }}</span>
                                                        <span class="old-price ms-1 line_through">{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, currency_converter($item->item->current_price)) }}</span>
                                                        <span class="old-price">{{ $item->item->flash_amount }}% {{ $keywords['OFF'] ?? __('OFF') }}</span>
                                                    @else
                                                        <span class="new-price">{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, currency_converter($item->item->current_price)) }}</span>
                                                        @if($item->item->previous_price > 0)
                                                            <span class="old-price line_through">{{ symbolPrice($userCurrentCurr->symbol_position, $userCurrentCurr->symbol, currency_converter($item->item->previous_price)) }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="btn-icon-group btn-inline">
                                                @if ($shop_settings->catalog_mode != 1)
                                                    <a class="btn btn-icon radius-sm cart-link cursor-pointer"
                                                       data-title="{{ $item->title }}"
                                                       data-current_price="{{ currency_converter($product_current_price) }}"
                                                       data-item_id="{{ $item->item_id }}"
                                                       data-language_id="{{ $uLang }}"
                                                       data-totalVari="{{ check_variation($item->item_id) }}"
                                                       data-variations="{{ check_variation($item->item_id) > 0 ? 'yes' : null }}"
                                                       data-href="{{ route('front.user.add.cart', ['id' => $item->item_id, getParam()]) }}"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ $keywords['Shop_Now'] ?? __('Shop Now') }}"><i class="far fa-shopping-cart"></i></a>
                                                @endif
                                                @php
                                                    $customer_id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : null;
                                                    $checkWishList = $customer_id ? checkWishList($item->item_id, $customer_id) : false;
                                                @endphp
                                                <a href="#" class="btn btn-icon radius-sm {{ $checkWishList ? 'remove-wish active' : 'add-to-wish' }}"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   data-item_id="{{ $item->item_id }}"
                                                   data-href="{{ route('front.user.add.wishlist', ['id' => $item->item_id, getParam()]) }}"
                                                   data-removeUrl="{{ route('front.user.remove.wishlist', ['id' => $item->item_id, getParam()]) }}"
                                                   title="{{ $keywords['Add to Wishlist'] ?? __('Add to Wishlist') }}"><i class="fal fa-heart"></i></a>
                                                <a class="btn btn-icon radius-sm quick-view-link" data-bs-toggle="tooltip"
                                                   data-bs-placement="top" data-item_id="{{ $item->item_id }}"
                                                   data-url="{{ route('front.user.productDetails.quickview', ['slug' => $item->slug, getParam()]) }}"
                                                   title="{{ $keywords['Quick View'] ?? __('Quick View') }}"><i class="fal fa-eye"></i></a>
                                                <a onclick="addToCompare('{{ route('front.user.add.compare', ['id' => $item->item_id, getParam()]) }}')"
                                                   class="btn btn-icon radius-sm" data-bs-toggle="tooltip" data-bs-placement="top"
                                                   title="{{ $keywords['Compare'] ?? __('Compare') }}"><i class="fal fa-random"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <h3 class="text-center">{{ $keywords['No related product found'] ?? __('No related product found') }}</h3>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

@include('user-front.partials.variation-modal')

<!-- Video / 3D Modal -->
<div class="modal fade" id="videoPopupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content radius-lg overflow-hidden bg-dark">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9" id="videoContainer"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
"use strict";

// 1. Video / 3D Popup
document.querySelectorAll('[data-type="popup"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.dataset.video;
        const container = document.getElementById('videoContainer');
        let iframe = '';

        if (url.includes('vectary.com')) {
            iframe = `<iframe src="${url}?autostart=1&transparentBackground=1&showNavigation=1" frameborder="0" allowfullscreen style="width:100%;height:100%;"></iframe>`;
        } else if (url.includes('youtube.com') || url.includes('youtu.be')) {
            let id = url.split('embed/')[1] || url.split('v=')[1]?.split('&')[0] || url.split('/').pop();
            iframe = `<iframe src="https://www.youtube.com/embed/${id}?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        }
        container.innerHTML = iframe;
        new bootstrap.Modal(document.getElementById('videoPopupModal')).show();
    });
});
document.getElementById('videoPopupModal').addEventListener('hidden.bs.modal', () => {
    document.getElementById('videoContainer').innerHTML = '';
});

// 2. Variant Image Swap + Out of Stock + Stock Display
document.addEventListener('DOMContentLoaded', function() {
    const mainSlider = $('.product-single-slider2');
    const slides = mainSlider.find('.product-single-single-item img.main-thumbnail');
    const originalSrcs = [];

    slides.each(i => originalSrcs[i] = slides.eq(i).data('src'));

    function resetImages() {
        slides.each(function(i) {
            $(this).attr('src', originalSrcs[i]).attr('data-src', originalSrcs[i]).removeClass('lazyloaded').addClass('lazyload');
        });
        if (typeof LazyLoad !== 'undefined') new LazyLoad();
    }

    $('.product-variant').on('change', function() {
        if (!this.checked) return;
        const img = $(this).data('variant-image');
        const stock = parseInt(this.value.split(':')[2]) || 0;

        $('#variantStockDisplay').text(stock > 0 ? `${stock} in stock` : 'Out of stock').css('color', stock > 0 ? 'green' : 'red');

        if (img) {
            slides.attr('src', img).attr('data-src', img).removeClass('lazyload').addClass('lazyloaded');
            $(".zoomContainer").remove();
        } else {
            resetImages();
        }
    });

    // Disable out-of-stock variants
    $('.product-variant').each(function() {
        const stock = parseInt(this.value.split(':')[2]) || 0;
        if (stock <= 0) {
            $(this).prop('disabled', true);
            $(`label[for="${this.id}"]`).addClass('out-of-stock-label');
        }
    });

    // Trigger initial variant
    $('.product-variant:checked').trigger('change');
});

// 3. Slick Slider + Thumbnail Click Fix + Zoom
$(document).ready(function() {
    const $main = $(".product-single-slider2");
    const $thumb = $(".slider-thumbnails2");

    if ($main.length && $thumb.length) {
        $main.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            dots: true,
            asNavFor: ".slider-thumbnails2",
            rtl: $('html').attr('dir') === 'rtl'
        });

        $thumb.slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: ".product-single-slider2",
            focusOnSelect: true,
            vertical: true,
            verticalSwiping: true,
            prevArrow: '<button type="button" class="btn-icon slider-btn slider-prev"><i class="fal fa-angle-up"></i></button>',
            nextArrow: '<button type="button" class="btn-icon slider-btn slider-next"><i class="fal fa-angle-down"></i></button>',
            responsive: [
                { breakpoint: 1200, settings: { slidesToShow: 4, vertical: false, verticalSwiping: false } },
                { breakpoint: 768, settings: { slidesToShow: 4, vertical: false, verticalSwiping: false } }
            ]
        });

        // CRITICAL: Force thumbnail click to change main image
        $thumb.on('click', '.thumbnail-img', function() {
            const index = $(this).index();
            $main.slick('slickGoTo', index);
        });

        // Re-init ElevateZoom after slide change
        $main.on('afterChange', function(event, slick, currentSlide) {
            $(".zoomContainer, .zoomWindowContainer").remove();
            const $img = $(slick.$slides[currentSlide]).find('img.main-thumbnail');
            if ($img.length && !$img.hasClass('elevateZoomed')) {
                $img.elevateZoom({
                    zoomType: "inner",
                    cursor: "crosshair",
                    scrollZoom: false
                });
                $img.addClass('elevateZoomed');
            }
        });

        // Initial zoom
        setTimeout(() => {
            $('.product-single-slider2 .slick-active img.main-thumbnail').elevateZoom({
                zoomType: "inner",
                cursor: "crosshair",
                scrollZoom: false
            });
        }, 500);
    }
});

// 4. Review Stars
$(document).on('click', '.review-value li a', function() {
    const val = $(this).data('href');
    $('.review-value li a i').removeClass('review-color');
    for(let i=1; i<=val; i++) $(`.review-${i} li a i`).addClass('review-color');
    $('#reviewValue').val(val);
});

// 5. Disqus
@if ($userBs->is_disqus == 1 && in_array('Disqus', $packagePermissions) && $shop_settings->disqus_comment_system == 1)
    (function() { var d = document, s = d.createElement('script'); s.src = 'https://{{ $userBs->disqus_shortname }}.disqus.com/embed.js'; s.setAttribute('data-timestamp', +new Date()); (d.head || d.body).appendChild(s); })();
@endif
</script>
@endsection