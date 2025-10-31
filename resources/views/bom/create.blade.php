@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('Add New BOM')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('bom.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('bom.store') }}">
                @csrf
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label class="sf-label">BOM Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('bom_date') is-invalid @enderror" 
                                name="bom_date" id="bom_date" value="{{ old('bom_date', date('Y-m-d')) }}">
                            @error('bom_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label class="sf-label">Finish Product</label>
                            <select class="form-control select2 @error('finish_product_id') is-invalid @enderror" name="finish_product_id" id="finish_product_id">
                                <option value="">Select Finish Product</option>
                                @foreach($finishProducts as $sRow)
                                    <option value="{{$sRow['id']}}" {{ old('finish_product_id') == $sRow['id'] ? 'selected' : '' }}>
                                        {{$sRow['name']}}
                                    </option>
                                @endforeach
                            </select>
                            @error('finish_product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label class="sf-label">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remarks" id="remarks" rows="2" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', '-') }}</textarea>
                            @error('remarks')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                    <div class="lineHeight">&nbsp;</div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered sf-table-list" id="purchaseOrderTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Row Product</th>
                                            <th class="text-center">Qty.</th>
                                            <th class="text-center">Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $row = 1; @endphp
                                        @foreach(old('bomDataArray', [1]) as $rowIndex)
                                        <tr id="row_{{ $row }}">
                                            <td>
                                                <input type="hidden" name="bomDataArray[]" value="{{ $row }}">
                                                <select name="rowProductId_{{ $row }}" id="rowProductId_{{ $row }}" class="form-control select2 @error('productId_'.$row) is-invalid @enderror" onchange="fetchLastPurchasePrice({{ $row }})">
                                                    <option value="">Select Product Detail</option>
                                                    @foreach($rawProducts as $product)
                                                        <optgroup label="{{ $product['name'] }}">
                                                            @foreach($product['variants'] as $variant)
                                                                <option value="{{ $variant['id'] }}" {{ old('rowProductId_'.$row) == $variant['id'] ? 'selected' : '' }}>
                                                                    {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                @error('rowProductId_'.$row)
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </td>

                                            <td>
                                                <input type="number" name="qty_{{ $row }}" id="qty_{{ $row }}" value="{{ old('qty_'.$row) }}" class="form-control @error('qty_'.$row) is-invalid @enderror" oninput="calculateSubtotal({{ $row }})">
                                                @error('qty_'.$row)
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </td>

                                            <td>
                                                <textarea name="description_{{ $row }}" id="description_{{ $row }}" rows="2" class="form-control @error('description_{{ $row }}') is-invalid @enderror">{{ old('description', '-') }}</textarea>
                                                @error('description')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </td>

                                            <td class="text-center">---</td>
                                        </tr>
                                        @php $row++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="button" class="btn btn-sm btn-primary" onclick="addMorePurchaseOrdersDetailRows()" value="Add More Rows">
                            </div>
                        </div>
                    </div>

                    <div class="lineHeight">&nbsp;</div>

                    {{-- ========== Buttons ========== --}}
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                            <button type="submit" class="btn btn-success btn-sm">Submit</button>
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
                    <input type="hidden" name="bomDataArray[]" id="bomDataArray" value="${rowCounter}" />
                    <select name="rowProductId_${rowCounter}" id="rowProductId_${rowCounter}" class="form-control requiredField new-select2" onchange="fetchLastPurchasePrice(${rowCounter})">
                        <option value="">Select Product Detail</option>
                            @foreach($rawProducts as $product)
                                <optgroup label="{{ $product['name'] }}">
                                    @foreach($product['variants'] as $variant)
                                        <option value="{{ $variant['id'] }}" {{ old('rowProductId_'.$row) == $variant['id'] ? 'selected' : '' }}>
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
                    <textarea name="description_${rowCounter}" id="description" rows="2" class="form-control @error('description_${rowCounter}') is-invalid @enderror">{{ old('description_${rowCounter}', '-') }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
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
        $('select[name^="rowProductId_"]').each(function () {
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
