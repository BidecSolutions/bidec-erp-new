@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Add New Supplier') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('suppliers.store') }}">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        @csrf
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Account Name</label>
                                <select name="acc_id" id="acc_id" class="form-control select2">
                                    @foreach ($chartOfAccountList as $coalRow)
                                        <option value="{{ $coalRow->code }}">{{ $coalRow->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Supplier Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') border border-danger @enderror" id="name"
                                    value="{{ old('name') }}" />
                                @error('name')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>NTN No</label>
                                <input type="text" name="ntn_no"
                                    class="form-control @error('ntn_no') border border-danger @enderror" id="ntn_no"
                                    value="{{ old('ntn_no') }}" />
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>STRN No</label>
                                <input type="text" name="strn_no"
                                    class="form-control @error('strn_no') border border-danger @enderror" id="strn_no"
                                    value="{{ old('strn_no') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>City Name</label>
                                <select name="city_id" id="city_id" class="form-control select2">
                                    @foreach ($cities as $cRow)
                                        <option value="{{ $cRow['id'] }}">{{ $cRow['city_name'] }}</option>
                                    @endforeach
                                </select>
                                 @error('city_id')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                <label>Physical Address</label>
                                <input type="text" name="physical_address" id="physical_address" value=""
                                    class="form-control" />
                                @error('physical_address')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>CNIC No</label>
                                <input type="text" name="cnic_no"
                                    class="form-control @error('cnic_no') border border-danger @enderror" id="cnic_no"
                                    value="{{ old('cnic_no') }}" />
                                @error('cnic_no')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Mobile No</label>
                                <input type="text" name="mobile_no"
                                    class="form-control @error('mobile_no') border border-danger @enderror" id="mobile_no"
                                    value="{{ old('mobile_no') }}" />
                                @error('mobile_no')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Phone No</label>
                                <input type="text" name="phone_no"
                                    class="form-control @error('phone_no') border border-danger @enderror" id="phone_no"
                                    value="{{ old('phone_no') }}" />
                                @error('phone_no')
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
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Bank Name</label>
                                <input type="text" name="bank_name"
                                    class="form-control @error('bank_name') border border-danger @enderror" id="bank_name"
                                    value="{{ old('bank_name') }}" />
                                @error('bank_name')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Account Title</label>
                                <input type="text" name="account_title"
                                    class="form-control @error('account_title') border border-danger @enderror"
                                    id="account_title" value="{{ old('account_title') }}" />
                                @error('account_title')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Account No</label>
                                <input type="text" name="account_no"
                                    class="form-control @error('account_no') border border-danger @enderror" id="account_no"
                                    value="{{ old('account_no') }}" />
                                @error('account_no')
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
    <script>
        $('.account_name').select2();
    </script>
@endsection
