@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Add New Purchase Order') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="row">
           <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder->id) }}">
    @csrf


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        {{-- ========== Row 1: Dates & Quotation ========== --}}
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <label class="sf-label">P.O Date <span class="text-danger">*</span></label>
    <input type="date"
           class="form-control @error('po_date') is-invalid @enderror"
           name="po_date"
           id="po_date"
           value="{{ old('po_date', $purchaseOrder->po_date) }}">
    @error('po_date')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label class="sf-label">Invoice/Quotation No. <span class="text-danger">*</span></label>
                <input type="text" class="form-control"
                       name="quotation_no" id="quotation_no"
                       value="{{ old('quotation_no', $purchaseOrder->invoice_quotation_no) }}"
                       placeholder="Invoice/Quotation No.">
            </div>

         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <label class="sf-label">Quotation Date <span class="text-danger">*</span></label>
    <input type="date"
           class="form-control @error('quotation_date') is-invalid @enderror"
           name="quotation_date"
           id="quotation_date"
           value="{{ old('quotation_date', $purchaseOrder->quotation_date) }}">
    @error('quotation_date')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

        </div>

        <div class="lineHeight">&nbsp;</div>

        {{-- ========== Row 2: Remarks & Payment Type ========== --}}
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label class="sf-label">Remarks <span class="text-danger">*</span></label>
                <textarea name="main_description" id="main_description" rows="2"
                          class="form-control">{{ old('main_description', $purchaseOrder->main_description) }}</textarea>
            </div>

 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <label class="sf-label">Payment Type <span class="text-danger">*</span></label>
    <select class="form-control @error('paymentType') is-invalid @enderror"
            name="paymentTypeTwo" id="paymentTypeTwo"
            onchange="touglePurchaseOrderPaymentRate()">
        <option value="">Select Payment Type</option>
        @foreach($payment_types as $ptRow)
            <option value="{{ $ptRow['id'] }}<*>{{ $ptRow['rate_type'] }}<*>{{ $ptRow['conversion_rate'] }}"
                {{ old('paymentType', $purchaseOrder->paymentType) == $ptRow['id'] ? 'selected' : '' }}>
                {{ $ptRow['name'] }}
            </option>
        @endforeach
    </select>
    <input type="hidden" name="paymentType" id="paymentType"
           value="{{ old('paymentType', $purchaseOrder->paymentType) }}">
    @error('paymentType')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <label class="sf-label">Payment Type Rate <span class="text-danger">*</span></label>
    <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001"
           value="{{ old('payment_type_rate', $purchaseOrder->payment_type_rate ?? 1) }}"
           class="form-control @error('payment_type_rate') is-invalid @enderror">
    @error('payment_type_rate')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


        </div>

        <div class="lineHeight">&nbsp;</div>

        {{-- ========== Row 3: Supplier & Note ========== --}}
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <label class="sf-label">Supplier Name <span class="text-danger">*</span></label>
    <select class="form-control select2 @error('supplier_id') is-invalid @enderror"
            name="supplier_id" id="supplier_id">
        <option value="">Select Supplier</option>
        @foreach($suppliers as $sRow)
            <option value="{{ $sRow['id'] }}"
                {{ old('supplier_id', $purchaseOrder->supplier_id) == $sRow['id'] ? 'selected' : '' }}>
                {{ $sRow['name'] }}
            </option>
        @endforeach
    </select>
    @error('supplier_id')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <label class="sf-label">Note <span class="text-danger">*</span></label>
                <textarea name="po_note" id="po_note" rows="2"
                          class="form-control">{{ old('po_note', $purchaseOrder->po_note) }}</textarea>
            </div>
        </div>

        <div class="lineHeight">&nbsp;</div>

        {{-- ========== Row 4: Product Table ========== --}}
        <div class="row">
            <div class="col-lg-12">
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
                 @foreach ($purchaseOrderData as $index => $data)
<tr id="row_{{ $index + 1 }}">
    <td>
        <select
            name="poDataArray[{{ $index + 1 }}][product_id]"
            id="productId_{{ $index + 1 }}"
            class="form-control @error('poDataArray.' . ($index + 1) . '.product_id') is-invalid @enderror">
            <option value="">Select Product Detail</option>
            @foreach ($products as $product)
                <optgroup label="{{ $product['name'] }}">
                    @foreach ($product['variants'] as $variant)
                        <option value="{{ $variant['id'] }}"
                            {{ $variant['id'] == $data->product_variant_id ? 'selected' : '' }}>
                            {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('poDataArray.' . ($index + 1) . '.product_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </td>

    <td>
        <input type="number" name="poDataArray[{{ $index + 1 }}][qty]"
               id="qty_{{ $index + 1 }}"
               value="{{ $data->qty }}"
               class="form-control @error('poDataArray.' . ($index + 1) . '.qty') is-invalid @enderror"
               oninput="calculateSubtotal({{ $index + 1 }})" />
        @error('poDataArray.' . ($index + 1) . '.qty')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </td>

    <td>
        <input type="number" name="poDataArray[{{ $index + 1 }}][unit_price]"
               id="unitPrice_{{ $index + 1 }}"
               value="{{ $data->unit_price }}"
               class="form-control @error('poDataArray.' . ($index + 1) . '.unit_price') is-invalid @enderror"
               oninput="calculateSubtotal({{ $index + 1 }})" />
        @error('poDataArray.' . ($index + 1) . '.unit_price')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </td>

    <td>
        <input type="number" name="poDataArray[{{ $index + 1 }}][sub_total]"
               id="subTotal_{{ $index + 1 }}"
               value="{{ $data->sub_total }}"
               class="form-control @error('poDataArray.' . ($index + 1) . '.sub_total') is-invalid @enderror"
               readonly />
        @error('poDataArray.' . ($index + 1) . '.sub_total')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </td>

    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow({{ $index + 1 }})">Remove</button>
    </td>
</tr>
@endforeach


                        </tbody>
                        @error('poDataArray')
    <tr>
        <td colspan="5">
            <small class="text-danger d-block text-center">
                {{ $message }}
            </small>
        </td>
    </tr>
@enderror
 
                    </table>

                    <button type="button" class="btn btn-primary btn-sm"
                            onclick="addMorePurchaseOrdersDetailRows()">Add More Rows</button>
                </div>
            </div>
        </div>

        <div class="lineHeight">&nbsp;</div>

        {{-- ========== Buttons ========== --}}
        <div class="row">
            <div class="col-lg-12 text-right">
                <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                <button type="submit" class="btn btn-success btn-sm">Update</button>
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

 var rowCounter = {{ count($purchaseOrderData) }}; // start from existing rows count

function addMorePurchaseOrdersDetailRows() {
    rowCounter++;
    var newRow = `
        <tr id="row_${rowCounter}">
            <td>
                <select name="poDataArray[${rowCounter}][product_id]" id="productId_${rowCounter}"
                        class="form-control new-select2" onchange="fetchLastPurchasePrice(${rowCounter})">
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
                <input type="number" name="poDataArray[${rowCounter}][qty]" id="qty_${rowCounter}"
                       class="form-control" oninput="calculateSubtotal(${rowCounter})" />
            </td>
            <td>
                <input type="number" name="poDataArray[${rowCounter}][unit_price]" id="unitPrice_${rowCounter}"
                       class="form-control" oninput="calculateSubtotal(${rowCounter})" />
            </td>
            <td>
                <input type="number" name="poDataArray[${rowCounter}][sub_total]" id="subTotal_${rowCounter}"
                       class="form-control" readonly />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm"
                        onclick="removePurchaseOrderRow(${rowCounter})">Remove</button>
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
  $('select[id^="productId_"]').each(function () {

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

    function fetchLastPurchasePrice(id) {
    const baseUrl = $("#url").val();
    const productId = $(`#productId_${id}`).val();

    if (!productId) return;

    // ✅ Identify the row properly
    const row = $(`#productId_${id}`).closest('tr');

    $.ajax({
        url: `${baseUrl}/purchase-orders/get-last-purchase-price/${productId}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            row.find(`#unitPrice_${id}`).val('...');
        },
        success: function (response) {
            // ✅ Parse if returned as string
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    response = {};
                }
            }

            // ✅ Handle valid response
            if (response && response.price !== undefined && response.price !== null) {
                const price = parseFloat(response.price);
                row.find(`#unitPrice_${id}`).val(isNaN(price) ? 0 : price.toFixed(2));
                calculateSubtotal(id);
            } else {
                row.find(`#unitPrice_${id}`).val(0);
                alert("⚠️ No purchase rate found for this product.");
            }
        },
        error: function (xhr) {
            console.error('Error fetching purchase price:', xhr.responseText);
            alert("❌ Error fetching purchase price. Please try again.");
            row.find(`#unitPrice_${id}`).val(0);
        }
    });
    calculateSubtotal(id);
}
    
</script>
@endsection
