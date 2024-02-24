@extends('backend.layouts.app')

@section('content')
<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Order Details')}}</h5>
        </div>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
       
        <div class="card-body">
          <form action="{{ route('orders.update',$order->id) }}" method="POST">
              <input name="id" type="hidden" value="{{$order->id}}">
              <input name="country" type="hidden" value="Bangladesh">
              
               @csrf
                @method('PUT')
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value=" {{ json_decode($order->shipping_address)->name }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="email">{{translate('Email Address')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control" value="{{ json_decode($order->shipping_address)->email }}" >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="phone">{{translate('phone')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('phone')}}" id="phone" name="phone" class="form-control" value =" {{ json_decode($order->shipping_address)->phone }} " required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="address">{{translate('address')}}</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="exampleFormControlTextarea1" id="address" placeholder="{{translate('address')}}" name ="address" rows="3"required>{{ json_decode($order->shipping_address)->address }}</textarea>
                    </div>
                </div>
               <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="city">{{translate('city')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('city')}}" id="city" name="city" class="form-control" value =" {{ json_decode($order->shipping_address)->city }}" required>
                    </div>
                </div>
                 <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="city">{{translate('postal_code')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('postal_code')}}" id="postal_code" name="postal_code" class="form-control" value =" {{ json_decode($order->shipping_address)->postal_code }}" required>
                    </div>
                </div>
                  @foreach ($order->orderDetails as $key => $orderDetail)
                   
                  @endforeach
                  
                  
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

