<?php
$id = $_GET['id'];
$m = CommonFacades::getSessionCompanyId();
$currentDate = date('Y-m-d');
$companyList = DB::Connection('mysql')->table('company')->where('status','=','1')->where('id','!=',$m)->get();
CommonFacades::companyDatabaseConnection($m);
$purchaseRequestDetail = DB::table('purchase_request')->where('purchase_request_no','=',$id)->get();
CommonFacades::reconnectMasterDatabase();
foreach ($purchaseRequestDetail as $row) {
if($row->purchase_request_status == 2 && $row->status == 1){
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php CommonFacades::displayPrintButtonInView('printPurchaseRequestVoucherVoucherDetail','','0');?>
    </div>
</div>
<div style="line-height:5px;">&nbsp;</div>
<?php }?>
<div class="row" id="printPurchaseRequestVoucherVoucherDetail">
    <?php //echo PurchaseFacades::displayApproveDeleteRepostButtonTwoTable($m,$row->demand_status,$row->status,$row->demand_no,'demand_no','demand_status','status','demand','demand_data');?>
    <?php echo Form::open(array('url' => 'pad/updatePurchaseRequestDetailandApprove?m='.$m.'','id'=>'updatePurchaseRequestDetailandApprove'));?>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
    <input type="hidden" name="PurchaseRequestNo" value="<?php echo $id; ?>">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                    <label style="border-bottom:2px solid #000 !important;">Printed On Date&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo CommonFacades::changeDateFormat($currentDate);?></label>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-5">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                             style="font-size: 30px !important; font-style: inherit;
    								font-family: -webkit-body; font-weight: bold;">
                            <?php echo CommonFacades::getCompanyName($m);?>
                        </div>
                        <br />
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                             style="font-size: 20px !important; font-style: inherit;
    								font-family: -webkit-body; font-weight: bold;">
                            Purchase Requisition
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-right">
                    <?php $nameOfDay = date('l', strtotime($currentDate)); ?>
                    <label style="border-bottom:2px solid #000 !important;">Printed On Day&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo '&nbsp;'.$nameOfDay;?></label>

                </div>
            </div>
            <div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div style="width:30%; float:left;">
                        <table  class="table table-bordered table-striped table-condensed tableMargin">
                            <tbody>
                            <tr>
                                <td style="width:40%;">Purchase Request No.</td>
                                <td style="width:60%;"><?php echo $row->purchase_request_no;?></td>
                            </tr>
                            <tr>
                                <td>Purchase Request Date</td>
                                <td><?php echo CommonFacades::changeDateFormat($row->purchase_request_date);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width:35%; float:right;">
                        <table  class="table table-bordered table-striped table-condensed tableMargin">
                            <tbody>
                            <tr>
                                <td style="width:40%;">Requested Date.</td>
                                <td style="width:60%;"><?php echo CommonFacades::changeDateFormat($row->required_date);?></td>
                            </tr>
                            <tr>
                                <td>Requested By</td>
                                <td><?php echo CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$row->sub_department_id);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    The following items are required
                </div>
                <div style="line-height:5px;">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table  class="table table-bordered table-striped table-condensed tableMargin">
                            <thead>
                            <tr>
                                <th class="text-center" style="width:50px;">S.No</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Item Code</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center">U.O.M</th>
                                <th class="text-center" style="width:150px;">Qty.</th>
                                <th class="text-center" style="width:150px;">Approx. Cost</th>
                                <th class="text-center" style="width:150px;">Approx. Sub Total</th>
                                <th class="text-center" style="width:150px;">Current Bal.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            CommonFacades::companyDatabaseConnection($m);
                            $purchaseRequestDataDetail = DB::table('purchase_request_data')->where('purchase_request_no','=',$id)->get();
                            CommonFacades::reconnectMasterDatabase();
                            $counter = 1;
                            $totalCountRows = count($purchaseRequestDataDetail);
                            foreach ($purchaseRequestDataDetail as $row1){
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $counter++;?>
                                    <input type="hidden" name="storePurchaseRequest_<?php echo $row1->id;?>" id="storePurchaseRequest_<?php echo $row1->id;?>" value="<?php echo '1'; ?>">
                                    <input type="hidden" name="rowId[]" id="rowId_<?php $row1->id;?>" value="<?php echo $row1->id;?>">
                                </td>
                                <td>
                                    <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$row1->category_id);?>
                                    <input type="hidden" name="categoryId_<?php echo $row1->id;?>" id="categoryId_<?php echo $row1->id;?>" value="<?php echo $row1->category_id;?>">
                                </td>
                                <td class="text-center">
                                    <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','item_code',$row1->sub_item_id);?>
                                </td>
                                <td>
                                    <?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row1->sub_item_id);?>
                                    <input type="hidden" name="subItemId_<?php echo $row1->id;?>" id="subItemId_<?php echo $row1->id;?>" value="<?php echo $row1->sub_item_id;?>">
                                </td>
                                <td class="text-center"><?php echo CommonFacades::getMasterTableValueById($m,'uom','uom_name',$row1->uom_id);?></td>
                                <td class="text-center">
                                    <?php echo $row1->qty;?>
                                    <input type="hidden" name="qty_<?php echo $row1->id;?>" id="qty_<?php echo $row1->id;?>" value="<?php echo $row1->qty;?>">
                                </td>
                                <td class="text-center">
                                    <?php echo $row1->approx_cost;?>
                                    <input type="text" name="approx_cost_<?php echo $row1->id;?>" id="approx_cost_<?php echo $row1->id;?>" value="<?php echo $row1->approx_cost;?>" onchange="makeApproxSubTotal(this.id,this.value,'<?php echo $row1->qty;?>')" class="hidden-print">
                                </td>
                                <td class="text-center">
                                    <?php echo $row1->approx_sub_total;?>
                                    <input type="text" readonly name="approx_sub_total_<?php echo $row1->id;?>" id="approx_sub_total_<?php echo $row1->id;?>" value="<?php echo $row1->approx_sub_total;?>" class="hidden-print">
                                </td>
                                <td class="text-center"><?php echo $currentBalance = CommonFacades::checkItemWiseCurrentBalanceQty($m,$row1->category_id,$row1->sub_item_id,'',$currentDate);?></td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="line-height:8px;">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table  class="table table-bordered table-striped table-condensed tableMargin">
                                    <thead>
                                    <tr>
                                        <th style="width:15%;">Approved By Deptt. Head</th>
                                        <th style="width:15%;"><br /><br /><?php //echo Auth::user()->name; ?></th>
                                        <th style="width:15%;">Checked by Purchase Deptt::</th>
                                        <th style="width:15%;"><br /><br /><?php //echo $row->username;?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hidden hidden-print qrCodeDiv">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate('View Purchase Request Voucher Detail'))!!} ">
                </div>
            </div>
        </div>
    </div>
    <?php }?>
    <?php //if($row->purchase_request_status == 1 && $row->status == 1){?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hiddend-print">
        {{ Form::button('Update and Approve', ['class' => 'btn btn-success btn-abc hidden-print']) }}
    </div>
    <?php //}?>
    <?php echo Form::close();?>
</div>
<script type="text/javascript">
    $(".btn-abc").click(function(e){
        var _token = $("input[name='_token']").val();
        jqueryValidationCustom();
        if(validate == 0){
            //alert(response);
        }else{
            return false;
        }
        formSubmitOne();
    });

    function formSubmitOne(e){

        var postData = $('#updatePurchaseRequestDetailandApprove').serializeArray();
        var formURL = $('#updatePurchaseRequestDetailandApprove').attr("action");
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data){
                $('#showDetailModelOneParamerter').modal('toggle');
                filterVoucherList();
            }
        });
    }

    function makeApproxSubTotal(id,value,qty) {
        var res = id.split('_');
        var recordNumber = res[2];
        var approxSubTotal = value * qty;
        $('#approx_sub_total_'+recordNumber+'').val(approxSubTotal);

    }
</script>

