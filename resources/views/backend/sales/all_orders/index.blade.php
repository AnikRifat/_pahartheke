@extends('backend.layouts.app')

@section('content')
    @php
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
    @endphp
    <div class="card">
        <form class="" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date"
                            placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to "
                            data-advanced-range="true" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search"
                            name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="payment_type" id="payment_type"
                        onchange="sort_orders()">
                        <option value="">{{ translate('Filter by Payment Status') }}</option>
                        <option value="paid"
                            @isset($payment_status) @if ($payment_status == 'paid') selected @endif @endisset>
                            {{ translate('Paid') }}</option>
                        <option value="unpaid"
                            @isset($payment_status) @if ($payment_status == 'unpaid') selected @endif @endisset>
                            {{ translate('Un-Paid') }}</option>
                        <option value="advance"
                            @isset($payment_status) @if ($payment_status == 'advance') selected @endif @endisset>
                            {{ translate('Advcance Paid') }}</option>
                    </select>
                </div>

                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status"
                        onchange="sort_orders()">
                        <option value="">{{ translate('Filter by Deliver Status') }}</option>
                        <option value="pending"
                            @isset($delivery_status) @if ($delivery_status == 'pending') selected @endif @endisset>
                            {{ translate('Pending') }}</option>
                        <option value="confirmed"
                            @isset($delivery_status) @if ($delivery_status == 'confirmed') selected @endif @endisset>
                            {{ translate('Confirmed') }}</option>
                        <option value="on_delivery"
                            @isset($delivery_status) @if ($delivery_status == 'on_delivery') selected @endif @endisset>
                            {{ translate('On delivery') }}</option>
                        <option value="delivered"
                            @isset($delivery_status) @if ($delivery_status == 'delivered') selected @endif @endisset>
                            {{ translate('Delivered') }}</option>
                    </select>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary"value="filter"
                            name="filter">{{ translate('Filter') }}</button>
                    </div>
                </div>

                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-warning" value="export"
                            name="export">{{ translate('Export') }}</button>
                    </div>
                </div>
            </div>
        </form>
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
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <th>{{ translate('Refund') }}</th>
                        @endif
                        <th class="text-right" width="15%">{{ translate('options') }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($orders as $key => $order)
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
                            <span class="badge badge-inline badge-info">{{ count(optional($order->user)->orders ?? []) }}</span>
                            @if (count(optional($order->user)->orders ?? []) < 2)
                                <span class="badge badge-inline badge-success">New</span>
                            @endif
                        </td>
                        <td>
                            {{ single_price(($order->grand_total ?? 0) - ($order->total_discount ?? 0)) }}
                        </td>
                        <td>
                            {{ single_price($order->total_discount ?? 0) }}
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
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="javascript:void(0)"
                                onclick="print_a4_invoice('{{ route('admin.invoice.print_a4', $order->id) }}')"
                                title="{{ translate('Print invoice') }}">
                                <i class="las la-print"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{ route('all_orders.edit', $order->id) }}" title="{{ translate('edit') }}">
                                <i class="las la-pen"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{ route('invoice.download', $order->id) }}"
                                title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>
                            @if (!$order->cancelled)
                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                    href="{{ route('orders.cancel', $order->id) }}" title="{{ translate('Cancel') }}">
                                    <i class="las la-times"></i>
                                </a>
                            @endif
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="{{ route('orders.destroy', $order->id) }}"
                                title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>




                    @endforeach
                </tbody>
            </table>
            @if ($paginate)
            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>
            @endif

        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function print_small_invoice(url) {
            var h = $(window).height();
            var w = $(window).width();
            window.open(url, '_blank', 'height=' + h + ',width=' + w + ',scrollbars=yes,status=no');
        }

        function print_a4_invoice(url) {
            var h = $(window).height();
            var w = $(window).width();
            window.open(url, '_blank', 'height=' + h + ',width=' + w + ',scrollbars=yes,status=no');
        }
    </script>
@endsection
