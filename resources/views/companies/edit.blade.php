@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Edit Company Detail')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('companies.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('companies.update',$company->id) }}" enctype="multipart/form-data">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Registration No</label>
                            <input type="text" name="registration_no"
                            class="form-control @error('registration_no') border border-danger @enderror"
                            id="registration_no" value="{{$company->registration_no}}" />
                            @error('company_code')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Company Code</label>
                            <input type="text" name="company_code"
                            class="form-control @error('company_code') border border-danger @enderror"
                            id="company_code" value="{{$company->company_code}}" />
                            @error('company_code')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Company Name</label>
                            <input type="text" name="name"
                            class="form-control @error('name') border border-danger @enderror"
                            id="name" value="{{$company->name}}" />
                            @error('name')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Address</label>
                            <input type="text" name="address"
                            class="form-control @error('address') border border-danger @enderror"
                            id="address" value="{{$company->address}}" />
                            @error('address')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Contact No</label>
                            <input type="text" name="contact_no"
                            class="form-control @error('contact_no') border border-danger @enderror"
                            id="contact_no" value="{{$company->contact_no}}" />
                            @error('contact_no')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>School Logo</label>
                            <input type="file" name="school_logo" id="school_logo" class="form-control" />
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
