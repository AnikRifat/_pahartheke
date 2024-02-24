@extends('frontend.layouts.app')
@php
    $citys = \App\City::get(['id', 'name']);

@endphp
@if (!Session::has('cart'))
    @php
        header('Location: ' . URL::to('/'), true, 302);
        exit();
    @endphp
@endif

@section('content')
    <section class="my-5">
        <div class="container text-left">
            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('checkout.easy_order_confirm') }}" class="form-default" role="form" method="POST"
                        id="checkout-form">
                        @csrf
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    Address Info
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-8 col-xl-10 mx-auto">
                                        <div class="row gutters-2">
                                            <div class="col-12 mb-2">
                                                <input type="text" class="form-control"
                                                    onkeyup="getUserByPhone(this.value)" placeholder="Your Phone"
                                                    @if ($user) value="{{ $user->phone }}" @endif
                                                    name="phone" required>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <input type="text" class="form-control"
                                                    @if ($user) value="{{ $user->name }}" @endif
                                                    placeholder="Your Name" name="name" required>
                                            </div>

                                            <div class="col-12 mb-2">
                                                <select name="city" class="form-control city"
                                                    onchange="onCitySelect(this)" placeholder="Select Shipping City"
                                                    required="true">
                                                    <option label="Select Shipping City" value=""></option>
                                                    @foreach ($citys as $city)
                                                        <option value="{{ $city->id }}"> {{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 mb-2">
                                                <input type="text" class="form-control" placeholder="Your Address"
                                                    @if ($user && $user->address) value="{{ $user->addresses[0]->address }}" @endif
                                                    name="address" required>
                                            </div>

                                            <div class="row gutters-10">
                                                @if (\App\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="sslcommerz" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('sslcommerz') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if (\App\BusinessSetting::where('type', 'aamar_pay')->first()->value == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="aamar_pay" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/aamarpay.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('aamarpay') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @php
                                                    $digital = 0;
                                                    if (Session::has('cart')) {
                                                        foreach (Session::get('cart') as $cartItem) {
                                                            if ($cartItem['digital'] == 1) {
                                                                $digital = 1;
                                                            }
                                                        }
                                                    }

                                                @endphp


                                                @if ($digital != 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="cash_on_delivery" class="online_payment"
                                                                type="radio" name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/cod.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center pt-3">
                            <div class="col-6">
                                <a href="{{ route('home') }}" class="link link--style-3">
                                    <i class="las la-arrow-left"></i>
                                    {{ translate('Return to shop') }}
                                </a>
                            </div>
                            <div class="col-6 text-right">
                                <button type="submit" id="order_complete"
                                    class="btn btn-primary fw-600">{{ translate('Complete Order') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div id="card_summary">
                        @include('frontend.partials.ec_cart_summary')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        function onCitySelect(e) {
            $('#order_complete').attr('disabled', true)
            $.get(`/easy-checkout-update-shipping-city/${e.value}`, function(data) {
                $('#card_summary').load(location.href + ' #card_summary');
                $('#order_complete').attr('disabled', false)
            });
        }

        function getUserByPhone(phone) {
            if (phone.length > 10) {
                $("input[name='name']").attr('disabled', true)
                $("input[name='address']").attr('disabled', true)
                $(".city").attr('disabled', true)
                $.get(`/easy-checkout-user/${phone}`, function(data) {
                    $("input[name='name']").attr('disabled', false)
                    $("input[name='address']").attr('disabled', false)
                    $(".city").attr('disabled', false)
                    if (data.user) {
                        $("input[name='name']").val(data.user.name);
                        $("input[name='address']").val(data.user.address);
                        $(".city").val(data.user.city).change();
                        $('.city option').each(function() {
                            if ($(this).text().toLowerCase().trim() == data.user.city.toLowerCase()
                                .trim()) {
                                $(this).prop('selected', true);
                                $(this).trigger('change');
                            } else {
                                $(this).prop('selected', false);
                            }
                        });
                    }

                });
            }
        }
    </script>
@endsection
