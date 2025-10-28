@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New Purchase Order')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">


            <form method="POST" action="{{ route('purchase-orders.store') }}">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @csrf
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel-body">
                                        
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">P.O Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" name="po_date" id="po_date" value="{{date('Y-m-d')}}" />
                                        </div>
                            
                                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Invoice/Quotation No.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control requiredField" name="quotation_no" id="quotation_no" placeholder="Invoice/Quotation No." value="" />
                                        </div>
                                         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Quotation Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" name="quotation_date" id="quotation_date" value="{{date('Y-m-d')}}" />
                                        </div>
                                    </div>
                                        <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                           <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Remarks</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                        </div>
                                          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Payment Type</label>
                                            <select class="form-control" name="paymentTypeTwo" id="paymentTypeTwo" onchange="touglePurchaseOrderPaymentRate()">
                                                <option value="">Select Payment Type</option>
                                                @foreach($payment_types as $ptRow)
                                                    <option value="{{$ptRow['id']}}<*>{{$ptRow['rate_type']}}<*>{{$ptRow['conversion_rate']}}" >{{$ptRow['name']}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="paymentType" id="paymentType" value="" />
                                        </div>
                                          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Payment Type Rate</label>
                                            <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001" value="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Supplier Name</label>
                                            <select class="form-control select2" name="supplier_id" id="supplier_id">
                                                <option value="">Select Supplier</option>
                                                @foreach($suppliers as $sRow)
                                                    <option value="{{$sRow['id']}}" >{{$sRow['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <label class="sf-label">Note</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
                                        </div>
                                    </div>
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered sf-table-list" id="purchaseOrderTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Qty.</th>
                                                            <th class="text-center">Unit Price</th>
                                                            <th class="text-center">Sub Total</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="row_1">
                                                            <td>
                                                                <input type="hidden" name="poDataArray[]" id="poDataArray" value="1" />
                                                                <select name="productId_1" id="productId_1" class="form-control requiredField select2" onchange="fetchLastPurchasePrice(1)">
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
                                                                <input type="number" name="qty_1" id="qty_1" value="" class="form-control" oninput="calculateSubtotal(1)" />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="unitPrice_1" id="unitPrice_1" value="" class="form-control" oninput="calculateSubtotal(1)" />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="subTotal_1" id="subTotal_1" value="" class="form-control" readonly />
                                                            </td>
                                                            <td class="text-center">
                                                                ---
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div>
                                                    <input type="button" class="btn btn-sm btn-primary" onclick="addMorePurchaseOrdersDetailRows()" value="Add More Rows" />
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

@section('script')
<script>
    function touglePurchaseOrderPaymentRate() {
        var paymentTypeTwo = $('#paymentTypeTwo').val();
        const paymentTypeSplit = paymentTypeTwo.split('<*>');
        var paymentType = $('#paymentType').val(paymentTypeSplit[0]);
        var conversionRateType = paymentTypeSplit[1];
        if(conversionRateType == 2){
            $('#payment_type_rate').removeAttr('readonly');
            $('#payment_type_rate').val(paymentTypeSplit[2]);   
        }else{
            $('#payment_type_rate').val(paymentTypeSplit[2]);
            $('#payment_type_rate').attr('readonly','readonly');
        }
    }

    var rowCounter = 1; // Keep track of the row numbers
    function addMorePurchaseOrdersDetailRows() {
        rowCounter++;
        var newRow = `
            <tr id="row_${rowCounter}">
                <td>
                    <input type="hidden" name="poDataArray[]" id="poDataArray" value="${rowCounter}" />
                    <select name="productId_${rowCounter}" id="productId_${rowCounter}" class="form-control requiredField new-select2">
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
                    <input type="number" name="qty_${rowCounter}" id="qty_${rowCounter}" value="" class="form-control" oninput="calculateSubtotal(${rowCounter})" />
                </td>
                <td>
                    <input type="number" name="unitPrice_${rowCounter}" id="unitPrice_${rowCounter}" value="" class="form-control" oninput="calculateSubtotal(${rowCounter})" />
                </td>
                <td>
                    <input type="number" name="subTotal_${rowCounter}" id="subTotal_${rowCounter}" value="" class="form-control" readonly />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removePurchaseOrderRow(${rowCounter})">Remove</button>
                </td>
            </tr>`;
        $('#purchaseOrderTable tbody').append(newRow);
        $('.new-select2').select2();
    }

    function removePurchaseOrderRow(rowId) {
        $(`#row_${rowId}`).remove();
    }

    function calculateSubtotal(rowId) {
        var qty = parseFloat(document.getElementById('qty_'+rowId).value) || 0;
        var unitPrice = parseFloat(document.getElementById('unitPrice_'+rowId).value) || 0;
        document.getElementById('subTotal_'+rowId).value = (qty * unitPrice).toFixed(2);
    }

    // Prevent duplicate product variants
    $('form').on('submit', function (e) {
        const selectedProducts = [];
        let hasDuplicate = false;
        $('select[name^="productId_"]').each(function () {
            const value = $(this).val();
            if (value) {
                if (selectedProducts.includes(value)) {
                    hasDuplicate = true;
                    return false;
                }
                selectedProducts.push(value);
            }
        });
        if (hasDuplicate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Product',
                text: 'A product cannot be added more than once in the same Purchase Order.',
                confirmButtonColor: '#d33'
            });
        }
    });
    
</script>
@endsection
