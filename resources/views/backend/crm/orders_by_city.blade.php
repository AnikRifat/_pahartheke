@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="aiz-titlebar text-left mt-2 mb-3">
                <div class=" align-items-center">
                    <h1 class="h3">Orders Grouped by City</h1>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>Total Orders</th>
                                <th>Total Sale Amount</th>
                                <th>Total Sale Quanttities</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Sort cities by quantities in descending order
                                arsort($ordersGroupedByCity);
                            @endphp

                            @foreach ($ordersGroupedByCity as $city => $data)
                                <tr>
                                    <td>{{ $city ?? 'Unlocated Order' }}</td>
                                    <td>{{ $data['totalOrders'] }}</td>
                                    <td>{{ format_price($data['totalSaleAmount']) }}</td>
                                    <td>{{ $data['totalOrderQuantity'] }}</td>
                                    <td>
                                        <a href="{{ route('cm.customer.list', $city) }}" class="btn btn-info">View
                                            Customers</a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
