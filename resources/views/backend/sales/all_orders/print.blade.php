<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            /* Wrap long words */
        }

        th {
            background-color: #f2f2f2;
        }

        .customer-info {
            margin-top: 30px;
        }

        .customer-info h2 {
            margin-bottom: 10px;
        }

        /* Additional styles */
        .blank-field {
            height: 20px;
            /* Adjust height as needed */
        }
    </style>
</head>
@php
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<body>

    <table class="table">
        <thead>
            <tr>
                <th>Total</th>
                <th>Orders</th>
                <th>Customers</th>
                <th>Sale</th>
                <th>Discount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total</td>
                <td>{{ $data['orders'] }}</td>
                <td>{{ $data['customer'] }}</td>
                <td>{{ $data['purchases'] }}</td>
                <td>{{ $data['discounts'] }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table aiz-table mb-0 sticky-table-head">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ translate('Order Code') }}</th>
                <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                <th data-breakpoints="md">{{ translate('Customer') }}</th>
                <th data-breakpoints="md">{{ translate('Customer Lifetime Orders') }}</th>
                <th data-breakpoints="md">{{ translate('Amount') }}</th>
                <th data-breakpoints="md">{{ translate(discount_col_name(1)) }}</th>
                <th data-breakpoints="md">{{ translate('shipping Charge') }}</th>
                <th data-breakpoints="md">{{ translate('Delivery Man') }}</th>
                <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                    <th>{{ translate('Refund') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @foreach ($orders as $key => $order)
                {{-- {{dd($order)}} --}}
                <tr>
                    <td>
                        {{ $key + 1 }}
                    </td>
                    <td>
                        {{ $order->code }}
                        @if ($order->cancelled)
                            <span class="badge badge-inline badge-danger">{{ translate('Cancelled') }}</span>
                        @endif
                    </td>
                    <td>
                        {{ count($order->orderDetails) }}
                    </td>
                    <td>
                        @if ($order->user != null)
                            Name: {{ $order->user->name ?? '' }}
                            <br>
                            Phone: {{ $order->user->phone ?? '' }}
                        @else
                            Guest ({{ $order->guest_id ?? '' }})
                            <br>
                            Guest ({{ $order->phone ?? '' }})
                        @endif
                    </td>
                    <td>
                        <span
                            class="badge badge-inline badge-info">{{ count(optional($order->user)->orders ?? []) }}</span>
                        @if (count(optional($order->user)->orders ?? []) < 2)
                            <span class="badge badge-inline badge-success">(New)</span>
                        @endif
                    </td>
                    <td>
                        {{ single_price(($order->grand_total ?? 0) - ($order->total_discount ?? 0)) }}
                    </td>
                    <td>
                        {{ single_price($order->total_discount) }}
                    </td>
                    <td>
                        {{ single_price($order->orderDetails->first()->shipping_cost) }}
                    </td>
                    <td>
                        @if ($order->deliveryBoy)
                            {{ $order->deliveryBoy->name }}
                            @endif
                    </td>
                    <td>
                        @php
                            $status = optional($order->orderDetails->first())->delivery_status;
                        @endphp
                        {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                    </td>
                    <td>
                        @if ($order->payment_status == 'paid')
                            <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                        @elseif($order->payment_status == 'unpaid')
                            <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                        @else
                            <span class="badge badge-inline badge-info">{{ translate('Advance Paid') }}</span>
                        @endif
                    </td>
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <td>
                            @if (count($order->refund_requests ?? []) > 0)
                                {{ count($order->refund_requests) }} {{ translate('Refund') }}
                            @else
                                {{ translate('No Refund') }}
                            @endif
                        </td>
                    @endif

                </tr>
            @endforeach
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>

{{-- <div class="card">

        <div class="card-body">
            <div class="form-group mt-3 row">
                <div class="col-md-3">
                    <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">Total</span>
                                Orders
                            </div>
                            <div class="h5 fw-700 mb-3"> <span id="sumOfTotalOrders"> {{ $data['orders'] }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">Total</span>
                                Customers
                            </div>
                            <div class="h5 fw-700 mb-3"> <span id="totalCustomers">{{ $data['customer'] }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">Total</span>
                                Sale
                            </div>
                            <div class="h5 fw-700 mb-3"> <span id="sumOfPurchaseAmount">{{ $data['purchases'] }}</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">Total</span>
                                Discount
                            </div>
                            <div class="h5 fw-700 mb-3"> <span id="sumOfDiscount">{{ $data['discounts'] }}</span></div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div> --}}
