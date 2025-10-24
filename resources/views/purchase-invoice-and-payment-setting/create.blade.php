@php
    use App\Helpers\CommonHelper;
    $optionArray = [];
    if ($type == 1) {
        $optionArray = [
            '1' => 'Purchase Invoice',
            '2' => 'Payments',
        ];
    } elseif ($type == 2) {
        $optionArray = [
            '3' => 'Sale Invoice',
            '4' => 'Receipts',
        ];
    }
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                @if($type == 1)
                    {{ CommonHelper::displayPageTitle('Add Purchase Invoice and Purchase Payments Settings') }}
                @else
                    {{ CommonHelper::displayPageTitle('Add Sale Invoice and Sale Receipt Settings') }}
                @endif
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ $url }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    @foreach($optionArray as $key => $oaRow)
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <input type="hidden" value="{{ $key }}" name="option_id[]" />
                                {{ $oaRow }}
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <select name="acc_id[]" id="acc_id" class="form-control select2">
                                    <option value="">Select Account</option>
                                    @foreach($chartOfAccountList as $account)
                                        <option value="{{ $account->id }}"
                                            {{ isset($savedSettings[$key]) && $savedSettings[$key] == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
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
                            <button type="submit" class="btn btn-sm btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
