@php 
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')

@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Customer')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('customers.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @if ($errors->any())
                    <div class="error-messages">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <form method="POST" action="{{ route('customers.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Account Name</label>
                            <select name="acc_id" id="acc_id" class="form-control select2 @error('acc_id') border border-danger @enderror">
                                @foreach($chartOfAccountList as $coalRow)
                                    <option value="{{$coalRow->code}}" {{ old('acc_id') == $coalRow->code ? 'selected' : '' }}>{{$coalRow->name}}</option>
                                @endforeach
                            </select>
                            @error('acc_id')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Customer Name</label>
                            <input type="text" name="name" 
                            class="form-control @error('name') border border-danger @enderror"
                            id="name" value="{{ old('name') }}" />
                            @error('name')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Mobile No</label>
                            <input type="text" name="mobile_no"
                            class="form-control @error('mobile_no') border border-danger @enderror"
                            id="mobile_no" value="{{ old('mobile_no') }}" />
                            @error('mobile_no')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>Email Address</label>
                            <input type="text" name="email_address"
                            class="form-control @error('email_address') border border-danger @enderror"
                            id="email_address" value="{{ old('email_address') }}" />
                            @error('email_address')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label>City Name</label>
                            <select name="city_id" id="city_id" class="form-control select2 @error('city_id') border border-danger @enderror">
                                @foreach($cities as $cRow)
                                    <option value="{{$cRow['id']}}" {{ old('city_id') == $cRow['id'] ? 'selected' : '' }}>{{$cRow['city_name']}}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                            <label>Physical Address</label>
                            <input type="text" name="physical_address" 
                            id="physical_address" value="{{ old('physical_address') }}" class="form-control" />
                            @error('physical_address')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="lineHeight">&nbsp;</div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            <button type="submit" class="btn btn-sm btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
