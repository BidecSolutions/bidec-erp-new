<?php
$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');
$getAllInput = $request->all();
?>

@extends('layouts.default')

@section('content')
    <div class="well">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="display: none;">
                        @include('Store.'.$accType.'storeMenu')
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">Create Store Challan Return Detail Form</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <?php echo Form::open(array('url' => 'stad/addStoreChallanReturnDetail?m='.$m.'','id'=>'addStoreChallanReturnDetail'));?>
                            <?php
                            $counter = 1;
                            $departmentId = '';
                            $demandDataNo = [];
                            $demandDataId = [];
                            foreach ($getAllInput as $key => $value){
                                if($counter == 1 || $key == 'm' || $key == 'parentCode' || $key == 'pageType'){
                                }else if($counter == 2){
                                    $departmentId = $value;
                                }else{
                                    $makeDemandId = explode('_',$key);
                                    $demandDataId[] .= ''.$makeDemandId[2].',';
                                    $demandDataNo[] = $value;
                                }
                                $counter++;
                            }
                            $paramOne = $departmentId;
                            $paramTwo = $demandDataId;
                            $paramThree = $demandDataNo;
                            CommonFacades::companyDatabaseConnection($m);
                            $seletedStoreChallanListandCreateStoreChallanReturn = DB::table('store_challan_data')
                                ->select('store_challan_data.store_challan_no','store_challan_data.store_challan_date','store_challan_data.category_id','store_challan_data.sub_item_id','store_challan.description','store_challan_data.issue_qty','store_challan_data.id','store_challan.slip_no','store_challan_data.demand_no','store_challan_data.demand_date')
                                ->join('store_challan','store_challan_data.store_challan_no','=','store_challan.store_challan_no')
                                ->whereIn('store_challan_data.id',$paramTwo)
                                ->whereIn('store_challan_data.store_challan_no', $paramThree)
                                ->where(['store_challan.sub_department_id' => $paramOne,'store_challan.status' => '1','store_challan.store_challan_status' => '2'])->get();
                            CommonFacades::reconnectMasterDatabase();
                            ?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="departmentId" value="<?php echo $paramOne;?>">
                            <input type="hidden" name="totalCounter" readonly id="totalCounter" value="<?php echo count($seletedStoreChallanListandCreateStoreChallanReturn);?>">
                            <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                            <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label class="sf-label">Slip No.</label>
                                                    <input type="text" class="form-control requiredField" placeholder="Slip No" name="slip_no" id="slip_no" value="" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label class="sf-label">Store Challan Return Date.</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <input type="date" class="form-control requiredField" max="<?php echo date('Y-m-d') ?>" name="store_challan_return_date" id="store_challan_return_date" value="<?php echo date('Y-m-d') ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <label class="sf-label">Department / Sub Department</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <input type="text" name="sub_department_name" id="sub_department_name" class="form-control" readonly value="<?php echo CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$paramOne);?>" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <label class="sf-label">Description</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    <textarea name="main_description" id="main_description" rows="4" cols="50" style="resize:none;" class="form-control requiredField"></textarea>
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
                                                            <table class="table table-bordered sf-table-list">
                                                                <thead>
                                                                <th class="text-center">S.No</th>
                                                                <th class="text-center">Store Challan No</th>
                                                                <th class="text-center">Store Challan Date</th>
                                                                <th class="text-center">Category Name</th>
                                                                <th class="text-center">Item Name</th>
                                                                <th class="text-center">Demand Qty.</th>
                                                                <th class="text-center">Issue Qty.</th>
                                                                <th class="text-center col-sm-2">Return Qty. <span class="rflabelsteric"><strong>*</strong></span></th>
                                                                <th class="text-center">Action</th>
                                                                </thead>
                                                                <tbody id="filterDemandVoucherList">
                                                                <?php
                                                                $counter1 = 1;
                                                                foreach ($seletedStoreChallanListandCreateStoreChallanReturn as $row){
                                                                ?>
                                                                <tr id="removeSelectedStoreChallanRow_<?php echo $counter1;?>">
                                                                    <td class="text-center">
                                                                        <?php echo $counter1;?>
                                                                        <input type="hidden" name="seletedStoreChallanReturnRow[]" readonly id="seletedStoreChallanReturnRow" value="<?php echo $counter1;?>" class="form-control" />
                                                                        <input type="hidden" name="storeChallanNo_<?php echo $counter1;?>" readonly id="storeChallanNo_<?php echo $counter1;?>" value="<?php echo $row->store_challan_no;?>" class="form-control" />
                                                                        <input type="hidden" name="storeChallanDate_<?php echo $counter1;?>" readonly id="storeChallanDate_<?php echo $counter1;?>" value="<?php echo $row->store_challan_date;?>" class="form-control" />
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php echo $row->store_challan_no;?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php echo CommonFacades::changeDateFormat($row->store_challan_date);?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$row->category_id);?>
                                                                        <input type="hidden" name="categoryId_<?php echo $counter1;?>" readonly id="categoryId_<?php echo $counter1;?>" value="<?php echo $row->category_id;?>" class="form-control" />
                                                                    </td>
                                                                    <td>
                                                                        <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row->sub_item_id);?>
                                                                        <input type="hidden" name="subItemId_<?php echo $counter1;?>" readonly id="subItemId_<?php echo $counter1;?>" value="<?php echo $row->sub_item_id;?>" class="form-control" />
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php
                                                                        $issueQty = StoreFacades::issueQtyItemWiseDetail($m,$row->category_id,$row->sub_item_id,$row->store_challan_no,'store_challan_no');
                                                                        echo $demandAndRemainingQty = StoreFacades::getDemandQtyByDemandNo($m,$row->category_id,$row->sub_item_id,$row->demand_no,'demand_no');;
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php echo $issueQty;?>
                                                                        <input type="hidden" name="storeChallanIssueQty_<?php echo $counter1;?>" readonly id="storeChallanIssueQty_<?php echo $counter1;?>" value="<?php echo $issueQty;?>" class="form-control" />
                                                                    </td>
                                                                    <td><input type="number" name="return_qty_<?php echo $counter1?>" id="return_qty_<?php echo $counter1?>" class="form-control requiredField" min="1" max="<?php echo $issueQty;?>" onkeyup="checkQty('storeChallanIssueQty_<?php echo $counter1;?>',this.value,this.id)" value="" /></td>
                                                                    <td class="text-center"><a onclick="removeSeletedStoreChallanRows('<?php echo $row->id?>','<?php echo $counter1?>')" class="btn btn-xs btn-danger">Remove</a></td>
                                                                </tr>
                                                                <?php
                                                                $counter1++;
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
                                    <div class="lineHeight">&nbsp;</div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                            {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                                            <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo Form::close();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $(".btn-success").click(function(e){
                var storeChallanReturn = new Array();
                var val;
                //$("input[name='demandsSection[]']").each(function(){
                storeChallanReturn.push($(this).val());
                //});
                var _token = $("input[name='_token']").val();
                for (val of storeChallanReturn) {
                    jqueryValidationCustom();
                    if(validate == 0){
                        //alert(response);
                    }else{
                        return false;
                    }
                }

            });
        });
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
                alert('Return Qty not allow to max Issue Qty!');
            }
        }
    </script>
@endsection
