@php
    use App\Helpers\CommonHelper;
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <form method="POST" action="{{ route('good-receipt-notes.store') }}">
            @csrf
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{$supplierId}}" />
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>GRN Date</label>
                    <input type="date" name="grn_date" id="grn_date" class="form-control" value="{{date('Y-m-d')}}" />
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
                    <input type="hidden" name="poRowsArray[]" id="poRowsArray" value="1" />
                    <select name="po_data_detail_1" id="po_data_detail_1" class="form-control po-selection" onchange="handleSelection(1)">
                        <option value="">Select PO Detail</option>
                        @foreach($purchaseOrders as $poRow)
                            <option value="{{$poRow->id.'<*>'.$poRow->purchase_order_id.'<*>'.$poRow->purchase_order_qty.'<*>'.$poRow->previous_receipt_qty}}">
                                {{$poRow->po_no}} - {{CommonHelper::changeDateFormat($poRow->po_date)}} - {{$poRow->product_name}} - {{$poRow->size_name}} - {{$poRow->purchase_order_qty}} - {{$poRow->previous_receipt_qty}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="poDataDetail1"></div>
            </div>
            <div id="poRows"></div>
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
    let x = 1;
    $('.po-selection').select2();

    function addMoreRows() {
        x++;
        const data = `
            <div id="removeGRNR_${x}">
                <div class="lineHeight">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-10">
                        <label>PO Detail</label>
                        <input type="hidden" name="poRowsArray[]" value="${x}" />
                        <select name="po_data_detail_${x}" id="po_data_detail_${x}" class="form-control po-selection" onchange="handleSelection(${x})">
                            <option value="">Select PO Detail</option>
                            @foreach($purchaseOrders as $poRow)
                                <option value="{{$poRow->id.'<*>'.$poRow->purchase_order_id.'<*>'.$poRow->purchase_order_qty.'<*>'.$poRow->previous_receipt_qty}}">
                                    {{$poRow->po_no}} - {{CommonHelper::changeDateFormat($poRow->po_date)}} - {{$poRow->product_name}} - {{$poRow->size_name}} - {{$poRow->purchase_order_qty}} - {{$poRow->previous_receipt_qty}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2" style="margin-top:30px;">
                        <button class="btn btn-xs btn-danger" onclick="removeGoodReceiptNoteRow(${x})">Remove</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="poDataDetail${x}"></div>
                </div>
            </div>
        `;
        $('#poRows').append(data);
        $('.po-selection').select2();
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

        const [poDataId, purchaseOrderId, purchaseOrderQty, previousReceiptQty] = selectedValue.split('<*>');
        const remainingQty = purchaseOrderQty - previousReceiptQty;

        // Prevent duplicate selection
        if (selectedValues.filter(val => val === poDataId).length > 1) {
            alert('The same PO Detail cannot be selected multiple times.');
            $(`#po_data_detail_${id}`).val('').trigger('change');
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
                    <input type="text" name="quotation_no_${id}" class="form-control" />
                </div>

                <div class="col-lg-3">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date_${id}" class="form-control" value="${new Date().toISOString().split('T')[0]}" />
                </div>

                <div class="col-lg-3">
                    <label>Remaining Purchase Order Qty</label>
                    <input type="number" value="${remainingQty}" class="form-control" readonly />
                </div>

                <div class="col-lg-3">
                    <label>Receipt Qty</label>
                    <input type="number" 
                           name="receive_qty_${id}" 
                           class="form-control receipt-qty" 
                           data-remaining="${remainingQty}" 
                           min="0" step="0.01">
                    <small class="text-danger error-message" style="display:none;"></small>
                </div>
            </div>
        `;
        $(`#poDataDetail${id}`).html(data);
    }

    function removeGoodReceiptNoteRow(rowId) {
        $(`#removeGRNR_${rowId}`).remove();
    }

    // Validation: check Receipt Qty not greater than Remaining Qty
    $(document).on('input', '.receipt-qty', function () {
        const receiptQty = parseFloat($(this).val()) || 0;
        const remainingQty = parseFloat($(this).data('remaining')) || 0;
        const errorField = $(this).siblings('.error-message');

        if (receiptQty > remainingQty) {
            errorField.text(`Receipt Qty cannot exceed remaining quantity (${remainingQty}).`);
            errorField.show();
            $(this).addClass('is-invalid');
        } else {
            errorField.hide();
            $(this).removeClass('is-invalid');
        }
    });

    //  Prevent form submission if any invalid qty found
    $('form').on('submit', function (e) {
        let hasError = false;

        $('.receipt-qty').each(function () {
            const receiptQty = parseFloat($(this).val()) || 0;
            const remainingQty = parseFloat($(this).data('remaining')) || 0;
            const errorField = $(this).siblings('.error-message');

            if (receiptQty > remainingQty) {
                errorField.text(`Receipt Qty cannot exceed remaining quantity (${remainingQty}).`);
                errorField.show();
                $(this).addClass('is-invalid');
                hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Please correct the highlighted errors before submitting.');
        }
    });
</script>

