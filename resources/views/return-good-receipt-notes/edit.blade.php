@extends('layouts.layouts')

@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {!! \App\Helpers\CommonHelper::displayPageTitle('Edit Return GRN') !!}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('return-good-receipt-notes.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>

            <form method="POST" action="{{ route('return-good-receipt-notes.update', $returnGrn->id) }}">
                @csrf

                <div class="form-group mt-3">
                    <label for="grn_id">Approved GRN</label>
                    <select name="good_receipt_note_id" id="grn_id" class="form-control select2" disabled>
                        <option value="{{ $returnGrn->good_receipt_note_id }}">
                            GRN# {{ $returnGrn->grn_no }} | Supplier: {{ $returnGrn->supplier_name }}
                        </option>
                    </select>
                    <input type="hidden" name="good_receipt_note_id" value="{{ $returnGrn->good_receipt_note_id }}">
                </div>

                <div class="mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Include</th>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Received Qty</th>
                                <th>Return Qty</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grnItems as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" 
                                               name="include_product[{{ $item->po_data_id }}]" 
                                               value="1" 
                                               {{ $item->is_included ? 'checked' : '' }}
                                               onchange="toggleRowInputs(this)">
                                    </td>
                                    <td>
                                        {{ $item->product_name }}
                                        <input type="hidden" class="row-input" name="product_ids[{{ $item->po_data_id }}]" value="{{ $item->product_id }}">
                                        <input type="hidden" class="row-input" name="po_ids[{{ $item->po_data_id }}]" value="{{ $item->po_id }}">
                                        <input type="hidden" class="row-input" name="po_data_ids[{{ $item->po_data_id }}]" value="{{ $item->po_data_id }}">
                                    </td>
                                    <td>{{ $item->size_name }}</td>
                                    <td>{{ $item->receive_qty }}</td>
                                    <td>
                                        <input type="number" 
                                               class="row-input form-control" 
                                               name="return_qtys[{{ $item->po_data_id }}]" 
                                               min="0" 
                                               max="{{ $item->receive_qty }}" 
                                               value="{{ $item->return_qty }}"
                                               {{ $item->is_included ? '' : 'disabled' }}>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               class="row-input form-control" 
                                               name="remarks[{{ $item->po_data_id }}]" 
                                               value="{{ $item->remarks }}"
                                               {{ $item->is_included ? '' : 'disabled' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group mt-4">
                    <label>Reason for Return</label>
                    <textarea name="reason" class="form-control" rows="3">{{ old('reason', $returnGrn->reason) }}</textarea>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Update Return GRN</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    function toggleRowInputs(checkbox) {
        const row = checkbox.closest('tr');
        const inputs = row.querySelectorAll('.row-input');
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                if (input.type === 'number') input.value = 0;
                else if (input.type === 'text') input.value = '';
            }
        });
    }

    // Initialize all rows on load
    document.querySelectorAll('input[name^="include_product"]').forEach(checkbox => {
        toggleRowInputs(checkbox);
    });
</script>
@endsection