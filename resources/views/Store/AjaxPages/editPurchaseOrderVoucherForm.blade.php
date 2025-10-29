<?php
$accType = Auth::user()->acc_type;
$m;
use App\Models\Account;
$currentDate = date('Y-m-d');
CommonFacades::companyDatabaseConnection($m);
$purchaseOrderDetail = DB::selectOne('select * from `purchase_order` where `status` = 1 and `purchase_order_no` = "'.$_GET['id'].'"');
$purchaseOrderDataDetail = DB::select('select * from `purchase_order_data` where `status` = 1 and `purchase_order_no` = "'.$_GET['id'].'"');
$supplierList = DB::select('select `id`,`name` from `supplier` where  `status` = 1');
$accounts = Account::where('status','=','1')->orderBy('level1', 'ASC')
        ->orderBy('level2', 'ASC')
        ->orderBy('level3', 'ASC')
        ->orderBy('level4', 'ASC')
        ->orderBy('level5', 'ASC')
        ->orderBy('level6', 'ASC')
        ->orderBy('level7', 'ASC')
        ->get();
CommonFacades::reconnectMasterDatabase();
$totalRows = count($purchaseOrderDataDetail);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <?php echo Form::open(array('url' => 'stad/editPurchaseOrderVoucherDetail?m='.$m.'','id'=>'editPurchaseOrderVoucherDetail'));?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                    <input type="hidden" name="prNo" id="prNo" value="<?php echo $purchaseOrderDetail->purchase_request_no ?>" class="form-control" readonly/>
                    <input type="hidden" name="prDate" id="prDate" value="<?php echo $purchaseOrderDetail->purchase_request_date ?>" class="form-control" readonly/>
                    <input type="hidden" name="subDepartmentId" id="subDepartmentId" value="<?php echo $purchaseOrderDetail->sub_department_id ?>" class="form-control" readonly/>
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label class="sf-label">Purchase Order No.</label>
                                    <input type="text" readonly="readonly" class="form-control requiredField" placeholder="Purchase Order No" name="purchase_order_no" id="purchase_order_no" value="<?php echo $purchaseOrderDetail->purchase_order_no?>" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label class="sf-label">Requisition No</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <input type="text" readonly class="form-control requiredField" name="requisition_no" id="requisition_no" value="<?php echo $purchaseOrderDetail->purchase_request_no ?>" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label class="sf-label">P.O Date.</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <input type="date" class="form-control requiredField kisNeBandKiDate" max="<?php echo date('Y-m-d') ?>" name="po_date" id="po_date" value="<?php echo $purchaseOrderDetail->purchase_order_date?>" />
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label class="sf-label">Delivery place</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <input type="text" class="form-control requiredField" name="delivery_place" id="delivery_place" placeholder="Delivery Place" value="<?php echo $purchaseOrderDetail->delivery_place?>" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label class="sf-label">Department / Sub Department</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <input type="text" name="sub_department_name" id="sub_department_name" class="form-control" readonly value="<?php echo CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$purchaseOrderDetail->sub_department_id);?>" >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label class="sf-label">Description</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <textarea name="main_description" id="main_description" style="resize:none;" rows="2" cols="50" class="form-control requiredField"><?php echo $purchaseOrderDetail->description; ?></textarea>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="well">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="table-responsive">
                                                    <table id="buildyourform" class="table table-bordered">
                                                        <?php
                                                        $j = 1;
                                                        $counter = 1;
                                                        foreach($purchaseOrderDataDetail as $row){
                                                        ?>
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center">Sales Tax Head</th>
                                                            <th class="text-center">Item Code <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Item Name <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Current Balance</th>
                                                            <th class="text-center">Supplier</th>
                                                            <th class="text-center">Qoutation No.</th>
                                                            <th class="text-center">Qoutation Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <input type="hidden" name="purchaseOrderDataSection[]" class="requiredField" id="purchaseOrderDataSection" value="<?php echo $row->id;?>" />
                                                        <input type="hidden" name="priviousSupplierId_<?php echo $row->id;?>" readonly id="priviousSupplierId_<?php echo $counter; ?>" value="<?php echo $row->supplier_id;?>" />
                                                        <input type="hidden" name="categoryId_<?php echo $row->id;?>" readonly id="categoryId_<?php echo $counter; ?>" value="<?php echo $row->category_id;?>" />
                                                        <input type="hidden" name="recordId_<?php echo $row->id;?>" readonly id="recordId_<?php echo $j;?>" value="<?php echo $row->id;?>" />
                                                        <input type="hidden" name="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $row->id;?>" readonly id="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $counter; ?>" value="<?php echo StoreFacades::priviousPurchaseOrderQtyThisPurchaseRequest($m,$row->purchase_request_no,$row->category_id,$row->sub_item_id,$row->id);?>" />
                                                        <?php
                                                        $purchaseRequestQty = StoreFacades::getTotalPurchaseRequestQtyItemWise($m,$row->purchase_request_no,$row->category_id,$row->sub_item_id);
                                                        $remainingPurchaseOrderQty = $purchaseRequestQty - StoreFacades::priviousPurchaseOrderQtyThisPurchaseRequest($m,$row->purchase_request_no,$row->category_id,$row->sub_item_id,$row->id);
                                                        ?>
                                                        <input type="hidden" name="remaining_purchase_order_qty_<?php echo $row->id?>" id="remaining_purchase_order_qty_<?php echo $row->id?>" value="<?php echo $remainingPurchaseOrderQty;?>" readonly />
                                                        <tr id="removeSelectedPurchaseOrderRow_<?php echo $j?>">
                                                            <td class="text-center">
                                                                <select name="sale_tax_head_<?php echo $row->id?>" id="sale_tax_head_<?php echo $row->id?>" class="form-control">
                                                                    <option value="0">Select Account</option>
                                                                    <?php foreach($accounts as $key => $y){?>
                                                                        <option value="<?php echo $y->id; ?>" <?php if($y->id == $row->sale_tax_head){echo 'selected';}?>><?php echo  $y->code .' ---- '. $y->name; ?></option>
                                                                    <?php }?>
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','item_code',$row->sub_item_id);?>
                                                            </td>
                                                            <td>
                                                                <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row->sub_item_id);?>
                                                                <input type="hidden" name="subItemId_<?php echo $row->id;?>" readonly id="subItemId_<?php echo $counter;?>" value="<?php echo $row->sub_item_id;?>" class="form-control" />
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo CommonFacades::checkItemWiseCurrentBalanceQty($m,$row->category_id,$row->sub_item_id,'',date('Y-m-d'));?>
                                                            </td>
                                                            <td>
                                                                <select name="supplier_id_<?php echo $row->id?>" id="supplier_id_<?php echo $counter?>" class="form-control requiredField">
                                                                    <option value="">Select Supplier</option>
                                                                    <?php echo CommonFacades::normalSupplierSelectListById($m,$row->supplier_id);?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control requiredField" name="qoutation_no_<?php echo $row->id?>" id="qoutation_no_<?php echo $counter?>" value="<?php echo $row->qoutation_no?>" placeholder="Qoutation No" />
                                                            </td>
                                                            <td>
                                                                <input type="date" class="form-control requiredField" name="qoutation_date_<?php echo $row->id?>" id="qoutation_date_<?php echo $counter?>" value="<?php echo $row->qoutation_date?>" placeholder="Qoutation No" />
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center">Delivery Days</th>
                                                            <th class="text-center">Payment Terms</th>
                                                            <th class="text-center">Tax Unit</th>
                                                            <th class="text-center">Purchase Order Qty.</th>
                                                            <th class="text-center">Unit Price</th>
                                                            <th class="text-center">Sub Total <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Sub Total With % <span class="rflabelsteric"><strong>*</strong></span></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><input type="number" class="form-control requiredField" name="delivery_days_<?php echo $row->id?>" id="delivery_days_<?php echo $counter?>" value="<?php echo $row->delivery_days?>" /></td>
                                                            <td><input type="number" class="form-control requiredField" placeholder="Payment Terms Days" name="payment_terms_<?php echo $row->id?>" id="payment_terms_<?php echo $counter?>" value="<?php echo $row->payment_terms?>" /></td>
                                                            <td class="text-center">
                                                                <input type="number" name="unit_<?php echo $row->id?>" id="unit_<?php echo $counter?>" step="0.01" placeholder="Type Unit" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $counter;?>)" value="<?php echo $row->unit;?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="number" name="purchase_order_qty_<?php echo $row->id?>" id="purchase_order_qty_<?php echo $counter?>" step="0.01" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $counter;?>)" max="<?php echo $remainingPurchaseOrderQty;?>" value="<?php echo $remainingPurchaseOrderQty;?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="number" name="unit_price_<?php echo $row->id?>" id="unit_price_<?php echo $counter?>" step="0.01" placeholder="Type Unit Price" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $counter;?>)" value="<?php echo $row->unit_price?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="number" readonly name="sub_total_<?php echo $row->id?>" id="sub_total_<?php echo $counter?>" step="0.01" placeholder="Type Sub Total" class="form-control requiredField" value="<?php echo $row->sub_total?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="number" readonly name="sub_total_with_persent_<?php echo $row->id?>" id="sub_total_with_persent_<?php echo $counter?>" step="0.01" placeholder="Type Sub Total With Persent" class="form-control requiredField" value="<?php echo $row->sub_total_with_persent?>">
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                        <?php
                                                        $j++;
                                                        $counter++;
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                            {{ Form::button('Submit', ['class' => 'btn btn-success btn-abc-submit btnSubmit']) }}
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>

                        </div>
                    </div>
                    <?php echo Form::close();?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("select").select2();
    });
    function makesubtotalamount(param1){
        var unit = $('#unit_'+param1+'').val();
        var purchase_order_qty = $('#purchase_order_qty_'+param1+'').val();
        var unit_price = $('#unit_price_'+param1+'').val();

        if(unit !== '' && purchase_order_qty !== '' && unit_price !== ''){
            //alert('Done');
            var subTotal = purchase_order_qty * unit_price;
            var sub_total_with_persent = parseInt(subTotal) * parseInt(unit) / parseInt('100');
            $('#sub_total_'+param1+'').val(subTotal);
            $('#sub_total_with_persent_'+param1+'').val(sub_total_with_persent);
            //overalltotalsection();
        }else{
            //alert('Empty Value');
            $('#sub_total_'+param1+'').val('');
            $('#sub_total_with_persent_'+param1+'').val('');
            //overalltotalsection();
        }
    }
    $(document).ready(function() {
        disableInputFormDateAccountYear();
        $(".btn-abc-submit").click(function(e){
            var purchaseOrder = new Array();
            var val;
            purchaseOrder.push($(this).val());
            var _token = $("input[name='_token']").val();
            for (val of purchaseOrder) {
                jqueryValidationCustom();
                if(validate == 0){
                    $(".btnSubmit").val('Sending, please wait...');
                    setTimeout(function(){
                        $(".btnSubmit").prop("type", "button");
                    },50);
                }else{
                    return false;
                }
            }
            formSubmitOne(e);

        });
    });
    function formSubmitOne(e){
        var postData = $('#editPurchaseOrderVoucherDetail').serializeArray();
        var formURL = $('#editPurchaseOrderVoucherDetail').attr("action");
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data){
                $('#showMasterTableEditModel').modal('toggle');
                filterVoucherList();
            }
        });
    }
</script>
