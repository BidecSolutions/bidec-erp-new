@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Country Detail') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('chart-of-account-settings.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
                <form method="POST" action="{{ route('chart-of-account-settings.update', $settings->id) }}">
                    @csrf

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        @foreach ($optionArray as $key => $oaRow)
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <!-- Ensure $key is cast to an integer -->
                                    <input type="hidden" value="{{ (int) $key }}" name="option_id[]" />
                                    {{ $oaRow }}
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <select name="acc_id[]" id="acc_id" class="form-control select2">
                                        <option value="">Select Account</option>
                                        @foreach ($chartOfAccountList as $coalRow)
                                            <option value="{{ $coalRow->id }}"
                                                {{ isset($settings->acc_id) && $settings->acc_id == $coalRow->id ? 'selected' : '' }}>
                                                {{ $coalRow->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">&nbsp;</div>
                            </div>
                        @endforeach

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
