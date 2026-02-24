@extends('user-front.layout')

@section('meta-description', !empty($seo) ? $seo->home_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->home_meta_keywords : '')
@section('page-title', 'Monarch - Furniture' ?? __('Monarch - Furniture Store'))
@section('og-meta')
  <!--- For Social Media Share Thumbnail --->
  <meta property="og:title" content="{{ $user->username }}">
  <meta property="og:image" content="{{ !empty($userBs->logo) ? asset('assets/front/img/user/' . $userBs->logo) : '' }}">
  <meta property="og:image:type" content="image/png">
  <meta property="og:image:width" content="1024">
  <meta property="og:image:height" content="1024">
  <style>
      .monarch-section {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    color: #222;
    font-family: "Poppins", sans-serif;
}

.monarch-section h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 20px;
}

.monarch-section h5 {
  max-width: 1000px;
    margin: 0 auto 50px;
    font-size: 15px;
    line-height: 1.6;
    font-weight: 500;
    color: #000;
}
.slider-content {
    position: absolute;
    bottom: 10%;
    left: 47%;
    display: flex;
    justify-content: center;
    align-items: center;
}
/* .monarch-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 40px;
    justify-items: center;
    align-items: start;
} */

/* .monarch-item {
  text-align: center;
    max-width: 280px;
    background-color: #eafaff;
    padding: 30px;
    height: 100%;
    border-radius: 10px;
} */

.monarch-item img {
  max-width: 120px;
    height: 120px;
    margin-bottom: 15px;
    object-fit: contain;
    object-position: center;
}

.monarch-item {
    box-shadow: 0px 5px 30px rgba(49, 49, 49, 0.1);
}
.monarch-section .monarch-item h6 {
  font-size: 14px;
    line-height: 1.8;
    color: #000000;
    font-weight: 500;
    /* text-align: left; */
    word-spacing: 2px;
}

@media (max-width: 768px) {
    .monarch-section h1 {
        font-size: 32px;
    }

    .monarch-section h5 {
        font-size: 16px;
        margin-bottom: 30px;
    }

    .monarch-item h6 {
        font-size: 15px;
    }
}
.clientele-section {
    background: #f5f5f5;
}

.clientele-title {
    font-weight: 700;
    font-size: 2rem;
    color: #333;
}

.view-more {
    display: inline-block;
    color: #00ccff;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s;
}

.view-more:hover {
    color: #0099cc;
}

.clientele-carousel .client-logo {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.clientele-carousel .client-logo img {
    width: 100%;
    height: auto;
    max-width: 120px;
    margin: 0 auto;
    display: block;
}

.clientele-carousel .client-logo:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
section.vectary_iframe {
    background-color: #eeeeee;

}

.slider {
      position: relative;
      width: 100%;
      /*max-width: 900px;*/
      height: 480px;
      /*margin: 60px auto;*/
      overflow: hidden;
      /*border-radius: 18px;*/
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
   
    }

    /* === SLIDES WRAPPER === */
    .slides {
      display: flex;
      width: 100%;
      height: 100%;
      transition: transform 0.7s ease-in-out;
    }

    /* === SINGLE SLIDE === */
    .slide {
      min-width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      transition: opacity 0.6s ease;
    }

    .slide.active {
      opacity: 1;
      position: relative;
    }

    .slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* === NAVIGATION BUTTONS === */
    .prev, .next {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      font-size: 26px;
      padding: 12px 18px;
      border-radius: 50%;
      cursor: pointer;
      z-index: 10;
      transition: background 0.3s, transform 0.2s;
    }

    .prev:hover, .next:hover {
      background: rgba(0, 0, 0, 0.8);
      transform: translateY(-50%) scale(1.05);
    }

    .prev {
      left: 15px;
    }

    .next {
      right: 15px;
    }

    /* === DOTS NAVIGATION === */
    .dots {
      text-align: center;
      position: absolute;
      bottom: 15px;
      width: 100%;
      z-index: 15;
    }

    .dot {
      display: inline-block;
      height: 12px;
      width: 12px;
      margin: 0 4px;
      background: #0fb0e2;
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s;
    }

    .dot.active,
    .dot:hover {
      background: #fff;
    }

    /* === RESPONSIVE DESIGN === */
    @media (max-width: 768px) {
      .slider {
        height: 340px;
      }

      .prev, .next {
        font-size: 20px;
        padding: 8px 12px;
      }
    }

    @media (max-width: 480px) {
      .slider {
        height: 260px;
      }

      .prev, .next {
        font-size: 18px;
        padding: 6px 10px;
      }

      .dot {
        height: 10px;
        width: 10px;
      }
    }

  </style>
  
  
  <style>
 
    .slider {
      position: relative;
      max-width: 100%;
      height: 600px;
      overflow: hidden;
    }

    .slides {
      display: flex;
      transition: transform 0.6s ease-in-out;
    }

    .slide {
      min-width: 100%;
      transition: opacity 1s ease;
      opacity: 0;
      position: absolute;
      top: 0;
      left: 0;
    }

    .slide.active {
      opacity: 1;
      position: relative;
    }

    .slide img {
      width: 100%;
      /*height: 600px;*/
      object-fit: cover;
    }

    /* Navigation arrows */
    .prev,
    .next {
      cursor: pointer;
      position: absolute;
      top: 50%;
      width: 40px;
      height: 40px;
      transform: translateY(-50%);
      color: white;
      background-color: rgba(0, 0, 0, 0.5);
      border: none;
      border-radius: 50%;
      font-size: 22px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
      transition: background 0.3s;
    }

    .prev:hover,
    .next:hover {
      background-color: rgba(0, 0, 0, 0.8);
    }

    .prev {
      left: 20px;
    }

    .next {
      right: 20px;
    }

    /* Dots */
    .dots {
      text-align: center;
      position: absolute;
      bottom: 20px;
      width: 100%;
    }



    .dot.active,
    .dot:hover {
      background-color: #fff;
    }
    
    @media (max-width: 1400px) {
  .slider {
    height: 500px;
  }
}
  </style>
  <style>
      
     .gallery-box {
  width: 100%;
  height: 100%;

}

.gallery-box .gallery-img {
  width: 100%;
  height: 350px;
  object-fit: cover;
  border-radius: 10px;
}

.hotspot {
  position: absolute;
  cursor: pointer;
}

.dot {
    width: 16px;
    height: 16px;
    background: #00c8fa;
    border-radius: 50%;
    animation: blink 1s infinite;
    box-shadow: 0 0 10px #ff4f4f;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.3; }
}

.popup {
  position: absolute;
  top: -10px;
  left: 25px;
  width: 180px;
  background: #fff;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 0 12px rgba(0,0,0,0.15);
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: 0.25s ease;
  z-index: 99;
}

.hotspot:hover .popup,
.hotspot.active .popup {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}


.gallery-box {
    width: 100%;
    height: 100%;
    overflow: visible;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0px 0px 6px 0px #0000000d;
}


.clients-section
.slick-slide{
    height:auto;
}

.bg2{
    background-color: #f5f2ed;
}
  </style>
  
   
        <style>
.sparkle-card {
  position: relative;
  overflow: hidden;
}

.sparkle-card .sparkle {
  position: absolute;
  width: 6px;
  height: 6px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  box-shadow: 0 0 8px #fff;
  animation: sparkleMove 1.5s infinite ease-in-out;
  pointer-events: none;
  z-index: 2;
}

@keyframes sparkleMove {
  0% { transform: translate(0, 0) scale(1); opacity: 1; }
  50% { transform: translate(20px, -20px) scale(1.5); opacity: 0.7; }
  100% { transform: translate(-10px, 10px) scale(1); opacity: 1; }
}


.bg11{
    background-color: #25aae1 !important;
}
        </style>
  
  <!--- For Social Media Share Thumbnail --->
@endsection

@php
  $additional_section_status = json_decode($userBs->additional_section_status, true);
  
@endphp
@section('content')
  @if ($ubs->slider_section == 1)

  <div class="slider">
<div class="slides">
    @foreach ($sliders->where('is_static', 0) as $slider)
  <div class="slide">
    <img src="{{ asset('assets/front/img/hero_slider/' . $slider->img) }}" alt="Slide 1">
    <div class="d-flex align-items-center">
                      <div class="slider-content">
                        <span class="sub-title color-primary" data-animation="animate__fadeInUp" data-delay=".3s">
                          {{ $slider->title }}
                        </span>
                        <h1 class="title lc-2" data-animation="animate__fadeInDown" data-delay=".1s">
                          {{ $slider->subtitle }}
                        </h1>
                        <p class="text-lg lc-3 text-dark" data-animation="animate__fadeInDown" data-delay=".2s">
                          {{ $slider->text }}
                        </p>
                        @if ($slider->btn_url && $slider->btn_name)
                          <a href="{{ $slider->btn_url }}" class="btn btn-lg btn-primary radius-sm"
                            data-animation="animate__fadeInUp" data-delay=".3s">
                            {{ $slider->btn_name }}
                          </a>
                        @endif
                      </div>
                    </div>
  </div>
 @endforeach
  <!--<div class="slide">-->
  <!--  <img src="{{ asset('images/2.jpg') }}" alt="Slide 2">-->
  <!--</div>-->

  <!--<div class="slide">-->
  <!--  <img src="{{ asset('images/3.jpg') }}" alt="Slide 3">-->
  <!--</div>-->

  <!--<div class="slide">-->
  <!--  <img src="{{ asset('images/4.jpg') }}" alt="Slide 4">-->
  <!--</div>-->
</div>


    <!-- Navigation arrows -->
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>

    <!-- Dots -->
    <div class="dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </div>

  <!-- Slider JavaScript -->
  <script>
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");
    const prev = document.querySelector(".prev");
    const next = document.querySelector(".next");

    let index = 0;
    let slideInterval;

    function showSlide(n) {
      slides.forEach((slide, i) => {
        slide.classList.toggle("active", i === n);
        dots[i].classList.toggle("active", i === n);
      });
      index = n;
    }

    function nextSlide() {
      index = (index + 1) % slides.length;
      showSlide(index);
    }

    function prevSlide() {
      index = (index - 1 + slides.length) % slides.length;
      showSlide(index);
    }

    function autoSlide() {
      slideInterval = setInterval(nextSlide, 3000);
    }

    // Event listeners
    next.addEventListener("click", () => {
      nextSlide();
      resetTimer();
    });

    prev.addEventListener("click", () => {
      prevSlide();
      resetTimer();
    });

    dots.forEach((dot, i) => {
      dot.addEventListener("click", () => {
        showSlide(i);
        resetTimer();
      });
    });

    function resetTimer() {
      clearInterval(slideInterval);
      autoSlide();
    }

    // Auto start
    autoSlide();
  </script>

  @endif

  @if (count($after_hero) > 0)
    @foreach ($after_hero as $cusHero)
      @if (isset($additional_section_status[$cusHero->id]))
        @if ($additional_section_status[$cusHero->id] == 1)
          @php
            $cusHeroContent = App\Models\User\AdditionalSectionContent::where([
                ['language_id', $uLang],
                ['addition_section_id', $cusHero->id],
            ])->first();
          @endphp
          @includeIf('user-front.additional-section', [
              'data' => $cusHeroContent,
              'possition' => $cusHero->possition,
          ]);
        @endif
      @endif
    @endforeach
  @endif
  
  
  <section class="monarch-section space">




  <div class="section-title title-inline mb-20 d-flex flex-column">
              <h2 class="title ">Monarch Ergo — Empowering Workspaces for 25+ Years
                <span class="line left_right_slide_anim"></span>
              </h2>
              <p class="text">Trusted by leading organizations to create innovative, high-performance workspaces. </p>
            </div>



    <h5>
        For over 25 years, leading organizations have trusted Monarch Ergo to help them innovate by creating dynamic,
        high-performing places that unlock the promise of their people. Monarch Ergo stands for innovative concepts,
        inspiring offices, and high-quality design, by offering workspace solutions designed to help people reach
        their full potential.
    </h5>

    <div class="container">
      <div class="row g-4 monarch-features">
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card h-100 text-center monarch-item p-3">
            <img src="{{ asset('images/26years.png') }}" class="card-img-top mx-auto d-block" alt="26 Years of Excellence" style="max-width:120px;">
            <div class="card-body">
              <h6 class="card-title">Having over 2 decades of industry experience in product innovation</h6>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
          <div class="card h-100 text-center monarch-item p-3">
            <img src="{{ asset('images/Network.png') }}" class="card-img-top mx-auto d-block" alt="Network" style="max-width:100px;">
            <div class="card-body">
              <h6 class="card-title mb-0">
                A network of 9 Indian metropolitan cities and 3 International locations.<br>
                <!--<small>Hyderabad, Bengaluru, Ahmedabad, Chennai, Delhi, Kolkata, Kochi, Mumbai, Pune</small>-->
              </h6>
            </div>
          </div>
        </div>
       

<div class="col-12 col-md-6 col-lg-3">
  <div class="card h-100 text-center monarch-item p-3 sparkle-card bg11">

    <!-- Sparkles -->
    <div class="sparkle" style="top:10%; left:20%; animation-delay:0s;"></div>
    <div class="sparkle" style="top:40%; left:70%; animation-delay:0.3s;"></div>
    <div class="sparkle" style="top:75%; left:30%; animation-delay:0.6s;"></div>
    <div class="sparkle" style="top:20%; left:80%; animation-delay:0.9s;"></div>

    <!-- Your image + text -->
    <img src="{{ asset('images/10yearwarranty.png') }}" class="card-img-top mx-auto d-block" alt="10 Year Warranty" style="max-width:100px;">
    <div class="card-body">
      <h6 class="card-title">10 year long industry strong product warranty</h6>
    </div>
  </div>
</div>

        <div class="col-12 col-md-6 col-lg-3">
          <div class="card h-100 text-center monarch-item p-3">
            <img src="{{ asset('images/Quality.png') }}" class="card-img-top mx-auto d-block" alt="Certified Quality" style="max-width:100px;">
            <div class="card-body">
              <h6 class="card-title">
               Certified with global quality standards including BIFMA, UL, ISO, and Greenguard.
              </h6>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>


  @if ($ubs->top_middle_banner_section == 0)
    <!-- Banner Collection Start -->
    <div class="banner-collection space">
      <div class="container">
        <div class="row g-0">
          @if (count($banners) > 0)
            @php
              $middle_banner_count = 1;
            @endphp
            @foreach ($banners as $banner)
              @if ($banner->position == 'top_middle')
                @if ($middle_banner_count <= 2)
                  <div class="col-sm-6">
                    <a href="{{route('front.user.shop')}}">
                      <div class="banner-sm rounded-1 mb-30 ratio p-60 ratio-21-9">
                        <img class="bg-img" src="{{ asset('assets/front/images/placeholder.png') }}"
                          data-src="{{ asset('assets/front/img/user/banners/' . $banner->banner_img) }}" alt="Banner">
                        <div class="banner-content mw-80">
                          <div class="content-inner">
                            <span class="sub-title color-medium">{{ $banner->title }}</span>
                            <h3 class="title lc-2">{{ $banner->subtitle }}</h3>
                            <span class="line left_right_slide_anim"></span>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  @php
                    $middle_banner_count = $middle_banner_count + 1;
                  @endphp
                @endif
              @endif
            @endforeach
          @endif
        </div>
      </div>
    </div>
    <!-- Banner Collection End -->
  @endif
  
  @if (count($after_middle_banner) > 0)
    @foreach ($after_middle_banner as $cusMiddleBanner)
      @if (isset($additional_section_status[$cusMiddleBanner->id]))
        @if ($additional_section_status[$cusMiddleBanner->id] == 1)
          @php
            $cusMiddleBannerContent = App\Models\User\AdditionalSectionContent::where([
                ['language_id', $uLang],
                ['addition_section_id', $cusMiddleBanner->id],
            ])->first();
          @endphp
          @includeIf('user-front.additional-section', [
              'data' => $cusMiddleBannerContent,
              'possition' => $cusMiddleBanner->possition,
          ]);
        @endif
      @endif
    @endforeach
  @endif

  @if ($ubs->category_section == 1)
    <!-- Category Start -->
    <section class="category category-2 space bg1">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section-title title-inline mb-20">
              <h2 class="title mb-2">{{ $userSec->category_section_title ?? ($keywords['Category'] ?? __('Category')) }}
                <span class="line left_right_slide_anim"></span>
              </h2>
              <p class="text">{{ $userSec->category_section_subtitle ?? '' }} </p>
            </div>
          </div>
          <div class="col-12">
            @if (count($item_categories) == 0)
              <h5 class="text-center mb-20">
                {{ $keywords['NO CATEGORIES FOUND'] ?? __('NO CATEGORIES FOUND') }}
              </h5>
            @else
              <div class="category-slider" id="cat-slider-furniture" data-slick='{"slidesToShow": 2}'>

                @foreach ($item_categories as $cat)
                  <div class="category-item    color-1">
                    <div class="category-icon" style="--category-bg:#{{ $cat->color }}">
                      <a
                          href="{{ route('front.user.shop', $cat->slug) }}"><img class="lazyload blur-up" src="{{ asset('assets/front/images/placeholder.png') }}"
                        data-src="{{ asset('assets/front/img/user/items/categories/' . $cat->image) }}" alt=""></a>
                    </div>
                    <div class="category-content border">
                      <h3 class="category-title lc-1"><a
                          href="{{ route('front.user.shop', $cat->slug) }}">{{ $cat->name }}</a>
                      </h3>
                      <span class="line"></span>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
    <!-- Category End -->
  @endif


<section class=" gallery space bg2" id="elevate">
    
      <div class="container">
  <div class="row g-4">
      
           <div class="col-12">
            <div class="section-title  text-center mb-20">
              <h2 class="title mb-2">Elevate Your Space
                <span class="line left_right_slide_anim"></span>
                
              </h2>
                            <p class="">Enhance your interiors with style, comfort, and modern elegance.</p>

            </div>
          </div>


 
 @php
use Illuminate\Support\Str;
@endphp
 
 @foreach($gallery as $image)
<div class="col-md-3">
  <div class="gallery-box position-relative">
    <img src="{{ asset('uploads/gallery/'.$image->image) }}" class="img-fluid gallery-img">
          <div class="hotspot" style="top: 20%; left: 30%;">
          <span class="dot"></span>
          <div class="popup">
               <img src="{{ asset('uploads/gallery/'.$image->image) }}" class="img-fluid mb-2">
            <h6>{{ $image->category_name }}</h6>
            <p>{{ Str::limit($image->item_title, 6) }}</p>
             <a href="{{ route('front.user.productDetails', ['slug' => $image->item_slug]) }}" class="btn btn-sm btn-primary">View Product</a>
          </div>
        </div>

        <div class="hotspot" style="top: 50%; left: 60%;">
          <span class="dot"></span>
            <div class="popup">
               <img src="{{ asset('uploads/gallery/'.$image->image) }}" class="img-fluid mb-2">
            <h6>{{ $image->category_name }}</h6>
           <p>{{ Str::limit($image->item_title, 6) }}</p>
             <a href="{{ route('front.user.productDetails', ['slug' => $image->item_slug]) }}" class="btn btn-sm btn-primary">View Product</a>
          </div>
        </div>

        <div class="hotspot" style="top: 80%; left: 40%;">
          <span class="dot"></span>
             <div class="popup">
               <img src="{{ asset('uploads/gallery/'.$image->image) }}" class="img-fluid mb-2">
            <h6>{{ $image->category_name }}</h6>
           <p>{{ Str::limit($image->item_title, 6) }}</p>
             <a href="{{ route('front.user.productDetails', ['slug' => $image->item_slug]) }}" class="btn btn-sm btn-primary">View Product</a>
          </div>
        </div>
     
  </div>
</div>
@endforeach
  </div>
</section>





<section class="vectary_iframe space">
<div class="section-title title-inline mb-20 d-flex flex-column">
              <h2 class="title ">Explore Our Smart Height Adjustable Table In 3D
                <span class="line left_right_slide_anim"></span>
              </h2>
              <p class="text">Experience innovation and comfort with our interactive 3D table view. </p>
            </div>


<iframe src="https://app.vectary.com/p/7GAytti3sVmxHmta7FbSSx" frameborder="0" width="100%" height="350"></iframe>
</section>
  

  @if (count($after_video) > 0)
    @foreach ($after_video as $cusVideo)
      @if (isset($additional_section_status[$cusVideo->id]))
        @if ($additional_section_status[$cusVideo->id] == 1)
          @php
            $cusVideoContent = App\Models\User\AdditionalSectionContent::where([
                ['language_id', $uLang],
                ['addition_section_id', $cusVideo->id],
            ])->first();
          @endphp
          @includeIf('user-front.additional-section', [
              'data' => $cusVideoContent,
              'possition' => $cusVideo->possition,
          ]);
        @endif
      @endif
    @endforeach
  @endif

  @if ($ubs->tab_section == 1)
    @include('user-front.furniture.tab_content')
  @endif

  @if (count($after_tab) > 0)
    @foreach ($after_tab as $cusTab)
      @if (isset($additional_section_status[$cusTab->id]))
        @if ($additional_section_status[$cusTab->id] == 1)
          @php
            $cusTabContent = App\Models\User\AdditionalSectionContent::where([
                ['language_id', $uLang],
                ['addition_section_id', $cusTab->id],
            ])->first();
          @endphp
          @includeIf('user-front.additional-section', [
              'data' => $cusTabContent,
              'possition' => $cusTab->possition,
          ]);
        @endif
      @endif
    @endforeach
  @endif

  <!-- Banner Collection Start -->
  @if ($ubs->middle_banner_section == 1)
    <div class="banner-collection space">
      <div class="container">
        <div class="row">
          @if ($banners)
            @php
              $banner_count = 1;
            @endphp
            @for ($i = 2; $i < count($banners); $i++)
              @if ($banners[$i]->position == 'middle')
                @if ($banner_count <= 2)
                  <div class="col-md-6 col-lg-6">
                    <a href="{{route('front.user.shop')}}">
                      <div class="banner-sm rounded-1 content-top mb-30 ratio ratio-21-9">
                        <img class="bg-img" src="{{ asset('assets/front/images/placeholder.png') }}"
                          data-src="{{ asset('assets/front/img/user/banners/' . $banners[$i]->banner_img) }}"
                          alt="Banner">
                        <div class="banner-content rounded-1 mx-auto">
                          <div class="content-inner p-20">
                            <h3 class="title text-white">{{ $banners[$i]->title }}</h3>
                            @if ($banners[$i]->button_text)
                              <span class="btn-text icon-end text-white">{{ $banners[$i]->button_text }}<i
                                  class="fal fa-long-arrow-right"></i></span>
                            @endif
                          </div>
                        </div>
                        <div class="inner-border"></div>
                        <div class="overlay"></div>
                      </div>
                    </a>
                  </div>
                  @php
                    $banner_count++;
                  @endphp
                @else
                  @break
                @endif
              @endif
            @endfor
          @endif
        </div>
      </div>
  @endif

  @if (count($after_banners) > 0)
    @foreach ($after_banners as $cusBanners)
      @if (isset($additional_section_status[$cusBanners->id]))
        @if ($additional_section_status[$cusBanners->id] == 1)
          @php
            $cusBannersContent = App\Models\User\AdditionalSectionContent::where([
                ['language_id', $uLang],
                ['addition_section_id', $cusBanners->id],
            ])->first();
          @endphp
          @includeIf('user-front.additional-section', [
              'data' => $cusBannersContent,
              'possition' => $cusBanners->possition,
          ]);
        @endif
      @endif
    @endforeach
  @endif
  </div>
  <!-- Banner Collection End -->
  @if ($ubs->cta_section_status == 0)
    <!-- Newsletter Start -->
    <section class="newsletter p-0">
      <div class="container">
        <div class="image-inner ptb-100">
          <!-- Background Image -->
          <img class="bg-img" src="{{ asset('assets/front/images/placeholder.png') }}"
            data-src="{{ @$userSec->action_section_background_image ? asset('assets/front/img/cta/' . @$userSec->action_section_background_image) : asset('assets/front/img/subscriber/default_bg.jpg') }}"
            alt="Bg-img">
          <!-- Background Image -->
          <div class="row">
            <div class="col-md-6">
              <div class="content mw-100 mb-30">
                <h3 class="title mb-20">
                  {{ @$userSec->call_to_action_section_title ?? __('Subscribe our newsletter for home delivery.') }}
                </h3>
                @if (!is_null(@$userSec->call_to_action_section_button_url))
                  <a href="{{route('front.user.shop')}}"
                    class="btn btn-md btn-primary rounded-pill">{{ $userSec->call_to_action_section_button_text }}</a>
                @endif
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Newsletter End -->
  @endif
  
  
  <section class="clients-section space bg1">
    <div class="container">



 <div class="section-title title-inline mb-20 d-flex flex-column">
              <h2 class="title ">Our Clientele
                <span class="line left_right_slide_anim"></span>
              </h2>
              <p class="text">Trusted by leading organizations worldwide</p>
            </div>



<div class="clientele-carousel" id="client-slider">
    <div class="client-logo">
        <img src="{{ asset('images/30johndeere-150x150.jpg') }}" alt="John Deere">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/23icici-150x150.jpg') }}" alt="ICICI Bank">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/22Hyland-150x150.jpg') }}" alt="Hyland">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/21HCL-150x150.jpg') }}" alt="HCL">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/20glandpharma-150x150.jpg') }}" alt="Gland Pharma">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/19geodesigns-150x150.jpg') }}" alt="Geodesigns">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/18genpact-150x150.jpg') }}" alt="Genpact">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/17fis-150x150.jpg') }}" alt="FIS">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/16escorts-150x150.jpg') }}" alt="Kubota Escorts">
    </div>
    <div class="client-logo">
        <img src="{{ asset('images/15ensemble-150x150.jpg') }}" alt="Ensemble">
    </div>
</div>

      
   
    </div>

    <div class="text-center mt-4 pt-3 ">
      <a href="https://monarchergo.com/clients" target="_blank" class="view-more">View More</a>
    </div>
  </section>

  <section class="section-testimonial space">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-7">
            <div class="section-heading text-center mb-40 ">
              <h2 class="mb-10">{{ @$testimonial_info->testimonial_section_title }}</h2>
              <P class="mb-10">{{ @$testimonial_info->testimonial_section_subtitle }}</P>
            </div>
          </div>

        </div>
        <div class="row">
          <div class="col-lg-12">
            @if (count($testimonials) > 0)
              <!-- Swiper -->
              <div class="testimonial-slider">
                @foreach ($testimonials as $testimonial)
                  <!-- testimonial-slide-item -->
                  <div class="testimonial-slide-item" style="background: #{{ @$testimonial->color }};">
                    <div
                      class="testimonial-slide-header d-flex flex-wrap justify-content-between align-items-center gap-sm-30 gap-14">
                      <div class="testimonial-author-info">
                        <div class="image">
                          <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                            data-src="{{ asset('assets/front/img/user/about/testimonial/' . @$testimonial->image) }}"
                            alt="testimonial">
                        </div>
                        <div>
                          <h6 class="small font-family-inter mb-1"><a href="#">{{ @$testimonial->name }}</a>
                          </h6>
                          <p class="mb-0 extra-small  designation">{{ @$testimonial->designation }}</p>
                        </div>
                      </div>
                      <div class="reating">
                        <div class="rating-icons">
                          @for ($i = 1; $i <= @$testimonial->rating; $i++)
                            <i class="fas fa-star"></i>
                          @endfor
                        </div>
                      </div>
                    </div>
                    <div class="testimonial-content">
                      <img class="img-fluid quote-icon lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                        data-src="{{ asset('images/quote.svg') }}" alt="quote">
                      <p class="small  mb-0">{{ @$testimonial->comment }}</p>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  



 

  {{-- Variation Modal Starts --}}
  @include('user-front.partials.variation-modal')
  {{-- Variation Modal Ends --}}

  <!-- Quick View Modal Start -->
  <div class="modal custom-modal quick-view-modal fade" id="quickViewModal" tabindex="-1"
    aria-labelledby="quickViewModal">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content radius-lg">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
          <div class="product-single-default">
            <div class="row" id="quickViewModalContent">
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
  <!-- Quick View Modal End -->

@endsection
<script>
document.querySelectorAll(".hotspot .dot").forEach(dot => {
  dot.addEventListener("click", function () {
    document.querySelectorAll(".hotspot").forEach(h => {
      if (h !== dot.parentElement) h.classList.remove("active");
    });
    dot.parentElement.classList.toggle("active");
  });
});
</script>


