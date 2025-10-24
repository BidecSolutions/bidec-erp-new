@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <!-- Page Header -->
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{ CommonHelper::displayPageTitle('Edit Direct Good Receipt Note') }}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('direct-good-receipt-note.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <!-- Edit Form -->
            <div class="row">
                <form method="POST" action="{{ route('direct-good-receipt-note.update', $purchaseOrder->id) }}">
                    @csrf
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel-body">
                                    <!-- Header Details -->
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">P.O Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" name="po_date" id="po_date"
                                                value="{{ $purchaseOrder->po_date }}" class="form-control requiredField" />

                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Delivery Place</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control" name="delivery_place"
                                                id="delivery_place" placeholder="Delivery Place"
                                                value="{{ $purchaseOrder->delivery_place }}" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Invoice/Quotation No.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control requiredField" name="quotation_no"
                                                id="quotation_no" placeholder="Invoice/Quotation No."
                                                value="{{ $purchaseOrder->invoice_quotation_no }}" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Quotation Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" name="quotation_date"
                                                id="quotation_date" value="{{ $purchaseOrder->quotation_date }}" />
                                        </div>
                                    </div>
                                    <!-- Remarks and Payment -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label class="sf-label">Remarks</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;"
                                                class="form-control">{{ $purchaseOrder->main_description }}</textarea>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Payment Type</label>
                                            <select class="form-control" name="paymentTypeTwo" id="paymentTypeTwo"
                                                onchange="tougleDirectGoodReceiptNotePaymentRate()">
                                                <option value="">Select Payment Type</option>
                                                @foreach ($payment_types as $ptRow)
                                                    <option value="{{ $ptRow['id'] }}"
                                                        data-rate-type="{{ $ptRow['rate_type'] }}"
                                                        data-conversion-rate="{{ $ptRow['conversion_rate'] }}"
                                                        {{ $purchaseOrder->payment_type == $ptRow['id'] ? 'selected' : '' }}>
                                                        {{ $ptRow['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="paymentType" id="paymentType"
                                                value="{{ $purchaseOrder->payment_type }}" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Payment Type Rate</label>
                                            <input type="number" readonly name="payment_type_rate" id="payment_type_rate"
                                                step="0.001" value="{{ $purchaseOrder->payment_type_rate }}"
                                                class="form-control" />
                                        </div>

                                    </div>

                                    <!-- Supplier and Note -->
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <label class="sf-label">Supplier Name</label>
                                            <select class="form-control" name="supplier_id" id="supplier_id">
                                                <option value="">Select Supplier Name</option>
                                                @foreach ($suppliers as $sRow)
                                                    @php
                                                        $selected =
                                                            $purchaseOrder->supplier_id == $sRow['id']
                                                                ? 'selected'
                                                                : '';
                                                    @endphp
                                                    <option value="{{ $sRow['id'] }}" {{ $selected }}>
                                                        {{ $sRow['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <label class="sf-label">Note</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control">{{ $purchaseOrder->po_note }}</textarea>
                                        </div>
                                    </div>
                                    <!-- Product Details Table -->
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered sf-table-list"
                                                    id="directGoodReceiptNoteTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Receive Qty.</th>
                                                            <th class="text-center">Unit Price</th>
                                                            <th class="text-center">Sub Total</th>
                                                            <th class="text-center">Expiry Date</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $rowCounter = 0;
                                                        @endphp
                                                        @foreach ($purchaseOrderData as $data)
                                                            @php
                                                                $rowCounter++;
                                                                $subTotal = $data->qty * $data->unit_price;
                                                            @endphp
                                                            <tr id="row_{{ $rowCounter }}">
                                                                <td>

                                                                    <select
                                                                        name="poDataArray[{{ $rowCounter }}][product_id]"
                                                                        id="productId_{{ $rowCounter }}"
                                                                        class="form-control requiredField">
                                                                        <option value="">Select Product Detail</option>
                                                                        @foreach ($products as $product)
                                                                            <optgroup label="{{ $product['name'] }}">
                                                                                @foreach ($product['variants'] as $variant)
                                                                                    @php
                                                                                        $selected =
                                                                                            $data->product_id ==
                                                                                            $variant['id']
                                                                                                ? 'selected'
                                                                                                : '';
                                                                                    @endphp
                                                                                    <option value="{{ $variant['id'] }}"
                                                                                        {{ $selected }}>
                                                                                        {{ $variant['size_name'] }} -
                                                                                        {{ number_format($variant['amount'], 2) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $rowCounter }}][qty]"
                                                                        id="qty_{{ $rowCounter }}"
                                                                        value="{{ $data->qty }}" class="form-control"
                                                                        oninput="calculateSubtotal({{ $rowCounter }})" />
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $rowCounter }}][unit_price]"
                                                                        id="unitPrice_{{ $rowCounter }}"
                                                                        value="{{ $data->unit_price }}"
                                                                        class="form-control"
                                                                        oninput="calculateSubtotal({{ $rowCounter }})" />
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="poDataArray[{{ $rowCounter }}][sub_total]"
                                                                        id="subTotal_{{ $rowCounter }}"
                                                                        value="{{ number_format($subTotal, 2, '.', '') }}"
                                                                        class="form-control" readonly />
                                                                </td>
                                                                <td>
                                                                    <input type="date"
                                                                        name="poDataArray[{{ $rowCounter }}][expiry_date]"
                                                                        id="expiryDate_{{ $rowCounter }}"
                                                                        value="{{ $data->expiry_date }}"
                                                                        class="form-control" />
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="removeDirectGoodReceiptNoteRow({{ $rowCounter }})">Remove</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div>
                                                    <input type="button" class="btn btn-sm btn-primary"
                                                        onclick="addMoreDirectGoodReceiptNoteDetailRows()"
                                                        value="Add More Rows" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Product Details Table -->
                                </div>
                            </div>
                        </div>
                        <!-- Form Actions -->
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
        function tougleDirectGoodReceiptNotePaymentRate() {
            var selectedOption = $('#paymentTypeTwo option:selected');
            var paymentTypeId = selectedOption.val();
            var rateType = selectedOption.data('rate-type');
            var conversionRate = selectedOption.data('conversion-rate');

            // Update the hidden field with the payment type id.
            $('#paymentType').val(paymentTypeId);

            // If rate type equals 2, allow editing; otherwise set it to readonly.
            if (rateType == 2) {
                $('#payment_type_rate').removeAttr('readonly');
                $('#payment_type_rate').val(conversionRate);
            } else {
                $('#payment_type_rate').val(conversionRate);
                $('#payment_type_rate').attr('readonly', 'readonly');
            }
        }




        // Call the function on page load to set the default payment type rate.

        // Initialize rowCounter to the last row number from existing data
        var rowCounter = {{ $rowCounter }};

        function addMoreDirectGoodReceiptNoteDetailRows() {
            rowCounter++;
            var newRow = `
         <tr id="row_${rowCounter}">
            <td>
                <select name="poDataArray[${rowCounter}][product_id]" id="productId_${rowCounter}" class="form-control requiredField">
                    <option value="">Select Product Detail</option>
                    @foreach ($products as $product)
                        <optgroup label="{{ $product['name'] }}">
                            @foreach ($product['variants'] as $variant)
                                <option value="{{ $variant['id'] }}">
                                    {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="poDataArray[${rowCounter}][qty]" id="qty_${rowCounter}" value="" class="form-control" oninput="calculateSubtotal(${rowCounter})" />
            </td>
            <td>
                <input type="number" name="poDataArray[${rowCounter}][unit_price]" id="unitPrice_${rowCounter}" value="" class="form-control" oninput="calculateSubtotal(${rowCounter})" />
            </td>
            <td>
                <input type="number" name="poDataArray[${rowCounter}][sub_total]" id="subTotal_${rowCounter}" value="" class="form-control" readonly />
            </td>
            <td>
                <input type="date" name="poDataArray[${rowCounter}][expiry_date]" id="expiryDate_${rowCounter}" value="{{ date('Y-m-d') }}" class="form-control" />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeDirectGoodReceiptNoteRow(${rowCounter})">Remove</button>
            </td>
          </tr>`;
            $('#directGoodReceiptNoteTable tbody').append(newRow);
        }


        function removeDirectGoodReceiptNoteRow(rowId) {
            $(`#row_${rowId}`).remove();
        }

        function calculateSubtotal(rowId) {
            var qty = parseFloat(document.getElementById('qty_' + rowId).value) || 0;
            var unitPrice = parseFloat(document.getElementById('unitPrice_' + rowId).value) || 0;
            var subTotal = qty * unitPrice;
            document.getElementById('subTotal_' + rowId).value = subTotal.toFixed(2);
        }
        $(document).ready(function() {
            tougleDirectGoodReceiptNotePaymentRate();
        });
    </script>
@endsection
