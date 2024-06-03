<?php error_reporting(0); ?>
@extends('backend.layouts.app')

@section('content')
    {{ route('pos.order_place') }}
    <section class="gry-bg py-4 profile">
        <div class="container-fluid">
            <form class="" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row gutters-10">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header d-block">
                                <div class="form-group">
                                    <input class="form-control form-control-sm" type="text" name="keyword"
                                        placeholder="Search by Product Name/Barcode" onkeyup="filterProducts()">
                                </div>
                                <div class="row gutters-5">
                                    <div class="col-md-6">
                                        <select name="poscategory" class="form-control form-control-sm aiz-selectpicker"
                                            data-live-search="true" onchange="filterProducts()">
                                            <option value="">All Categories</option>
                                            @foreach (\App\Category::all() as $key => $category)
                                                <option value="category-{{ $category->id }}">
                                                    {{ $category->getTranslation('name') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="brand" class="form-control form-control-sm aiz-selectpicker"
                                            data-live-search="true" onchange="filterProducts()">
                                            <option value="">All Brands</option>
                                            @foreach (\App\Brand::all() as $key => $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="aiz-pos-product-list c-scrollbar-light">
                                    <div class="row gutters-5" id="product-list">

                                    </div>
                                    <div id="load-more">
                                        <p class="text-center fs-14 fw-600 p-2 bg-soft-primary c-pointer"
                                            onclick="loadMoreProduct()">Load More</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <select name="user_id"
                                            class="form-control form-control-sm aiz-selectpicker pos-customer"
                                            data-live-search="true" onchange="getShippingAddress()">
                                            <option value="">{{ translate('Walk In Customer') }}</option>
                                            @foreach (\App\Customer::all() as $key => $customer)
                                                @if ($customer->user)
                                                    <option value="{{ $customer->user->id }}"
                                                        data-contact="{{ $customer->user->email }}">
                                                        {{ $customer->user->name }} - {{ $customer->user->phone }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-icon btn-soft-dark ml-3"
                                        data-target="#new-customer" data-toggle="modal">
                                        <i class="las la-truck"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card mar-btm" id="cart-details">
                            <div class="card-body">
                                <div class="aiz-pos-cart-list c-scrollbar-light" style="overflow-x: scroll;">
                                    <table class="table aiz-table mb-0 mar-no" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="50%">{{ translate('Product') }}</th>
                                                <th width="15%">{{ translate('QTY') }}</th>
                                                <th>{{ translate('Price') }}</th>
                                                <th>{{ translate('Discount') }}</th>
                                                <th>{{ translate('Discount Type') }}</th>
                                                <th>{{ translate('Subtotal') }}</th>
                                                <th class="text-right">{{ translate('Remove') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $subtotal = 0;
                                                $tax = 0;
                                                $shipping = 0;
                                            @endphp
                                            @if (Session::has('posCart'))
                                                @forelse (Session::get('posCart') as $key => $cartItem)
                                                    @php
                                                        $subtotal += $cartItem['price'] * $cartItem['quantity'];
                                                        $tax += $cartItem['tax'] * $cartItem['quantity'];
                                                        $shipping += $cartItem['shipping'] * $cartItem['quantity'];
                                                        if (Session::get('pos_shipping', 0) == 0) {
                                                            $shipping = 0;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <span class="media">
                                                                <div class="media-left">
                                                                    <img class="mr-3" height="60"
                                                                        src="{{ uploaded_asset(\App\Product::find($cartItem['id'])->thumbnail_img) }}">
                                                                </div>
                                                                <div class="media-body" style="min-width: 150px;">
                                                                    {{ \App\Product::find($cartItem['id'])->name }}
                                                                    ({{ $cartItem['variant'] }})
                                                                </div>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="" style="width: 65px;">
                                                                <input type="number" class="form-control px-0 text-center"
                                                                    placeholder="1" id="qty-{{ $key }}"
                                                                    value="{{ $cartItem['quantity'] }}"
                                                                    onchange="updateQuantity({{ $key }})"
                                                                    min="1">
                                                            </div>
                                                        </td>
                                                        <td>{{ single_price($cartItem['price']) }}</td>
                                                        <td>
                                                            <div class="prod_discount_cont"
                                                                style="width: 75px; display: flex; align-items: center;">
                                                                <span>৳</span>
                                                                <?php
                                                                /*$the_discount = 0;
                                                                if($cartItem['discount_type'] == 'amount'){
                                                                    $the_discount = $cartItem['discount']/$cartItem['quantity'];
                                                                }elseif($cartItem['discount_type'] == 'percent' && $cartItem['discount_percent']){
                                                                    $the_discount = $cartItem['discount_percent']/$cartItem['quantity'];
                                                                }*/
                                                                ?>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $cartItem['discount'] }}" name="discount"
                                                                    id="discount-{{ $key }}"
                                                                    onchange="updateDiscount({{ $key }})">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php //$pord_dis_type = \DB::table('products')->where('id', '=', $cartItem['product_id'])->select('discount_type')->get();
                                                            //echo $pord_dis_type[0]->discount_type;
                                                            ?>
                                                            {{-- {{ $pord_dis_type}} --}}
                                                            <select class="form-control" name="discount_type"
                                                                id="discount_type-{{ $key }}"
                                                                onchange="updateDiscountType({{ $key }})"
                                                                style="width: 120px;">
                                                                <option value="none"
                                                                    @if ($cartItem['discount_type'] == 'none' || $cartItem['discount_type'] == '') : selected @endif>None
                                                                </option>
                                                                <option value="amount"
                                                                    @if ($cartItem['discount_type'] == 'amount') selected @endif>Amount
                                                                </option>
                                                                <option value="percent"
                                                                    @if ($cartItem['discount_type'] == 'percent') selected @endif>
                                                                    Percent</option>
                                                            </select>
                                                        </td>

                                                        <td>{{ single_price($cartItem['price'] * $cartItem['quantity']) }}
                                                        </td>

                                                        <td class="text-right">
                                                            <button type="button"
                                                                class="btn btn-circle btn-icon btn-sm btn-danger"
                                                                onclick="removeFromCart({{ $key }})">
                                                                <i class="las la-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">
                                                            <i class="las la-frown la-3x opacity-50"></i>
                                                            <p>{{ translate('No Product Added') }}</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- <div class="card-footer bord-top">
                            <table class="table mb-0 mar-no" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{translate('Sub Total')}}</th>
                                        <th class="text-center">{{translate('Total Tax')}}</th>
                                        <th class="text-center">{{translate('Total Shipping')}}</th>
                                        <th class="text-center">{{translate('Discount')}}</th>
                                        <th class="text-center">{{translate('Total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ single_price($subtotal) }}</td>
                                        <td class="text-center">{{ single_price($tax) }}</td>
                                        <td class="text-center">{{ single_price(Session::get('pos_shipping', 0)) }}</td>
                                        <td class="text-center">{{ single_price(Session::get('pos_discount', 0)) }}</td>
                                        <td class="text-center">{{ single_price($subtotal + $tax + Session::get('pos_shipping', 0) - Session::get('pos_discount', 0)) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> --}}
                        </div>
                        <div class="pos-footer mar-btm">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <div class="dropdown mr-3 dropup">
                                        <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            {{ translate('Shipping') }}
                                        </button>
                                        <div class="dropdown-menu p-3 dropdown-menu-lg">
                                            <div class="input-group">
                                                <input type="number" min="0" placeholder="Amount"
                                                    name="shipping" class="form-control"
                                                    value="{{ Session::get('pos_shipping', 0) }}" required
                                                    onchange="setShipping()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown dropup">
                                        <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            {{ translate('Discount') }}
                                        </button>
                                        <div class="dropdown-menu p-3 dropdown-menu-lg">
                                            <div class="input-group">
                                                <input type="number" min="0" placeholder="Amount"
                                                    name="discount" class="form-control flat_discount"
                                                    value="{{ Session::get('pos_discount', 0) }}" required
                                                    onchange="setDiscount()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ translate('Flat') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown dropup">
                                        <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            {{ translate('Advance') }}
                                        </button>
                                        <div class="dropdown-menu p-3 dropdown-menu-lg">
                                            <div class="input-group">
                                                <input type="number" min="0" placeholder="Amount" name="advance"
                                                    class="form-control flat_advance advance"
                                                    value="{{ Session::get('pos_advance', 0) }}" required
                                                    onchange="setAdvance()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ translate('Flat') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown dropup">
                                        <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            {{ translate('PreviousDue') }}
                                        </button>
                                        <div class="dropdown-menu p-3 dropdown-menu-lg">
                                            <div class="input-group">
                                                <input type="number" min="0" placeholder="Amount" name="previous_due"
                                                    class="form-control flat_previous_due previous_due"
                                                    value="{{ Session::get('pos_previous_due', 0) }}" required
                                                    onchange="setPreviousDue()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ translate('Flat') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <button type="button" class="btn btn-primary btn-block"
                                        onclick="orderConfirmation()">{{ translate('Place Order') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('modal')
    <!-- Address Modal -->
    <div id="new-customer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{ translate('Shipping Address') }}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form id="shipping_form">
                    <div class="modal-body" id="shipping_address">


                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal"
                        id="close-button">{{ translate('Close') }}</button>
                    <button type="button" class="btn btn-primary btn-styled btn-base-1" id="confirm-address"
                        data-dismiss="modal">{{ translate('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- new address modal -->
    <div id="new-address-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{ translate('Shipping Address') }}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-horizontal" action="{{ route('addresses.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="set_customer_id" value="">
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="address">{{ translate('Address') }}</label>
                                <div class="col-sm-10">
                                    <textarea placeholder="{{ translate('Address') }}" id="address" name="address" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="email">{{ translate('Country') }}</label>
                                <div class="col-sm-10">
                                    <select name="country" id="country" class="form-control aiz-selectpicker" required
                                        data-placeholder="{{ translate('Select country') }}">
                                        @foreach (\App\Country::where('status', 1)->get() as $key => $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="city">{{ translate('City') }}</label>
                                <div class="col-sm-10">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" name="city"
                                        required>
                                        @foreach (\App\City::get() as $key => $city)
                                            <option value="{{ $city->name }}">{{ $city->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label"
                                    for="postal_code">{{ translate('Postal code') }}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{ translate('Postal code') }}"
                                        id="postal_code" name="postal_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="phone">{{ translate('Phone') }}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{ translate('Phone') }}"
                                        id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3"
                            data-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit"
                            class="btn btn-primary btn-styled btn-base-1">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="product-variation" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg">
            <div class="modal-content" id="variants">

            </div>
        </div>
    </div>

    <div id="order-confirm" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-xl">
            <div class="modal-content" id="variants">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{ translate('Order Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="order-confirmation">
                    <div class="p-4 text-center">
                        <i class="las la-spinner la-spin la-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3"
                        data-dismiss="modal">{{ translate('Close') }}</button>
                    <button type="button" onclick="submitOrder('cash')"
                        class="btn btn-styled btn-base-1 btn-primary">{{ translate('Comfirm Order') }}</button>
                </div>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection


@section('script')
    <script type="text/javascript">
        var products = null;

        $(document).ready(function() {
            $('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
            $('#product-list').on('click', '.product-card', function() {
                var id = $(this).data('id');
                $.get('{{ route('variants') }}', {
                    id: id
                }, function(data) {
                    if (data == 1) {
                        addToCart(id, null, 1);
                    } else if (data == 0) {
                        AIZ.plugins.notify('danger', '{{ translate('Out of stock') }}');
                    } else {
                        $('#variants').html(data);
                        $('#product-variation').modal('show');
                    }
                });
            });
            filterProducts();
            getShippingAddress();
        });

        $("#confirm-address").click(function() {
            var data = new FormData($('#shipping_form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('pos.set-shipping-address') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    $('#cart-details').html(data);
                    $('#product-variation').modal('hide');
                }
            })
        });

        function filterProducts() {
            var keyword = $('input[name=keyword]').val();
            var category = $('select[name=poscategory]').val();
            var brand = $('select[name=brand]').val();
            $.get('{{ route('pos.search_product') }}', {
                keyword: keyword,
                category: category,
                brand: brand
            }, function(data) {
                products = data;
                $('#product-list').html(null);
                setProductList(data);
            });
        }

        function loadMoreProduct() {
            if (products != null && products.links.next != null) {
                $.get(products.links.next, {}, function(data) {
                    products = data;
                    setProductList(data);
                });
            }
        }

        function orderConfirmation() {
            $('#order-confirmation').html(
                `<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $('#order-confirm').modal('show');
            $.post('{{ route('pos.getOrderSummary') }}', {
                _token: AIZ.data.csrf
            }, function(data) {
                $('#order-confirmation').html(data);
            });
        }

        function setProductList(data) {
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append('<div class="col-3">' +
                    '<div class="card bg-light c-pointer mb-2 product-card" data-id="' + data.data[i].id + '" >' +
                    '<span class="absolute-top-left bg-dark text-white px-1">' + data.data[i].price + '</span>' +
                    '<img src="' + data.data[i].thumbnail_image +
                    '" class="card-img-top img-fit h-100px mw-100 mx-auto" >' +
                    '<div class="card-body p-2">' +
                    '<div class="text-truncate-2 small">' + data.data[i].name + '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
            }
            if (data.links.next != null) {
                $('#load-more').find('.text-center').html('Load More');
            } else {
                $('#load-more').find('.text-center').html('Nothing more found');
            }
            $('[data-toggle="tooltip"]').tooltip();
        }

        function removeFromCart(key) {
            $.post('{{ route('pos.removeFromCart') }}', {
                _token: '{{ csrf_token() }}',
                key: key
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addToCart(product_id, variant, quantity) {
            $.post('{{ route('pos.addToCart') }}', {
                _token: '{{ csrf_token() }}',
                product_id: product_id,
                variant: variant,
                quantity,
                quantity
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addVariantProductToCart(id) {
            var variant = $('input[name=variant]:checked').val();
            addToCart(id, variant, 1);
        }

        function updateQuantity(key) {
            $.post('{{ route('pos.updateQuantity') }}', {
                _token: '{{ csrf_token() }}',
                key: key,
                quantity: $('#qty-' + key).val()
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateDiscount(key) {
            $.post('{{ route('pos.updateDiscount') }}', {
                _token: '{{ csrf_token() }}',
                key: key,
                discount: $('#discount-' + key).val()
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateDiscountType(key) {
            $.post('{{ route('pos.updateDiscountType') }}', {
                _token: '{{ csrf_token() }}',
                key: key,
                discount_type: $('#discount_type-' + key).val()
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function setDiscount() {
            var discount = $('.flat_discount').val();

            $.post('{{ route('pos.setDiscount') }}', {
                _token: '{{ csrf_token() }}',
                discount: discount
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function setAdvance() {
            var advance = $('.flat_advance').val();
            console.log(advance);
            $.post('{{ route('pos.setAdvance') }}', {
                _token: '{{ csrf_token() }}',
                advance: advance
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }
        function setPreviousDue() {
            var previous_due = $('.flat_previous_due').val();
            console.log(previous_due);
            $.post('{{ route('pos.setPreviousDue') }}', {
                _token: '{{ csrf_token() }}',
                previous_due: previous_due
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function setShipping() {
            var shipping = $('input[name=shipping]').val();
            $.post('{{ route('pos.setShipping') }}', {
                _token: '{{ csrf_token() }}',
                shipping: shipping
            }, function(data) {
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function getShippingAddress() {

            $.post('{{ route('pos.getShippingAddress') }}', {
                _token: '{{ csrf_token() }}',
                id: $('select[name=user_id]').val()
            }, function(data) {
                $('#shipping_address').html(data);
            });
        }

        function add_new_address() {
            var customer_id = $('#customer_id').val();
            $('#set_customer_id').val(customer_id);
            $('#new-address-modal').modal('show');
            $("#close-button").click();
        }

        function submitOrder(payment_type) {
            var user_id = $('select[name=user_id]').val();
            var name = $('input[name=name]').val();
            var email = $('input[name=email]').val();
            var address = $('textarea[name=address]').val();
            var country = $('select[name=country]').val();
            var city = $('input[name=city]').val();
            var postal_code = $('input[name=postal_code]').val();
            var phone = $('input[name=phone]').val();
            var shipping = $('input[name=shipping]:checked').val();
            var discount = $('.flat_discount').val();
            var advance_payment = $('.advance').val();
            var address = $('input[name=address_id]:checked').val();

            $.post('{{ route('pos.order_place') }}', {
                _token: '{{ csrf_token() }}',
                user_id: user_id,
                name: name,
                email: email,
                address: address,
                country: country,
                city: city,
                postal_code: postal_code,
                phone: phone,
                advance_payment: advance_payment,
                shipping_address: address,
                payment_type: payment_type,
                shipping: shipping,
                discount: discount
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Order Completed Successfully.') }}');
                    location.reload();
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
