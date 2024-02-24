@extends('frontend.layouts.app')

@section('content')
    {{-- Sliders --}}
    @if (get_setting('home_slider_images') != null)
    <div class="home-banner-area">
        @php $slider_images = json_decode(get_setting('home_slider_images'), true);  @endphp
        <div class="aiz-carousel dots-inside-bottom dot-small-white" data-dots="true" data-infinite="true" data-autoplay='true'>
            @foreach ($slider_images as $key => $value)
            <div class="position-relative">
                <a href="{{ json_decode(get_setting('home_slider_links'), true)[$key] }}">
                    <img src="{{ uploaded_asset($slider_images[$key]) }}" class="mw-100 mx-auto w-100">
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif


    {{-- Featured Section --}}
    <section class="pt-5 pb-4 bg-white">
        <div class="container">
            <div class="d-flex align-items-end mb-5 justify-content-start">
                <span class="text-uppercase fs-20 fw-700">{{ translate('Featured Products') }}</span>
            </div>
            <div class="aiz-carousel gutters-5 dot-small-black" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2"  data-autoplay="true" data-dots='true'>
                @foreach (filter_products(\App\Product::where('published', 1)->where('featured', '1'))->limit(12)->get() as $key => $product)
                <div class="carousel-box">
                    @include('frontend.partials.p_box_1',['product'=> $product])
                </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- Banner section 1 --}}
    <div class="bg-white pt-5">
        <div class="container">
            <div class="row gutters-10">
                @if (get_setting('home_banner1_images') != null)
                    @php $banner_1_imags = json_decode(get_setting('home_banner1_images')); @endphp
                    @foreach ($banner_1_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}" class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($banner_1_imags[$key]) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @if(get_setting('filter_categories') != null)
    <div id="section_home_categories">
        
    </div>
    @endif

    <section class="py-7 bg-primary text-white bg-cover bg-center" style="background-image: url('{{ uploaded_asset(get_setting('why_choose_bg')) }}');">
        <div class="container">
            <div class="row">
                @if (get_setting('why_choose_icon') != null)
                    @php $why_choose_icon = json_decode(get_setting('why_choose_icon')); @endphp
                    @foreach ($why_choose_icon as $key => $value)
                    <div class="col-xl-4 col-md-6 mb-4 text-center">
                        <img src="{{ uploaded_asset($value) }}" class="mb-2 h-45px">
                        <div class="text-uppercase fs-15 fw-900 mb-2">{{ json_decode(get_setting('why_choose_title'), true)[$key] }}</div>
                        <div class="opacity-80 ">{{ json_decode(get_setting('why_choose_subtitle'), true)[$key] }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <section class="bg-white py-6">
        <div class="container">
            <div class="d-flex align-items-end mb-5 justify-content-center">
                <span class="text-uppercase fs-20 fw-700">{{ translate('Customer Review') }}</span>
            </div>
            <div class="aiz-carousel gutters-5 dot-small-black" data-items="3" data-xl-items="3" data-lg-items="2"  data-md-items="1" data-sm-items="1" data-xs-items="1" data-dots='true' >
                @if (get_setting('customer_reviews_image') != null)
                    @foreach (json_decode(get_setting('customer_reviews_image'), true) as $key => $value)
                        <div class="carousel-box">
                            <img src="{{ uploaded_asset($value) }}" class="img-fluid w-100">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    @php
        $todays_deal = filter_products(\App\Product::where('published', 1)->where('todays_deal', 1 ))->get();
    @endphp
    @if($todays_deal->count() > 0)
    <section class="bg-white pb-6 border-bottom">
        <div class="container">
            <div class="d-flex align-items-end mb-4 justify-content-between">
                <span class="text-uppercase fs-20 fw-700">{{ translate('Hot Products') }}</span>
            </div>
            <div class="aiz-carousel gutters-5 dot-small-black" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2"  data-autoplay="true" data-dots='true'>
                @foreach ($todays_deal as $key => $product)
                    <div class="carousel-box">
                        @include('frontend.partials.p_box_1',['product'=> $product])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @php
        $blogs = \App\Blog::where('status', 1)->latest()->get()->take(3);
    @endphp
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex align-items-end mb-4 justify-content-between">
                <span class="text-uppercase fs-20 fw-700">{{ translate('From our blog') }}</span>
                <a href="{{ route('blog') }}" class="btn fs-12 btn-primary text-uppercase text-dark rounded-pill fw-600">{{ translate('View All') }}</a>
            </div>
            <div class="aiz-carousel gutters-5 dot-small-black" data-items="3" data-xl-items="3" data-lg-items="2"  data-md-items="2" data-sm-items="1" data-xs-items="1"  data-autoplay="true" data-dots='true'>
                @foreach ($blogs as $key => $blog)
                <div class="carousel-box">
                    <div class="border rounded-bottom mb-3">
                        <a href="{{ route('blog.details', $blog->slug) }}" class="text-reset d-block">
                            <img
                                src="{{ uploaded_asset($blog->banner) }}"
                                class="h-230px img-fit"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                            >
                        </a>
                        <div class="p-4">
                            <a href="{{ route('blog.details', $blog->slug) }}" class="text-reset d-block">
                                <h3 class="fs-16 lh-1-5  fw-600">{{ $blog->title }}</h3>
                            </a>
                            <p class="opacity-70 lh-1-7 mb-4">
                                {{ $blog->short_description }}
                            </p>
                            <a href="{{ route('blog.details', $blog->slug) }}" class="btn btn-primary text-dark fw-600 rounded-pill">
                                {{ translate('View More') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $.post('{{ route('home.section.featured') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });
            $.post('{{ route('home.section.home_categories') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_home_categories').html(data);
            });
        });
    </script>
@endsection