{{-- In grn-items.blade.php --}}
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Include</th>
            <th>Product</th>
            <th>Received Qty</th>
            <th>Return Qty</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grnItems as $index => $item)
            <tr>
                <td>
                    <input type="checkbox" 
                           name="include_product[{{ $item->id }}]" 
                           value="1" 
                           checked
                           onchange="toggleRowInputs(this)">
                </td>
                <td>
                    {{ $item->product_name ?? 'N/A' }} ({{ $item->size_name ?? 'N/A' }})
                    <input type="hidden" class="row-input" name="product_ids[{{ $item->id }}]" value="{{ $item->product_id ?? 0 }}">
                    <input type="hidden" class="row-input" name="grn_data_ids[{{ $item->id }}]" value="{{ $item->id }}">
                    <input type="hidden" class="row-input" name="po_ids[{{ $item->id }}]" value="{{ $item->po_id ?? 0 }}">
                    <input type="hidden" class="row-input" name="po_data_ids[{{ $item->id }}]" value="{{ $item->po_data_id ?? 0 }}">
                </td>
                <td>
                    {{ $item->receive_qty ?? 0 }}
                </td>
                <td>
                    <input type="number"
                           class="row-input form-control"
                           name="return_qtys[{{ $item->id }}]"
                           min="0"
                           max="{{ $item->receive_qty }}"
                           value="0"
                           required>
                </td>
                <td>
                    <input type="text"
                           class="row-input form-control"
                           name="remarks[{{ $item->id }}]"
                           placeholder="Optional">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>