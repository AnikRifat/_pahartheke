@php
    $total = 0;
@endphp
{{-- @php
    $discountRules = App\DiscountRule::where('status', 1)
    ->where('expire_date', '>=', date('Y-m-d'))
    ->orderBy('conditon_value', 'desc')
    ->get();

    $orderAmount = 1151;
    $orderQty = 51;
    $discountAmount = 0;
    $shipping_cost = null;
    $messages = [];
    if ($discountRules) {
        foreach ($discountRules as $discountRule) {
            switch ($discountRule->type) {
              case 1: // Free delivery
                if ($discountRule->condition_key == 1) { //Total Amount
                    $message = "Delivery charge free, if <b class='highlight_discount'>Total Order Amount $discountRule->conditon_oprator $discountRule->conditon_value</b> Taka";
                    array_push($messages, $message);
                }elseif($discountRule->condition_key == 2){ // Quantity
                    $message = "Delivery charge free, if <b class='highlight_discount'>Total Order Quantity $discountRule->conditon_oprator $discountRule->conditon_value</b> PCS";
                    array_push($messages, $message);
                }
                break;
              case 2: // Flat discount
                if ($discountRule->condition_key == 1) { //Total Amount
                    $message = "$discountRule->discount_amount Taka Flat discount on total order amount, if <b class='highlight_discount'>Total order Amount $discountRule->conditon_oprator $discountRule->conditon_value</b> Taka";
                    array_push($messages, $message);
                }elseif($discountRule->condition_key == 2){ //Quantity
                    $shipping_cost = 0;
                    $message = "$discountRule->discount_amount Taka Flat discount on total order amount, if <b class='highlight_discount'>total order quantity $discountRule->conditon_oprator $discountRule->conditon_value</b> PCS";
                    array_push($messages, $message);
                }
                break;
              case 3: // Percent discount
                if ($discountRule->condition_key == 1) { //Total Amount
                    $message = "$discountRule->discount_amount % discount on total order amount, if <b class='highlight_discount'>Total Order Amount $discountRule->conditon_oprator $discountRule->conditon_value</b> Taka ";
                    array_push($messages, $message);
                }elseif($discountRule->condition_key == 2){ //Quantity
                    $message = "$discountRule->discount_amount % discount on total order amount, if <b class='highlight_discount'>Total order quantity $discountRule->conditon_oprator $discountRule->conditon_value</b> PCS";
                    array_push($messages, $message);
                }
              break;
            }
        }
    }
@endphp
 --}}
<div class="d-flex align-items-center justify-content-between border-bottom px-3 py-2 bg-white sticky-top position-sticky">
    <h5 class="mb-0 h6 strong-600">
        <i class="la la-shopping-cart"></i>
        @if (Session::has('cart'))
        <span class="">{{ count(Session::get('cart'))}} Item(s)</span>
        @else
            <span class="">0 Item(s)</span>
        @endif
    </h5>
    <button class="btn btn-icon" data-toggle="class-toggle" data-target=".cart-sidebar"><i class="la la-times"></i></button>
</div>
{{-- <div class="cart-offer-wrap border-bottom">
    @forelse ($messages as $message)
        <p>{!! $message !!}</p>
    @empty
        <p>No discount found</p>
    @endforelse
</div> --}}
@if(Session::has('cart') && count($cart = Session::get('cart')) > 0)
    <div class="p-3 flex-grow-1">
        @foreach($cart as $key => $cartItem)
            @php
                $product = \App\Product::find($cartItem['id']);
                $total = $total + ( $cartItem['price'] - $cartItem['discount'] )*$cartItem['quantity'];
            @endphp
            <div class="cart-item d-flex align-items-center">
                <div class="flex-shrink-0 mr-3">
                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="mw-100 size-60px" width="60">
                </div>
                <div class="flex-grow-1 minw-0">
                    <div class="fs-13 text-truncate fw-600">{{ $product->getTranslation('name') }}</div>
                    <div class="my-1 c-base-1 fw-600">{{ single_price($cartItem['price'] - $cartItem['discount']) }} x {{ $cartItem['quantity'] }}</div>
                    <div class="d-flex align-items-center">
                        <button class="btn col-auto btn-icon btn-sm border" type="button" data-type="minus" data-quantity='{{ $cartItem['quantity'] }}' data-key="{{ $key }}" onclick="updateNavCart(this)" @if( $cartItem['quantity'] == 1) disabled @endif style="width: 30px;height: 30px;padding: 5px;">
                            <i class="las la-minus"></i>
                        </button>
                        <span class="mx-3">{{ $cartItem['quantity'] }}</span>
                        <button class="btn col-auto btn-icon btn-sm border" type="button" data-type="plus" data-quantity='{{ $cartItem['quantity'] }}' data-key="{{ $key }}" onclick="updateNavCart(this)" style="width: 30px;height: 30px;padding: 5px;">
                            <i class="las la-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="ml-3">
                    <button class="btn btn-default btn-icon btn-sm border" onclick="removeFromCart({{ $key }})"><i class="la la-trash fs-18"></i></button>
                </div>
            </div>
        @endforeach
        @php
            $cardItems = session('cart', []);
            $suggestedProducts = \App\Product::where('is_suggestion', 1)
                ->whereNotIn('id', array_column($cardItems->toArray(), 'id'))
                ->where('current_stock', '>', 0)
                ->limit(10)->get();
            // $suggestedProducts = \App\Product::whereNotIn('id', array_column($cardItems->toArray(), 'id'))
            //     ->whereIn('category_id', function ($query) use ($cardItems) {
            //         $query->select('category_id')
            //             ->from('products')
            //             ->whereIn('id', $cardItems);
            //     })
            //     ->where('current_stock', '>', 0)
            //     ->limit(2)
            //     ->get();
            // if ($suggestedProducts->isEmpty()) {
            //     $otherCategoryProducts = \App\Product::whereNotIn('id', array_column($cardItems->toArray(), 'id'))
            //         ->where('current_stock', '>', 0)
            //         ->limit(5)
            //         ->get();
            //     $suggestedProducts = $otherCategoryProducts;
            // }
        @endphp
        @if(!$suggestedProducts->isEmpty())
        <div class="suggesion-wrap">
            <h6>Suggestions for you</h6>
            @foreach ($suggestedProducts as $suggestedProduct)
                <div class="cart-item suggesion-item d-flex align-items-center">
                    <div class="flex-shrink-0 mr-3">
                        <img src="{{ uploaded_asset($suggestedProduct->thumbnail_img) }}" class="mw-100 size-60px" width="60">
                    </div>
                    <div class="flex-grow-1 minw-0">
                        <div class="fs-13 text-truncate fw-600">
                            <a href="{{ route('product', $suggestedProduct->slug) }}">{{ $suggestedProduct->getTranslation('name') }}</a>
                        </div>
                        <div class="my-1 c-base-1 fw-600">{{ single_price($suggestedProduct->unit_price) }}</div>
                        <div class="d-flex align-items-center">
                            <span class="mx-3">1</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <button class="btn btn-default btn-icon btn-sm border" data-id="{{ $suggestedProduct->id }}" data-type="plus"
                            data-quantity='1' data-key="{{ $suggestedProduct->id }}" onclick="addToCart(this)"><i class="la la-plus fs-18"></i></button>
                    </div>
                </div>
            @endforeach

        </div>
        @endif
    </div>
    @php
        $discountRules = App\DiscountRule::where('status', 1)
        ->where('expire_date', '>=', date('Y-m-d'))
        ->orderBy('conditon_value',  'asc')
        ->get();


        $total_rules_discount = 0;
    $amount_need_toadd_for_dis = '';
    $selected_rule = null;

    foreach ($discountRules as $discountRule) {
        switch ($discountRule['type']) {
            case 2: // Flat discount
                if ($discountRule['condition_key'] == 1 && $discountRule['conditon_oprator'] == '>' && $discountRule['conditon_value'] <= $total) { // Total Amount
                    $selected_rule = $discountRule;
                }
                break;
            case 3: // Percent discount
                if ($discountRule['condition_key'] == 1 && $discountRule['conditon_oprator'] == '>' && $discountRule['conditon_value'] <= $total) { // Total Amount
                    $selected_rule = $discountRule;
                }
                break;
        }
    }

    if ($selected_rule) {
        switch ($selected_rule['type']) {
            case 2: // Flat discount
                $total_rules_discount = $selected_rule['discount_amount'];
                $amount_need_toadd_for_dis = "You get {$selected_rule['discount_amount']} ৳ discount on $total ৳";
                break;
            case 3: // Percent discount
                $total_rules_discount = floor(($selected_rule['discount_amount'] / 100) * $total);
                $amount_need_toadd_for_dis = "You got {$selected_rule['discount_amount']} % discount on $total ৳";
                break;
        }
    }
@endphp
    <div class="cart-s-offer-wrap">
        <p>{!! $amount_need_toadd_for_dis !!}</p>
        <div class="combar" width="50%"></div>
    </div>
    <div class="bg-white border-top px-3 py-2 sticky-bottom position-sticky" style="z-index: 1000000;">
        <a href="{{ route('checkout.easy_checkout') }}" class="btn btn-primary btn-block" style="z-index: 1100000;">
            <span>Checkout</span>
            <span class="ml-2">({{ single_price($total) }})</span>
        </a>
    </div>
@else
    <div class="p-3 flex-grow-1  text-center">
        <!-- <img src="{{ static_asset('frontend/images/no-shop.jpg') }}" class="img-fluid"> -->
        <h4>Your shopping bag is empty. Start shopping</h4>
    </div>
@endif
