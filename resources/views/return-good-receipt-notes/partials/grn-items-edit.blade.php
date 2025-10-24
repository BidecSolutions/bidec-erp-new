<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>PO No</th>
            <th>Return Qty</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grnItems as $index => $item)
            <tr>
                <td>
                    {{ $item->product_name }}
                    <input type="hidden" name="product_ids[]" value="{{ $item->product_variant_id }}">
                    <input type="hidden" name="po_ids[]" value="{{ $item->po_id }}">
                    <input type="hidden" name="po_data_ids[]" value="{{ $item->po_data_id }}">
                </td>
                <td>{{ $item->size_name }}</td>
                <td>{{ $item->po_no }}</td>
                <td>
                    <input type="number" name="return_qtys[]" class="form-control" step="0.01" min="0"
                        value="{{ old('return_qtys.' . $index, $item->return_qty) }}">
                </td>
                <td>
                    <input type="text" name="remarks[]" class="form-control"
                        value="{{ old('remarks.' . $index, $item->remarks) }}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
