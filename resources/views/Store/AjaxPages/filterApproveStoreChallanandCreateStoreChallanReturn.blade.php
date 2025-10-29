<?php
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$m = CommonFacades::getSessionCompanyId();
$paramOne = $_GET['paramOne'];
$parentCode = $_GET['parentCode'];

if(empty($paramOne)){
    $subDepartmentsList = DB::select('select `id`,`sub_department_name` from `sub_department` where `company_id` = '.$m.'');
}else{
    $subDepartmentsList = DB::select('select `id`,`sub_department_name` from `sub_department` where `company_id` = '.$m.' and `id` = '.$paramOne.'');
}
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel">
            <div class="panel-body">
                <?php
                foreach ($subDepartmentsList as $row){
                CommonFacades::companyDatabaseConnection($m);
                $approveStoreChallanListandCreateStoreChallanReturn = \DB::table("store_challan_data")
                    ->select("store_challan_data.id","store_challan_data.store_challan_no","store_challan_data.store_challan_date","store_challan_data.category_id","store_challan_data.sub_item_id","store_challan_data.issue_qty","store_challan_data.demand_no","store_challan_data.demand_date","store_challan.slip_no","store_challan.description")
                    ->join('store_challan','store_challan_data.store_challan_no','=','store_challan.store_challan_no')
                    ->whereBetween('store_challan_data.store_challan_date',[$fromDate,$toDate])
                    ->where(['store_challan.sub_department_id' => $row->id,'store_challan.status' => '1','store_challan.store_challan_status' => '2'])
                    ->get();
                CommonFacades::reconnectMasterDatabase();
                if(count($approveStoreChallanListandCreateStoreChallanReturn) == 0){}else{
                ?>
                <?php echo Form::open(array('url' => 'stad/createStoreChallanReturnDetailForm?m='.$m.'&&parentCode='.$parentCode.'&&pageType=add#SFR','id'=>'createStoreChallanReturnDetailForm_'.$row->id.''));?>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="departmentId" value="<?php echo $row->id;?>">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                <th class="text-center">S.No</th>
                                <th class="text-center"><input type="checkbox" style="display: none" name="checkedAll_<?php echo $row->id?>" id="checkedAll_<?php echo $row->id?>" class="checkedAll_<?php echo $row->id?>" /></th>
                                <th class="text-center">Store Challan No.</th>
                                <th class="text-center">Demand No.</th>
                                <th class="text-center">Store Challan Date</th>
                                <th class="text-center">Demand Date</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center">Demand Qty.</th>
                                <th class="text-center">Issue Qty.</th>
                                </thead>
                                <tbody id="filterDemandVoucherList">
                                <?php
                                $counter = 1;
                                foreach ($approveStoreChallanListandCreateStoreChallanReturn as $row1){
                                //if($row1->issue_qty == $row1->qty){}else{
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $counter;?></td>
                                    <td class="text-center">
                                        <input type="checkbox" name="checkAll_<?php echo $row->id?>_<?php echo $row1->id?>"
                                               class="checkSingle_<?php echo $row->id?>"
                                               id="chekedItemWise_<?php echo $row->id?>_<?php echo $row1->sub_item_id?>"
                                               value="<?php echo $row1->store_challan_no?>" onclick="checkCheckedBox(this.id,'<?php echo $row->id;?>','<?php echo $row1->id;?>')">

                                    </td>
                                    <td class="text-center"><?php echo $row1->store_challan_no;?></td>
                                    <td class="text-center"><?php echo $row1->demand_no;?></td>
                                    <td class="text-center"><?php echo CommonFacades::changeDateFormat($row1->store_challan_date);?></td>
                                    <td class="text-center"><?php echo CommonFacades::changeDateFormat($row1->demand_date);?></td>
                                    <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$row1->category_id);?></td>
                                    <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row1->sub_item_id);?></td>
                                    <td class="text-center">
                                        <?php echo StoreFacades::getDemandQtyByDemandNo($m,$row1->category_id,$row1->sub_item_id,$row1->demand_no,'demand_no');?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $row1->issue_qty;?>
                                    </td>
                                </tr>
                                <?php
                                //}
                                $counter++;}
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                    </div>
                </div>
                <?php echo Form::close();?>
                <?php }?>
                <div class="lineHeight">&nbsp;</div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $(".checkedAll_<?php echo $row->id?>").change(function(){
                            if(this.checked){
                                $(".checkSingle_<?php echo $row->id?>").each(function(){
                                    this.checked=true;
                                })
                            }else{
                                $(".checkSingle_<?php echo $row->id?>").each(function(){
                                    this.checked=false;
                                })
                            }
                        });

                        $(".checkSingle_<?php echo $row->id?>").click(function () {
                            if ($(this).is(":checked")){
                                var isAllChecked = 0;
                                $(".checkSingle_<?php echo $row->id?>").each(function(){
                                    if(!this.checked)
                                        isAllChecked = 1;
                                })
                                //if(isAllChecked == 0){ $(".checkedAll_<?php echo $row->id?>").prop("checked", true); }
                            }else {
                                $(".checkedAll_<?php echo $row->id?>").prop("checked", false);
                            }
                        });
                    });
                </script>
                <?php }?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function checkCheckedBox(id,sIdOne,sIdTwo) {
        if ($('#'+id+':checked').length <= 1){
        }else{
            alert("Please select at least one checkbox Same Item.");
            $("input[name='checkAll_"+sIdOne+"_"+sIdTwo+"']:checkbox").prop('checked', false);
        }

    }
</script>