<section class="mt-auto py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-7 col-xl-8 col-md-10">
                <div class="d-lg-flex align-items-center">
                    <div class="">
                        <div class="text-uppercase fs-20 ml-2 fw-700">{{ translate('Subscribe Us') }}</div>
                    </div>
                    <form class="ml-lg-3 flex-grow-1" method="POST" action="{{ route('subscribers.store') }}">
                        @csrf
                        <div class="input-group mb-0">
                            <input type="email" class="form-control flex-grow-1 mb-0"
                                placeholder="{{ translate('Your Email Here') }}" name="email" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary bg-alter border-alter text-dark">
                                    {{ translate('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    </form>
                </div>
            </div>

        </div>
</section>

<section class="bg-primary py-5 text-light footer-widget bg-cover bg-center"
    style="background-image: url('{{ uploaded_asset(get_setting('footer_bg')) }}');">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-xl-4 text-center text-md-left">
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="d-block mb-3">
                        @if (get_setting('footer_logo') != null)
                            <img class="lazyload" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                data-src="{{ uploaded_asset(get_setting('footer_logo')) }}" alt="{{ env('APP_NAME') }}"
                                height="100">
                        @else
                            <img class="lazyload" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                data-src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                height="100">
                        @endif
                    </a>
                    <div class="mb-4">
                        @php
                            echo get_setting('about_us_description');
                        @endphp
                    </div>
                    <div>
                        <ul class="list-inline social colored ">
                            @if (get_setting('facebook_link') != null)
                                <li class="list-inline-item">
                                    <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i
                                            class="lab la-facebook-f"></i></a>
                                </li>
                            @endif
                            @if (get_setting('twitter_link') != null)
                                <li class="list-inline-item">
                                    <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i
                                            class="lab la-twitter"></i></a>
                                </li>
                            @endif
                            @if (get_setting('instagram_link') != null)
                                <li class="list-inline-item">
                                    <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i
                                            class="lab la-instagram"></i></a>
                                </li>
                            @endif
                            @if (get_setting('youtube_link') != null)
                                <li class="list-inline-item">
                                    <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i
                                            class="lab la-youtube"></i></a>
                                </li>
                            @endif
                            @if (get_setting('linkedin_link') != null)
                                <li class="list-inline-item">
                                    <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i
                                            class="lab la-linkedin-in"></i></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 ml-xl-auto mr-0">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 mb-4">
                        {{ get_setting('widget_one') }}
                    </h4>
                    <ul class="list-unstyled">
                        @if (get_setting('widget_one_labels') != null)
                            @foreach (json_decode(get_setting('widget_one_labels'), true) as $key => $value)
                                <li class="mb-2">
                                    <a href="{{ json_decode(get_setting('widget_one_links'), true)[$key] }}"
                                        class="opacity-90 hov-opacity-100 text-reset">
                                        {{ $value }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 mb-4">
                        {{ get_setting('widget_two') }}
                    </h4>
                    <ul class="list-unstyled">
                        @if (get_setting('widget_two_labels') != null)
                            @foreach (json_decode(get_setting('widget_two_labels'), true) as $key => $value)
                                <li class="mb-2">
                                    <a href="{{ json_decode(get_setting('widget_two_links'), true)[$key] }}"
                                        class="opacity-90 hov-opacity-100 text-reset">
                                        {{ $value }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 mb-4">
                        {{ translate('Contact Us') }}
                    </h4>
                    <div class="mb-3">
                        <span class="d-block opacity-60 text-uppercase">{{ translate('Address') }}:</span>
                        <span class="d-block opacity-90">{{ get_setting('contact_address') }}</span>
                    </div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.7707805013583!2d90.36413257528028!3d23.755552078666835!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c1d94de8c4e9%3A0xc208080b6f5ff2d0!2sPahar%20Theke!5e0!3m2!1sbn!2sbd!4v1702567953733!5m2!1sbn!2sbd"
                    height="300" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="mb-2">
                        <div class="rounded-pill bg-alter p-1 d-inline-block">
                            <div class="d-flex align-items-center">
                                <i
                                    class="las la-phone fs-18 size-35px bg-alter-2 rounded-circle d-flex align-items-center justify-content-center"></i>
                                <span class="text-dark mx-2 fs-17 fw-600">
                                    {{ translate('Hotline') }}:
                                    {{ get_setting('contact_phone') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="rounded-pill bg-alter p-1 d-inline-block">
                            <div class="d-flex align-items-center">
                                <i
                                    class="las la-envelope fs-18 size-35px bg-alter-2 rounded-circle d-flex align-items-center justify-content-center"></i>
                                <span class="text-dark mx-2 fs-17 fw-600">
                                    {{ get_setting('contact_email') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <img src="https://pahartheke.com/assets/img/footer-pg.png" class="w-100" alt="">
</section>

<div class="aiz-mobile-bottom-nav d-xl-none fixed-bottom bg-white shadow-lg border-top">
    <div class="d-flex justify-content-around align-items-center">
        <a href="{{ route('home') }}"
            class="flex-grow-1 text-center py-2 text-reset {{ areActiveRoutes(['home'], 'text-alter') }}">
            <i class="las la-home fs-20"></i>
            <span class="d-block fs-11 fw-600">{{ translate('Home') }}</span>
        </a>
        <a href="javascript:void(0)"
            class="text-reset flex-grow-1 text-center py-2 text-reset mobile-category-trigger"
            data-toggle="class-toggle" data-target=".mobile-category-sidebar" data-same=".category-trigger">
            <i class="las la-list-ul fs-20"></i>
            <span class="d-block fs-11 fw-600">{{ translate('Categories') }}</span>
        </a>
        <a href="javascript:void(0)" class="flex-grow-1 text-center py-2 text-reset " data-toggle="class-toggle"
            data-target=".cart-sidebar" data-same=".cart-trigger">
            <i class="las la-shopping-cart fs-20"></i>
            <span class="d-block fs-11 fw-600">
                {{ translate('Cart') }}
                @if (Session::has('cart'))
                    (<span class="total-cart">{{ count(Session::get('cart')) }}</span>)
                @else
                    (<span class="total-cart">0</span>)
                @endif
            </span>
        </a>
        @if (Auth::check())
            @if (isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-reset flex-grow-1 text-center py-2">
                    <span class="d-block mx-auto mb-1">
                        @if (Auth::user()->photo != null)
                            <img src="{{ custom_asset(Auth::user()->avatar_original) }}"
                                class="rounded-circle size-20px">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                class="rounded-circle size-20px">
                        @endif
                    </span>
                    <span class="d-block fs-11 fw-600">{{ translate('Account') }}</span>
                </a>
            @else
                <a href="javascript:void(0)" class="text-reset flex-grow-1 text-center py-2 mobile-side-nav-thumb"
                    data-toggle="class-toggle" data-target=".aiz-mobile-side-nav">
                    <span class="d-block mx-auto mb-1">
                        @if (Auth::user()->photo != null)
                            <img src="{{ custom_asset(Auth::user()->avatar_original) }}"
                                class="rounded-circle size-20px">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}"
                                class="rounded-circle size-20px">
                        @endif
                    </span>
                    <span class="d-block fs-11 fw-600">{{ translate('Account') }}</span>
                </a>
            @endif
        @else
            <a href="{{ route('user.login') }}" class="text-reset flex-grow-1 text-center py-2">
                <span class="d-block mx-auto mb-1">
                    <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="rounded-circle size-20px">
                </span>
                <span class="d-block fs-11 fw-600">{{ translate('Account') }}</span>
            </a>
        @endif
    </div>
</div>
@if (Auth::check() && !isAdmin())
    <div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
        <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle"
            data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb"></div>
        <div class="collapse-sidebar bg-white">
            @include('frontend.inc.user_side_nav')
        </div>
    </div>
@endif

<div class="mobile-category-sidebar collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
    <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle"
        data-target=".mobile-category-sidebar" data-same=".mobile-category-trigger"></div>
    <div class="collapse-sidebar bg-white">
        <div class="pt-4 position-relative z-1 shadow-sm">
            <div class="px-3">
                <h4 class="fw-600 h5">{{ translate('Categories') }}</h4>
            </div>
            <div class="absolute-top-right">
                <button class="btn btn-sm p-2" data-toggle="class-toggle" data-target=".mobile-category-sidebar"
                    data-same=".mobile-category-trigger">
                    <i class="las la-times la-2x"></i>
                </button>
            </div>
            <div>
                @foreach (\App\Category::where('level', 0)->orderBy('name', 'asc')->get() as $key => $category)
                    <div class="mt-3">
                        <div class="px-3 py-2 border-bottom fs-16 fw-600">
                            <a href="{{ route('products.category', $category->slug) }}"
                                class="text-reset">{{ $category->getTranslation('name') }}</a>
                        </div>
                        <div class="px-3">
                            @foreach (\App\Utility\CategoryUtility::get_immediate_children_ids($category->id) as $key => $first_level_id)
                                <div class="">
                                    <h6 class="mb-3"><a class="text-reset fw-600 fs-14"
                                            href="{{ route('products.category', \App\Category::find($first_level_id)->slug) }}">{{ \App\Category::find($first_level_id)->getTranslation('name') }}</a>
                                    </h6>
                                    <ul class="mb-3 list-unstyled pl-2">
                                        @foreach (\App\Utility\CategoryUtility::get_immediate_children_ids($first_level_id) as $key => $second_level_id)
                                            <li class="mb-2">
                                                <a class="text-reset"
                                                    href="{{ route('products.category', \App\Category::find($second_level_id)->slug) }}">{{ \App\Category::find($second_level_id)->getTranslation('name') }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if (get_setting('header_menu_label') != null)
                    <div class="mt-5">
                        <div class="px-3">
                            <h4 class="fw-600 h5">{{ translate('Menu') }}</h4>
                        </div>
                        @php $header_menus = json_decode(get_setting('header_menu_label'), true);  @endphp
                        <ul class="mb-0 list-group list-group-flush">
                            @foreach ($header_menus as $key => $value)
                                <li class="list-group-item px-3">
                                    <a class="text-reset fw-600 py-2"
                                        href="{{ json_decode(get_setting('header_menu_link'), true)[$key] }}">{{ $value }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="sidebar-cart">

    @php
        $total = 0;
    @endphp
    @if (Session::has('cart'))
        @if (count($cart = Session::get('cart')) > 0)
            @foreach ($cart as $key => $cartItem)
                @php
                    $total = $total + ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
                @endphp
            @endforeach
        @endif
    @endif
    <button class="cart-toggler text-dark cart-trigger bg-base-1 rounded-left text-center px-3 z-1021" type="button"
        data-toggle="class-toggle" data-target=".cart-sidebar" style="min-width: 72px">
        <span class="d-inline-block position-relative">
            <i class="la la-shopping-cart la-2x text-dark pr-1"></i>
            <span
                class="absolute-top-right badge bg-alter badge-inline badge-pill text-dark fw-700 mr-n1 shadow-md total-cart">
                @if (Session::has('cart'))
                    {{ count(Session::get('cart')) }}
                @else
                    0
                @endif
            </span>
        </span>
        <span
            class="d-block fs-10 border-top lh-1 pt-1 border-top border-gray-500 opacity-50">{{ translate('Total') }}</span>
        <span class="d-block strong-700 c-base-1">
            <span class="">{{ currency_symbol() }}</span><span class="total-price">{{ $total }}</span>
        </span>
    </button>
    <div class="collapse-sidebar-wrap sidebar-all sidebar-right z-1035 cart-sidebar">
        <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle" data-target=".cart-sidebar"
            data-same=".cart-trigger"></div>
        <div class="bg-white d-flex flex-column shadow-lg cart-sidebar collapse-sidebar c-scrollbar-light"
            id="sidebar-cart">
            @include('frontend.partials.sidebar_cart')
        </div>
    </div>
</div>
