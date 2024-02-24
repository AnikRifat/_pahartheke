@extends('backend.layouts.app')
@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col">
			<h1 class="h3">{{ translate('Home Page Settings') }}</h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xxl-8 mx-auto">
		
		{{-- Home Slider --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Slider') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Photos & Links') }}</label>
						<div class="home-slider-target">
							<input type="hidden" name="types[]" value="home_slider_images">
							<input type="hidden" name="types[]" value="home_slider_links">
							@if (get_setting('home_slider_images') != null)
								@foreach (json_decode(get_setting('home_slider_images'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-5">
											<label>{{ translate('Product Image') }}</label>
											<div class="input-group" data-toggle="aizuploader" data-type="image">
				                                <div class="input-group-prepend">
				                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
				                                </div>
				                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
												<input type="hidden" name="types[]" value="home_slider_images">
				                                <input type="hidden" name="home_slider_images[]" class="selected-files" value="{{ json_decode(get_setting('home_slider_images'), true)[$key] }}">
				                            </div>
				                            <div class="file-preview box sm">
				                            </div>
										</div>
										<div class="col">
											<div class="form-group">
												<label>{{ translate('Link') }}</label>
												<input type="hidden" name="types[]" value="home_slider_links">
												<input type="text" class="form-control" placeholder="link with http:// or https://" name="home_slider_links[]" value="{{ json_decode(get_setting('home_slider_links'), true)[$key] }}">
											</div>
										</div>
										<div class="col-auto">
											<button type="button" class="mt-4 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
												<i class="las la-times"></i>
											</button>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm mt-3"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-5">
									<label>{{ translate('Product Image') }}</label>
									<div class="input-group" data-toggle="aizuploader" data-type="image">
										<div class="input-group-prepend">
											<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
										</div>
										<div class="form-control file-amount">{{ translate('Choose File') }}</div>
										<input type="hidden" name="types[]" value="home_slider_images">
										<input type="hidden" name="home_slider_images[]" class="selected-files">
									</div>
									<div class="file-preview box sm">
									</div>
								</div>
								<div class="col">
									<div class="form-group">
										<label>{{ translate('Link') }}</label>
										<input type="hidden" name="types[]" value="home_slider_links">
										<input type="text" class="form-control" placeholder="link with http:// or https://" name="home_slider_links[]">
									</div>
								</div>
								<div class="col-auto">
									<button type="button" class="mt-4 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
										<i class="las la-times"></i>
									</button>
								</div>
							</div>'
							data-target=".home-slider-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('About Us') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('About description') }}</label>
						<input type="hidden" name="types[]" value="home_about_us_description">
						<textarea class="aiz-text-editor form-control" name="home_about_us_description" data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]' placeholder="Type.." data-min-height="150">
                            @php echo get_setting('home_about_us_description'); @endphp
                        </textarea>
					</div>
					<div class="row">
						<div class="col-md">
	                        <div class="form-group">
	                            <label>{{ translate('Button label') }}</label>
	                            <input type="hidden" name="types[]" value="home_about_button">
	                            <input type="text" class="form-control" placeholder="Label" name="home_about_button" value="{{ get_setting('home_about_button') }}">
	                        </div>
						</div>
						<div class="col-md">
	                        <div class="form-group">
	                            <label>{{ translate('Button Link') }}</label>
	                            <input type="hidden" name="types[]" value="home_about_button_link">
	                            <input type="text" class="form-control" placeholder="http://" name="home_about_button_link" value="{{ get_setting('home_about_button_link') }}">
	                        </div>
						</div>
					</div>

					<div class="form-group">
                        <label>{{ translate('Youtube video ID') }}</label>
                        <input type="hidden" name="types[]" value="home_about_image">
                        <input type="text" class="form-control" placeholder="http://" name="home_about_image" value="{{ get_setting('home_about_image') }}">
                    </div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Home Banner 1 --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Banner 1 (Max 3)') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Banner & Links') }}</label>
						<div class="home-banner1-target">
							<input type="hidden" name="types[]" value="home_banner1_images">
							<input type="hidden" name="types[]" value="home_banner1_links">
							@if (get_setting('home_banner1_images') != null)
								@foreach (json_decode(get_setting('home_banner1_images'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-5">
											<div class="input-group" data-toggle="aizuploader" data-type="image">
				                                <div class="input-group-prepend">
				                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
				                                </div>
				                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
												<input type="hidden" name="types[]" value="home_banner1_images">
				                                <input type="hidden" name="home_banner1_images[]" class="selected-files" value="{{ json_decode(get_setting('home_banner1_images'), true)[$key] }}">
				                            </div>
				                            <div class="file-preview box sm">
				                            </div>
										</div>
										<div class="col">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_banner1_links">
												<input type="text" class="form-control" placeholder="http://" name="home_banner1_links[]" value="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}">
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
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm mt-3"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-5">
									<div class="input-group" data-toggle="aizuploader" data-type="image">
										<div class="input-group-prepend">
											<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
										</div>
										<div class="form-control file-amount">{{ translate('Choose File') }}</div>
										<input type="hidden" name="types[]" value="home_banner1_images">
										<input type="hidden" name="home_banner1_images[]" class="selected-files">
									</div>
									<div class="file-preview box sm">
									</div>
								</div>
								<div class="col">
									<div class="form-group">
										<input type="hidden" name="types[]" value="home_banner1_links">
										<input type="text" class="form-control" placeholder="http://" name="home_banner1_links[]">
									</div>
								</div>
								<div class="col-auto">
									<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
										<i class="las la-times"></i>
									</button>
								</div>
							</div>'
							data-target=".home-banner1-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- category wise product filter --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Category wise product filter') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
                    <div class="form-group row">
						<label class="col-md-2 col-from-label">{{translate('Select category (Max 6)')}}</label>
						<div class="col-md-10">
							<input type="hidden" name="types[]" value="filter_categories">
							<select name="filter_categories[]" class="form-control aiz-selectpicker" multiple data-max-options="6" data-live-search="true" data-selected={{ get_setting('filter_categories') }} required>
								@foreach (\App\Category::where('parent_id', 0)->with('childrenCategories')->get() as $category)
									<option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
									@foreach ($category->childrenCategories as $childCategory)
										@include('categories.child_category', ['child_category' => $childCategory])
									@endforeach
								@endforeach
							</select>
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- why choose us --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Why choose us') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
                    <div class="form-group">
                        <label class="form-label" for="signinSrEmail">{{ translate('Background') }}</label>
                        <div class="input-group " data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="types[]" value="why_choose_bg">
                            <input type="hidden" name="why_choose_bg" class="selected-files" value="{{ get_setting('why_choose_bg') }}">
                        </div>
                        <div class="file-preview"></div>
                    </div>

					<div class="form-group">
						<label>{{ translate('Icon & texts') }}</label>
						<div class="why-choose-target">
							<input type="hidden" name="types[]" value="why_choose_icon">
							<input type="hidden" name="types[]" value="why_choose_title">
							<input type="hidden" name="types[]" value="why_choose_subtitle">
							@if (get_setting('why_choose_icon') != null)
								@foreach (json_decode(get_setting('why_choose_icon'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-lg-3">
											<div class="form-group">
												<div class="input-group" data-toggle="aizuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="why_choose_icon">
					                                <input type="hidden" name="why_choose_icon[]" class="selected-files" value="{{ json_decode(get_setting('why_choose_icon'), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<input type="hidden" name="types[]" value="why_choose_title">
												<input type="text" class="form-control" placeholder="{{ translate('Title') }}" name="why_choose_title[]" value="{{ json_decode(get_setting('why_choose_title'), true)[$key] }}">
											</div>
										</div>
										<div class="col-lg">
											<div class="form-group">
												<input type="hidden" name="types[]" value="why_choose_subtitle">
												<input type="text" class="form-control" placeholder="{{ translate('Subtitle') }}" name="why_choose_subtitle[]" value="{{ json_decode(get_setting('why_choose_subtitle'), true)[$key] }}">
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

						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-lg-3">
									<div class="form-group">
										<div class="input-group" data-toggle="aizuploader" data-type="image">
			                                <div class="input-group-prepend">
			                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
			                                </div>
			                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[]" value="why_choose_icon">
			                                <input type="hidden" name="why_choose_icon[]" class="selected-files" >
			                            </div>
			                            <div class="file-preview box sm">
			                            </div>
		                            </div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<input type="hidden" name="types[]" value="why_choose_title">
										<input type="text" class="form-control" placeholder="{{ translate('Title') }}" name="why_choose_title[]" >
									</div>
								</div>
								<div class="col-lg">
									<div class="form-group">
										<input type="hidden" name="types[]" value="why_choose_subtitle">
										<input type="text" class="form-control" placeholder="{{ translate('Subtitle') }}" name="why_choose_subtitle[]" >
									</div>
								</div>
								<div class="col-auto">
									<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
										<i class="las la-times"></i>
									</button>
								</div>
							</div>'
							data-target=".why-choose-target">
							{{ translate('Add New') }}
						</button>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>


		{{-- Customer review --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Customer review') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf

					<div class="form-group">
						<label>{{ translate('Reviews') }}</label>
						<div class="customer-review-target">
							<input type="hidden" name="types[]" value="customer_reviews_image">
							@if (get_setting('customer_reviews_image') != null)
								@foreach (json_decode(get_setting('customer_reviews_image'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col">
											<div class="form-group">
												<div class="input-group" data-toggle="aizuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="customer_reviews_image">
					                                <input type="hidden" name="customer_reviews_image[]" class="selected-files" value="{{ json_decode(get_setting('customer_reviews_image'), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
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

						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col">
									<div class="form-group">
										<div class="input-group" data-toggle="aizuploader" data-type="image">
			                                <div class="input-group-prepend">
			                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
			                                </div>
			                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[]" value="customer_reviews_image">
			                                <input type="hidden" name="customer_reviews_image[]" class="selected-files" >
			                            </div>
			                            <div class="file-preview box sm">
			                            </div>
		                            </div>
								</div>
								<div class="col-auto">
									<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
										<i class="las la-times"></i>
									</button>
								</div>
							</div>'
							data-target=".customer-review-target">
							{{ translate('Add New') }}
						</button>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>


	</div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
		$(document).ready(function(){
		    AIZ.plugins.bootstrapSelect('refresh');
		});
    </script>
@endsection
