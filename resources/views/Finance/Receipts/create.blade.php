@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Receipt Voucher')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('receipts.index') }}" class="btn btn-success btn-xs"><span></span> View List</a>
            </div>
        </div>
        <form method="POST" action="{{ route('receipts.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Voucher Type</label>
                    <select name="voucher_type" id="voucher_type" onchange="chequeOptionEnableDisable()" class="form-control @error('voucher_type') border border-danger @enderror select2">
                        <option value="{{old('voucher_type') ?? 1}}">Cash Receipt</option>
                        <option value="{{old('voucher_type') ?? 2}}">Bank Receipt</option>
                    </select>
                    @error('voucher_type')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Receipe Voucher Date <span class="text-danger">*</span></label>
                    <input type="date" name="rv_date"
                    class="form-control @error('rv_date') border border-danger @enderror"
                    id="rv_date" value="{{old('rv_date') ?? date('Y-m-d')}}" />
                    @error('rv_date')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Slip No <span class="text-danger">*</span></label>
                    <input type="text" name="slip_no"
                    class="form-control @error('slip_no') border border-danger @enderror"
                    id="slip_no" value="{{old('slip_no')}}" />
                    @error('slip_no')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Receipt To <span class="text-danger">*</span></label>
                    <input type="text" name="receipt_to"
                    class="form-control @error('receipt_to') border border-danger @enderror"
                    id="receipt_to" value="{{old('receipt_to')}}" />
                    @error('receipt_to')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row @if(old('voucher_type') === '1') hidden @elseif(old('voucher_type') === '2') @else hidden @endif" id="chequeDetail">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Cheque No <span class="text-danger">*</span></label>
                    <input type="text" name="cheque_no"
                    class="form-control @error('cheque_no') border border-danger @enderror"
                    id="cheque_no" value="{{old('cheque_no')}}" />
                    @error('cheque_no')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Cheque Date <span class="text-danger">*</span></label>
                    <input type="date" name="cheque_date"
                    class="form-control @error('cheque_date') border border-danger @enderror"
                    id="cheque_date" value="{{old('cheque_date') ?? date('Y-m-d')}}" />
                    @error('cheque_date')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Description <span class="text-danger">*</span></label>
                    <input type="text" name="description"
                    class="form-control @error('description') border border-danger @enderror"
                    id="description" value="{{old('description')}}" />
                    @error('description')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Debit Account <span class="text-danger">*</span></label>
                    <select name="debit_account_id" id="debit_account_id" class="form-control @error('debit_account_id') border border-danger @enderror select2">
                        <option value="">Select Parent Code</option>
                        @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                            <option value="{{old('debit_account_id') ?? $row->id}}">{{$row->code}} ---- {{$row->name}}</option>
                        @endforeach
                    </select>
                    @error('debit_account_id')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Credit Account <span class="text-danger">*</span></label>
                    <select name="credit_account_id" id="credit_account_id" class="form-control @error('credit_account_id') border border-danger @enderror select2">
                        <option value="">Select Parent Code</option>
                        @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                            <option value="{{old('credit_account_id') ?? $row->id}}">{{$row->code}} ---- {{$row->name}}</option>
                        @endforeach
                    </select>
                    @error('credit_account_id')
                        <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Transaction Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount"
                    class="form-control @error('amount') border border-danger @enderror"
                    id="amount" value="{{old('amount')}}" />
                    @error('amount')
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
@endsection
@section('script')
    <script>
        function chequeOptionEnableDisable(){
            var voucherType = $('#voucher_type').val();
            if(voucherType == 1){
                $('#chequeDetail').addClass('hidden');
            }else{
                $('#chequeDetail').removeClass('hidden');
            }
        }
    </script>
@endsection

