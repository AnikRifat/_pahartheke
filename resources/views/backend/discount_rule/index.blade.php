@extends('backend.layouts.app')
@section('content')
<div class="row">
	<div class="col-xl-12 mx-auto">
		<div class="aiz-titlebar text-left mt-2 mb-3">
			<div class=" align-items-center">
				{{-- <h1 class="h3">Product wise sales report</h1> --}}
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<span class="h6 mb-0">Discount Rule</span>
				<div>
					<div class="form-group row">
						<div class="col-md-12">
							<a href="{{ route('discount.create') }}" class="btn btn-primary">Add New Rule</a>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-bordered aiz-table mb-0">
					<thead>
						<tr>
							<th width="20%">Rule Name</th>
							{{-- <th>Order By</th> --}}
							<th>Discount Type</th>
							<th>Discount Amount</th>
							<th>Condition On</th>
							<th>Condition Type</th>
							<th>Condition Value</th>
							<th>Expire Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($discountRules as $discountRule)

							<tr>
								<td>{{ $discountRule->name }}</td>
								{{-- <td>{{ $discountRule->order_by }}</td> --}}
								<td>
									@if($discountRule->type == 1)
										<span>Free Delivery</span>
									@elseif($discountRule->type == 2)
										<span>Flat Discount</span>
									@elseif($discountRule->type == 3)
										<span>Percent Discount</span>
									@endif
								</td>
								<td>{{ $discountRule->discount_amount }}</td>
								<td>
									@if($discountRule->condition_key == 1)
										<span>Total Amount</span>
									@elseif($discountRule->condition_key == 2)
										<span>Quantity</span>
									@endif
								</td>
								<td>{{ $discountRule->conditon_oprator }}</td>
								<td>{{ $discountRule->conditon_value }}</td>
								<td>
									@if($discountRule->expire_date >= date('Y-m-d'))
										<span class="text-success">{{ $discountRule->expire_date }}</span>
									@else
										<span class="text-danger">{{ $discountRule->expire_date }}</span>
									@endif
								</td>
								<td>
									@if($discountRule->status == 1)
										<span class="text-success">Active</span>
									@else
										<span>InActive</span>
									@endif
								</td>
								<td>
									<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('discount.edit', ['id' => $discountRule->id]) }}" title="Edit">
									   	<i class="las la-edit"></i>
								   	</a>
								   	<a href="{{ route('discount.delete', ['id' => $discountRule->id]) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="Delete">
			                            <i class="las la-trash"></i>
			                        </a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="9">No Discount Rules Found</td>
							</tr>
						@endforelse

					</tbody>
					<tfoot>
						<tr>
							<td colspan="9">
								<p><b>Note: </b>Only 1st rule of the table will be applied at a time. Change the rule order you can edit rule and set order_by bigger than 1st rule.</p>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
