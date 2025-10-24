@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{ CommonHelper::displayPageTitle('Add New Direct Good Receipt Note') }}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('direct-good-receipt-note.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('direct-good-receipt-note.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="sf-label">G.R.N Date</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <input type="date" class="form-control requiredField" name="grn_date" id="grn_date" value="{{date('Y-m-d')}}" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Delivery place</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <input type="text" class="form-control" name="delivery_place" id="delivery_place" placeholder="Delivery Place" value="Factory" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Invoice/Quotation No.</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <input type="text" class="form-control requiredField" name="quotation_no" id="quotation_no" placeholder="Invoice/Quotation No." value="-" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Quotation Date</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <input type="date" class="form-control requiredField" name="quotation_date" id="quotation_date" value="{{date('Y-m-d')}}" />
                                    </div>
                                </div>
                                <div class="row hidden">
                                    <div class="col-lg-6">
                                        <label class="sf-label">Note</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <textarea name="po_note" id="po_note" rows="2" class="form-control">-</textarea>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Payment Type</label>
                                        <select class="form-control select2" name="paymentTypeTwo" id="paymentTypeTwo" onchange="tougleDirectGoodReceiptNotePaymentRate()">
                                            @foreach($payment_types as $ptRow)
                                                <option value="{{$ptRow['id']}}<*>{{$ptRow['rate_type']}}<*>{{$ptRow['conversion_rate']}}" >{{$ptRow['name']}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="paymentType" id="paymentType" value="" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Payment Type Rate</label>
                                        <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001" value="1" class="form-control" />
                                    </div>
                                </div>
                                <div class="lineHeight">&nbsp;</div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="sf-label">Supplier Name</label>
                                        <select class="form-control select2" name="supplier_id" id="supplier_id">
                                            <option value="">Select Supplier Name</option>
                                            @foreach($suppliers as $sRow)
                                                <option value="{{$sRow['id']}}" >{{$sRow['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-8">
                                        <label class="sf-label">Remarks</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <textarea name="main_description" id="main_description" rows="2" class="form-control">-</textarea>
                                    </div>
                                </div>
                                <div class="lineHeight">&nbsp;</div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered sf-table-list" id="directGoodReceiptNoteTable">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Product</th>
                                                        <th class="text-center">Receive Qty</th>
                                                        <th class="text-center">Unit Price</th>
                                                        <th class="text-center">Sub Total</th>
                                                        <th class="text-center">Expiry Date</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="row_1">
                                                        <td>
                                                            <input type="hidden" name="poDataArray[]" value="1" />
                                                            <select name="productId_1" id="productId_1" class="form-control requiredField select2">
                                                                <option value="">Select Product Detail</option>
                                                                @foreach($products as $product)
                                                                    <optgroup label="{{ $product['name'] }}">
                                                                        @foreach($product['variants'] as $variant)
                                                                            <option value="{{ $variant['id'] }}">
                                                                                {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="qty_1" id="qty_1" class="form-control" oninput="calculateSubtotal(1)" /></td>
                                                        <td><input type="number" name="unitPrice_1" id="unitPrice_1" class="form-control" oninput="calculateSubtotal(1)" /></td>
                                                        <td><input type="number" name="subTotal_1" id="subTotal_1" class="form-control" readonly /></td>
                                                        <td class="hidden"><input type="date" name="expiryDate_1" id="expiryDate_1" value="{{date('Y-m-d')}}" class="form-control" /></td>
                                                        <td class="text-center">---</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <input type="button" class="btn btn-sm btn-primary" onclick="addMoreDirectGoodReceiptNoteDetailRows()" value="Add More Rows" />
                                                </div>
                                                <div class="col-lg-8">
                                                    <label><strong>Gross Amount</strong></label>
                                                    <input type="number" name="gross_amount" id="gross_amount" class="form-control" readonly value="0" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">&nbsp;</div>
                                                <div class="col-lg-8">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <label class="sf-label">Tax Account</label>
                                                            <select name="tax_account_id" id="tax_account_id" class="form-control select2" onchange="toggleTaxAmount()">
                                                                <option value="">Select Tax Account</option>
                                                                @foreach($tax_accounts as $ta)
                                                                    <option value="{{$ta['acc_id']}}">{{$ta['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label>Tax Amount</label>
                                                            <input type="number" name="tax_amount" id="tax_amount" class="form-control" value="0" oninput="calculateGrossAndNet()" readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-lg-4">&nbsp;</div>
                                                <div class="col-lg-8">
                                                    <label><strong>Net Amount (After Tax)</strong></label>
                                                    <input type="number" name="net_amount" id="net_amount" class="form-control" readonly value="0" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
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

@section('script')
<script>
    function toggleTaxAmount() {
        let taxAccount = document.getElementById("tax_account_id").value;
        let taxAmountField = document.getElementById("tax_amount");
        var grossAmount = $('#gross_amount').val();

        if (taxAccount) {
            taxAmountField.removeAttribute("readonly");
        } else {

            taxAmountField.value = 0; // reset value
            $('#net_amount').val(grossAmount);
            taxAmountField.setAttribute("readonly", "readonly");
        }
    }
    function tougleDirectGoodReceiptNotePaymentRate(){
        var paymentTypeTwo = $('#paymentTypeTwo').val();
        const paymentTypeSplit = paymentTypeTwo.split('<*>');
        $('#paymentType').val(paymentTypeSplit[0]);
        var conversionRateType = paymentTypeSplit[1];
        if(conversionRateType == 2){
            $('#payment_type_rate').removeAttr('readonly').val(paymentTypeSplit[2]);   
        }else{
            $('#payment_type_rate').val(paymentTypeSplit[2]).attr('readonly','readonly');
        }
    }
    tougleDirectGoodReceiptNotePaymentRate();

    var rowCounter = 1;
    function addMoreDirectGoodReceiptNoteDetailRows() {
        rowCounter++;
        var newRow = `
            <tr id="row_${rowCounter}">
                <td>
                    <input type="hidden" name="poDataArray[]" value="${rowCounter}" />
                    <select name="productId_${rowCounter}" id="productId_${rowCounter}" class="form-control requiredField productSelection">
                        <option value="">Select Product Detail</option>
                        @foreach($products as $product)
                            <optgroup label="{{ $product['name'] }}">
                                @foreach($product['variants'] as $variant)
                                    <option value="{{ $variant['id'] }}">
                                        {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="qty_${rowCounter}" id="qty_${rowCounter}" class="form-control" oninput="calculateSubtotal(${rowCounter})" /></td>
                <td><input type="number" name="unitPrice_${rowCounter}" id="unitPrice_${rowCounter}" class="form-control" oninput="calculateSubtotal(${rowCounter})" /></td>
                <td><input type="number" name="subTotal_${rowCounter}" id="subTotal_${rowCounter}" class="form-control" readonly /></td>
                <td class="hidden"><input type="date" name="expiryDate_${rowCounter}" id="expiryDate_${rowCounter}" value="{{date('Y-m-d')}}" class="form-control" /></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDirectGoodReceiptNoteRow(${rowCounter})">Remove</button>
                </td>
            </tr>`;
        $('#directGoodReceiptNoteTable tbody').append(newRow);
        $('.productSelection').select2();
    }

    function removeDirectGoodReceiptNoteRow(rowId) {
        $(`#row_${rowId}`).remove();
        calculateGrossAndNet();
    }

    function calculateSubtotal(rowId) {
        var qty = parseFloat(document.getElementById('qty_'+rowId).value) || 0;
        var unitPrice = parseFloat(document.getElementById('unitPrice_'+rowId).value) || 0;
        var subTotal = qty * unitPrice;
        document.getElementById('subTotal_'+rowId).value = subTotal.toFixed(2);
        calculateGrossAndNet();
    }

    function calculateGrossAndNet(){
        var gross = 0;
        $("input[id^='subTotal_']").each(function(){
            gross += parseFloat($(this).val()) || 0;
        });
        $("#gross_amount").val(gross.toFixed(2));

        var tax = parseFloat($("#tax_amount").val()) || 0;
        var net = gross + tax;
        $("#net_amount").val(net.toFixed(2));
    }
</script>
@endsection
