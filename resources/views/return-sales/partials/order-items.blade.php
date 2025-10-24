<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th></th>
            <th>Product Name</th>
            <th>Variant Name</th>
            <th>Ordered Qty</th>
            <th>Returned Qty</th>
            <th>Return Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cartItems as $item)
            <tr>
                <td>
                    <input type="checkbox"
                           name="include_product[{{ $item->id }}]"
                           value="1"
                           onchange="toggleReturnQtyInput(this, {{ $item->id }})"
                           checked>
                </td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->size_name }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->returned_qty }}</td>
                <td>
                    <input type="number"
                           class="form-control return-qty"
                           name="return_qtys[{{ $item->id }}]"
                           min="0"
                           max="{{ $item->qty - $item->returned_qty }}"
                           value="0"
                           id="return_qty_{{ $item->id }}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
