@extends('backend.layouts.app')

@section('content')

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Customers')}}</h5>
        </div>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        @foreach($customer as $row)
       
        <div class="card-body">
          <form action="{{ route('customers.update',$row->id) }}" method="POST">
                <input name="id" type="hidden" value="{{$row->id}}">
                @csrf
                @method('PUT')
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{$row->name}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="email">{{translate('Email Address')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control" value="{{$row->email}}" >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="phone">{{translate('phone')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('phone')}}" id="phone" name="phone" class="form-control" value ="{{$row->phone}}"required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="password">{{translate('address')}}</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="exampleFormControlTextarea1" id="address" placeholder="{{translate('address')}}" name ="address" rows="3"required>{{$row->address}}</textarea>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection




