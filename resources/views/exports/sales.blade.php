@extends('backend.layouts.app')
@section('content')
<style>

@media print {
* {
    font-size: 8px !important;
}
 }
</style>

    <div class="card">
    
        <div class="card-body">
            <h4 class="text-center">{{ translate('Order Summery') }}{{  }}</h4>
            <table class="table" id="printTable">
                <thead>
                    <tr>
                        <th>{{ translate('Order Code') }}</th>
                        <th>{{ translate('Customer') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th>{{ translate(discount_col_name(1)) }}</th>
                        <th>{{ translate('Delivery Status') }}</th>
                        <th>{{ translate('Payment Status') }}</th>
                        <th>{{ translate('Delivery partner Status') }}</th>
                        <th>{{ translate('comment') }}</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($saleData as $key => $item)
                    <tr>

                        <td>{{ $item->code}}</td>
                        <td>
                            @if ($item->user != null)
                                {{ $item->user->name }} <br>
                                {{ $item->user->phone }}
                            @else
                                Guest ({{ $item->guest_id }}) <br>
                                Guest ({{ $item->phone }})
                            @endif
                        </td>
                        <td>{{ count($item->orderDetails) }}</td>

                        <td>{{ single_price($item->grand_total - $item->total_discount) }}</td>
                        <td>{{ single_price($item->total_discount) }}</td>
                        <td>{{ optional($item->orderDetails->first())->delivery_status }}</td>
                        <td>
                            @if ($item->payment_status == 'paid')
                                <span class="">{{ translate('Paid') }}</span>
                            @elseif($item->payment_status == 'unpaid')
                                <span class="">{{ translate('Unpaid') }}</span>
                            @else
                                <span class="">{{ translate('Advance Paid') }}</span>
                            @endif
                        </td>

                    </tr>

                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total Customers:{{ $data['customer'] }} <br> total orders:{{ $data['orders'] }}  </td>
                        <td>Total </td>
                        <td>{{ $data['purchases'] }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Cash</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Bkash</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
