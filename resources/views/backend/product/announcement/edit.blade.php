@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Edit Announcement Information</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
           @foreach($annmt as $ancval)
            <form class="p-4" action="{{ route('announcement.update', $ancval->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="_method" type="hidden" value="PATCH">
                
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Title </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Name" id="name" name="name" value="{{ $ancval->name }}" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Attached File </label>
                    <div class="col-sm-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="logo" value="{{ $ancval->logo }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name"> Status </label>
                    <div class="col-sm-9">
                        <select class="form-control" name="status" required>
                            <option value="">Select Show/Hide (1=show,0=hide)</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
             @endforeach
        </div>
    </div>
</div>

@endsection
