@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">{{translate('Seller Based Selling Report')}}</h1>
	</div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller_sale_report.index') }}" method="GET">
                <div class="form-group row offset-lg-2">
                    <label class="col-md-3 col-form-label">{{translate('Sort by seller')}} :</label>
					<div class="col-md-3">
						<div>
							<input type="text" class="aiz-date-range form-control" name="date" value="{{ $date }}" placeholder="Select Date" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off" />
						</div>
					</div>
                    <div class="col-md-3">
                        <select class="from-control aiz-selectpicker" name="seller_id">
							<option value="">{{ translate('All Sellers') }}</option>
							@foreach (\App\User::where('user_type', 'seller')->get() as $key => $seller)
								<option value="{{ $seller->id }}" @if ($seller->id == $seller_id)
									selected
								@endif >{{ $seller->name }}</option>
							@endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-light" type="submit">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered aiz-table mb-0">
                <thead>
                    <tr>
						<th>{{ translate('Seller') }}</th>
						<th>{{ translate('Order Code') }}</th>
			            <th width="25%">{{ translate('Product') }}</th>
			            <th>{{ translate('Variant') }}</th>
			            <th>{{ translate('Quantity') }}</th>
			            <th>{{ translate('Price') }}</th>
			            <th>{{ translate('Tax') }}</th>
			            <th>{{ translate('Shipping Cost') }}</th>
			            <th>{{ translate('Profit') }}</th>
                    </tr>
                </thead>
                <tbody>
					@foreach ($order_details as $orderDetail)
						<tr>
							<td>
								{{ optional($orderDetail->seller)->name }}
							</td>
							<td>
								{{ optional($orderDetail->order)->code }}
							</td>
							<td>
								{{ optional($orderDetail->product)->name }}
							</td>
							<td>{{ $orderDetail->variation }}</td>
							<td>{{ $orderDetail->quantity }}</td>
							<td>{{ $orderDetail->price }}</td>
							<td>{{ $orderDetail->tax }}</td>
							<td>{{ $orderDetail->shipping_cost }}</td>
							<td>{{ $orderDetail->profit }}</td>
						</tr>
					@endforeach
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">
                {{ $order_details->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
