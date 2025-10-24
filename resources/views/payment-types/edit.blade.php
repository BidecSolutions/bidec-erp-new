@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Edit Payment Type Detail')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('payment-types.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('payment-types.update', $paymentType->id) }}">
                @csrf
            
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <!-- Name Field -->
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Payment Type Name</label>
                            <input 
                                type="text" 
                                name="name"
                                class="form-control @error('name') border border-danger @enderror"
                                id="name" 
                                value="{{ old('name', $paymentType->name) }}" 
                            />
                            @error('name')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <!-- Conversion Rate Field -->
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Conversion Rate</label>
                            <input 
                                type="text" 
                                name="conversion_rate"
                                class="form-control @error('conversion_rate') border border-danger @enderror"
                                id="conversion_rate" 
                                value="{{ old('conversion_rate', $paymentType->conversion_rate) }}" 
                            />
                            @error('conversion_rate')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <!-- Rate Type Field -->
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Rate Type</label>
                            <select 
                                name="rate_type" 
                                id="rate_type" 
                                class="form-control select2 @error('rate_type') border border-danger @enderror"
                            >
                                <option value="1" {{ old('rate_type', $paymentType->rate_type) == 1 ? 'selected' : '' }}>Fixed</option>
                                <option value="2" {{ old('rate_type', $paymentType->rate_type) == 2 ? 'selected' : '' }}>Changeable</option>
                            </select>
                            @error('rate_type')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="lineHeight">&nbsp;</div>
                    
                    <!-- Submit and Reset Buttons -->
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
