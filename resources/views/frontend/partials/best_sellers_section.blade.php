@if (get_setting('vendor_system_activation') == 1)
    <section class="bg-white pt-5 pb-4">
        <div class="container">
            <div class="separator separator-alter mb-4">
                <span class="bg-white px-5 h4 fw-900">{{ translate('Featured Shops') }}</span>
            </div>
            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="3" data-lg-items="3"  data-md-items="2" data-sm-items="2" data-xs-items="1" data-rows="2" data-autoplay="true">
                @foreach (json_decode(get_setting('featured_shops')) as $key => $value)
                @php
                    $shop = \App\Shop::find($value);
                    $total = 0;
                    $rating = 0;
                    foreach ($shop->user->products as $key => $seller_product) {
                        $total += $seller_product->reviews->count();
                        $rating += $seller_product->reviews->sum('rating');
                    }
                @endphp
                @if ($shop != null)
                    <div class="carousel-box">
                        <div class="row no-gutters box-3 align-items-center bg-alter-3 my-2 has-transition p-2">
                            <div class="col-auto">
                                <a href="{{ route('shop.visit', $shop->slug) }}" class="d-block p-3 size-110px bg-white">
                                    <img
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="@if ($shop->logo !== null) {{ uploaded_asset($shop->logo) }} @else {{ static_asset('assets/img/placeholder.jpg') }} @endif"
                                        alt="{{ $shop->name }}"
                                        class="img-fluid lazyload"
                                    >
                                </a>
                            </div>
                            <div class="col">
                                <div class="px-3 text-left">
                                    <h2 class="h6 fw-600 text-truncate">
                                        <a href="{{ route('shop.visit', $shop->slug) }}" class="text-reset">{{ $shop->name }}</a>
                                    </h2>
                                    <div class="rating rating-sm mb-2">
                                        @if ($total > 0)
                                            {{ renderStarRating($rating/$total) }}
                                        @else
                                            {{ renderStarRating(0) }}
                                        @endif
                                    </div>
                                    <a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-primary btn-sm rounded-0 text-uppercase fs-12">
                                        {{ translate('Visit Store') }} 
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
