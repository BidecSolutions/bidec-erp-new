<?php
	use App\Models\Account;
	$m;
	$makeGetValue = explode('<*>',$_GET['mrNo']);
	$mrNo = $makeGetValue[0];
	$mrDate = $makeGetValue[1];
?>
<script>
	function updateOverAllDebitAmount(){
		var sum = 0;
		$("input[class *= 'yesSubTotalAmount']").each(function(){
			sum += +$(this).val();
		});
		$('#pv_debit_amount').val(sum);
	}
	
	function calculateTotalTaxHeadAmount(){
		var sum = 0;
		$("input[class *= 'yesTaxHeadAmount']").each(function(){
			sum += +$(this).val();
		});
		$('#overAllTaxAmount').val(sum);
	}
	
	function optionEnableAndDisableTaxHeadSlap(paramOne){
		var taxHeadOption = $('#tax_head_option_'+paramOne+'').val();
		if(taxHeadOption == 1){
			$('#tax_head_amount_'+paramOne+'').addClass('yesTaxHeadAmount');
			$('#tax_head_amount_'+paramOne+'').prop("readonly", false);
		}else{
			$('#tax_head_amount_'+paramOne+'').removeClass('yesTaxHeadAmount');
			$('#tax_head_amount_'+paramOne+'').val('0');
			$('#tax_head_amount_'+paramOne+'').prop("readonly", true);
		}
		calculateTotalTaxHeadAmount();
	}
	
	function optionEnableAndDisablePurchaseOrderRequestRegionWise(paramOne,paramTwo,paramThree){
		var generatePurchaseOrderType = $('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').val();
		var countYesValue = $('#countYesValue_'+paramOne+'').val();
		if(generatePurchaseOrderType == 1){
			$('#countYesValue_'+paramOne+'').val(parseInt(countYesValue) + parseInt('1'));
			$('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').addClass('yesOption_'+paramOne+'');
			$('#purchase_order_qty_'+paramOne+'_'+paramThree+'').val('1');
			$('#unit_price_'+paramOne+'_'+paramThree+'').val('1');
			$('#sub_total_'+paramOne+'_'+paramThree+'').val('1');
			$('#sub_total_with_persent_'+paramOne+'_'+paramThree+'').val('0');
		}else{
			$('#countYesValue_'+paramOne+'').val(parseInt(countYesValue) - parseInt('1'));
			$('#generate_purchase_order_type_'+paramOne+'_'+paramThree+'').removeClass('yesOption_'+paramOne+'');
			$('#purchase_order_qty_'+paramOne+'_'+paramThree+'').val('0');
			$('#unit_price_'+paramOne+'_'+paramThree+'').val('0');
			$('#sub_total_'+paramOne+'_'+paramThree+'').val('0');
			$('#sub_total_with_persent_'+paramOne+'_'+paramThree+'').val('0');
		}
		var countYesValueTwo = $('#countYesValue_'+paramOne+'').val();
		if(countYesValueTwo == 1){
			$('.yesOption_'+paramOne+'').prop("disabled", true);
			updateOverAllDebitAmount();
		}else{
			$('.yesOption_'+paramOne+'').prop("disabled", false);
			updateOverAllDebitAmount();
		}
	}
	
	
	
	function calculateTaxHeadPercentageAndAmount(paramOne,paramTwo){
		var pvDebitAmount = $('#pv_debit_amount').val();
		if(pvDebitAmount == '0'){
			alert('Something Wrong!');
		}else if(pvDebitAmount == ''){
			alert('Something Wrong!');
		}else{
			var taxHeadPercentage = $('#tax_head_percentage_'+paramOne+'').val();
			var taxHeadAmount = $('#tax_head_amount_'+paramOne+'').val();
			if(paramTwo == 1){
				//Convert our percentage value into a decimal.
				var percentInDecimal = parseInt(taxHeadPercentage) / 100;
				//Get the result.
				var percentAmount = percentInDecimal * pvDebitAmount;
				//Print it out - Result is 232.
				$('#tax_head_amount_'+paramOne+'').val(percentAmount);
			}else if(paramTwo == 2){
				//Convert our percentage value into a decimal.
				var percentInDecimal = parseInt(taxHeadAmount) / pvDebitAmount;
				//Get the result.
				var percent = percentInDecimal * 100;
				//Print it out - Result is 232.
				$('#tax_head_percentage_'+paramOne+'').val(percent);
			}
		}
	}
    var totalRows = '<?php echo count($getMaterialRequestDataDetail)?>';
    
    function storeChallanOptionDisableAndEnable(id){
        var issueStatusValue = $('#issue_status_'+id+'').val();
        if(issueStatusValue == 1){
            totalRows += 1;
            $("#issue_status_"+id+"").addClass("issueStatus");
            $("#storeChallanDetailRow_"+id+" input").prop('disabled', false);
        }else{
            totalRows -= 1;
            $("#storeChallanDetailRow_"+id+" input").prop('disabled', true);
            $("#issue_status_"+id+"").removeClass("issueStatus");

        }
        if(totalRows == 1){
            $('.issueStatus').prop('disabled', true);
        }else{
            $('.issueStatus').prop('disabled', false);
        }

    }
	if(totalRows == 1){
        $('.issueStatus').prop('disabled', true);
    }
</script>
<div>
    <input type="hidden" name="mrNo" id="mrNo" value="<?php echo $mrNo ?>" readonly/>
    <input type="hidden" name="mrDate" id="mrDate" value="<?php echo $mrDate ?>" readonly/>
    <input type="hidden" name="subDepartmentId" id="subDepartmentId" value="<?php echo $getMaterialRequestDetail->sub_department_id ?>" readonly/>
    <input type="hidden" name="locationId" id="locationId" value="<?php echo $getMaterialRequestDetail->location_id ?>" readonly/>
    <input type="hidden" name="projectId" id="projectId" value="<?php echo $getMaterialRequestDetail->project_id ?>" readonly/>
    <input type="hidden" name="departmentId" id="departmentId" value="<?php echo $getMaterialRequestDetail->department_id?>" readonly />
    <input type="hidden" name="initialEmailAddress" id="initialEmailAddress" value="<?php echo CommonFacades::voucherInitialEmailAddress($getMaterialRequestDetail->user_id)?>" />
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Store Challan No</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control" readonly name="store_challan_return_no" id="store_challan_return_no" value="<?php echo $mrNo ?>" />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Location</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="location_name" id="location_name" class="form-control" readonly value="<?php echo $getMaterialRequestDetail->location_name;?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Department / Sub Department</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="sub_department_name" id="sub_department_name" class="form-control" readonly value="<?php echo $getMaterialRequestDetail->department_name.' / '.$getMaterialRequestDetail->sub_department_name?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Store Challan Date.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField fromDateDatePicker" name="store_challan_return_date" id="store_challan_return_date" readonly value="<?php echo $formDateValue ?>" />
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Project</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="project_name" id="project_name" class="form-control" readonly value="<?php echo $getMaterialRequestDetail->project_name;?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Slip No.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField" placeholder="Slip No" name="slip_no" id="slip_no" value="" />
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label class="sf-label">Remarks</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-bordered sf-table-list">
                <thead>
                    <tr>
                        <th class="text-center">Option</th>
                        <th class="text-center">Item Code / Item Name</th>
                        <th class="text-center">Store Challan Qty</th>
                        <th class="text-center">Privious Return Qty.</th>
                        <th class="text-center">Qty.</th>
                        <th class="text-center">Sub Description</th>
                    </tr>
                </thead>
                <?php
                    $counter = 1;
                    foreach($getMaterialRequestDataDetail as $gmrddRow){
                        $remainingStoreChallanQty = $gmrddRow->issue_qty - $gmrddRow->totalReturnQty;
                        // $currentBalance = CommonFacades::checkItemWiseCurrentBalanceQtyNew($m,$gmrddRow->category_id,$gmrddRow->sub_item_id,'',date('Y-m-d'),$getMaterialRequestDetail->location_id);
                        $currentBalance = CommonFacades::stockLocationWiseSum($gmrddRow->category_id, $gmrddRow->sub_item_id, $getMaterialRequestDetail->location_id, $getMaterialRequestDetail->purpose)
                        
                ?>
                        <input type="hidden" name="categoryId_<?php echo $gmrddRow->id?>" id="categoryId_<?php echo $gmrddRow->id?>" value="<?php echo $gmrddRow->category_id?>" />
                        <input type="hidden" name="subItemId_<?php echo $gmrddRow->id?>" id="categoryId_<?php echo $gmrddRow->id?>" value="<?php echo $gmrddRow->sub_item_id?>" />
                        <input type="hidden" name="seletedStoreChallanReturnRow[]" readonly id="seletedStoreChallanReturnRow" value="<?php echo $gmrddRow->id?>" class="form-control" />
                        <input type="hidden" name="storeChallanNo_<?php echo $gmrddRow->id?>" readonly id="storeChallanNo_<?php echo $gmrddRow->id?>" value="<?php echo $gmrddRow->store_challan_no;?>" class="form-control" />
                        <input type="hidden" name="storeChallanDate_<?php echo $gmrddRow->id?>" readonly id="storeChallanDate_<?php echo $gmrddRow->id?>" value="<?php echo $gmrddRow->store_challan_date;?>" class="form-control" />
                        <input type="hidden" name="purpose_<?php echo $gmrddRow->id?>" readonly id="purpose<?php echo $gmrddRow->id?>" value="<?php echo $getMaterialRequestDetail->purpose;?>" class="form-control" />
                        <input type="hidden" name="warehouse_from_id_<?php echo $gmrddRow->id?>" readonly id="purpose<?php echo $gmrddRow->id?>" value="<?php echo $getMaterialRequestDetail->warehouse_from_id;?>" class="form-control" />
                        <input type="hidden" name="warehouse_to_id_<?php echo $gmrddRow->id?>" readonly id="purpose<?php echo $gmrddRow->id?>" value="<?php echo $getMaterialRequestDetail->warehouse_to_id;?>" class="form-control" />
                        <input type="hidden" name="from_sub_department_id_<?php echo $gmrddRow->id?>" readonly id="purpose<?php echo $gmrddRow->id?>" value="<?php echo $getMaterialRequestDetail->from_sub_department_id;?>" class="form-control" />
                        <tr id="storeChallanDetailRow_<?php echo $gmrddRow->id?>" class="@if($remainingStoreChallanQty == 0) hidden @endif">
                            <td>
                                <select name="issue_status_<?php echo $gmrddRow->id?>" id="issue_status_<?php echo $gmrddRow->id?>" class="issueStatus form-control" onchange="storeChallanOptionDisableAndEnable('<?php echo $gmrddRow->id?>')">
                                    <option value="1">Yes</option>
                                    <option value="2" @if($remainingStoreChallanQty == 0) selected @endif>No</option>
                                </select>
                            </td>
                            <td><?php echo $gmrddRow->item_code;?> / <?php echo $gmrddRow->sub_ic;?></td>
                            <td class="text-center">
                                <?php if(empty($gmrddRow->issue_qty)){echo 0;}else{echo $gmrddRow->issue_qty;}?>
                                <input type="hidden" name="remaining_store_challan_qty_<?php echo $gmrddRow->id?>" id="remaining_store_challan_qty_<?php echo $gmrddRow->id?>" value="<?php echo $remainingStoreChallanQty?>" />
                            </td>
                            <td><?php if(empty($gmrddRow->totalReturnQty)){echo 0;}else{echo $gmrddRow->totalReturnQty;}?></td>
                            <td>
                                <input type="text" name="return_qty_<?php echo $gmrddRow->id?>" id="return_qty_<?php echo $gmrddRow->id?>" class="form-control" value="{{$remainingStoreChallanQty}}" />
                            </td>
                            <td>
                                <input type="text" name="sub_description_<?php echo $gmrddRow->id?>" id="sub_description_<?php echo $gmrddRow->id?>" class="form-control" value="-" />
                            </td>
                        </tr>  
                <?php
                    $counter++;
                    }
                ?>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        {{ Form::button('Submit', ['class' => 'btn btn-success btn-add-success btnSubmit','id' => 'submit-btn-abc']) }}
        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("select").select2();
    });
    
    $(".btn-add-success").click(function(e){
        var storeChallanData = new Array();
		var val;
		$("input[name='seletedStoreChallanReturnRow[]']").each(function(){
			storeChallanData.push($(this).val());
		});
		var _token = $("input[name='_token']").val();
		for (val of storeChallanData) {
            jqueryValidationCustom();
			if(validate == 0){
                $('.issueStatus').prop('disabled', false);
				$(".btnSubmit").val('Sending, please wait...');
				$('.btnSubmit').prop("disabled", false);
				setTimeout(function(){
					$(".btnSubmit").prop("type", "button");
				},50);
			}else{
				return false;
			}
		}
        formSubmitOne();
	});

    function formSubmitOne(){

        var postData = $('#addStoreChallanDetail').serializeArray();
        var formURL = $('#addStoreChallanDetail').attr("action");
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data){
                window.location.href = "<?php echo url('/')?>/store/viewStoreChallanReturnList?pageType=viewlist&&parentCode=158#SFR";
            }
        });
        }
	$(document).ready(function() {
        var startAccountYear = $("#startAccountYearDMYFormat").val();
        var endAccountYear = $("#endAccountYearDMYFormat").val();
        $(".fromDateDatePicker").datepicker({
            showAnim: "slideDown",
            dateFormat: "dd-mm-yy",
            maxDate: endAccountYear,
            minDate: startAccountYear
        });
    });

    // function checkAvailableBalance(id,value,availanleBalance){
    //     if(availanleBalance < value){
    //         $('#'+id+'').val('0');
    //         alert('Something went wrong! Your available is less than issue qty....');
    //     }
    // }
</script>

