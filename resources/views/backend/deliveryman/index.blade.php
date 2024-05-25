@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Delivery Men')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('deliveryman.create') }}" class="btn btn-primary">
                <span>{{translate('Add New Delivery Man')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Delivery Men') }}</h5>
        <form class="" id="sort_delivery_men" action="" method="GET">
            <div class="box-inline pad-rgt pull-left">
                <div class="" style="min-width: 200px;">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Phone')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryMen as $key => $deliveryMan)
                <tr>
                    <td>{{ ($key+1) + ($deliveryMen->currentPage() - 1)*$deliveryMen->perPage() }}</td>
                    <td>{{ $deliveryMan->name }}</td>
                    <td>{{ $deliveryMan->phone }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('deliveryman.edit', $deliveryMan->id)}}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('deliveryman.destroy', $deliveryMan->id)}}" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $deliveryMen->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
@include('modals.delete_modal')
@endsection
