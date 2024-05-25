@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
			<h1 class="h3">All Announcement</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card">
		    <div class="card-header row gutters-5">
				<div class="col text-center text-md-left">
					<h5 class="mb-md-0 h6"> Announcement Info</h5>
				</div>
		    </div>
		    <div class="card-body">
		        <table class="table aiz-table mb-0">
		            <thead>
		                <tr>
		                    <th>Location</th>
		                    <th>Title</th>
		                    <th>File</th>
		                    <th>Status (1=show,0=hide)</th>
		                    <th class="text-right">Options</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($announcement as $ancval)
		                    <tr>
		                        <td>{{ $ancval->type }}</td>
		                        <td>{{ $ancval->name }}</td>
		                        <td>
		                             <img src="{{ uploaded_asset($ancval->logo) }}" alt="" class="h-50px">
		                        </td>
							    <td>{{ $ancval->status }}</td>
		                        <td class="text-right">
		                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('announcement.edit', ['id'=>$ancval->id])}}" title="Edit">
		                                <i class="las la-edit"></i>
		                            </a>
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

