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
			</div>
			<div class="card-body">
				<div>
					@if ($errors->any())
					    <div class="alert alert-danger">
					        <ul>
					            @foreach ($errors->all() as $error)
					                <li>{{ $error }}</li>
					            @endforeach
					        </ul>
					    </div>
					@endif
				</div>
				<form action="{{ route('discount.update', ['id' => $discount_rule->id]) }}" method="POST">
					@csrf
					<div class="row gutters-5">
						<div class="col-md-3">
							<div class="form-group">
								<label class="col-from-label">Rule Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" placeholder="Discount Rule Name" value="{{ $discount_rule->name }}" value="{{ $discount_rule->name }}" name="name">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="col-from-label">Discount Type <span class="text-danger">*</span></label>
								<select value="{{ $discount_rule->type }}" name="type" class="custom-select">
									{{-- <option value="1" @if($discount_rule->type == 1) selected @endif>Free Delivery</option> --}}
									<option value="2" @if($discount_rule->type == 2) selected @endif>Flat Discount (On Total Amount)</option>
									<option value="3" @if($discount_rule->type == 3) selected @endif>Percent Discount (On Total Amount)</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Discount Amount </label>
							<input type="number" class="form-control" placeholder="Ex: 100" value="{{ $discount_rule->discount_amount }}" name="discount_amount">
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Expire Date</label>
							<input type="date" class="form-control" value="{{ $discount_rule->expire_date }}" name="expire_date">
						</div>

						<div class="col-md-3">
							<label class="col-from-label">Condition On<span class="text-danger">*</span></label>
							<select value="{{ $discount_rule->condition_key }}" name="condition_key" class="custom-select">
								<option value="1" @if($discount_rule->condition_key == 1) selected @endif>Total Amount</option>
								{{-- <option value="2" @if($discount_rule->condition_key == 2) selected @endif>Quantity</option> --}}
							</select>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Discount Condition <span class="text-danger">*</span></label>
							<select value="{{ $discount_rule->conditon_oprator }}" name="conditon_oprator" class="custom-select">
								<option value=">" @if($discount_rule->conditon_oprator == ">") selected @endif>Greater than</option>
								{{-- <option value="<" @if($discount_rule->conditon_oprator == "<") selected @endif>Less Than</option> --}}
								{{-- <option value="==" @if($discount_rule->conditon_oprator == "==") selected @endif>Equal to</option> --}}
							</select>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Condition Value<span class="text-danger">*</span></label>
							<input type="number" class="form-control" placeholder="Ex: 1000" value="{{ $discount_rule->conditon_value }}" name="conditon_value" required>
						</div>
						{{-- <div class="col-md-3">
							<label class="col-from-label">Order<span class="text-danger">*</span></label>
							<input type="number" class="form-control" placeholder="Ex:1" name="order_by" value="{{ $discount_rule->order_by }}" required>
						</div> --}}
						<div class="col-md-1 pt-4 text-right">
	                        <div class="d-flex align-items-center pt-3 g-3">
								<label class="aiz-switch aiz-switch-success mb-0 mr-2">
		                            <input type="checkbox" name="status" @if($discount_rule->status == 1) checked @endif>
		                            <span></span>
		                        </label>
	                        	<span>Status</span>
	                        </div>
						</div>
						<div class="col-md-2 pt-4 text-right">
							<button type="submit" class="btn btn-primary px-5">Update</button>
						</div>
					</div>
				</form>

				{{-- <table class="table table-bordered aiz-table mb-0">
					<thead>
						<tr>
							<th width="20%">Rule Name</th>
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
						<tr>
							<td>{{ $discountRule->name }}</td>
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
							<td>{{ $discountRule->expire_date }}</td>
							<td>
								@if($discountRule->status == 1)
									<span>Active</span>
								@else
									<span>InActive</span>
								@endif
							</td>
							<td>
								<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="#" title="Edit">
								   	<i class="las la-edit"></i>
							   	</a>
							   	<a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete">
		                            <i class="las la-trash"></i>
		                        </a>
							</td>
						</tr>
					</tbody>
				</table> --}}
			</div>
		</div>
	</div>
</div>
@endsection
