@extends('backend.layouts.app')
@section('content')
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="aiz-titlebar text-left mt-2 mb-3">
                <div class=" align-items-center">
                    <h1 class="h3">Product wise sales report</h1>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <form action="" method="GET" class="d-block flex-1 w-full flex-fill">
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{-- <label class="col-md-3 col-form-label">Select product</label> --}}
                                <select class="select2 form-control aiz-selectpicker" name="product_id"
                                    data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                    <option value="0">Select product</option>
                                    @foreach ($all_products as $all_product)
                                        <option value="{{ $all_product->id }}"
                                            @if (request()->product_id == $all_product->id) selected @endif>{{ $all_product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <input type="text" class="aiz-date-range form-control" name="date"
                                        value="{{ request()->date }}" placeholder="Select Date" data-format="DD-MM-Y"
                                        data-separator=" to " data-advanced-range="true" autocomplete="off" />
                                </div>
                            </div>

                            <div class="col-md-4 text-right">
                                <button class="btn btn-light" type="submit">{{ translate('Filter') }}</button>
                                <a href="{{ route('sale_report.product_wise') }}" class="btn btn-light">Reset</a>
                                <button class="btn btn-light" type="submit" value="export"
                                    name="export">{{ translate('Export') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">



                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th width="30%">Product Name</th>
                                <th>Quantity</th>
                                <th>Total Sales pos</th>
                                <th>Total Sales web</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPosSales = 0;
                                $totalWebSales = 0;
                                $totalSales = 0;
                            @endphp
                            @foreach ($sales as $sale)
                                @if ($sale['product_name'])
                                    @php
                                        $totalPosSales += $sale['pos'];
                                        $totalWebSales += $sale['web'];
                                        $totalSales += $sale['total_price_sale'];
                                    @endphp
                                    <tr>
                                        <td>{{ $sale['product_name'] }}</td>
                                        <td>{{ $sale['total_quantity'] }} ({{ $sale['unit'] }})</td>
                                        <td>{{ format_price($sale['pos']) }}</td>
                                        <td>{{ format_price($sale['web']) }}</td>
                                        <td>{{ format_price($sale['total_price_sale']) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                     
                        </tbody>
                    </table>

                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>Total Sales pos</th>
                                <th>Total Sales web</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td><strong>{{ format_price($totalPosSales) }}</strong></td>
                                <td><strong>{{ format_price($totalWebSales) }}</strong></td>
                                <td><strong>{{ format_price($totalSales) }}</strong></td>
                            </tr>

                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
@endsection
