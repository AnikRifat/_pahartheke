@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col">
			<h1 class="h3">{{ translate('Website Header') }}</h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 mx-auto">
		<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">{{ translate('Topbar Setting') }}</h6>
				</div>
				<div class="card-body">
                    <div class="form-group">
						<label>{{ translate('Left Text') }}</label>
						<input type="hidden" name="types[]" value="topbar_left">
						<input type="text" class="form-control" placeholder="{{ translate('Welcome') }}" name="topbar_left" value="{{ get_setting('topbar_left') }}">
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">{{ translate('Header Setting') }}</h6>
				</div>
				<div class="card-body">
					<div class="form-group row">
	                    <label class="col-md-3 col-from-label">{{ translate('Header Logo') }}</label>
						<div class="col-md-8">
		                    <div class=" input-group " data-toggle="aizuploader" data-type="image">
		                        <div class="input-group-prepend">
		                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
		                        </div>
		                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
								<input type="hidden" name="types[]" value="header_logo">
		                        <input type="hidden" name="header_logo" class="selected-files" value="{{ get_setting('header_logo') }}">
		                    </div>
		                    <div class="file-preview"></div>
						</div>
	                </div>
	                <div class="form-group row">
						<label class="col-md-3 col-from-label">{{translate('Enable stikcy header?')}}</label>
						<div class="col-md-8">
							<label class="aiz-switch aiz-switch-success mb-0">
								<input type="hidden" name="types[]" value="header_stikcy">
								<input type="checkbox" name="header_stikcy" @if( get_setting('header_stikcy') == 'on') checked @endif>
								<span></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">{{ translate('Header Menu') }}</h6>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label>{{ translate('Label & Links') }}</label>
						<div class="header-menu-target">
							@if (get_setting('header_menu_label') != null)
								@foreach (json_decode(get_setting('header_menu_label'), true) as $key => $value)
								<div class="row gutters-5">
									<div class="col-md">
										<div class="form-group">
											<input type="hidden" name="types[]" value="header_menu_label">
											<input type="text" class="form-control" placeholder="Label" name="header_menu_label[]" value="{{ json_decode(get_setting('header_menu_label'), true)[$key] }}">
										</div>
									</div>
									<div class="col-md">
										<div class="form-group">
											<input type="hidden" name="types[]" value="header_menu_link">
											<input type="text" class="form-control" placeholder="link with http:// or https://" name="header_menu_link[]" value="{{ json_decode(get_setting('header_menu_link'), true)[$key] }}">
										</div>
									</div>
									<div class="col-auto">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
								@endforeach
							@endif
						</div>
					</div>
					<button
						type="button"
						class="btn btn-soft-secondary btn-sm"
						data-toggle="add-more"
						data-target=".header-menu-target"
						data-content='<div class="row gutters-5">
									<div class="col-md">
										<div class="form-group">
											<input type="hidden" name="types[]" value="header_menu_label">
											<input type="text" class="form-control" placeholder="Label" name="header_menu_label[]">
										</div>
									</div>
									<div class="col-md">
										<div class="form-group">
											<input type="hidden" name="types[]" value="header_menu_link">
											<input type="text" class="form-control" placeholder="link with http:// or https://" name="header_menu_link[]">
										</div>
									</div>
									<div class="col-auto">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>'
					>{{ translate('Add New') }}
					</button>
				</div>
			</div>
			<div class="text-right mb-3">
				<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
			</div>
		</form>
	</div>
</div>
<!-- Google Tag Manager -->

@endsection
