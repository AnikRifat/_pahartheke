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