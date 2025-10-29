<?php
$id = $_GET['id'];
$m = CommonFacades::getSessionCompanyId();
$currentDate = date('Y-m-d');
CommonFacades::companyDatabaseConnection($m);
$purchaseRequestDetail = DB::table('purchase_request')->where('purchase_request_no','=',$id)->get();
CommonFacades::reconnectMasterDatabase();
foreach ($purchaseRequestDetail as $row) {
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo StoreFacades::displayApproveDeleteRepostButtonPurchaseRequestSale($m,$row->purchase_request_status,$row->status,$row->purchase_request_no,'purchase_request_no','purchase_request_status','status','purchase_request','purchase_request_data');?>
    </div>
    <div style="line-height:5px;">&nbsp;</div>
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
                            <?php StoreFacades::checkVoucherStatus($row->purchase_request_status,$row->status);?>
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
                                <td style="width:50%;">Purchase Request No.</td>
                                <td style="width:50%;"><?php echo $row->purchase_request_no;?></td>
                            </tr>
                            <tr>
                                <td>Purchase Request Date</td>
                                <td><?php echo CommonFacades::changeDateFormat($row->purchase_request_date);?></td>
                            </tr>
                            <tr>
                                <td>Supplier Name</td>
                                <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'supplier','name',$row->supplier_id);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width:30%; float:right;">
                        <table  class="table table-bordered table-striped table-condensed tableMargin">
                            <tbody>
                            <tr>
                                <td style="width:60%;">Slip No.</td>
                                <td style="width:40%;"><?php echo $row->slip_no;?></td>
                            </tr>
                            <tr>
                                <td>Department / Sub Department</td>
                                <td><?php echo CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$row->sub_department_id);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table  class="table table-bordered table-striped table-condensed tableMargin">
                            <thead>
                            <tr>
                                <th class="text-center" style="width:50px;">S.No</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center" style="width:150px;">Qty.</th>
                                <th class="text-center" style="width:150px;">Rate.</th>
                                <th class="text-center" style="width:150px;">Sub Total.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            CommonFacades::companyDatabaseConnection($m);
                            $purchaseRequestDataDetail = DB::table('purchase_request_data')->where('purchase_request_no','=',$id)->get();
                            CommonFacades::reconnectMasterDatabase();
                            $counter = 1;
                            foreach ($purchaseRequestDataDetail as $row1){
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $counter++;?></td>
                                <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'category','main_ic',$row1->category_id);?></td>
                                <td><?php echo CommonFacades::getCompanyDatabaseTableValueById($m,'subitem','sub_ic',$row1->sub_item_id);?></td>
                                <td class="text-center"><?php echo $row1->purchase_request_qty;?></td>
                                <td class="text-center"><?php echo $row1->rate;?></td>
                                <td class="text-center"><?php echo $row1->sub_total;?></td>
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
                                        <th>Description</th>
                                        <th colspan="5"><?php echo $row->description;?></th>
                                    </tr>
                                    <tr>
                                        <th style="width:15%;">Printed On</th>
                                        <th style="width:15%;"><?php echo Auth::user()->name; ?></th>
                                        <th style="width:15%;">Created By</th>
                                        <th style="width:15%;"><?php echo $row->username;?></th>
                                        <th style="width:20%;">Approved By</th>
                                        <th style="width:20%;"><?php echo $row->approve_username;?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>
</div>