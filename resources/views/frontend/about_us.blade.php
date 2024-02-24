@extends('frontend.layouts.app')
@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">About Us</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('about_us') }}">"About Us"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
		    

@if (get_setting('home_about_us_description') != null)
<section class="py-6" style="background-image: url({{ static_asset('assets/img/bg.png') }});background-position: bottom center;background-repeat: repeat-x;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg col-xl-5 offset-xl-1">
                <h2 class="text-uppercase fs-24 fw-700 mb-2">{{ translate('About Us') }}</h2>
                <div class="mb-3 fs-15">{!! get_setting('home_about_us_description') !!}</div>
                <a href="{{ get_setting('home_about_button_link') }}" class="mb-3 btn btn-primary btn-pill btn-circle text-uppercase fw-600 text-dark">{{ get_setting('home_about_button') }}</a>
            </div>
            <div class="col-lg col-xl-5">
                <div class="embed-responsive embed-responsive-4by3 rounded">
                    <iframe
                        class="embed-responsive-item"
                        sandbox="allow-scripts allow-same-origin"
                        frameborder="0"
                        allowfullscreen="1"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        title="YouTube video player"
                        width="100%"
                        height="100%"
                        src="https://www.youtube.com/embed/{{ get_setting('home_about_image') }}?controls=0&playsinline=1&modestbranding=0&rel=0&loop=1&autoplay=1&mute=0&enablejsapi=1&playlist={{ get_setting('home_about_image') }}"
                    ></iframe>

                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
