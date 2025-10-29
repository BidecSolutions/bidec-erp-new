<tr id="removeMaterialRequestsRows_{{ $id }}_{{ $counter }}">
    <input type="hidden" name="materialRequestDataSection[]" class="form-control requiredField materialRequestDataSection_{{ $id }}" value="0" />

    <td>
        <select name="product_id[]" class="form-control requiredField select2">
            <option value="">Select Product</option>
            @foreach($products as $product)
                <optgroup label="{{ $product['name'] }}">
                    @foreach($product['variants'] as $variant)
                        <option value="{{ $variant['id'] }}">
                            {{ $variant['size_name'] }} - {{ number_format($variant['amount'], 2) }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </td>

    <td>
        <input type="number" name="qty[]" step="0.0001" class="form-control requiredField" />
    </td>

    <td>
        <input type="text" name="sub_description[]" value="-" class="form-control requiredField" />
    </td>

    <td class="text-center">
        <button type="button" onclick="removeMaterialRequestsRows('{{ $id }}','{{ $counter }}')" class="btn btn-xs btn-danger">Remove</button>
    </td>
</tr>

<script>
    $("select").select2(); // reinitialize select2 after adding new row
</script>
