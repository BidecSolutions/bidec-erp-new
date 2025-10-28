@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Direct Sale Invoice')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('direct-sale-invoices.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('direct-sale-invoices.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="sf-label">S.I.Date.</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <input type="date" class="form-control requiredField" name="si_date" id="si_date" value="{{date('Y-m-d')}}" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Customer Name</label>
                                        <select class="form-control select2" name="customer_id" id="customer_id">
                                            <option value="">Select Customer Name</option>
                                            @foreach($customers as $sRow)
                                                <option value="{{$sRow['id']}}">{{$sRow['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="sf-label">Remarks</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <textarea name="main_description" id="main_description" rows="2" style="resize:none;" class="form-control">-</textarea>
                                    </div>
                                </div>
                                <div class="row hidden">
                                    <div class="col-lg-6">
                                        <label class="sf-label">Note</label>
                                        <span class="rflabelsteric"><strong>*</strong></span>
                                        <textarea name="si_note" id="si_note" rows="2" style="resize:none;" class="form-control">-</textarea>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="sf-label">Payment Type</label>
                                        <select class="form-control select2" name="paymentTypeTwo" id="paymentTypeTwo" onchange="tougleDirectSaleInvoicePaymentRate()">
                                            @foreach($payment_types as $ptRow)
                                                <option value="{{$ptRow['id']}}<*>{{$ptRow['rate_type']}}<*>{{$ptRow['conversion_rate']}}">{{$ptRow['name']}}</option>
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
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered sf-table-list" id="directSaleInvoiceTable">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Product</th>
                                                        <th class="text-center">Sell Qty.</th>
                                                        <th class="text-center">Unit Price</th>
                                                        <th class="text-center">Sub Total</th>
                                                        <th class="text-center">Average Purchase Rate</th>
                                                        <th class="text-center">Average Purchase Amount</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="row_1">
                                                        <td>
                                                            <input type="hidden" name="siDataArray[]" value="1" />
                                                            <select name="productId_1" id="productId_1" class="form-control requiredField select2" onchange="loadAveragePurchaseRate('1')">
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
                                                        <td>
                                                            <input type="number" name="qty_1" id="qty_1" class="form-control" oninput="calculateSubtotal(1)" />
                                                        </td>
                                                        <td>
                                                            <input type="number" name="unitPrice_1" id="unitPrice_1" class="form-control" oninput="calculateSubtotal(1)" />
                                                        </td>
                                                        <td>
                                                            <input type="number" name="subTotal_1" id="subTotal_1" class="form-control" readonly />
                                                        </td>
                                                        <td>
                                                            <input type="number" name="averagePurchaseRate_1" id="averagePurchaseRate_1" class="form-control" readonly />
                                                        </td>
                                                        <td>
                                                            <input type="number" name="averagePurchaseAmount_1" id="averagePurchaseAmount_1" class="form-control" readonly />
                                                        </td>
                                                        <td class="text-center">---</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <input type="button" class="btn btn-sm btn-primary" onclick="addMoreDirectSaleInvoiceDetailRows()" value="Add More Rows" />
                                                </div>
                                                <div class="col-lg-8">
                                                    <label>Total Amount</label>
                                                    <input type="number" name="total_amount" id="total_amount" class="form-control" value="0" readonly />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">&nbsp;</div>
                                                <div class="col-lg-8">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <label class="sf-label">Tax Account</label>
                                                            <select name="tax_account_id" id="tax_account_id" onchange="toggleTaxAmount()" class="form-control select2">
                                                                <option value="">Select Tax Account</option>
                                                                @foreach($tax_accounts as $ta)
                                                                    <option value="{{$ta['acc_id']}}">{{$ta['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label>Tax Amount</label>
                                                            <input type="number" name="tax_amount" id="tax_amount" readonly class="form-control" value="0" oninput="calculateTotal()" />
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-12">
                                                            <label><strong>Net Amount (After Tax)</strong></label>
                                                            <input type="number" name="net_amount" id="net_amount" class="form-control" value="0" readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lineHeight">&nbsp;</div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>Debit Account Detail</label>
                                                    <select class="form-control select2 account-select requiredField" required name="debit_account_id" id="debit_account_id">
                                                        <option value="">-- Select --</option>
                                                        <?php foreach($allChartOfAccounts as $acoaRow){ ?>
                                                            <option value="{{$acoaRow->id}}">{{$acoaRow->code}} ---- {{$acoaRow->name}}</option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>Credit Account Detail</label>
                                                    <select class="form-control select2 account-select requiredField" required name="credit_account_id" id="credit_account_id">
                                                        <option value="">-- Select --</option>
                                                        <?php foreach($allChartOfAccounts as $acoaRow){ ?>
                                                            <option value="{{$acoaRow->id}}">{{$acoaRow->code}} ---- {{$acoaRow->name}}</option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> <!-- /table -->
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
        var totalAmount = $('#total_amount').val();
        if (taxAccount) {
            taxAmountField.removeAttribute("readonly");
        } else {

            taxAmountField.value = 0; // reset value
            $('#net_amount').val(totalAmount);
            taxAmountField.setAttribute("readonly", "readonly");
        }
    }
    function tougleDirectSaleInvoicePaymentRate(){
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
    tougleDirectSaleInvoicePaymentRate();

    var rowCounter = 1;
    function addMoreDirectSaleInvoiceDetailRows() {
        rowCounter++;
        var newRow = `
            <tr id="row_${rowCounter}">
                <td>
                    <input type="hidden" name="siDataArray[]" value="${rowCounter}" />
                    <select name="productId_${rowCounter}" id="productId_${rowCounter}" class="form-control requiredField productSelection"  onchange="loadAveragePurchaseRate(${rowCounter})">
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
                <td><input type="number" name="averagePurchaseRate_${rowCounter}" id="averagePurchaseRate_${rowCounter}" class="form-control" readonly /></td>
                <td><input type="number" name="averagePurchaseAmount_${rowCounter}" id="averagePurchaseAmount_${rowCounter}" class="form-control" readonly /></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeDirectSaleInvoiceRow(${rowCounter})">Remove</button></td>
            </tr>`;
        $('#directSaleInvoiceTable tbody').append(newRow);
        $('.productSelection').select2();
    }

    function removeDirectSaleInvoiceRow(rowId) {
        $(`#row_${rowId}`).remove();
        calculateTotal();
    }

    function calculateSubtotal(rowId) {
        var qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
        var unitPrice = parseFloat($(`#unitPrice_${rowId}`).val()) || 0;
        var avgPurRate = parseFloat($(`#averagePurchaseRate_${rowId}`).val()) || 0;
        var subTotal = qty * unitPrice;
        var avgPurAmount = qty * avgPurRate;
        $(`#subTotal_${rowId}`).val(subTotal.toFixed(2));
        $(`#averagePurchaseAmount_${rowId}`).val(avgPurAmount.toFixed(2));
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;
        $("input[name^='subTotal_']").each(function(){
            total += parseFloat($(this).val()) || 0;
        });
        $("#total_amount").val(total.toFixed(2));

        var tax = parseFloat($("#tax_amount").val()) || 0;
        var netAmount = total + tax;
        $("#net_amount").val(netAmount.toFixed(2));
    }

    function loadAveragePurchaseRate(counter) {
        var productVariantId = $('#productId_' + counter).val();

        if (!productVariantId) return;
        $.ajax({
            url: "{{ route('direct-sale-invoices.product-wise-average-rate') }}",
            type: "GET",
            data: {
                product_variant_id: productVariantId
            },
            success: function (response) {
                if (response.success) {
                    $("#averagePurchaseRate_" + counter).val(response.avg_purchase_rate);
                } else {
                    $("#averagePurchaseRate_" + counter).val(0);
                    alert(response.message || "No record found");
                }
            },
            error: function () {
                alert("Error fetching average purchase rate.");
            }
        });
    }
</script>
@endsection
