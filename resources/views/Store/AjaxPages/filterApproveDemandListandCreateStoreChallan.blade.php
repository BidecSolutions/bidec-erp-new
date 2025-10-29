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

                $approveDemandVoucherListandCreateStoreChallan = \DB::table("demand_data")
                    ->select("demand_data.demand_no","demand_data.demand_date","demand_data.category_id","demand_data.sub_item_id","demand_data.description","demand_data.id","demand.slip_no","demand_data.qty","demand_data.demand_send_type",
                        \DB::raw("(SELECT SUM(issue_qty) as issue_qty FROM store_challan_data
                        WHERE store_challan_data.issue_qty != 0 and store_challan_data.category_id = demand_data.category_id
                        and store_challan_data.sub_item_id = demand_data.sub_item_id
                        and store_challan_data.demand_no = demand_data.demand_no
                        ) as issue_qty"))
                    ->join('demand','demand_data.demand_no','=','demand.demand_no')
                    ->whereBetween('demand.demand_date',[$fromDate,$toDate])
                    ->where(['demand.sub_department_id' => $row->id,'demand.status' => '1','demand.demand_type' => '1','demand.demand_status' => '2','demand_data.demand_send_type' => '100000'])
                    //->where('demand_data.store_challan_status','!=','2')
                    ->get();
                CommonFacades::reconnectMasterDatabase();
                $counter = 1;
                if(count($approveDemandVoucherListandCreateStoreChallan) == 0){}else{
                ?>
                <?php echo Form::open(array('url' => 'stad/createStoreChallanDetailForm?m='.$m.'&&parentCode='.$parentCode.'&&pageType=add#SFR','id'=>'createStoreChallanDetailForm_'.$row->id.''));?>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="departmentId" value="<?php echo $row->id;?>">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                <th class="text-center">S.No</th>
                                <th class="text-center"><input type="checkbox" style="display: none" name="checkedAll_<?php echo $row->id?>" id="checkedAll_<?php echo $row->id?>" class="checkedAll_<?php echo $row->id?>" /></th>
                                <th class="text-center">Demand No.</th>
                                <th class="text-center">Slip No.</th>
                                <th class="text-center">Demand Date</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Demand Qty.</th>
                                <th class="text-center">Issue Qty.</th>
                                <th class="text-center">Return Qty.</th>
                                </thead>
                                <tbody id="filterDemandVoucherList">
                                <?php
                                foreach ($approveDemandVoucherListandCreateStoreChallan as $row1){
                                $returnQty = StoreFacades::getReturnQtyByDemandNo($m,$row1->category_id,$row1->sub_item_id,$row1->demand_no,'demand_no');
                                if($row1->issue_qty == $row1->qty - $returnQty){}else{
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $counter;?></td>
                                    <td class="text-center">
                                        <input type="checkbox" name="checkAll_<?php echo $row->id?>_<?php echo $row1->id?>"
                                               class="checkSingle_<?php echo $row->id?>"
                                               id="chekedItemWise_<?php echo $row->id?>_<?php echo $row1->sub_item_id?>"
                                               value="<?php echo $row1->demand_no?>" onclick="checkCheckedBox(this.id,'<?php echo $row->id;?>','<?php echo $row1->id;?>')">
                                    </td>
                                    <td class="text-center"><?php echo $row1->demand_no;?></td>
                                    <td class="text-center"><?php echo $row1->slip_no;?></td>
                                    <td class="text-center"><?php echo CommonFacades::changeDateFormat($row1->demand_date);?></td>
                                    <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$row1->category_id);?></td>
                                    <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row1->sub_item_id);?></td>
                                    <td><?php echo $row1->description;?></td>
                                    <td class="text-center">
                                        <?php echo $row1->qty;?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $row1->issue_qty;?>
                                    </td>
                                    <td class="text-center"><?php echo $returnQty;?></td>
                                </tr>
                                <?php
                                }
                                $counter++;
                                }
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