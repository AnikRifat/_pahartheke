<a href="javascript:void(0)" class="d-flex align-items-center text-reset h-100" data-toggle="dropdown" data-display="static">
    <span class="position-relative text-reset btn bg-sm btn-circle btn-icon bg-alter">
        <i class="la la-shopping-cart fs-20 opacity-80"></i>
        <span class="absolute-top-right" style="top: -5px;right: -5px;">
            @if(Session::has('cart'))
                <span class="badge badge-primary badge-inline badge-pill">{{ count(Session::get('cart'))}}</span>
            @else
                <span class="badge badge-primary badge-inline badge-pill">0</span>
            @endif
        </span>
    </span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg p-0 stop-propagation">
    @if(Session::has('cart'))
        @if(count($cart = Session::get('cart')) > 0)
            <div class="p-3 fs-15 fw-600 p-3 border-bottom">
                {{translate('Cart Items')}}
            </div>
            <ul class="h-250px overflow-auto c-scrollbar-light list-group list-group-flush">
                @php
                    $total = 0;
                @endphp
                @foreach($cart as $key => $cartItem)
                    @php
                        $product = \App\Product::find($cartItem['id']);
                        $total = $total + ( $cartItem['price'] - $cartItem['discount'] )*$cartItem['quantity'];
                    @endphp
                    @if ($product != null)
                        <li class="list-group-item">
                            <span class="d-flex align-items-center">
                                <a href="{{ route('product', $product->slug) }}" class="text-reset d-flex align-items-center flex-grow-1">
                                    <img
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                        class="img-fit lazyload size-60px rounded"
                                        alt="{{  $product->getTranslation('name')  }}"
                                    >
                                    <span class="minw-0 pl-2 flex-grow-1">
                                        <span class="fw-600 mb-1 text-truncate-2">
                                                {{  $product->getTranslation('name')  }}
                                        </span>
                                        <span class="">{{ $cartItem['quantity'] }}x</span>
                                        <span class="">{{ single_price($cartItem['price'] - $cartItem['discount']) }}</span>
                                    </span>
                                </a>
                                <span class="">
                                    <button onclick="removeFromCart({{ $key }})" class="btn btn-sm btn-icon stop-propagation">
                                        <i class="la la-close"></i>
                                    </button>
                                </span>
                            </span>
                        </li>
                    @endif
                @endforeach
            </ul>
            <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
                <span class="opacity-60">{{translate('Subtotal')}}</span>
                <span class="fw-600">{{ single_price($total) }}</span>
            </div>
            <div class="px-3 py-2 text-center border-top">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="{{ route('cart') }}" class="btn btn-soft-primary btn-sm">
                            {{translate('View cart')}}
                        </a>
                    </li>
                    @if (Auth::check())
                    <li class="list-inline-item">
                        <a href="{{ route('checkout.easy_checkout') }}" class="btn btn-primary btn-sm">
                            {{translate('Checkout')}}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        @else
            <div class="text-center p-3">
                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                <h3 class="h6 fw-700">{{translate('Your Cart is empty')}}</h3>
            </div>
        @endif
    @else
        <div class="text-center p-3">
            <i class="las la-frown la-3x opacity-60 mb-3"></i>
            <h3 class="h6 fw-700">{{translate('Your Cart is empty')}}</h3>
        </div>
    @endif
</div>
