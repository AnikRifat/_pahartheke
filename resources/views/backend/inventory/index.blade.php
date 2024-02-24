@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Ware House') }}</h1>
            </div>
        </div>
    </div>
    <br>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="" method="GET">

                <div class="row">
                    <div class="col-md-5">
                        @csrf


                        <div class="form-group my-2">
                            <select id="productSelect" class="select2 form-control aiz-selectpicker" name="product_id"
                                data-toggle="select2" data-placeholder="Choose..." data-live-search="true">
                            <option value="">All Product</option>
                                @foreach ($products as $product)

                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}- @php

                                            $qty = 0;
                                            if ($product->variant_product) {
                                                foreach ($product->stocks as $key => $stock) {
                                                    echo $stock->variant . ' - ' . $stock->qty . '<br>';
                                                }
                                            } else {
                                                echo $product->current_stock;
                                            }
                                        @endphp
                                    </option>
                                @endforeach
                            </select>
                        </div>



                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-block btn-primary" value="adjust" name="button">
                            {{ translate('Adjust') }}
                        </button>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <input type="text" class="aiz-date-range form-control" name="date"
                                value="{{ $date }}" placeholder="Select Date" data-format="DD-MM-Y"
                                data-separator=" to " data-advanced-range="true" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-block btn-info" type="submit" value="filter"
                            name="button">{{ translate('Filter') }}</button>
                    </div>

                    {{-- <div class="col-md-2">

                    <button class="btn btn-block btn-light" type="submit" value="export" name="button">{{ translate('Export') }}</button>
                </div> --}}
                    <div class="col-md-1">

                        <button class="btn btn-block btn-light" type="submit" value="reset"
                            name="button">{{ translate('Reset') }}</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    </div>
    <div class="card">
        
        <div class="card-body">
            <table class="table aiz-table mb-0 sticky-table-head">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Product') }}</th>
                        {{-- <th width="50%">{{ translate('Name') }}</th> --}}

                        <th>{{ translate('Sku') }}</th>
                        <th>{{ translate('Type') }}</th>
                        {{-- <th>{{ translate('Current Stock') }}</th> --}}
                        <th>{{ translate('Qantity') }}</th>
                        <th>{{ translate('Added By') }}</th>
                        <th>{{ translate('Date & time') }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($reports as $key => $report)
                        <tr>
                            <td>{{ $key + 1 + ($reports->currentPage() - 1) * $reports->perPage() }}</td>
                            <td>
                                <a href="{{ route('product', $report->product->slug) }}" target="_blank">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <img src="{{ uploaded_asset($report->product->thumbnail_img) }}" alt="Image"
                                                class="w-50px">
                                        </div>
                                        <div class="col-md-8">
                                            <span class="text-muted">{{ $report->product->getTranslation('name') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td><span
                                    class="badge badge-inline badge-light">{{ $report->sku ? $report->sku : 'no varient' }}
                                </span></td>
                            <td>
                                @if ($report->quantity > 0)
                                    <span class="badge badge-inline badge-success">Added</span>
                                @else
                                    <span class="badge badge-inline badge-danger">Decreased</span>
                                @endif
                            </td>
                            {{-- <td>@php

                                $qty = 0;
                                if ($report->product->variant_product) {
                                    foreach ($report->product->stocks as $key => $stock) {
                                        if ($stock->variant == $report->sku) {
                                            echo $stock->variant . ' - ' . $stock->qty . '<br>';
                                        }

                                    }
                                } else {
                                    echo $report->product->current_stock;
                                }
                            @endphp</td> --}}
                            <td>{{ $report->quantity }}</td>
                            <td>{{ $report->user->name }}</td>
                            <td>{{ $report->created_at->format('d M Y') }} <small> {{  $report->created_at->format('h i a') }}</small></td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $reports->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function sort_products() {
            $('#sort_products').submit();
        }
    </script>
@endsection
