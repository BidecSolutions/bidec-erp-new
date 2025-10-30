<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <form method="POST" action="{{ route('good-receipt-notes.update', $goodReceiptNoteId) }}"
            onsubmit="return validateForm()">

            @csrf
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $supplierId }}" />
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>GRN Date</label>
                    <input type="date" name="grn_date" id="grn_date" class="form-control"
                        value="{{ date('Y-m-d') }}" />
                    <input type="hidden" value="{{ $goodReceiptNoteId }}" name="goodReceiptNoteId">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Description</label>
                    <textarea name="description" id="description" class="form-control">-</textarea>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>PO Detail</label>
                    <div id="poRows">
                        @foreach ($assignedPOIds as $index => $poId)
                            @php
                                $poRow = collect($purchaseOrders)->firstWhere('id', $poId);
                                $rowNumber = $index + 1;
                            @endphp
                            <div id="removeGRNR_{{ $rowNumber }}">
                                <div class="lineHeight">&nbsp;</div>
                                <div class="row">
                                    <div id="removeGRNR_{{ $rowNumber }}" class="row-border">
                                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                            <input type="hidden" name="poRowsArray[]" value="{{ $rowNumber }}" />
                                            <select name="po_data_detail_{{ $rowNumber }}"
                                                id="po_data_detail_{{ $rowNumber }}"
                                                class="form-control po-selection"
                                                onchange="handleSelection({{ $rowNumber }})">
                                                <option value="">Select PO Detail</option>
                                                @foreach ($purchaseOrders as $po)
                                                    @php
                                                        $isSelected = $po->id == $poId ? 'selected' : '';
                                                    @endphp
                                                    <option
                                                        value="{{ $po->id }}<*>{{ $po->purchase_order_id }}<*>{{ $po->purchase_order_qty }}<*>{{ $po->previous_receipt_qty }}<*>{{ $po->quotation_no ?? '' }}<*>{{ $po->expiry_date ?? '' }}<*>{{ $po->receive_qty ?? '' }}"
                                                        {{ $isSelected }}>
                                                        {{ $po->po_no }} - {{ $po->po_date }} -
                                                        {{ $po->product_name }} -
                                                        {{ $po->size_name }} - {{ $po->purchase_order_qty }} -
                                                        {{ $po->previous_receipt_qty }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-top:30px;">
                                            <button class="btn btn-xs btn-danger"
                                                onclick="removeGoodReceiptNoteRow({{ $rowNumber }})">Remove</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                            id="poDataDetail{{ $rowNumber }}"></div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">

                    <input type="button" value="Add More Rows" onclick="addMoreRows()" class="btn btn-sm btn-info" />
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
     $('.po-selection').select2();
    // Add this validation function
    function validateForm() {
        const poRows = $('[id^="removeGRNR_"]').length;
        if (poRows === 0) {
            alert('At least one PO Detail must be selected!');
            return false;
        }
        return true;
    }
    let x = {{ count($assignedPOIds) }};

    function addMoreRows() {
        x++;
        const data = `
            <div id="removeGRNR_${x}">
                <div class="lineHeight">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        <label>PO Detail</label>
                        <input type="hidden" name="poRowsArray[]" value="${x}" />
                        <select name="po_data_detail_${x}" id="po_data_detail_${x}" class="form-control po-selection" onchange="handleSelection(${x})">
                            <option value="">Select PO Detail</option>
                             @foreach ($purchaseOrders as $poRow)
                            @php
                                $isSelected = in_array($poRow->id, $assignedPOIds) ? 'selected' : '';
                            @endphp
                            <option
                                value="{{ $poRow->id . '<*>' . $poRow->purchase_order_id . '<*>' . $poRow->purchase_order_qty . '<*>' . $poRow->previous_receipt_qty }}"
                                {{ $isSelected }}>
                                {{ $poRow->po_no }} - {{ $poRow->po_date }} - {{ $poRow->product_name }} -
                                {{ $poRow->size_name }} - {{ $poRow->purchase_order_qty }} -
                                {{ $poRow->previous_receipt_qty }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-top:30px;">
                        <button class="btn btn-xs btn-danger" onclick="removeGoodReceiptNoteRow(${x})">Remove</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="poDataDetail${x}"></div>
                </div>
            </div>
        `;
        $('#poRows').append(data);
    }

    function handleSelection(id) {
        const selectedValues = [];
        $('.po-selection').each(function() {
            if ($(this).val()) {
                selectedValues.push($(this).val().split('<*>')[0]);
            }
        });

        const selectedValue = $(`#po_data_detail_${id}`).val();
        if (!selectedValue) return;

        const [poDataId, purchaseOrderId, purchaseOrderQty, previousReceiptQty, quotationNo, expiryDate, receiveQty] =
        selectedValue.split('<*>');
        const remainingQty = purchaseOrderQty - previousReceiptQty;

        if (selectedValues.filter(val => val === poDataId).length > 1) {
            alert('The same PO Detail cannot be selected multiple times.');
            $(`#po_data_detail_${id}`).val('');
            $(`#poDataDetail${id}`).html('');
            return;
        }

        const data = `
        <div class="lineHeight">&nbsp;</div>
        <div class="row">
            <input type="hidden" name="purchase_order_id_${id}" value="${purchaseOrderId}" />
            <input type="hidden" name="po_data_id_${id}" value="${poDataId}" />
            
            <div class="col-lg-3">
                <label>Quotation No</label>
                <input type="text" name="quotation_no_${id}" class="form-control" value="${quotationNo}" />
            </div>
            
            <div class="col-lg-3">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date_${id}" class="form-control" value="${expiryDate || new Date().toISOString().split('T')[0]}" />
            </div>
            
            <div class="col-lg-3">
                <label>Remaining Purchase Order Qty</label>
                <input type="number" value="${remainingQty}" disabled class="form-control" />
            </div>
            
            <div class="col-lg-3">
                <label>Receipt Qty</label>
                <input type="number" name="receive_qty_${id}" class="form-control" value="${receiveQty}" />
            </div>
        </div>
    `;
        $(`#poDataDetail${id}`).html(data);
    }

    function removeGoodReceiptNoteRow(rowId) {
        $(`#removeGRNR_${rowId}`).remove();
    }
    $(document).ready(function() {
        $('.po-selection').each(function() {
            if ($(this).val()) {
                let id = $(this).attr('id').split('_').pop(); // Extract row number
                handleSelection(id);
            }
        });
    });
</script>
<!-- Add this CSS -->
<style>
    .po-selection {
        border: 1px solid #ccc !important;
    }

    .row-border {
        border: 2px solid transparent;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
