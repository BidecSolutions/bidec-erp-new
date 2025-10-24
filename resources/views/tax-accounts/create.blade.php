@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Add New Tax Account') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('tax-accounts.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('tax-accounts.store') }}">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Account Name</label>
                                <select name="acc_id" id="acc_id" class="form-control select2">
                                    @foreach ($chartOfAccountList as $coalRow)
                                        <option value="{{ $coalRow->code }}">{{ $coalRow->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') border border-danger @enderror" id="name"
                                    value="{{ old('name') }}" />
                                @error('name')
                                    <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Percent Value</label>
                                <input type="text" name="percent_value"
                                    class="form-control @error('percent_value') border border-danger @enderror" id="percent_value"
                                    value="{{ old('percent_value') }}" />
                                @error('percent_value')
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
