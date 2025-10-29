<?php
	use App\Models\Account;
    use App\Helpers\ReuseableCode;
	$m;
	$makeGetValue = explode('<*>',$_GET['prNo']);
	$prNo = $makeGetValue[0];
	$prDate = $makeGetValue[1];
    $expenseAccountHtml = '';
    foreach ($accounts as $key => $y) {        
        $expenseAccountHtml .= '<option value="'.$y->id.'">'. $y->code .' ---- '. str_replace("'", "", $y->name).'</option>';
    }
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
</script>
<div>
    <input type="hidden" name="prNo" id="prNo" value="<?php echo $prNo ?>" readonly/>
    <input type="hidden" name="prDate" id="prDate" value="<?php echo $prDate ?>" readonly/>
    <input type="hidden" name="subDepartmentId" id="subDepartmentId" value="<?php echo $getPurchaseRequestDetail->sub_department_id ?>" readonly/>
    <input type="hidden" name="locationId" id="locationId" value="<?php echo $getPurchaseRequestDetail->location_id ?>" readonly/>
    <input type="hidden" name="projectId" id="projectId" value="<?php echo $getPurchaseRequestDetail->project_id ?>" readonly/>
    <input type="hidden" name="departmentId" id="departmentId" value="<?php echo $getPurchaseRequestDetail->department_id?>" readonly />
    <input type="hidden" name="initialEmailAddress" id="initialEmailAddress" value="<?php echo CommonFacades::voucherInitialEmailAddress($getPurchaseRequestDetail->user_id)?>" />
    <input type="hidden" id="tax_percent" name="tax_percent" value="">
    <input type="hidden" id="sales_tax_acc_code" name="sales_tax_acc_code" value="">
</div>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Purchase Request No</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control" readonly name="purchase_request_no" id="purchase_request_no" value="<?php echo $prNo ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Location</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="location_name" id="location_name" class="form-control" readonly value="<?php echo $getPurchaseRequestDetail->location_name;?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">P.O Date.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField fromDateDatePicker" name="po_date" id="po_date" readonly value="<?php echo $formDateValue ?>" />
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Department / Sub Department</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="sub_department_name" id="sub_department_name" class="form-control" readonly value="<?php echo $getPurchaseRequestDetail->department_name.' / '.$getPurchaseRequestDetail->sub_department_name?>" >
    </div>
    
</div>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Project</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="project_name" id="project_name" class="form-control" readonly value="<?php echo $getPurchaseRequestDetail->project_name;?>" >
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Delivery place</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control" name="delivery_place" id="delivery_place" placeholder="Delivery Place" value="Factory" />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Invoice/Quotation No.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField" name="qoutation_no" id="qoutation_no" placeholder="Invoice/Quotation No." value="" />
    </div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Remarks</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
    </div>
    {{-- <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Term & Condition</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea  rows="2" cols="50" style="resize:none;" class="form-control">-Purchase Order should not be accepted if any alterations have been made to the date,quantity,rate, description or name of the Supplier.
             Payment will be made  in advance In same account tital as per invoice mentioned. Defective material shall not be accepted & will be replaced at vendor cost
        </textarea>
    </div> --}}
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">TOS Advance %</label>        
        {{-- <input class="form-control" id="termCondition" name="termCondition" type="number" /> --}}
        <select class="form-control" name="termCondition" id="termCondition">
            <option value="0">0</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50</option>
            <option value="60">60</option>
            <option value="70">70</option>
            <option value="80">80</option>
            <option value="90">90</option>
            <option value="100">100</option>
        </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Invoice Date</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="date" name="qoutation_date" id="qoutation_date" value="{{date('Y-m-d')}}" class="form-control requiredField" />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Payment Type</label>
        <select class="form-control" name="paymentTypeTwo" id="paymentTypeTwo" onchange="touglePurchaseOrderPaymentRate()">
            @foreach($paymentTypeList as $ptlRow)
                <option value="{{$ptlRow->id}}<*>{{$ptlRow->conversion_rate_type}}<*>{{$ptlRow->conversion_rate}}">{{$ptlRow->payment_type_name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="paymentType" id="paymentType" value="" />
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Payment Type Rate</label>
        <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001" value="1" class="form-control" />
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label class="sf-label">Note</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control">-</textarea>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-bordered sf-table-list">
                <input type="hidden" name="totalGPRDDRow" id="totalGPRDDRow" value="<?php echo count($getPurchaseRequestDataDetail)?>" />
                <?php
                    $counter = 1;
                    foreach($getPurchaseRequestDataDetail as $gprddRow){
                        $totalGPRDDRow = count($getPurchaseRequestDataDetail);
                        $disableOption = '';
                        if($totalGPRDDRow == 1){
                            $disableOption = 'disabled';
                        }
                ?>
                        
                        <thead>
                            <tr>
                                <th class="text-center hidden"><label><input type="checkbox" name="sale_tax_head_checkbox_<?php echo $gprddRow->id?>" id="sale_tax_head_checkbox_<?php echo $gprddRow->id?>" value="2" onchange="saleTaxEnableAndDisable('<?php echo $gprddRow->id?>')"> Sales Tax Head</label></th>
                                <th class="text-center hidden">Tax Unit</th>
                                <th class="text-center">Option</th>
                                <th class="text-center">Item Code / Item Name</th>
                                <th class="text-center">Supplier Name</th>
                                <th class="text-center">Invoice/Quotation No.</th>
                                <th class="text-center" colspan="2">Qoutation Date</th>
                            </tr>
                        </thead>
                        <tbody class="displayRowPRow_<?php echo $gprddRow->id;?>">
                            <tr>
                                <td class="text-center hidden">
                                    <input type="hidden" name="seletedPurchaseRequestRow[]" readonly id="seletedPurchaseRequestRow" value="<?php echo $gprddRow->id;?>" class="form-control" />
                                    <input type="hidden" name="purchaseRequestSendType_<?php echo $gprddRow->id;?>" readonly id="purchaseRequestSendType_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->purchase_request_send_type;?>" class="form-control" />
                                    <input type="hidden" name="categoryId_<?php echo $gprddRow->id;?>" readonly id="categoryId_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->category_id;?>" class="form-control" />
                                    <input type="hidden" name="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $gprddRow->id;?>" readonly id="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->priviousPurchaseOrderQty > 0 ? $gprddRow->priviousPurchaseOrderQty : 0 ;?>" class="form-control" />
                                    <input type="number" class="form-control requiredField" name="delivery_days_<?php echo $gprddRow->id?>" id="delivery_days_<?php echo $gprddRow->id?>" value="30" />
                                    <input type="number" class="form-control requiredField" placeholder="Payment Terms Days" name="payment_terms_<?php echo $gprddRow->id?>" id="payment_terms_<?php echo $gprddRow->id?>" value="45" />
                                    <?php
                                        $remainingPurchaseOrderQty = $gprddRow->qty - $gprddRow->priviousPurchaseOrderQty;
                                    ?>
                                    <input type="hidden" name="remaining_purchase_order_qty_<?php echo $gprddRow->id?>" id="remaining_purchase_order_qty_<?php echo $gprddRow->id?>" value="<?php echo $remainingPurchaseOrderQty;?>" readonly />
    
                                    <select name="sale_tax_head_<?php echo $gprddRow->id?>" id="sale_tax_head_<?php echo $gprddRow->id?>" class="form-control">
                                        <?php echo SelectListFacades::getChartOfAccountList($m,1,0,0);?>
                                    </select>
                                </td>
                                <td class="text-center hidden">
                                    <input type="number" name="unit_<?php echo $gprddRow->id?>" id="unit_<?php echo $gprddRow->id?>" step="0.00001" placeholder="Type Unit Percent" class="form-control requiredField" onkeyup="makesubtotalamount(<?php echo $gprddRow->id?>,0)" value="0">
                                </td>
                                <td>
                                    <select name="option_<?php echo $gprddRow->id;?>" id="option_<?php echo $gprddRow->id;?>" class="form-control optionPurchaseSkip" onchange="optionDisableAndEnable('<?php echo $gprddRow->id?>')" <?php echo $disableOption;?>>
                                        <option value="1">Purchase</option>
                                        <option value="2">Skip</option>
                                    </select>
                                </td>
                                <td>
                                    <?php echo $gprddRow->item_code;?> / <?php echo $gprddRow->sub_ic;?>
                                    <input type="hidden" name="subItemId_<?php echo $gprddRow->id;?>" readonly id="subItemId_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->sub_item_id;?>" class="form-control" />
                                </td>
                                <td>
                                    <select onchange="makesubtotalamount(<?php echo $gprddRow->id?>,1)" class="form-control requiredField" name="supplier_id_<?php echo $gprddRow->id;?>" id="supplier_id_<?php echo $gprddRow->id;?>">
                                        <?php echo SelectListFacades::getSupplierList($m,1,0);?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control requiredField" name="qoutation_no_<?php echo $gprddRow->id?>" id="qoutation_no_<?php echo $gprddRow->id?>" value="-" placeholder="Qoutation No" />
                                </td>
                                <td colspan="2">
                                    <input type="date" class="form-control requiredField" name="qoutation_date_<?php echo $gprddRow->id?>" id="qoutation_date_<?php echo $gprddRow->id?>" value="<?php echo date('Y-m-d') ?>" placeholder="Qoutation No" />
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th class="text-center">Location Name</th>
                                <th class="text-center">Current Balance</th>
                                <th class="text-center">Purchase Order Qty./Previous Purchased Qty.</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Sub Total <span class="rflabelsteric"><strong>*</strong></span></th>
                                <th class="text-center hidden" colspan="2">Sub Total With % <span class="rflabelsteric"><strong>*</strong></span></th>
                            </tr>
                        </thead>
                        <tbody class="displayRowPRow_<?php echo $gprddRow->id;?>">
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" readonly name="countYesValue_<?php echo $gprddRow->id?>" id="countYesValue_<?php echo $gprddRow->id?>" value="3" />
                                    <input type="hidden" readonly name="optionRegionArray<?php echo $gprddRow->id?>[]" id="optionRegionArray<?php echo $gprddRow->id?>[]" value="1" />
                                    <input type="hidden" name="location_id_<?php echo $gprddRow->id?>_1" id="location_id_<?php echo $gprddRow->id?>_1" value="<?php echo $getPurchaseRequestDetail->location_id?>" />
                                    <?php echo $getPurchaseRequestDetail->location_name;?>
                                </td>
                                <td class="text-center">
                                    <?php echo CommonFacades::stockLocationWiseSum($gprddRow->category_id,$gprddRow->sub_item_id,$getPurchaseRequestDetail->location_id);?>
                                </td>
                                <td class="text-center" style="display: flex; justify-content: space-around;">
                                <input type="number" 
                                style="width: 45%;" 
                                name="purchase_order_qty_<?php echo $gprddRow->id; ?>_1" 
                                id="purchase_order_qty_<?php echo $gprddRow->id; ?>_1" 
                                step="0.00001" 
                                placeholder="Type Purchase Order Qty" 
                                class="form-control requiredField" 
                                onkeyup="makesubtotalamount(<?php echo $gprddRow->id; ?>, 1); checkRemainingPurchaseOrderQty(<?php echo $gprddRow->id; ?>, 1);" 
                                value="<?php echo $remainingPurchaseOrderQty; ?>" 
                            >
                                    <input type="hidden" name="purchase_request_qty_<?php echo $gprddRow->id?>_1" id="purchase_request_qty_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $gprddRow->id?>,1)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="<?php echo $remainingPurchaseOrderQty;?>">
                                    <input disabled class="form-control" style="width: 45%;" type="text" value="{{ CommonFacades::checkPriviousReceiveQtyPurchaseRequestWise($gprddRow->category_id, $gprddRow->sub_item_id, $gprddRow->purchase_request_no) }}">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="unit_price_<?php echo $gprddRow->id?>_1" id="unit_price_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Unit Price" class="form-control requiredField unit_price" onkeyup="makesubtotalamount(<?php echo $gprddRow->id?>,1)" value="<?php echo ReuseableCode::last_item_cost($gprddRow->sub_item_id)?>">
                                </td>
                                <td class="text-center">
                                    <input type="number" readonly name="sub_total_<?php echo $gprddRow->id?>_1" id="sub_total_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Sub Total" class="form-control requiredField yesSubTotalAmount" value="{{ ReuseableCode::last_item_cost($gprddRow->sub_item_id) * $remainingPurchaseOrderQty }}">
                                </td>
                                <td class="text-center hidden" colspan="2">
                                    <input type="number" readonly name="sub_total_with_persent_<?php echo $gprddRow->id?>_1" id="sub_total_with_persent_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Sub Total With Persent" class="form-control" value="">
                                </td>
                            </tr>
                        </tbody>
                        <?php $counter++;?>
                        <script type="text/javascript">
                            saleTaxEnableAndDisable('<?php echo $gprddRow->id?>');
                            function optionDisableAndEnable(paramOne){
                                var optionValue = $('#option_'+paramOne+'').val();
                                var totalGPRDDRow = $('#totalGPRDDRow').val();
                                
                                if(optionValue == 2){
                                    if(totalGPRDDRow == 1){
                                        alert('Something Wrong! Atleast one item in Purchase Order...');
                                        return false;
                                    }else{
                                        var totalGPRDDRow = parseInt(totalGPRDDRow) - parseInt(1);
                                        $('.displayRowPRow_'+paramOne+' :input').prop("disabled", true);
                                        $('#option_'+paramOne+'').removeClass("optionPurchaseSkip");
                                        $('.displayRowPRow_'+paramOne+' :input').removeClass("requiredField");
                                        
                                    }
                                }else{
                                    var totalGPRDDRow = parseInt(totalGPRDDRow) + parseInt(1);
                                    $('.displayRowPRow_'+paramOne+' :input').prop("disabled", false);
                                    $('#option_'+paramOne+'').addClass("optionPurchaseSkip");
                                    $('.displayRowPRow_'+paramOne+' :input').addClass("requiredField");
                                }
                                $('#totalGPRDDRow').val(totalGPRDDRow);
                                $('#option_'+paramOne+'').prop("disabled", false);
                                var totalGPRDDRowTwo = $('#totalGPRDDRow').val();
                                if(totalGPRDDRowTwo == 1){
                                    $('.optionPurchaseSkip').prop("disabled", true); 
                                }else{
                                    $('.optionPurchaseSkip').prop("disabled", false);
                                }
                                var sum = 0;
                                $('.yesSubTotalAmount').each(function() {
                                    var isDisabled = $(this).prop('disabled');
                                    console.log(isDisabled);
                                    if(!isDisabled){
                                        sum += Number($(this).val());
                                    }
                                }); 
                                $('#total_amount').html(sum);
                            }
                        </script>
                        <tr>
                            <td colspan="15">&nbsp;</td>
                        </tr>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden">
		<label>Select Purchase Order Voucher Type</label>
		<select name="purchase_order_voucher_type" id="purchase_order_voucher_type" class="form-control" onchange="optionEnableAndDisablePurchaseOrderVoucherTypeAmountField()">
			<option value="1">Without Payment</option>
			<option value="2">Paid Amount Advanced</option>
		</select>
	</div>
</div>
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="row hidden">
            @if ($getPurchaseRequestDetail->purchase_request_type != 1)
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label for="">Expense</label>
                    <input type="checkbox" name="expense_added" id="expense_added" onclick="tougleRequiredFieldInAdditionalSection()">
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table id="expenseTable" class="table table-bordered d-none">
                        <thead>
                        <tr>
                            <th class="text-center">Expense Type<span class="rflabelsteric"><strong>*</strong></span></th>
                            <th class="text-center">Amount<span class="rflabelsteric"><strong>*</strong></span></th>
                            <th class="text-center"><button type="button" class="btn btn-sm btn-primary" id="BtnAddMore" onclick="AddMoreRowsExpense()">Add More</button></th>
                        </tr>
                        </thead>
                        <tbody id="AppendExpenseHtml">
                            <tr class="text-center" id="RemoveExpenseRow1">
                                <td class="hidden">1<input type="hidden" name="expenseArray[]" value="1" /></td>
                                <td>
                                    <select class="form-control additionalSection" name="expense_head_id_1" id="expense_head_id1">
                                        <option value="">Select Head</option>
                                        {!! $expenseAccountHtml !!}
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control text-right GetAmount additionalSection" id="expense_amount1" name="expense_amount_1" placeholder="Expense Amount" step="any" onkeyup="expensetotal()">
                                </td>
                                <td class="text-center"><input type="button" value="Remove" class="btn btn-xs btn-danger" onclick="removeExpenseRows(1)" /></td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <input type="hidden" name="main_counter" id="main_counter" value="1" />
                                <td><strong style="font-size: 20px">TOTAL</strong></td>
                                <td class="text-right"><strong style="font-size: 20px" id="TotalExpenseAmount"></strong></td>
                                <td style="background-color: darkgray"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-responsive">
                        <tbody>
                            <tr>
                                <td>
                                    <label>Tax Percent</label>
                                    <select onchange="calculateTaxAmount()" class="form-control" name="sales_tax_id" id="sales_tax_id">
                                        <option value="0,0">No Tax</option>
                                        @if (getSessionCompanyId() == 1)                                                                    
                                            <option value="2-1-1-1-13-1,18">GST 18% Payable</option>                                                                                                                                    
                                        @endif
                                        @if (getSessionCompanyId() == 2)                                                                    
                                            <option value="2-1-1-1-13-1,5">VAT 5% Payable</option>                                                                                                                                    
                                            <option value="2-1-1-1-13-2,13">13% Payable</option>                                                                                                                                    
                                            <option value="2-1-1-1-13-3,18">18% Payable</option>                                                                                                                                    
                                        @endif   
                                        <option value="2-1-1-1-13-2,17">GST 17% Payable</option>
                                        <option value="2-1-1-1-13-3,16">PRA Tax Output (16%)</option>
                                        <option value="2-1-1-1-13-4,10">SRB Output 10% Payable</option>            
                                        <option value="2-1-1-1-13-6,13">GST 13% payable</option>            
                                        <option value="2-1-1-1-13-7,10">GST 10% payable</option>
                                        <option value="2-1-1-1-13-8,manual">GST Manual</option>                
                                    </select>
                                    <input class="form-control" type="hidden" name="tax_type"  id="tax_type" />
                                    <input onKeyup="calculateTaxAmount()" class="form-control" style="width:120px;" type="hidden" name="manual_tax" value="0" id="manual_tax" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Total:</label>
                                    <span id="total_amount">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Tax Amount:</label>
                                    <span id="tax_amount">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Net Amount:</label>
                                    <span id="net_amount">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Purchase Order Discount</label>
                                    <input type="number" name="po_discount" id="po_discount" onchange="calculateGrandTotalAmount()" class="form-control" min="0" step="any">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Grand Total With Discount:</label>
                                    <span id="grand_amount">0</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        {{ Form::submit('Submit', ['class' => 'btn btn-success btn-add-success btnSubmit','id' => 'submit-btn-abc']) }}
        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("select").select2();
        $('#expense_added').on('change', function() {
            if ($(this).is(':checked')) {
                $('#expenseTable').removeClass('d-none');
            } else {
                $('#expenseTable').addClass('d-none');            
            }
        });
    });
    function tougleRequiredFieldInAdditionalSection(){
        if ($("#expense_added").is(":checked")){
            $(".additionalSection").addClass("requiredField");
            $(".additionalSection").attr("required","required");
        }else{
            $(".additionalSection").removeClass("requiredField");
            $(".additionalSection").removeAttr("required");
        }
    }
    var counter = 1;
    function AddMoreRowsExpense(){
        var oldCounter = $('#main_counter').val();
        var newCounter = parseInt(oldCounter) + parseInt(1);
        counter++;
        $('#AppendExpenseHtml').append('<tr class="text-center AutoNo" id="RemoveExpenseRow'+counter+'">' +
            '<td class="hidden">'+counter +'<input type="hidden" name="expenseArray[]" value="'+counter+'" /></td>'+
            '<td>'+'<select class="form-control additionalSection" name="expense_head_id_'+counter+'" id="expense_head_id'+counter+'">'+'{!! $expenseAccountHtml !!}'+'</select>'+'</td>'+
            '<td>'+'<input type="number" class="form-control text-right GetAmount additionalSection" id="expense_amount'+counter+'" name="expense_amount_'+counter+'" placeholder="Expense Amount" step="any" onkeyup="expensetotal()">'+'</td>'+
            '<td class="text-center"><input type="button" value="Remove" class="btn btn-xs btn-danger" onclick="removeExpenseRows('+counter+')" /></td>'+
            '</tr>'
            );       
            $("select").select2();
            $('#main_counter').val(newCounter);
    }
    function removeExpenseRows(id){
        var main_counter = $('#main_counter').val();
        if(main_counter == 1){
            alert('Something went wrong! Please atleast One Row in Purchase Order Expense.....');
            return false;
        }else{
            var elemThree = document.getElementById('RemoveExpenseRow'+id+'');
            elemThree.parentNode.removeChild(elemThree);
            
            var new_main_counter = parseInt(main_counter) - parseInt(1);
            $('#main_counter').val(new_main_counter);
        }
    }
    function checkRemainingPurchaseOrderQty(paramOne,paramTwo){
        var remainingPOQty = $('#purchase_request_qty_'+paramOne+'_'+paramTwo+'').val();
        var newPOQty = $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val();
        if(parseInt(remainingPOQty) < newPOQty){
           alert('Something went wrong! Your Purchase Order Qty is Greater Than Remaining Purchase Order Qty....');
           $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val(remainingPOQty);
        }
        makesubtotalamount(paramOne,paramTwo);

        $('.unit_price').each(function() {
            $(this).trigger('keyup');
        });

    }
    function expensetotal(){
        var total = 0;
        $('.GetAmount').each(function(index, element,) {
        var value = $(element).val();
        total += parseInt(value);
        });
        $('#TotalExpenseAmount').html(total);
    }
	function optionEnableAndDisbaleBankDetail(){
		var pvVoucherType = $('#pv_voucher_type').val();
		if(pvVoucherType == '1'){
			$('#pv_cheque_no').removeClass('requiredField');
			$('#pv_cheque_date').removeClass('requiredField');
			
			$('#pv_cheque_no').prop("disabled", true);
			$('#pv_cheque_date').prop("disabled", true);
			
			$("#pvPaymentVoucherTaxHeadOption :input").attr("disabled", true);
			$("#pvPaymentVoucherTaxHeadOption select").removeClass("requiredField");
			$("#pvPaymentVoucherTaxHeadOption input").removeClass("requiredField");
			
		}else{
			$('#pv_cheque_no').addClass('requiredField');
			$('#pv_cheque_date').addClass('requiredField');
			
			$('#pv_cheque_no').prop("disabled", false);
			$('#pv_cheque_date').prop("disabled", false);
			
			$("#pvPaymentVoucherTaxHeadOption :input").attr("disabled", false);
			$("#pvPaymentVoucherTaxHeadOption select").addClass("requiredField");
			$("#pvPaymentVoucherTaxHeadOption input").addClass("requiredField");
			
			
		}
	}
	function optionEnableAndDisablePurchaseOrderVoucherTypeAmountField(){
		var purchaseOrderVoucherType = $('#purchase_order_voucher_type').val();
		if(purchaseOrderVoucherType == 1){
			$("#pvPaymentVoucherOption :input").attr("disabled", true);
			$("#pvPaymentVoucherOption select").removeClass("requiredField");
			$("#pvPaymentVoucherOption input").removeClass("requiredField");
		}else{
			$("#pvPaymentVoucherOption :input").attr("disabled", false);
			$("#pvPaymentVoucherOption select").addClass("requiredField");
			$("#pvPaymentVoucherOption input").addClass("requiredField");
			optionEnableAndDisbaleBankDetail();
		}
	}
	optionEnableAndDisablePurchaseOrderVoucherTypeAmountField();
	optionEnableAndDisbaleBankDetail();
    function makesubtotalamount(param1,param2){
		var unit = $('#unit_'+param1+'').val();
        var purchase_order_qty = $('#purchase_order_qty_'+param1+'_'+param2+'').val();
        var unit_price = $('#unit_price_'+param1+'_'+param2+'').val();

        if(unit !== '' && purchase_order_qty !== '' && unit_price !== ''){
            // alert('Done');
            var subTotal = purchase_order_qty * unit_price;
            var sub_total_with_persent = parseInt(subTotal) * parseInt(unit) / parseInt('100');
            $('#sub_total_'+param1+'_'+param2+'').val(subTotal);
            $('#sub_total_with_persent_'+param1+'_'+param2+'').val(sub_total_with_persent);
			updateOverAllDebitAmount();
            //overalltotalsection();
        }else{
            // alert('Empty Value');
            $('#sub_total_'+param1+'_'+param2+'').val('');
            $('#sub_total_with_persent_'+param1+'_'+param2+'').val('');
			updateOverAllDebitAmount();
            //overalltotalsection();
        }        
    }

    $(".btn-add-success").click(function(e){
		var seletedPurchaseRequestRow = new Array();
		var val;
		$("input[name='seletedPurchaseRequestRow[]']").each(function(){
			seletedPurchaseRequestRow.push($(this).val());
		});
		var _token = $("input[name='_token']").val();
		for (val of seletedPurchaseRequestRow) {
			jqueryValidationCustom();
            console.log(validate);
			if(validate == 0){
				$(".btnSubmit").val('Sending, please wait...');
				$('.mainOption').prop("disabled", false);
				setTimeout(function(){
					$(".btnSubmit").prop("type", "button");
				},50);
			}else{
				return false;
			}
		}

	});
	$(document).ready(function() {
        var sum = 0; 
            $('.yesSubTotalAmount').each(function() {
                var isDisabled = $(this).prop('disabled');
                console.log(isDisabled);
                if(!isDisabled){
                    sum += Number($(this).val());
                }
            }); 
            $('#total_amount').html(sum);
            calculateTaxAmount();
            //console.log(sum);
        var startAccountYear = $("#startAccountYearDMYFormat").val();
        var endAccountYear = $("#endAccountYearDMYFormat").val();
        $(".fromDateDatePicker").datepicker({
            showAnim: "slideDown",
            dateFormat: "dd-mm-yy",
            maxDate: endAccountYear,
            minDate: startAccountYear
        });
    });
    touglePurchaseOrderPaymentRate();
</script>

