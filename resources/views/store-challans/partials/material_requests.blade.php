@php
    use App\Helpers\CommonHelper;
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <form method="POST" action="{{ route('store-challans.store') }}">
            @csrf
            <input type="hidden" name="department_id" id="department_id" value="{{ $departmentId }}" />

            {{-- Store Challan Info --}}
            <div class="row">
                <div class="col-lg-6">
                    <label>Store Challan Date</label>
                    <input type="date" name="store_challan_date" class="form-control" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="col-lg-6">
                    <label>Description</label>
                    <textarea name="description" class="form-control">-</textarea>
                </div>
            </div>

            <div class="lineHeight">&nbsp;</div>

            {{-- MR Details Section --}}
            <div class="row">
                <div class="col-lg-12">
                    <label>MR Detail</label>
                    <input type="hidden" name="mrRowsArray[]" value="1" />
                    <select name="mr_data_detail_1" id="mr_data_detail_1" class="form-control po-selection" onchange="handleSelection(1)">
                        <option value="">Select MR Detail</option>
                        @foreach($materialRequests as $poRow)
                            <option value="{{ $poRow->id.'<*>'.$poRow->material_request_id.'<*>'.$poRow->material_request_qty.'<*>'.$poRow->previous_issue_qty.'<*>'.$poRow->variant_id }}">
                                {{ $poRow->material_request_no }} - {{ CommonHelper::changeDateFormat($poRow->material_request_date) }} -
                                {{ $poRow->product_name }} - {{ $poRow->size_name }} -
                                {{ $poRow->material_request_qty }} - {{ $poRow->previous_issue_qty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-12" id="poDataDetail1"></div>
            </div>

            <div id="poRows"></div>

            <div class="lineHeight">&nbsp;</div>

            {{-- Buttons --}}
            <div class="row">
                <div class="col-lg-12 text-right">
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
            <div id="removeSC_${x}">
                <div class="lineHeight">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-10">
                        <label>MR Detail</label>
                        <input type="hidden" name="mrRowsArray[]" value="${x}" />
                        <select name="mr_data_detail_${x}" id="mr_data_detail_${x}" class="form-control po-selection" onchange="handleSelection(${x})">
                            <option value="">Select MR Detail</option>
                            @foreach($materialRequests as $poRow)
                                <option value="{{ $poRow->id.'<*>'.$poRow->material_request_id.'<*>'.$poRow->material_request_qty.'<*>'.$poRow->previous_issue_qty.'<*>'.$poRow->variant_id }}">
                                    {{ $poRow->material_request_no }} - {{ CommonHelper::changeDateFormat($poRow->material_request_date) }} -
                                    {{ $poRow->product_name }} - {{ $poRow->size_name }} -
                                    {{ $poRow->material_request_qty }} - {{ $poRow->previous_issue_qty }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2" style="margin-top:30px;">
                        <button type="button" class="btn btn-xs btn-danger" onclick="removeStoreChallanRow(${x})">Remove</button>
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

        const selectedValue = $(`#mr_data_detail_${id}`).val();
        if (!selectedValue) return;

        const [mrDataId, mrId, mrQty, prevIssueQty, variantId] = selectedValue.split('<*>');
        const remainingQty = mrQty - prevIssueQty;

        // Prevent duplicate MR Detail selection
        if (selectedValues.filter(v => v === mrDataId).length > 1) {
            alert('The same MR Detail cannot be selected multiple times.');
            $(`#mr_data_detail_${id}`).val('').trigger('change');
            $(`#poDataDetail${id}`).html('');
            return;
        }

        const data = `
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <input type="hidden" name="material_request_id_${id}" value="${mrId}">
                <input type="hidden" name="mr_data_id_${id}" value="${mrDataId}">
                <input type="hidden" name="variant_id_${id}" value="${variantId}">
                <input type="hidden" name="remaining_qty_${id}" value="${remainingQty}">

                <div class="col-lg-3">
                    <label>Material Request Qty</label>
                    <input type="number" value="${remainingQty}" disabled class="form-control">
                </div>

                <div class="col-lg-3">
                    <label>Issued Qty</label>
                    <input type="number" step="0.01" name="issue_qty_${id}" id="issue_qty_${id}" class="form-control" required>
                    <span id="error_issue_qty_${id}" class="text-danger" style="font-size:12px;display:none;"></span>
                </div>

                <div class="col-lg-6">
                    <label>Description</label>
                    <textarea name="data_description_${id}" id="data_description_${id}" rows="2" class="form-control">-</textarea>
                </div>
            </div>
        `;

        $(`#poDataDetail${id}`).html(data);

        // Validation for issue qty not exceeding remaining qty
        $(`#issue_qty_${id}`).on('input', function() {
            const entered = parseFloat($(this).val() || 0);
            const maxAllowed = parseFloat(remainingQty);
            const errorSpan = $(`#error_issue_qty_${id}`);

            if (entered > maxAllowed) {
                errorSpan.text(`Issued qty cannot exceed remaining qty (${maxAllowed}).`);
                errorSpan.show();
            } else {
                errorSpan.hide();
            }
        });
    }

    function removeStoreChallanRow(rowId) {
        $(`#removeSC_${rowId}`).remove();
    }
</script>
