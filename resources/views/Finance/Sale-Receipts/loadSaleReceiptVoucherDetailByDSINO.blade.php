@php
    use App\Helpers\CommonHelper;
    $counterOne = 1;
    $counterTwo = 1;
    $totalItemSummaryAmount = 0;
    $totalReceiptAmount = 0;
@endphp
<div class="row">
    <!-- Item Summary Table -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th colspan="6">Item Summary</th>
                    </tr>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Category Name</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Size Name</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Unit Price</th>
                        <th class="text-center">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemSummaryList as $islRow)
                        @php
                            $totalItemSummaryAmount += $islRow->total_amount;
                        @endphp
                        <tr>
                            <td class="text-center">{{$counterOne++}}</td>
                            <td>{{$islRow->category_name}}</td>
                            <td>{{$islRow->product_name}}</td>
                            <td>{{$islRow->size_name}}</td>
                            <td>{{$islRow->qty}}</td>
                            <td>{{$islRow->rate}}</td>
                            <td class="text-right">{{number_format($islRow->total_amount,0)}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total Item Summary Amount</th>
                        <th class="text-right">{{number_format($totalItemSummaryAmount,0)}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Payment Summary Table -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th colspan="5">Receipt Summary</th>
                    </tr>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">R.V. No</th>
                        <th class="text-center">R.V. Date</th>
                        <th class="text-center">Account Head</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receiptSummaryList as $pslRow)
                        @php
                            $totalReceiptAmount += $pslRow->amount;
                        @endphp
                        <tr>
                            <td class="text-center">{{$counterTwo++}}</td>
                            <td>{{$pslRow->rv_no}}</td>
                            <td>{{CommonHelper::changeDateFormat($pslRow->rv_date)}}</td>
                            <td>{{$pslRow->account_head}}</td>
                            <td class="text-right">{{number_format($pslRow->amount,0)}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total Receipt Amount</th>
                        <th class="text-right">{{number_format($totalReceiptAmount,0)}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<!-- Remaining Amount Calculation -->
@php
    $remainingAmount = $totalItemSummaryAmount - $totalReceiptAmount;
@endphp

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
        <label>Receipt Voucher Date <span class="text-danger">*</span></label>
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
        id="slip_no" value="{{old('slip_no','-')}}" />
        @error('slip_no')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label>Receipt To <span class="text-danger">*</span></label>
        <input type="text" name="receipt_to"
        class="form-control @error('receipt_to') border border-danger @enderror"
        id="receipt_to" value="{{old('receipt_to','-')}}" />
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
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
        <label>Description <span class="text-danger">*</span></label>
        <input type="text" name="description"
        class="form-control @error('description') border border-danger @enderror"
        id="description" value="{{old('description')}}" />
        @error('description')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label>Remaining Amount</label>
        <input type="text" class="form-control" id="remaining_amount" name="remaining_amount" value="{{ $remainingAmount }}" readonly />
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <input type="hidden" name="credit_account_id" id="credit_account_id" value="{{$customerDetail->acc_id}}" />
        <label>Credit Account <span class="text-danger">*</span></label>
        <select disabled class="form-control select2">
            <option>{{$customerDetail->name}}</option>
        </select>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label>Debit Account <span class="text-danger">*</span></label>
        <select name="debit_account_id" id="debit_account_id" class="form-control voucher-type @error('debit_account_id') border border-danger @enderror select2">
            <option value="">Select Parent Code</option>
            @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                <option value="{{old('debit_account_id') ?? $row->id}}" @if($customerDetail->acc_id == $row->id) disabled @endif>{{$row->code}} ---- {{$row->name}}</option>
            @endforeach
        </select>
        @error('debit_account_id')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label>Transaction Amount <span class="text-danger">*</span></label>
        <input type="number" name="amount" id="amount" class="form-control @error('amount') border border-danger @enderror" value="{{old('amount')}}" 
        min="1" max="{{ $remainingAmount }}" step="1" />
        @error('amount')
            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Client-side Validation Script -->
<script>
    $('.voucher-type').select2();
    document.getElementById('amount').addEventListener('input', function() {
        const remainingAmount = parseFloat(document.getElementById('remaining_amount').value.replace(/,/g, ''));
        const transactionAmount = parseFloat(this.value);
        if (transactionAmount > remainingAmount) {
            alert("Transaction Amount cannot exceed Remaining Amount.");
            this.value = remainingAmount;
        }
    });
</script>
