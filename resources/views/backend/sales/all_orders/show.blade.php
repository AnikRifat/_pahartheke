@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="h2">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
            </div>
            @php
                $delivery_status = optional($order->orderDetails->first())->delivery_status;
                $payment_status = optional($order->orderDetails->first())->payment_status;
            @endphp
            <div class="col-md-3 ml-auto">
                <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="update_payment_status">
                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid') }}
                    </option>
                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid') }}
                    </option>
                    <option value="advance" @if ($payment_status == 'advance') selected @endif>
                        {{ translate('Advance Paid') }}
                    </option>
                </select>
            </div>
            <div class="col-md-3 ml-auto">
                <label for='update_delivery_status'>{{ translate('Delivery Status') }}</label>
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="update_delivery_status">
                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{ translate('Pending') }}
                    </option>
                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{ translate('Confirmed') }}
                    </option>
                    <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>
                        {{ translate('On delivery') }}</option>
                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{ translate('Delivered') }}
                    </option>
                </select>
            </div>
        </div>
        <form action="{{ route('all_orders.update_price', $order->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="card-header row gutters-6">
                    <div class="col text-center text-md-left">
                        <address>
                            <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                            {{ json_decode($order->shipping_address)->email }}<br>
                            {{ json_decode($order->shipping_address)->phone }}<br>
                            {{ json_decode($order->shipping_address)->address }}
                            {{ json_decode($order->shipping_address)->city }},
                            {{ json_decode($order->shipping_address)->postal_code }}
                            <br>
                            {{ json_decode($order->shipping_address)->country }}
                        </address>
                        @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                            <br>
                            <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                            {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                            {{ translate('Amount') }}:
                            {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                            {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                            <br>
                            <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}"
                                target="_blank"><img
                                    src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}"
                                    alt="" height="100"></a>
                        @endif
                    </div>
                    <div class="col-md-4 ml-auto">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="text-main text-bold">{{ translate('Order #') }}</td>
                                    <td class="text-right text-info text-bold"> {{ $order->code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                                    @php
                                        $status = optional($order->orderDetails->first())->delivery_status;
                                    @endphp
                                    <td class="text-right">
                                        @if ($status == 'delivered')
                                            <span
                                                class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                        @else
                                            <span
                                                class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-main text-bold">{{ translate('Order Date') }} </td>
                                    <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-main text-bold">{{ translate('Total amount') }} </td>
                                    <td class="text-right">
                                        {{ single_price($order->grand_total - $order->total_discount) }}
                                    </td>
                                </tr>
                                @if ($order->advance_payment > 0)
                                    <tr>
                                        <td class="text-main text-bold">{{ translate('Advance Paid') }} </td>
                                        <td class="text-right">
                                            {{ single_price($order->advance_payment) }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                                    <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr class="new-section-sm bord-no">
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered invoice-summary">
                            <thead>
                                <tr class="bg-trans-dark">
                                    <th class="min-col">#</th>
                                    <th width="10%">{{ translate('Photo') }}</th>
                                    <th class="text-uppercase">{{ translate('Description') }}</th>
                                    <th class="text-uppercase">{{ translate('Delivery Type') }}</th>
                                    <th class="min-col text-center text-uppercase">{{ translate('Qty') }}</th>
                                    <th class="min-col text-center text-uppercase">{{ translate('Price') }}</th>
                                    <th class="min-col text-center text-uppercase">{{ translate('Discount') }}</th>
                                    <th class="min-col text-center text-uppercase">{{ translate('Discount Type') }}</th>
                                    <th class="min-col text-right text-uppercase">{{ translate('Total') }}</th>
                                    {{-- <th class="min-col text-right text-uppercase">{{translate('Action')}}</th> --}}

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $key => $orderDetail)
                                    {{-- old  form here --}}
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        {{-- <input type="hidden" id ="orderDetailsID" name="orderDetailsID" value="{{ $orderDetail->id }}"> --}}
                                        <td>
                                            @if ($orderDetail->product != null)
                                                <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank"><img height="50"
                                                        src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                            @else
                                                <strong>{{ translate('N/A') }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($orderDetail->product != null)
                                                <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                        target="_blank"
                                                        class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                                <small>{{ $orderDetail->variation }}</small>
                                            @else
                                                <strong>{{ translate('Product Unavailable') }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                                {{ translate('Home Delivery') }}
                                            @elseif ($orderDetail->shipping_type == 'pickup_point')
                                                @if ($orderDetail->pickup_point != null)
                                                    {{ $orderDetail->pickup_point->getTranslation('name') }}
                                                    ({{ translate('Pickup Point') }})
                                                @else
                                                    {{ translate('Pickup Point') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <input type="text" value="{{ $orderDetail->quantity }}" id ="quantity"
                                                class="form-control" name="quantity[]" required>
                                        </td>
                                        <td class="text-center">
                                            {{ single_price($orderDetail->price / $orderDetail->quantity) }}
                                        </td>
                                        <td>
                                            <div class="prod_discount_cont">
                                                <span>৳</span>
                                                <?php
                                                $the_discount = 0;
                                                if ($orderDetail->discount_type == 'amount') {
                                                    $the_discount = $orderDetail->discount;
                                                } elseif ($orderDetail->discount_type == 'percent' && $orderDetail->discount_percent) {
                                                    $the_discount = $orderDetail->discount_percent;
                                                }

                                                ?>
                                                <input type="text" class="form-control" value="{{ $the_discount }}"
                                                    name="discount[]">
                                            </div>
                                        </td>
                                        <td>
                                            <?php $pord_dis_type = \DB::table('products')
                                                ->where('id', '=', $orderDetail->product_id)
                                                ->select('discount_type')
                                                ->get();
                                            //echo $pord_dis_type[0]->discount_type;
                                            ?>
                                            {{-- {{ $pord_dis_type}} --}}
                                            <select class="form-control" name="discount_type[]">
                                                <option value="none" @if ($orderDetail->discount_type == 'none' || $orderDetail->discount_type == '') : selected @endif>
                                                    None</option>
                                                <option value="amount" @if ($orderDetail->discount_type == 'amount') selected @endif>
                                                    Amount</option>
                                                <option value="percent" @if ($orderDetail->discount_type == 'percent') selected @endif>
                                                    Percent</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            {{ single_price($orderDetail->price) }}

                                            <input type="hidden"
                                                value="{{ $orderDetail->price / $orderDetail->quantity }}"
                                                name="prod_price[]">
                                            <input type="hidden" value="{{ $orderDetail->id }}" name="order_details_id[]">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Product Order Note') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-2 col-from-label">{{ translate('Order Note') }}</label>
                                    <div class="col-md-10">
                                        <textarea class="aiz-text-editor" name="order_note">
                                            {!! $order->order_note !!}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix float-right">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->orderDetails->sum('price')) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Tax') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->orderDetails->sum('tax')) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                                </td>
                                <td>
                                    <input type="text" name="shipping_cost" class="form-control"
                                        value="{{ $delivery_charge->shipping_cost }}">
                                    {{-- {{ single_price(round($delivery_charge->shipping_cost)) }} --}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Advance Paid') }} :</strong>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="advance_payment"
                                        value="{{ $order->advance_payment }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('previous due') }} :</strong>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="previous_due_payment"
                                        value="{{ $order->previous_due_payment }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Product wise total discount') }} :</strong>
                                </td>
                                <td>
                                    {{ single_price($order->total_discount) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Discount on grand total') }} :</strong>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="coupon_discount"
                                        value="{{ $order->coupon_discount }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ single_price($order->grand_total - $order->total_discount - $order->advance_payment) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    {{-- <input type="hidden" value="{{ ($order->discount) }}" name="old_discount">
                            <input type="hidden" value="{{ ($order->grand_total) }}" name="old_total">
                            <input type="hidden" value="{{ ($orderDetail->price) }}" name="old_price">
                            <input type="hidden" value="{{ $orderDetail->price/$orderDetail->quantity }}" name="price"> --}}
                                    {{-- <input type="text" value="{{ $delivery_charge->shipping_cost }}" name="shipping_cost"> --}}
                                    <div class="form-group mb-0 text-right">
                                        <button type="submit"
                                            class="btn btn-block btn-primary">{{ translate('Update') }}</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right no-print">
                        <a href="javascript:void(0)" type="button" class="btn btn-icon btn-light"
                            onclick="print_a4_invoice('{{ route('admin.invoice.print_a4', $order->id) }}')"><i
                                class="las la-print"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            });
        });
        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        });

        function print_a4_invoice(url) {
            var h = $(window).height();
            var w = $(window).width();
            window.open(url, '_blank', 'height=' + h + ',width=' + w + ',scrollbars=yes,status=no');
        }
    </script>
@endsection


@section('custom-css')
    .prod_discount_cont{
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
    }
    .prod_discount_cont input{
    max-width: 110px;
    }
@endsection
