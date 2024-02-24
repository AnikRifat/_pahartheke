<div class="row">
    <div class="col-xl-6">
        @php
            $subtotal = 0;
            $tax = 0;
            $total_discount = 0;
        @endphp
        @if (Session::has('posCart'))
            <ul class="list-group list-group-flush">
                @forelse (Session::get('posCart') as $key => $cartItem)
                    @php
                        $subtotal += $cartItem['price'] * $cartItem['quantity'];
                        $tax += $cartItem['tax'] * $cartItem['quantity'];
                        $product = \App\Product::find($cartItem['id']);
                    @endphp
                    <li class="list-group-item px-0">
                        <div class="row gutters-10 align-items-center">
                            <div class="col">
                                <div class="d-flex">
                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="img-fit size-60px">
                                    <span class="flex-grow-1 ml-3 mr-0">
                                        <div class="text-truncate-2">{{ $product->name }}</div>
                                        <span
                                            class="span badge badge-inline fs-12 badge-soft-secondary">{{ $cartItem['variant'] }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="fs-14 fw-600 text-right">{{ single_price($cartItem['price']) }}</div>
                                <div class="fs-14 text-right">{{ translate('QTY') }}: {{ $cartItem['quantity'] }}</div>
                                <div class="fs-14 text-right">{{ translate('Discount') }}:

                                    <?php
                                    $the_discount = 0;
                                    if ($cartItem['discount_type'] == 'amount') {
                                        $the_discount = $cartItem['discount'];
                                    } elseif ($cartItem['discount_type'] == 'percent') {
                                        $the_discount = ($cartItem['price'] * $cartItem['quantity'] * $cartItem['discount']) / 100;
                                    } else {
                                        $the_discount = $cartItem['discount'];
                                    }
                                    ?>
                                    <b>{{ single_price($the_discount) }}</b>

                                    <?php $total_discount += $the_discount; ?>

                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">
                        <div class="text-center">
                            <i class="las la-frown la-3x opacity-50"></i>
                            <p>{{ translate('No Product Added') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
        @else
            <div class="text-center">
                <i class="las la-frown la-3x opacity-50"></i>
                <p>{{ translate('No Product Added') }}</p>
            </div>
        @endif
    </div>
    <div class="col-xl-6">
        <div class="pl-xl-4">
            <div class="card mb-4">
                <div class="card-header"><span class="fs-16">{{ translate('Customer Info') }}</span></div>
                <div class="card-body">
                    @if (Session::has('pos_shipping_info') && Session::get('pos_shipping_info')['name'] != null)
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Name') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['name'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Email') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['email'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Phone') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['phone'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Address') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['address'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Country') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['country'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('City') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['city'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between  mb-2">
                            <span class="">{{ translate('Postal Code') }}:</span>
                            <span class="fw-600">{{ Session::get('pos_shipping_info')['postal_code'] }}</span>
                        </div>
                    @else
                        <div class="text-center p-4">
                            {{ translate('No customer information selected.') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Total') }}</span>
                <span>{{ single_price($subtotal) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Tax') }}</span>
                <span>{{ single_price($tax) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Shipping') }}</span>
                <span>{{ single_price(Session::get('pos_shipping', 0)) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Advance Payment') }}</span>
                <span>{{ single_price(Session::get('pos_advance', 0)) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Product wise total discount') }}</span>
                <span>{{ single_price($total_discount) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                <span>{{ translate('Discount on grand total') }}</span>
                <span>{{ single_price(Session::get('pos_discount', 0)) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
                <span>{{ translate('Total') }}</span>
                <span>{{ single_price($subtotal + $tax + Session::get('pos_shipping', 0) - Session::get('pos_discount', 0) - $total_discount - Session::get('pos_advance', 0)) }}</span>
            </div>
        </div>
    </div>
</div>
