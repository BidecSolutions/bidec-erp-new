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
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">P.O Date</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" value="{{ old('po_date', $purchaseOrder->po_date) }}"
                                                class="form-control requiredField" name="po_date" id="po_date" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Delivery Place</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text"
                                                value="{{ old('delivery_place', $purchaseOrder->delivery_place) }}"
                                                class="form-control" name="delivery_place" id="delivery_place" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Invoice/Quotation No.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text"
                                                value="{{ old('invoice_quotation_no', $purchaseOrder->invoice_quotation_no) }}"
                                                class="form-control requiredField" name="quotation_no" id="quotation_no" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Quotation Date</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date"
                                                value="{{ old('quotation_date', $purchaseOrder->quotation_date) }}"
                                                class="form-control requiredField" name="quotation_date"
                                                id="quotation_date" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label class="sf-label">Remarks</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;"
                                                class="form-control">{{ old('main_description', $purchaseOrder->main_description) }}</textarea>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Payment Type</label>
                                            <select class="form-control" name="paymentType" id="paymentType">
                                                <option value="">Select Payment Type</option>
                                                @foreach ($payment_types as $ptRow)
                                                    <option value="{{ $ptRow['id'] }}"
                                                        {{ $ptRow['id'] == $purchaseOrder->paymentType ? 'selected' : '' }}>
                                                        {{ $ptRow['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Payment Type Rate</label>
                                            <input type="number" readonly name="payment_type_rate" id="payment_type_rate"
                                                step="0.001"
                                                value="{{ old('payment_type_rate', $purchaseOrder->payment_type_rate) }}"
                                                class="form-control" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Supplier Name</label>
                                            <select class="form-control" name="supplier_id" id="supplier_id">
                                                <option value="">Select Supplier Name</option>
                                                @foreach ($suppliers as $sRow)
                                                    <option value="{{ $sRow['id'] }}"
                                                        {{ $sRow['id'] == $purchaseOrder->supplier_id ? 'selected' : '' }}>
                                                        {{ $sRow['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <label class="sf-label">Note</label>
                                            <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control">{{ old('po_note', $purchaseOrder->po_note) }}</textarea>
                                        </div>
                                    </div>
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
                                                        @foreach ($purchaseOrderData as $index => $data)
                                                            <tr id="row_{{ $index + 1 }}">
                                                                <td>
                                                                    <select
                                                                        name="poDataArray[{{ $index }}][product_id]"
                                                                        id="productId_{{ $index }}"
                                                                        class="form-control requiredField">
                                                                        <option value="">Select Product Detail</option>
                                                                        @foreach ($products as $product)
                                                                            <optgroup label="{{ $product['name'] }}">
                                                                                @foreach ($product['variants'] as $variant)
                                                                                    <option value="{{ $variant['id'] }}"
                                                                                        {{ $variant['id'] == $data->product_variant_id ? 'selected' : '' }}>
                                                                                        {{ $variant['size_name'] }} -
                                                                                        {{ $data->product_id }} -
                                                                                        {{ number_format($variant['amount'], 2) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $index }}][qty]"
                                                                        id="qty_{{ $index }}"
                                                                        value="{{ $data->qty }}" class="form-control"
                                                                        oninput="calculateSubtotal({{ $index }})" />
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $index }}][unit_price]"
                                                                        id="unitPrice_{{ $index }}"
                                                                        value="{{ $data->unit_price }}"
                                                                        class="form-control"
                                                                        oninput="calculateSubtotal({{ $index }})" />
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $index }}][sub_total]"
                                                                        id="subTotal_{{ $index }}"
                                                                        value="{{ $data->sub_total }}"
                                                                        class="form-control" readonly />
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="removeRow({{ $index + 1 }})">Remove</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="addMorePurchaseOrdersDetailRows()">Add More Rows</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                            <button type="reset" id="reset" class="btn btn-primary">Clear
                                                Form</button>
                                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                                        </div>
                                    </div>
                                </div>
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
        var rowCounter = {{ count($purchaseOrderData) }};

        function touglePurchaseOrderPaymentRate() {
            var paymentTypeTwo = $('#paymentTypeTwo').val();
            const paymentTypeSplit = paymentTypeTwo.split('<*>');
            var paymentType = $('#paymentType').val(paymentTypeSplit[0]);
            var conversionRateType = paymentTypeSplit[1];
            if (conversionRateType == 2) {
                $('#payment_type_rate').removeAttr('readonly');
                $('#payment_type_rate').val(paymentTypeSplit[2]);
            } else {
                $('#payment_type_rate').val(paymentTypeSplit[2]);
                $('#payment_type_rate').attr('readonly', 'readonly');
            }
        }

        function addMorePurchaseOrdersDetailRows() {
            var newIndex = rowCounter + 1;
            var newRow = `
            <tr id="row_${newIndex}">
               <td>
                <select name="poDataArray[${newIndex}][product_id]" class="form-control requiredField">
                    <option value="">Select Product Detail</option>
                    @foreach ($products as $product)
                        <optgroup label="{{ $product['name'] }}">
                            @foreach ($product['variants'] as $variant)
                                <option value="{{ $variant['id'] }}">{{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
               </td>
               <td>
                <input type="number" 
                       name="poDataArray[${newIndex}][qty]" 
                       class="form-control" 
                       oninput="calculateSubtotal(${newIndex})" />
               </td>
               <td>
                <input type="number" 
                       name="poDataArray[${newIndex}][unit_price]" 
                       class="form-control" 
                       oninput="calculateSubtotal(${newIndex})" />
               </td>
               <td>
                <input type="number" 
                       name="poDataArray[${newIndex}][sub_total]" 
                       class="form-control" 
                       readonly />
                </td>
                <td>
                <button type="button" 
                        class="btn btn-danger btn-sm" 
                        onclick="removeRow(${newIndex})">Remove</button>
                </td>
            </tr>`;
            $('#purchaseOrderTable tbody').append(newRow);
            rowCounter++; // Increment after adding
        }

        function calculateSubtotal(rowId) {
            var qty = parseFloat($(`input[name="poDataArray[${rowId}][qty]"]`).val()) || 0;
            var unitPrice = parseFloat($(`input[name="poDataArray[${rowId}][unit_price]"]`).val()) || 0;
            var subTotal = qty * unitPrice;
            $(`input[name="poDataArray[${rowId}][sub_total]"]`).val(subTotal.toFixed(2));
        }

        function removeRow(rowId) {
            $(`#row_${rowId}`).remove();
        }

        function calculateSubtotal(rowId) {
            var qty = parseFloat($(`input[name="poDataArray[${rowId}][qty]"]`).val()) || 0;
            var unitPrice = parseFloat($(`input[name="poDataArray[${rowId}][unit_price]"]`).val()) || 0;
            var subTotal = qty * unitPrice;
            $(`input[name="poDataArray[${rowId}][sub_total]"]`).val(subTotal.toFixed(2));
        }
    </script>
@endsection
