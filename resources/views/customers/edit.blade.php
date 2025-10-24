@php
    use App\Helpers\CommonHelper;
    $disabled = '';
    if(!empty($isUsedInTransactions)){
        $disabled = 'disabled';
    }
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Customers
                     Detail') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('customers.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                    @csrf
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Account Name </label>
                                <input type="hidden" name="customer_acc_id" id="customer_acc_id" value="{{$customer->acc_id}}" />
                                <input type="hidden" name="old_acc_id" id="old_acc_id" value="{{$customer->parent_code}}" />
                                <select name="acc_id" id="acc_id" {{$disabled}} class="form-control select2">
                                    @foreach ($chartOfAccountList as $coalRow)
                                        <option value="{{ $coalRow->code }}"
                                            {{ $coalRow->code == $customer->parent_code ? 'selected' : '' }}>
                                            {{ $coalRow->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Customer Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') border border-danger @enderror" id="name"
                                    value="{{ old('name', $customer->name) }}" />
                                @error('name')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Mobile No</label>
                                <input type="text" name="mobile_no"
                                    class="form-control @error('mobile_no') border border-danger @enderror" id="mobile_no"
                                    value="{{ old('mobile_no', $customer->mobile_no) }}" />
                                @error('mobile_no')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Email Address</label>
                                <input type="text" name="email_address"
                                    class="form-control @error('email_address') border border-danger @enderror"
                                    id="email_address" value="{{ old('email_address', $customer->email_address) }}" />
                                @error('email_address')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>City Name</label>
                                <select name="city_id" id="city_id" class="form-control select2">
                                    @foreach ($cities as $cRow)
                                        <option value="{{ $cRow->id }}"
                                            {{ $cRow->id == $customer->city_id ? 'selected' : '' }}>
                                            {{ $cRow->city_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                <label>Physical Address</label>
                                <input type="text" name="physical_address" id="physical_address"
                                    value="{{ old('physical_address', $customer->physical_address) }}"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                <button type="submit" class="btn btn-sm btn-success" onclick="disableRemove()">Update</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        function disableRemove() {
            $('#acc_id').prop('disabled', false); // 'disabled' is the correct property name
        }
    </script>
@endsection
