<!-- Top Bar -->
<div class="top-navbar bg-alter-3 text-white z-1021">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-7 col-6">
                <div class="fs-14 opacity-90 fw-500">{{ get_setting('topbar_left') }}</div>
            </div>

            <div class="col-5 text-right">
                <ul class="list-inline mb-0">
                    @auth
                        @if(isAdmin())
                            <li class="list-inline-item mr-3">
                                <a href="{{ route('admin.dashboard') }}" class="text-reset py-2 d-inline-block opacity-90">{{ translate('My Panel')}}</a>
                            </li>
                        @else
                            <li class="list-inline-item mr-3">
                                <a href="{{ route('dashboard') }}" class="text-reset py-2 d-inline-block opacity-90">{{ translate('My Panel')}}</a>
                            </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="{{ route('logout') }}" class="text-reset py-2 d-inline-block opacity-90">{{ translate('Logout')}}</a>
                        </li>
                    @else
                        <li class="list-inline-item mr-3">
                            <a href="{{ route('user.login') }}" class="text-reset py-2 d-inline-block opacity-90">{{ translate('Login')}}</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('user.registration') }}" class="text-reset py-2 d-inline-block opacity-90">{{ translate('Registration')}}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END Top Bar -->
<header class="@if(get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white shadow-sm">
    <div class="position-relative logo-bar-area">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-xl d-flex align-items-center align-self-stretch">
                    <div class="align-self-stretch category-menu-icon-box mr-3 h-100 d-none d-xl-block">
                        <div class="h-100 d-flex align-items-center" id="category-menu-icon">
                            <div class="dropdown-toggle navbar-light bg-primary h-100 rounded-0 c-pointer px-4">
                                <span class="text-uppercase ml-3 text-dark fw-600">{{ translate('All Categories') }}</span>
                            </div>
                        </div>
                    </div>
                    @if (get_setting('header_menu_label') != null)
                    <div class="mr-auto d-none d-xl-block">
                        @php $header_menus = json_decode(get_setting('header_menu_label'), true);  @endphp
                        <ul class="mb-0 list-inline">
                            @foreach ($header_menus as $key => $value)
                            <li class="list-inline-item">
                                <a class="text-reset fw-600 px-3 py-2" href="{{ json_decode(get_setting('header_menu_link'), true)[$key] }}">{{ $value }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="col d-xl-none mr-auto mr-0">
                    <a class="px-2 py-3 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".mobile-category-sidebar" data-same=".category-trigger">
                        <i class="las la-list-ul la-2x"></i>
                    </a>
                </div>

                <div class="col-auto mx-auto">
                    <a class="d-block mb-n5 bg-white px-15px py-20px rounded-circle shadow-sm" href="{{ route('home') }}">
                        @php
                            $header_logo = get_setting('header_logo');
                        @endphp
                        @if($header_logo != null)
                            <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 mt-n1 h-60px h-md-70px" height="70">
                        @else
                            <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 mt-n1 h-60px h-md-70px" height="70">
                        @endif
                    </a>
                </div>
                <div class="col d-xl-none ml-auto mr-0 text-right">
                    <a class="px-2 py-3 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>

                <div class="col col-xl front-header-search d-flex align-items-center bg-white">
                    <div class="position-relative flex-grow-1">
                        <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                            <div class="d-flex position-relative align-items-center">
                                <div class="d-xl-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="border-0 border-lg form-control" id="search" name="q" placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-primary text-dark" type="submit">
                                            <i class="la la-search la-flip-horizontal fs-18"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader"><div></div><div></div><div></div></div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-content" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3" id="hover-category-menu">
        <div class="container">
            <div class="row gutters-10 position-relative">
                <div class="col-lg-3 position-static">
                    @include('frontend.partials.category_menu')
                </div>
            </div>
        </div>
    </div>
</header>
