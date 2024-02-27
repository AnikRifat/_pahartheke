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
                                <th>Selling Date</th>
                                <th width="30%">Product Name</th>
                                <th>Unit Price</th>
                                {{-- <th>In House Order Quantity <br /> (Product unit like PCS, KG)</th> --}}
                                {{-- <th>Website Order Quantity <br /> (Product unit like PCS, KG)</th> --}}
                                <th>Total Sales Quantity <br /> (Product unit like PCS, KG)</th>
                                <th>Discount</th>
                                <th>Total Sales Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{ date('d M Y', strtotime($sale->updated_at)) }}</td>
                                    <td> <span>{{ $sale->name }}</span> </td>
                                    <td>{{ format_price($sale->unit_price) }}</td>
                                    {{-- <td>
                                        {{ $sale->total_pos_quantity }}
                                        @if (is_numeric($sale->unit))
                                            (Unit not set)
                                        @else
                                            ({{ $sale->unit }})
                                        @endif
                                    </td>
                                    <td>
                                        {{ $sale->total_web_quantity }}
                                        @if (is_numeric($sale->unit))
                                            (Unit not set)
                                        @else
                                            ({{ $sale->unit }})
                                        @endif
                                    </td> --}}
                                    <td>
                                        {{ $sale->quantity }}
                                        @if (is_numeric($sale->unit))
                                            (Unit not set)
                                        @else
                                            ({{ $sale->unit }})
                                        @endif
                                    </td>
                                    <td>
                                        @if ($sale->discount_type == 'percent')
                                            {{ format_price(($sale->total_order_amount / 100) * $sale->discount) }}
                                        @else
                                            {{ format_price($sale->discount) }}
                                        @endif
                                    </td>

                                    <td>{{ format_price($sale->unit_price * $sale->quantity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
