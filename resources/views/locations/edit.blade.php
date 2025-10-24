@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Edit Campus Detail')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('locations.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('locations.update', $location->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Email</label>
                            <input type="text" name="email"
                                class="form-control @error('email') border border-danger @enderror" id="email"
                                value="{{ old('email', $location->email) }}" />
                            @error('email')
                                <div class="text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Location Code</label>
                            <input type="text" name="location_code"
                                class="form-control @error('location_code') border border-danger @enderror"
                                id="location_code" value="{{ old('location_code', $location->location_code) }}" />
                            @error('location_code')
                                <div class="text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label>Location Name</label>
                            <input type="text" name="name"
                                class="form-control @error('name') border border-danger @enderror" id="name"
                                value="{{ old('name', $location->name) }}" />
                            @error('name')
                                <div class="text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Address</label>
                            <input type="text" name="address"
                                class="form-control @error('address') border border-danger @enderror" id="address"
                                value="{{ old('address', $location->address) }}" />
                            @error('address')
                                <div class="text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <label>Contact No</label>
                            <input type="text" name="phone_no"
                                class="form-control @error('phone_no') border border-danger @enderror" id="phone_no"
                                value="{{ old('phone_no', $location->phone_no) }}" />
                            @error('phone_no')
                                <div class="text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="sf-label">Select Company</label>
                            <select id="company_detail_1" class="multiselect-ui form-control select2" name="company_id" required="required">
                                <option value="">Select Company</option>
                                <?php
                                    $accType = Auth::user()->acc_type;
            
                                    if ($accType == 'client') {
                                        $companiesList = DB::Connection('mysql')->table('companies')->select(['name', 'id', 'dbName'])->where('status', '=', '1')->get();
                                    } else if ($accType == 'owner' || $accType == 'superadmin' || $accType == 'superuser') {
                                        $checkCompanyId = Auth::user()->company_id;
                                        $a = explode("<*>", $checkCompanyId);
                                        $companiesList = DB::Connection('mysql')->table('companies')->select(['name', 'id', 'dbName'])->where('status', '=', '1')->whereIn('id', $a)->get();
                                    } 
                                    foreach ($companiesList as $cRow1) {
                                ?>
                                    <option value="<?php echo $cRow1->id; ?>" 
                                        <?php echo $location->company_id == $cRow1->id ? 'selected' : ''; ?>>
                                        <?php echo $cRow1->name; ?>
                                    </option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
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
