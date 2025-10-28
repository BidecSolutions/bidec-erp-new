<?php
    $id = Input::get('id'); 
    $m;
    $currentDate = date('Y-m-d');

    $eUsername = $getMaterialRequestDetail->username;
	$eDate = CommonFacades::changeDateFormat($getMaterialRequestDetail->date);
	if($getMaterialRequestDetail->material_request_status == '2'){
		$aUsername = $getMaterialRequestDetail->approve_username;
		$aDate = CommonFacades::changeDateFormat($getMaterialRequestDetail->approve_date);
	}else{
		$aUsername = '-';
		$aDate = '-';
	}

?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php CommonFacades::displayPrintButtonInView('printMaterialRequestVoucherVoucherDetail','','0');?>
    </div>
</div>
<div style="line-height:5px;">&nbsp;</div>
<div class="row" id="printMaterialRequestVoucherVoucherDetail">
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
		.tableBorder > thead > tr > th, .tableBorder > tbody > tr > th, .tableBorder > tfoot > tr > th, .tableBorder > thead > tr > td, .tableBorder > tbody > tr > td, .tableBorder > tfoot > tr > td {
			border: 1px solid #000;
		}
		th,td {
			font-size: 11px !important;
		}
		.rowBorderBottom {
			border-bottom: inset; font-weight: bold;
		}
		@media print{
			.tableBorder > thead > tr > th, .tableBorder > tbody > tr > th, .tableBorder > tfoot > tr > th, .tableBorder > thead > tr > td, .tableBorder > tbody > tr > td, .tableBorder > tfoot > tr > td {
				border: 1px solid #000;
			}
			th,td {
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
				border-bottom: inset; font-weight: bold;
			}
		}
	</style>
    <?php echo Form::open(array('url' => 'stad/updateMaterialRequestDetailandApprove?m='.$m.'','id'=>'updateMaterialRequestDetailandApprove'));?>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="pageType" value="<?php echo Input::get('pageType')?>">
        <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode')?>">
        <input type="hidden" name="MaterialRequestNo" value="<?php echo $id; ?>">
        <input type="hidden" name="MaterialRequestDate" value="<?php echo $getMaterialRequestDetail->material_request_date; ?>">
        <input type="hidden" name="initialEmailAddress" id="initialEmailAddress" value="<?php echo CommonFacades::voucherInitialEmailAddress($getMaterialRequestDetail->user_id)?>" /> 
        <input type="hidden" name="location_id" id="location_id" value="<?php echo $getMaterialRequestDetail->location_id?>" />
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hiddend-print">
        <?php if($getMaterialRequestDetail->material_request_status != 2 && $getMaterialRequestDetail->status == 1){?>
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <label>Voucher Status</label>
                    @if (singlePermission($m, Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type))                        
                    <select name="mrVoucherStatus" id="mrVoucherStatus" class="form-control">
                        <option value="4">Approve</option>
                        <option value="3">Reject</option>
                    </select>
                    @else
                    <select disabled class="form-control">
                        <option>Approve</option>
                        <option>Reject</option>
                    </select>                        
                    @endif
                </div>
                <div class="col-lg-8 col-md-8 col-xs-8 col-xs-12">
                    @if (singlePermission($m, Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type))
                        <label>Remarks</label>
                        <textarea name="mrVoucherRemarks" id="mrVoucherRemarks" class="form-control"><?php if(empty($getMaterialRequestDetail->additional_remarks)){echo '-';}else{echo $getMaterialRequestDetail->additional_remarks;}?></textarea>
                    @else
                        <label>Remarks</label>
                        <textarea disabled class="form-control"></textarea>
                    @endif
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right hiddend-print">
                    @if (singlePermission($m, Auth::user()->id, Input::get('parentCode'), 'right_approve', Auth::user()->acc_type))
                    {{ Form::button('Submit', ['class' => 'btn btn-success btn-abc hidden-print']) }}
                    @endif
                </div>
            </div>
            <?php }?>
        </div>
        <div style="line-height:5px;">&nbsp;</div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="well">
                <?php
                    if(empty($getHeaderFooterSettingDetail)){
                        echo CommonFacades::headerPrintSectionInPrintView($m);
                    }else{
                        echo CommonFacades::headerSettingPrintSectionInPrintView($m,'1');
                    }
                ?>
                <div style="line-height:5px;">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center borderBottom borderTop quotationHeading">View Material Request Voucher Detail</div>
                </div>
                <div style="line-height:5px;">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="floatLeft">
                            <table  class="table table-bordered table-condensed tableMargin tableBorder">
                                <tbody>
                                <tr>
                                    <td style="width:40%;">Material Request No.</td>
                                    <td style="width:60%;"><?php echo $getMaterialRequestDetail->material_request_no;?></td>
                                </tr>
                                <tr>
                                    <td>Material Request Date</td>
                                    <td><?php echo CommonFacades::changeDateFormat($getMaterialRequestDetail->material_request_date);?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="floatRight">
                            <table  class="table table-bordered table-condensed tableMargin tableBorder">
                                <tbody>
                                    <tr>
                                        <td>Location</td>
                                        <td><?php echo $getMaterialRequestDetail->location_name?></td>
                                    </tr>
                                    <tr>
                                        <td>Department / Sub Department</td>
                                        <td><?php echo $getMaterialRequestDetail->department_name?> / <?php echo $getMaterialRequestDetail->sub_department_name?></td>
                                    </tr>
                                    <tr>
                                        <td>Project</td>
                                        <td><?php echo $getMaterialRequestDetail->project_name?></td>
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
                            <table  class="table table-bordered table-condensed tableMargin tableBorder">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:50px;">S.No</th>
                                        <th class="text-center">Category Name</th>
                                        <th class="text-center">Item Code</th>
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center" style="width:150px;">Description</th>
                                        <th class="text-center">U.O.M</th>
                                        <th class="text-center" style="width:150px;">Qty.</th>
                                        <th class="text-center" style="width:150px;">Issue Qty.</th>
                                        <th class="text-center" style="width:150px;">Remaining Qty.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $counter = 1;
                                        $totalCountRows = count($getMaterialRequestDataDetail);
                                        foreach ($getMaterialRequestDataDetail as $row1){
                                    ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php echo $counter++;?>
                                                    <input type="hidden" name="storeMaterialRequest_<?php $row1->id;?>" value="<?php echo '0'; ?>">
                                                    <input type="hidden" name="rowId[]" id="rowId_<?php $row1->id;?>" value="<?php echo $row1->id;?>">
                                                </td>
                                                <td>
                                                    <?php echo $row1->main_ic;?>
                                                    <input type="hidden" name="categoryId_<?php echo $row1->id;?>" id="categoryId_<?php echo $row1->id;?>" value="<?php echo $row1->category_id;?>">
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $row1->item_code;?>
                                                </td>
                                                <td>
                                                    <?php echo $row1->sub_ic;?>
                                                    <input type="hidden" name="subItemId_<?php echo $row1->id;?>" id="subItemId_<?php echo $row1->id;?>" value="<?php echo $row1->sub_item_id;?>">
                                                </td>
                                                <td class="text-center"><?php echo $row1->sub_description;?></td>
                                                <td class="text-center"><?php echo $row1->uom_name;?></td>
                                                <td class="text-center"><?php echo $row1->qty;?></td>
                                                <td class="text-center">{{$row1->issueQty ?? '0'}}</td>
                                                <td class="text-center"><?php echo $row1->qty - $row1->issueQty;?></td>
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
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="panel panelBorder">
                                    <div class="text-center" style="font-weight: bold;">Entered By</div>
                                    <div style="border-bottom: inset;"></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Sign:
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Name:&nbsp;&nbsp;&nbsp;<?php echo $eUsername;?>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Date:&nbsp;&nbsp;&nbsp;<?php echo $eDate;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                <div class="panel panelBorder">
                                    <div class="text-center" style="font-weight: bold;">Approved By</div>
                                    <div style="border-bottom: inset;"></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Sign:
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Name:&nbsp;&nbsp;&nbsp;<?php echo $aUsername;?>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rowBorderBottom">
                                                Date:&nbsp;&nbsp;&nbsp;<?php echo $aDate;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
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

        var postData = $('#updateMaterialRequestDetailandApprove').serializeArray();
        var formURL = $('#updateMaterialRequestDetailandApprove').attr("action");
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data){
                $('#showDetailModelOneParamerter').modal('toggle');
                //alert(data);
                filterVoucherList();
            }
        });
    }
</script>

