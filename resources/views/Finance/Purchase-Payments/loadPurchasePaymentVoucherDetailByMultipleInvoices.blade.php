@php
    use App\Helpers\CommonHelper;
    $counterOne = 1;
    $counterTwo = 1;
    $totalPaidAmount = 0;
@endphp
<input type="hidden" name="pi_voucher_type" id="pi_voucher_type" value="2" />
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label>Voucher Type</label>
        <select name="voucher_type" id="voucher_type" onchange="chequeOptionEnableDisable()" class="form-control voucher-type @error('voucher_type') border border-danger @enderror select2">
            <option value="1" {{ old('voucher_type') == '1' ? 'selected' : '' }}>Cash Payment</option>
            <option value="2" {{ old('voucher_type') == '2' ? 'selected' : '' }}>Bank Payment</option>
        </select>
        @error('voucher_type')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label>Payment Voucher Date <span class="text-danger">*</span></label>
        <input type="date" name="pv_date"
        class="form-control @error('pv_date') border border-danger @enderror"
        id="pv_date" value="{{old('pv_date') ?? date('Y-m-d')}}" />
        @error('pv_date')
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
        <label>Paid To <span class="text-danger">*</span></label>
        <input type="text" name="paid_to"
        class="form-control @error('paid_to') border border-danger @enderror"
        id="paid_to" value="{{old('paid_to')}}" />
        @error('paid_to')
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
        <input type="hidden" name="debit_account_id" id="debit_account_id" value="{{$detailSupplierAndInvoice->acc_id}}" />
        <label>Debit Account <span class="text-danger">*</span></label>
        <select disabled class="form-control select2">
            <option>{{$detailSupplierAndInvoice->name}}</option>
        </select>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label>Credit Account <span class="text-danger">*</span></label>
        <select name="credit_account_id" id="credit_account_id" class="form-control voucher-type @error('credit_account_id') border border-danger @enderror select2">
            @if(count($purchaseInvoiceSetting) != 0)
                @foreach($purchaseInvoiceSetting as $key => $row)
                    <option value="{{old('credit_account_id') ?? $row->id}}" @if($detailSupplierAndInvoice->acc_id == $row->id) disabled @endif>{{$row->code}} ---- {{$row->name}}</option>
                @endforeach
            @else
                <option value="">Select Parent Code</option>
                @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                    <option value="{{old('credit_account_id') ?? $row->id}}" @if($detailSupplierAndInvoice->acc_id == $row->id) disabled @endif>{{$row->code}} ---- {{$row->name}}</option>
                @endforeach
            @endif
        </select>
        @error('credit_account_id')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label>Transaction Amount <span class="text-danger">*</span></label>
        <input type="number" name="amount" id="amount" class="form-control @error('amount') border border-danger @enderror" value="{{old('amount')}}" 
        min="1" step="1" />
        @error('amount')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>
