<?php
$accType = Auth::user()->acc_type;
$m;
$currentDate = date('Y-m-d');
CommonFacades::companyDatabaseConnection($m);
$storeChallanDetail = DB::selectOne('select * from `store_challan` where `status` = 1 and `store_challan_no` = "'.$_GET['id'].'"');
$storeChallanDataDetail = DB::select('select * from `store_challan_data` where `status` = 1 and `store_challan_no` = "'.$_GET['id'].'"');
CommonFacades::reconnectMasterDatabase();
$totalRows = count($storeChallanDataDetail);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <?php echo Form::open(array('url' => 'stad/editStoreChallanVoucherDetail?m='.$m.'','id'=>'storeChallanVoucherForm'));?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                    <input type="hidden" name="totalCounter" readonly id="totalCounter" value="<?php echo count($storeChallanDataDetail);?>">
                    <input type="hidden" name="departmentId" value="<?php echo $storeChallanDetail->sub_department_id;?>">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Store Challan No.</label>
                                            <input type="text" readonly="readonly" class="form-control requiredField" placeholder="Store Challan No" name="store_challan_no" id="store_challan_no" value="<?php echo $storeChallanDetail->store_challan_no?>" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label class="sf-label">Slip No.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" class="form-control requiredField" placeholder="Slip No" name="slip_no" id="slip_no" value="<?php echo $storeChallanDetail->slip_no?>" />
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label class="sf-label">Store Challan Date.</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="date" class="form-control requiredField" max="<?php echo date('Y-m-d') ?>" name="store_challan_date" id="store_challan_date" value="<?php echo $storeChallanDetail->store_challan_date?>" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="sf-label">Department / Sub Department</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <select class="form-control requiredField" disabled name="sub_department_id" id="sub_department_id">
                                                <option value="">Select Department</option>
                                                @foreach($departments as $key => $y)
                                                    <optgroup label="{{ $y->department_name}}" value="{{ $y->id}}">
                                                        <?php
                                                        $subdepartments = DB::select('select `id`,`sub_department_name` from `sub_department` where `company_id` = '.$m.' and `department_id` ='.$y->id.'');
                                                        ?>
                                                        @foreach($subdepartments as $key2 => $y2)
                                                            <option value="{{ $y2->id}}" {{ $storeChallanDetail->sub_department_id == $y2->id ? 'selected=selected' : '' }}>{{ $y2->sub_department_name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="sf-label">Description</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <textarea name="description" id="description" style="resize:none;" rows="4" cols="50" class="form-control requiredField"><?php echo $storeChallanDetail->description; ?></textarea>
                                        </div>
                                    </div>
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
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center">Category <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Sub Item <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Demand Qty <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Privious Issue Qty <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center">Current Bal. <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center" style="width:100px;">Issue Qty. <span class="rflabelsteric"><strong>*</strong></span></th>
                                                            <th class="text-center" style="width:100px;">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="addMoreStoreChallanDetailRows" id="addMoreStoreChallanDetailRows">
                                                        <?php
                                                        $j = 1;
                                                        foreach($storeChallanDataDetail as $row){
                                                        ?>
                                                        <input type="hidden" name="storeChallanDataSection[]" class="form-control requiredField" id="storeChallanDataSection" value="<?php echo $j;?>" />
                                                        <input type="hidden" name="demandNo_<?php echo $j;?>" readonly id="demandNo_<?php echo $j;?>" value="<?php echo $row->demand_no;?>" class="form-control" />
                                                        <input type="hidden" name="demandDate_<?php echo $j;?>" readonly id="demandDate_<?php echo $j;?>" value="<?php echo $row->demand_date;?>" class="form-control" />
                                                        <input type="hidden" name="recordId_<?php echo $j;?>" readonly id="recordId_<?php echo $j;?>" value="<?php echo $row->id;?>" class="form-control" />
                                                        <tr id="removeSelectedStoreChallanRow_<?php echo $j?>">
                                                            <td>
                                                                <select name="category_name_<?php echo $j?>" id="category_name_<?php echo $j?>" disabled onchange="subItemListLoadDepandentCategoryId(this.id,this.value)" class="form-control requiredField">
                                                                    <?php echo CommonFacades::categoryList($m,$row->category_id);?>
                                                                    <input type="hidden" name="categoryId_<?php echo $j?>" value="<?php echo $row->category_id;?>">
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="sub_item_name_<?php echo $j?>" id="sub_item_name_<?php echo $j?>" disabled class="form-control requiredField">
                                                                    <?php echo CommonFacades::subItemList($m,$row->sub_item_id,$row->category_id);?>
                                                                    <input type="hidden" name="subItemId_<?php echo $j?>" value="<?php echo $row->sub_item_id;?>">
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                $issueQty = StoreFacades::issueQtyItemWiseDetail($m,$row->category_id,$row->sub_item_id,$row->demand_no,'demand_no');
                                                                echo $demandAndRemainingQty = StoreFacades::getDemandQtyByDemandNo($m,$row->category_id,$row->sub_item_id,$row->demand_no,'demand_no');
                                                                ?>
                                                                <input type="hidden" name="demandAndRemainingQty_<?php echo $j;?>" readonly id="demandAndRemainingQty_<?php echo $j;?>" value="<?php echo $demandAndRemainingQty - $issueQty + $row->issue_qty;?>" class="form-control" />
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo StoreFacades::itemWiseCreatedStoreChallan($m,$row->demand_no,$row->category_id,$row->sub_item_id,$row->store_challan_no);?>
                                                            </td>
                                                            <td class="text-center"><?php echo $row->issue_qty + $currentBalance = StoreFacades::checkItemWiseCurrentBalanceQty($m,$row->category_id,$row->sub_item_id,$row->demand_no,'demand_no');?></td>
                                                            <td><input type="number" name="issue_qty_<?php echo $j?>" id="issue_qty_<?php echo $j?>" class="form-control requiredField" min="1" max="<?php echo $currentBalance + $row->issue_qty;?>" onkeyup="checkQty('demandAndRemainingQty_<?php echo $j;?>',this.value,this.id)" value="<?php echo $row->issue_qty?>" /></td>
                                                            <td class="text-center"><a onclick="removeSeletedStoreChallanRows('<?php echo $row->id?>','<?php echo $j?>')" class="btn btn-xs btn-danger">Remove</a></td>
                                                        </tr>
                                                        <?php
                                                        $j++;
                                                        }
                                                        ?>
                                                        </tbody>
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
                            {{ Form::button('Submit', ['class' => 'btn btn-success']) }}
                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>

                        </div>
                    </div>
                    <?php echo Form::close();?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".btn-success").click(function(e){
            var storeChallan = new Array();
            var val;
            storeChallan.push($(this).val());
            var _token = $("input[name='_token']").val();
            for (val of storeChallan) {
                jqueryValidationCustom();
                if(validate == 0){
                }else{
                    return false;
                }
            }
            formSubmitOne(e);

        });
    });
    function formSubmitOne(e){

        var postData = $('#storeChallanVoucherForm').serializeArray();
        var formURL = $('#storeChallanVoucherForm').attr("action");
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

    function removeSeletedStoreChallanRows(id,counter){
        var totalCounter = $('#totalCounter').val();
        if(totalCounter == 1){
            alert('Last Row Not Deleted');
        }else{
            var lessCounter = totalCounter - 1;
            var totalCounter = $('#totalCounter').val(lessCounter);
            var elem = document.getElementById('removeSelectedStoreChallanRow_'+counter+'');
            elem.parentNode.removeChild(elem);
        }

    }

    function checkQty(paramOne,paramTwo,paramThree) {
        var remainingQty = $('#'+paramOne+'').val();
        if(parseInt(paramTwo) <= parseInt(remainingQty)){
        }else{
            $('#'+paramThree+'').val('');
            alert('Issue Qty not allow to max Demand Qty!');
        }
    }
</script>
