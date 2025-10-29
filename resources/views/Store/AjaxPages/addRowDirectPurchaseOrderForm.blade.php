<thead>
    <tr>
        <th class="text-center hidden"><label><input type="checkbox" name="sale_tax_head_checkbox_1" id="sale_tax_head_checkbox_1" value="2" onchange="saleTaxEnableAndDisable('1')"> Sales Tax Head</label></th>
        <th class="text-center hidden">Tax Unit</th>
        <th class="text-center">Category</th>
        <th colspan="2" class="text-center">Item Code / Item Name</th>
        <th class="text-center">Supplier Name</th>
        
        <th class="text-center" colspan="2">Qoutation Date</th>
    </tr>
</thead>
<tbody class="displayRowPRow_1">
    <tr>
        <td class="text-center hidden">
            <input type="hidden" name="seletedPurchaseRequestRow[]" readonly id="seletedPurchaseRequestRow" value="{{ $counter }}" class="form-control" />
            <input type="number" class="form-control requiredField" name="delivery_days_{{ $counter }}_1" id="delivery_days_{{ $counter }}_1" value="30" />
            <input type="number" class="form-control requiredField" placeholder="Payment Terms Days" name="payment_terms_{{ $counter }}_1" id="payment_terms_{{ $counter }}_1" value="45" />
            <select name="sale_tax_head_{{ $counter }}" id="sale_tax_head_{{ $counter }}" class="form-control">
                <?php echo SelectListFacades::getChartOfAccountList($m,1,0,0);?>
            </select>
        </td>
        <td class="text-center hidden">
            <input type="number" name="unit_{{ $counter }}_1" id="unit_{{ $counter }}_1" step="0.00001" placeholder="Type Unit Percent" class="form-control requiredField" onkeyup="makesubtotalamount({{ $counter }},1)" value="0">
        </td>
        <td>
            <select name="categoryId_{{ $counter }}_1" id="categoryId_{{ $counter }}_1" onchange="subItemListLoadDepandentCategoryId(this.id,this.value)" class="form-control requiredField">
                <?php echo PurchaseFacades::categoryList($m,'0');?>
            </select>
        </td>
        <td colspan="2">
            <select name="subItemId_{{ $counter }}_1" id="subItemId_{{ $counter }}_1" class="form-control requiredField">
            </select>
        </td>
        <td>
            <select onchange="makesubtotalamount(1,{{ $counter }})" class="form-control requiredField" name="supplier_id_{{ $counter }}_1" id="supplier_id_{{ $counter }}_1">
                <?php echo SelectListFacades::getSupplierList($m,1,0);?>
            </select>
        </td>
        
        <td colspan="2">
            <input type="date" class="form-control requiredField" name="qoutation_date_{{ $counter }}_1" id="qoutation_date_{{ $counter }}_1" value="<?php echo date('Y-m-d') ?>" placeholder="Qoutation No" />
        </td>
    </tr>
</tbody>
<thead>
    <tr>
        <th class="text-center">Invoice/Quotation No.</th>
        <th class="text-center">Purchase Order Qty./Previous Purchased Qty.</th>
        <th class="text-center">Unit Price</th>
        <th class="text-center">Sub Total <span class="rflabelsteric"><strong>*</strong></span></th>
        <th class="text-center hidden" colspan="2">Sub Total With % <span class="rflabelsteric"><strong>*</strong></span></th>
    </tr>
</thead>
<tbody class="displayRowPRow_1">
    <tr>
        <td>
            <input type="text" class="form-control requiredField" name="qoutation_no_{{ $counter }}_1" id="qoutation_no_{{ $counter }}_1" value="-" placeholder="Qoutation No" />
        </td>
        <td class="text-center" style="display: flex; justify-content: space-around;">

            <input type="number" style="width: 45%;" name="purchase_order_qty_{{ $counter }}_1" id="purchase_order_qty_{{ $counter }}_1" step="0.00001" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount({{ $counter }},1)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="">
            <input type="hidden" name="purchase_request_qty_1_1" id="purchase_order_qty_1_1" step="0.00001" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount({{ $counter }},1)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="">                                                                                                                                                                
        </td>
        <td class="text-center">
            <input type="number" name="unit_price_{{ $counter }}_1" id="unit_price_{{ $counter }}_1" step="0.00001" placeholder="Type Unit Price" class="form-control requiredField unit_price" onkeyup="makesubtotalamount({{ $counter }},1)" value="">
        </td>
        <td class="text-center">
            <input type="number" readonly name="sub_total_{{ $counter }}_1" id="sub_total_{{ $counter }}_1" step="0.00001" placeholder="Type Sub Total" class="form-control requiredField yesSubTotalAmount" value="">
        </td>
        <td class="text-center hidden" colspan="2">
            <input type="number" readonly name="sub_total_with_persent_1_{{ $counter }}" id="sub_total_with_persent_1_{{ $counter }}" step="0.00001" placeholder="Type Sub Total With Persent" class="form-control" value="">
        </td>
    </tr>
</tbody>

<script>
    $(function () {
            $("select").select2();
        });        
</script>