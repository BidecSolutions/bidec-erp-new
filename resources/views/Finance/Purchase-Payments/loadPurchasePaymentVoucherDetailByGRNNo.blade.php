@php
    use App\Helpers\CommonHelper;
    $counterOne = 1;
    $counterTwo = 1;
    $totalItemSummaryAmount = 0;
    $totalPaidAmount = 0;
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
                            $totalItemSummaryAmount += $islRow->sub_total;
                        @endphp
                        <tr>
                            <td class="text-center">{{$counterOne++}}</td>
                            <td>{{$islRow->category_name}}</td>
                            <td>{{$islRow->product_name}}</td>
                            <td>{{$islRow->size_name}}</td>
                            <td>{{$islRow->qty}}</td>
                            <td>{{$islRow->unit_price}}</td>
                            <td class="text-right">{{number_format($islRow->sub_total,0)}}</td>
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
                        <th colspan="5">Payment Summary</th>
                    </tr>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">P.V. No</th>
                        <th class="text-center">P.V. Date</th>
                        <th class="text-center">Account Head</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentSummaryList as $pslRow)
                        @php
                            $totalPaidAmount += $pslRow->amount;
                        @endphp
                        <tr>
                            <td class="text-center">{{$counterTwo++}}</td>
                            <td>{{$pslRow->pv_no}}</td>
                            <td>{{CommonHelper::changeDateFormat($pslRow->pv_date)}}</td>
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
                        <th colspan="4">Total Paid Amount</th>
                        <th class="text-right">{{number_format($totalPaidAmount,0)}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="lineHeight">&nbsp;</div>

<!-- Remaining Amount Calculation -->
@php
    $remainingAmount = $totalItemSummaryAmount - $totalPaidAmount;
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
        <input type="hidden" name="debit_account_id" id="debit_account_id" value="{{$supplierDetail->acc_id}}" />
        <label>Debit Account <span class="text-danger">*</span></label>
        <select disabled class="form-control select2">
            <option>{{$supplierDetail->name}}</option>
        </select>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label>Credit Account <span class="text-danger">*</span></label>
        <select name="credit_account_id" id="credit_account_id" class="form-control voucher-type @error('credit_account_id') border border-danger @enderror select2">
            <option value="">Select Parent Code</option>
            @foreach(CommonHelper::get_all_chart_of_account(1) as $key => $row)
                <option value="{{old('credit_account_id') ?? $row->id}}" @if($supplierDetail->acc_id == $row->id) disabled @endif>{{$row->code}} ---- {{$row->name}}</option>
            @endforeach
        </select>
        @error('credit_account_id')
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
