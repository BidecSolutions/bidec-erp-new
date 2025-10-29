<?php
$id = Input::get('id');
$m;
$currentDate = date('Y-m-d');

$eUsername = $getStoreChallanDetail->username;
$eDate = CommonFacades::changeDateFormat($getStoreChallanDetail->date);
if ($getStoreChallanDetail->store_challan_status == '1') {
    $aUsername = $getStoreChallanDetail->approve_username;
    $aDate = CommonFacades::changeDateFormat($getStoreChallanDetail->approve_date);
} else {
    $aUsername = '-';
    $aDate = '-';
}

?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php CommonFacades::displayPrintButtonInView('printStoreChallanVoucherVoucherDetail', '', '0'); ?>
    </div>
</div>
<div style="line-height:5px;">&nbsp;</div>
<div class="row" id="printStoreChallanVoucherVoucherDetail">
    <style>
        .floatLeft {
            width: 30%;
            float: left;
        }

        .floatRight {
            width: 35%;
            float: right;
        }

        .tableBorder {
            border: 3px solid black !important;
        }

        .borderBottom {
            border-bottom: 3px solid black;
        }

        .borderTop {
            border-top: 3px solid black;
        }

        .tableBorder>thead>tr>th,
        .tableBorder>tbody>tr>th,
        .tableBorder>tfoot>tr>th,
        .tableBorder>thead>tr>td,
        .tableBorder>tbody>tr>td,
        .tableBorder>tfoot>tr>td {
            border: 1px solid #000;
        }

        th,
        td {
            font-size: 11px !important;
        }

        .rowBorderBottom {
            border-bottom: inset;
            font-weight: bold;
        }

        @media print {

            .tableBorder>thead>tr>th,
            .tableBorder>tbody>tr>th,
            .tableBorder>tfoot>tr>th,
            .tableBorder>thead>tr>td,
            .tableBorder>tbody>tr>td,
            .tableBorder>tfoot>tr>td {
                border: 1px solid #000;
            }

            th,
            td {
                font-size: 11px !important;
            }

            .tableBorder {
                border: 3px solid black !important;
            }

            .borderBottom {
                border-bottom: 3px solid black;
            }

            .borderTop {
                border-top: 3px solid black;
            }

            .rowBorderBottom {
                border-bottom: inset;
                font-weight: bold;
            }
        }
    </style>
    <?php echo Form::open(['url' => 'stad/updateStoreChallanDetailandApprove?m=' . $m . '', 'id' => 'updateStoreChallanDetailandApprove']); ?>
    <input hidden value=" <?php echo $getStoreChallanDetail->store_challan_status; ?>" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="pageType" value="<?php echo Input::get('pageType'); ?>">
    <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode'); ?>">
    <input type="hidden" name="StoreChallanNo" value="<?php echo $id; ?>">
    <input type="hidden" name="StoreChallanDate" value="<?php echo $getStoreChallanDetail->store_challan_date; ?>">
    <input type="hidden" name="locationId" value="<?php echo $getStoreChallanDetail->location_id; ?>">
    <input type="hidden" name="warehouse_from_id" value="<?php echo $getStoreChallanDetail->warehouse_from_id; ?>">
    <input type="hidden" name="warehouse_to_id" value="<?php echo $getStoreChallanDetail->warehouse_to_id; ?>">
    <input hidden name="store_challan_status" value=" <?php echo $getStoreChallanDetail->store_challan_status; ?>" />
    <input type="hidden" name="purpose" value=" <?php echo $getStoreChallanDetail->purpose; ?>" />
    <?php if($getStoreChallanDetail->store_challan_status == 1 && $getStoreChallanDetail->status == 1){?>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <label>Select Person who will receive:</label>
        <select name="receiver_user_id" id="receiver_user_id" class="form-control">
            <?php echo SelectListFacades::getUserDetailList($m, 0, 0); ?>
        </select>
    </div>
    <?php }?>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <?php if($getStoreChallanDetail->store_challan_status == 1 && $getStoreChallanDetail->status == 1){?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left hiddend-print">
            @if (singlePermission($m, Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type))
                {{ Form::button('Approve', ['class' => 'btn btn-success btn-abc hidden-print']) }}
            @endif
        </div>
        <?php }?>
    </div>
   
    <?php if($getStoreChallanDetail->store_challan_status == 2 && $getStoreChallanDetail->status == 1 && $getStoreChallanDetail->store_challan_status !=4 ){?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        @if (singlePermission($m, Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type))
            {{ Form::button('Recive', ['class' => 'btn btn-success btn-abc hidden-print']) }}
        @endif
    </div>
    <?php }?>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well">
            <?php
            if (empty($getHeaderFooterSettingDetail)) {
                echo CommonFacades::headerPrintSectionInPrintView($m);
            } else {
                echo CommonFacades::headerSettingPrintSectionInPrintView($m, '1');
            }
            ?>
            <div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div
                    class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center borderBottom borderTop quotationHeading">
                    View Store Challan Voucher Detail</div>
            </div>
            <div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="floatLeft">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
                                <tr>
                                    <td style="width:40%;">Store Challan No.</td>
                                    <td style="width:60%;"><?php echo $getStoreChallanDetail->store_challan_no; ?></td>
                                </tr>
                                <tr>
                                    <td>Store Challan Date</td>
                                    <td><?php echo CommonFacades::changeDateFormat($getStoreChallanDetail->material_request_date); ?></td>
                                </tr>
                                <tr>
                                    <td style="width:40%;">Material Request No.</td>
                                    <td style="width:60%;"><?php echo $getStoreChallanDetail->material_request_no; ?></td>
                                </tr>
                                <tr>
                                    <td>Material Request Date</td>
                                    <td><?php echo CommonFacades::changeDateFormat($getStoreChallanDetail->material_request_date); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="floatRight">
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <tbody>
                                <tr>
                                    <td>Location</td>
                                    <td><?php echo $getStoreChallanDetail->location_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Department / Sub Department</td>
                                    <td><?php echo $getStoreChallanDetail->department_name; ?> / <?php echo $getStoreChallanDetail->sub_department_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Project</td>
                                    <td><?php echo $getStoreChallanDetail->project_name; ?></td>
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
                        <table class="table table-bordered table-condensed tableMargin tableBorder">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">S.No</th>
                                    <th class="text-center">Category Name</th>
                                    <th class="text-center">Item Code</th>
                                    <th class="text-center">Item Name</th>
                                    <th class="text-center">Item Type</th>
                                    <th class="text-center">U.O.M</th>
                                    <th class="text-center">Request Qty.</th>
                                    <th class="text-center">Issue Qty.</th>
                                    <th class="text-center">Receive Qty.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                        $counter = 1;
                                        $totalCountRows = count($getStoreChallanDataDetail);
                                        foreach ($getStoreChallanDataDetail as $row1){
                                            $itemType = 'New';
                                            if($row1->item_type == 2){
                                                $itemType = 'Refurb';
                                            }
                                    ?>
                                <input type="hidden" name="storeChallanDataRow[]" id="storeChallanDataRow"
                                    value="<?php echo $row1->id; ?>" />
                                <input type="hidden" name="category_id_<?php echo $row1->id; ?>"
                                    id="category_id_<?php echo $row1->id; ?>" value="<?php echo $row1->category_id; ?>" />
                                <input type="hidden" name="sub_item_id_<?php echo $row1->id; ?>"
                                    id="sub_item_id_<?php echo $row1->id; ?>" value="<?php echo $row1->sub_item_id; ?>" />
                                <tr>
                                    <td class="text-center"><?php echo $counter++; ?></td>
                                    <td><?php echo $row1->main_ic; ?></td>
                                    <td class="text-center"><?php echo $row1->item_code; ?></td>
                                    <td><?php echo $row1->sub_ic; ?></td>
                                    <td><?php echo $itemType; ?></td>
                                    <td class="text-center"><?php echo $row1->uom_name; ?></td>
                                    <td class="text-center"><?php echo $row1->qty; ?></td>
                                    <td class="text-center"><?php echo $row1->issue_qty; ?></td>
                                    <td class="text-center">
                                        <input type="text" name="receive_qty_<?php echo $row1->id; ?>"
                                            id="receive_qty_<?php echo $row1->id; ?>" value="<?php echo $row1->issue_qty; ?>"
                                            class="form-control" />
                                    </td>
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
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panelBorder">
                                <div class="text-center" style="font-weight: bold;">Entered By</div>
                                <div style="border-bottom: inset;"></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Sign:
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Name:&nbsp;&nbsp;&nbsp;<?php echo $eUsername; ?>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Date:&nbsp;&nbsp;&nbsp;<?php echo $eDate; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panelBorder">
                                <div class="text-center" style="font-weight: bold;">Approved By</div>
                                <div style="border-bottom: inset;"></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Sign:
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Name:&nbsp;&nbsp;&nbsp;<?php echo $aUsername; ?>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Date:&nbsp;&nbsp;&nbsp;<?php echo $aDate; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panelBorder">
                                <div class="text-center" style="font-weight: bold;">Security</div>
                                <div style="border-bottom: inset;"></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Sign:
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Name:&nbsp;&nbsp;&nbsp;<?php echo $aUsername; ?>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Date:&nbsp;&nbsp;&nbsp;<?php echo $aDate; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panelBorder">
                                <div class="text-center" style="font-weight: bold;">Received By</div>
                                <div style="border-bottom: inset;"></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Sign:
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Name:
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                            Date:
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo Form::close(); ?>
</div>
<script type="text/javascript">
    $(".btn-abc").click(function(e) {
        var _token = $("input[name='_token']").val();
        jqueryValidationCustom();
        if (validate == 0) {
            //alert(response);
        } else {
            return false;
        }
        formSubmitOne();
    });

    function formSubmitOne(e) {

        var postData = $('#updateStoreChallanDetailandApprove').serializeArray();
        var formURL = $('#updateStoreChallanDetailandApprove').attr("action");
        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            success: function(data) {
                $('#showDetailModelOneParamerter').modal('toggle');
                //alert(data);
                filterVoucherList();
            }
        });
    }
</script>
