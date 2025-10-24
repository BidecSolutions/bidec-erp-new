@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Chart of Account')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('chartofaccounts.index') }}" class="btn btn-success btn-xs"><span></span> View List</a>
            </div>
        </div>
        <form method="POST" action="{{ route('chartofaccounts.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Parent Account</label>
                    <select name="parent_code" id="parent_code" class="form-control @error('parent_code') border border-danger @enderror select2">
                        <option value="0">Select Parent Code</option>
                        @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                            <option value="{{old('parent_code') ?? $row->code}}">{{$row->code}} ---- {{$row->name}}</option>
                        @endforeach
                    </select>
                    @error('parent_code')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Account Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') border border-danger @enderror" value="{{old('name')}}" />
                    @error('name')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ledger_type_section">
                    <label>Ledger Type</label>
                    <select name="ledger_type" id="ledger_type" class="form-control">
                        <option value="1">Normal Entries (Assets, Expense) (Ammount will be decrease on credit and increase on debit)</option>
                        <option value="2">Apposite Entries (Income,Liability, Capital) (Ammount will be increase on credit and decrease on debit)</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 hidden">
                    <label>Transaction Type</label>
                    <select name="debit_credit" id="debit_credit" class="form-control @error('debit_credit') border border-danger @enderror select2">
                        <option value="{{old('debit_credit') ?? 1}}">Debit</option>
                        <option value="{{old('debit_credit') ?? 2}}">Credit</option>
                    </select>
                    @error('debit_credit')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="display:none;">
                    <label>Opening Balance</label>
                    <input type="number" name="opening_balance" id="opening_balance" class="form-control @error('opening_balance') border border-danger @enderror" value="0" />
                    @error('opening_balance')
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
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        function toggleLedgerType() {
            let parentCode = $('#parent_code').val();
            if (parentCode === "0" || parentCode === "" || parentCode === null) {
                $('#ledger_type_section').show();
            } else {
                $('#ledger_type_section').hide();
            }
        }

        // Initial check
        toggleLedgerType();

        // When parent_code changes
        $('#parent_code').on('change', function() {
            toggleLedgerType();
        });
    });
</script>
@endsection
