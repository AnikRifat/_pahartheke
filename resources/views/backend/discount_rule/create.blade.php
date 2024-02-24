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
				<form method="POST" action="{{ route('discount.store') }}">
					@csrf
					<div class="row gutters-5">
						<div class="col-md-3">
							<div class="form-group">
								<label class="col-from-label">Rule Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" placeholder="Discount Rule Name" name="name" required>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="col-from-label">Discount Type <span class="text-danger">*</span></label>
								<select name="type" class="custom-select" required>
									{{-- <option value="1">Free Delivery</option> --}}
									<option value="2">Flat Discount (On Total Amount)</option>
									<option value="3">Percent Discount (On Total Amount)</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Discount Amount </label>
							<input type="number" class="form-control" placeholder="Ex: 100" name="discount_amount">
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Expire Date</label>
							<input type="date" class="form-control" name="expire_date">
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Condition On<span class="text-danger">*</span></label>
							<select name="condition_key" class="custom-select" required>
								<option value="1">Total Amount</option>
								{{-- <option value="2">Quantity</option> --}}
							</select>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Discount Condition <span class="text-danger">*</span></label>
							<select name="conditon_oprator" class="custom-select" required>
								<option value=">">Greater than</option>
								{{-- <option value="<">Less Than</option> --}}
								{{-- <option value="==">Equal to</option> --}}
							</select>
						</div>
						<div class="col-md-3">
							<label class="col-from-label">Condition Value<span class="text-danger">*</span></label>
							<input type="number" class="form-control" placeholder="Ex: 1000" name="conditon_value" required>
						</div>
						{{-- <div class="col-md-3">
							<label class="col-from-label">Order<span class="text-danger">*</span></label>
							<input type="number" class="form-control" placeholder="Ex:1" name="order_by" required>
						</div> --}}
						<div class="col-md-1 pt-4 text-right">
	            <div class="d-flex align-items-center pt-3 g-3">
								<label class="aiz-switch aiz-switch-success mb-0 mr-2">
                    <input type="checkbox" name="status">
                    <span></span>
                </label>
              	<span>Status</span>
              </div>
						</div>
						<div class="col-md-2 pt-4 text-right">
							<button type="submit" class="btn btn-primary px-5">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
