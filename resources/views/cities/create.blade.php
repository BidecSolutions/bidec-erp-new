@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New City')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('cities.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
	    <div class="row">
            <form method="POST" action="{{ route('cities.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>State Name</label>
                            <select name="state_id" id="state_id" class="form-control select2">
                                @foreach (CommonHelper::get_all_states(1) as $row)
                                    <option value="{{ $row->id }}">{{ $row->state_name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>City Name</label>
                            <input type="text" name="city_name"
                            class="form-control @error('city_name') border border-danger @enderror"
                            id="city_name" value="{{old('city_name')}}" />
                            @error('city_name')
                                <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Reset</button>
                            <button type="submit" class="btn btn-sm btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
