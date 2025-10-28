<table id="buildyourform" class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Category <span class="rflabelsteric"><strong>*</strong></span></th>
            <th class="text-center">Sub Item <span class="rflabelsteric"><strong>*</strong></span></th>
            <th class="text-center">Qty in Unit <span class="rflabelsteric"><strong>*</strong></span></th>
            <th class="text-center">Description</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody class="addMoreMaterialRequestsDetailRows_1" id="addMoreMaterialRequestsDetailRows_1">
        @foreach ($recipeData as $key => $item)
            <input type="hidden" name="materialRequestDataSection[]" class="form-control requiredField materialRequestDataSection_1" id="materialRequestDataSection_1" value="{{ $key+1 }}" />
            <tr id="removeMaterialRequestsRows_1_{{$key+1}}">
                <td>
                    <select name="category_id[]" id="category_id_1_{{ $key+1 }}" onchange="subItemListLoadDepandentCategoryId(this.id,this.value)" class="form-control requiredField">
                        <?php echo PurchaseFacades::categoryList($m, $item->subItem->main_ic_id);?>
                    </select>
                </td>
                <td>
                    <select name="sub_item_id[]" id="sub_item_id_1_{{ $key+1 }}" class="form-control requiredField">
                        <option value="{{ $item->subItem->id }}">{{ $item->subItem->sub_ic }}</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="qty[]" id="qty_1_{{ $key+1 }}" value="{{ $item->quantity * $no_of_qty }}" step="0.0001" class="form-control requiredField" />
                </td>
                <td>
                    <input type="text" name="sub_description[]" id="sub_description_1_{{ $key+1 }}" value="-" class="form-control requiredField" />
                </td>
                <td class="text-center">
                    <button onclick="removeMaterialRequestsRows('1','{{ $key+1 }}')" class="btn btn-xs btn-danger">Remove</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>