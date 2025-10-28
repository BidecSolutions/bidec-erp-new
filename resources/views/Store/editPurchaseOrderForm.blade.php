<?php
    $accType = Auth::user()->acc_type;
    $currentDate = date('Y-m-d');
    $m;
?>
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    <div class="">
	    <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <span class="subHeadingLabelClass">Edit Purchase Order {{ $purchaseOrderData->purchase_order_no ?? '' }}</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="row">
                            <?php echo Form::open(array('url' => 'stad/updatePurchaseOrderDetail?m='.$m.'','id'=>'addPurchaseOrderDetail'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo Input::get('pageType')?>">
                            <input type="hidden" name="parentCode" value="<?php echo Input::get('parentCode')?>">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label class="sf-label">Purchae Request Detail</label>
                                                <span class="rflabelsteric"><strong>*</strong></span>
                                                <select disabled     class="form-control requiredField" required name="purchase_request_no" id="purchase_request_no" onchange="loadPurchaseOrderDetailByPRNo()">
                                                    <option value=""><?php echo $purchaseOrderData->purchase_request_no.'<*>'.$purchaseOrderData->purchase_request_date?>"><?php echo 'PR No => &nbsp;&nbsp;&nbsp;'.$purchaseOrderData->purchase_request_no.'&nbsp;, PR Date => &nbsp;&nbsp;&nbsp;'.CommonFacades::changeDateFormat($purchaseOrderData->purchase_request_date).' , Created By => &nbsp;&nbsp;&nbsp;'.$purchaseOrderData->username.' , Location => &nbsp;&nbsp;&nbsp;'.$purchaseOrderData->location->location_name .' , Department / Sub Department => &nbsp;&nbsp;&nbsp;'.$purchaseOrderData->department->department_name.' / '.$purchaseOrderData->subDepartment->sub_department_name?></option>                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="lineHeight">&nbsp;</div>
                                        {{-- <div class="loadPurchaseOrderDetailSection"></div> --}}
                                        <div>
                                            <?php
	use App\Models\Account;
	$m;
	
	$prNo = $purchaseOrderData->purchase_request_no;
	$prDate = $purchaseOrderData->purchase_request_date;
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
    function touglePurchaseOrderPaymentRate(){
        var paymentTypeTwo = $('#paymentTypeTwo').val();
        const paymentTypeSplit = paymentTypeTwo.split('<*>');
        var paymentType = $('#paymentType').val(paymentTypeSplit[0]);
        var conversionRateType = paymentTypeSplit[1];
        if(conversionRateType == 2){
            $('#payment_type_rate').removeAttr('readonly');
            $('#payment_type_rate').val(paymentTypeSplit[2]);   
        }else{
            $('#payment_type_rate').val(paymentTypeSplit[2]);
            $('#payment_type_rate').attr('readonly','readonly');
        }
    }
    touglePurchaseOrderPaymentRate();
</script>
<div>
    <input type="hidden" name="poNo" id="poNo" value="<?php echo $purchaseOrderData->purchase_order_no ?>" readonly/>
    <input type="hidden" name="prNo" id="prNo" value="<?php echo $prNo ?>" readonly/>
    <input type="hidden" name="prDate" id="prDate" value="<?php echo $prDate ?>" readonly/>
    <input type="hidden" name="subDepartmentId" id="subDepartmentId" value="<?php echo $purchaseOrderData->subDepartment->id ?>" readonly/>
    <input type="hidden" name="locationId" id="locationId" value="<?php echo $purchaseOrderData->location->id ?>" readonly/>
    <input type="hidden" name="projectId" id="projectId" value="<?php echo $purchaseOrderData->project->id ?>" readonly/>
    <input type="hidden" name="departmentId" id="departmentId" value="<?php echo $purchaseOrderData->department->id?>" readonly />
    <input type="hidden" name="initialEmailAddress" id="initialEmailAddress" value="<?php echo CommonFacades::voucherInitialEmailAddress($purchaseOrderData->user_id)?>" />
    <input type="hidden" id="tax_percent" name="tax_percent" value="">
    <input type="hidden" id="sales_tax_acc_code" name="sales_tax_acc_code" value="">
</div>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Purchase Request No</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control" readonly name="purchase_request_no" id="purchase_request_no" value="<?php echo $purchaseOrderData->purchase_request_no ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Location</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="location_name" id="location_name" class="form-control" readonly value="<?php echo $purchaseOrderData->location->location_name;?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">P.O Date.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField fromDateDatePicker" name="po_date" id="po_date" readonly value="<?php echo $purchaseOrderData->purchase_order_date ?>" />
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Department / Sub Department</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="sub_department_name" id="sub_department_name" class="form-control" readonly value="<?php echo $purchaseOrderData->department->department_name.' / '.$purchaseOrderData->subDepartment->sub_department_name?>" >
    </div>
    
</div>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Project</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" name="project_name" id="project_name" class="form-control" readonly value="<?php echo $purchaseOrderData->project->project_name;?>" >
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Delivery place</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField" name="delivery_place" id="delivery_place" placeholder="Delivery Place" value="<?php echo $purchaseOrderData->delivery_place;?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Invoice/Quotation No.</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="text" class="form-control requiredField" name="qoutation_no" id="qoutation_no" placeholder="Invoice/Quotation No." value="<?php echo $purchaseOrderData->qoutation_no;?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">Invoice Date</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <input type="date" class="form-control requiredField" name="qoutation_date" id="qoutation_date" value="<?php echo $purchaseOrderData->qoutation_date;?>" />
    </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <label class="sf-label">Remarks</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea name="main_description" id="main_description" rows="2" cols="50" style="resize:none;" class="form-control"><?php echo $purchaseOrderData->description;?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <label class="sf-label">TOS Advance %</label>        
        <select class="form-control" name="termCondition" id="termCondition">
            <option value="0" <?php if($purchaseOrderData->term_and_condition == 0){echo 'selected';}?>>0</option>
            <option value="10" <?php if($purchaseOrderData->term_and_condition == 10){echo 'selected';}?>>10</option>
            <option value="20" <?php if($purchaseOrderData->term_and_condition == 20){echo 'selected';}?>>20</option>
            <option value="30" <?php if($purchaseOrderData->term_and_condition == 30){echo 'selected';}?>>30</option>
            <option value="40" <?php if($purchaseOrderData->term_and_condition == 40){echo 'selected';}?>>40</option>
            <option value="50" <?php if($purchaseOrderData->term_and_condition == 50){echo 'selected';}?>>50</option>
            <option value="60" <?php if($purchaseOrderData->term_and_condition == 60){echo 'selected';}?>>60</option>
            <option value="70" <?php if($purchaseOrderData->term_and_condition == 70){echo 'selected';}?>>70</option>
            <option value="80" <?php if($purchaseOrderData->term_and_condition == 80){echo 'selected';}?>>80</option>
            <option value="90" <?php if($purchaseOrderData->term_and_condition == 90){echo 'selected';}?>>90</option>
            <option value="100" <?php if($purchaseOrderData->term_and_condition == 100){echo 'selected';}?>>100</option>
        </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        <?php /*?><label class="sf-label">Payment Type</label>
        <select class="form-control" name="paymentType" id="paymentType" onchange="touglePurchaseOrderPaymentRate()">
            <option value="1" <?php if($purchaseOrderData->paymentType == 1){echo 'selected';}?>>RS</option>
            <option value="2" <?php if($purchaseOrderData->paymentType == 2){echo 'selected';}?>>US Dolar</option>
        </select><?php */?>
        <label class="sf-label">Payment Type</label>
        <select class="form-control" name="paymentTypeTwo" id="paymentTypeTwo" onchange="touglePurchaseOrderPaymentRate()">
            @foreach($paymentTypeList as $ptlRow)
                <option value="{{$ptlRow->id}}<*>{{$ptlRow->conversion_rate_type}}<*>{{$ptlRow->conversion_rate}}" <?php if($purchaseOrderData->paymentType == $ptlRow->id){echo 'selected';}?>>{{$ptlRow->payment_type_name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="paymentType" id="paymentType" value="" />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <label class="sf-label">Payment Type Rate</label>
        <input type="number" readonly name="payment_type_rate" id="payment_type_rate" step="0.001" value="<?php echo $purchaseOrderData->payment_type_rate;?>" class="form-control" />
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <label class="sf-label">Note</label>
        <span class="rflabelsteric"><strong>*</strong></span>
        <textarea name="po_note" id="po_note" rows="2" cols="50" style="resize:none;" class="form-control"><?php echo $purchaseOrderData->po_note;?></textarea>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-bordered sf-table-list">
                <input type="hidden" name="totalGPRDDRow" id="totalGPRDDRow" value="<?php echo count($purchaseOrderData->purchaseOrderData)?>" />
                <?php
                    $counter = 1;
                    foreach($purchaseOrderData->purchaseOrderData as $gprddRow){
                        $totalGPRDDRow = count($purchaseOrderData->purchaseOrderData);
                        $disableOption = '';    
                        if($totalGPRDDRow == 1){
                            $disableOption = 'disabled';
                        }
                ?>
                        
                        <thead>
                            <tr>
                                <th class="text-center hidden"><label><input type="checkbox" name="sale_tax_head_checkbox_<?php echo $gprddRow->id?>" id="sale_tax_head_checkbox_<?php echo $gprddRow->id?>" value="2"> Sales Tax Head</label></th>
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
                                    <input type="hidden" name="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $gprddRow->id;?>" readonly id="priviousPurchaseOrderQtyThisPurchaseRequest_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->priviousPurchaseOrderQty;?>" class="form-control" />
                                    <input type="hidden" name="purchaseOrderDataId_<?php echo $gprddRow->id;?>" readonly id="purchaseOrderDataId_<?php echo $gprddRow->id;?>" value="<?php echo $gprddRow->id;?>" class="form-control" />
                                    <input type="number" class="form-control requiredField" name="delivery_days_<?php echo $gprddRow->id?>" id="delivery_days_<?php echo $gprddRow->id?>" value="30" />
                                    <input type="number" class="form-control requiredField" placeholder="Payment Terms Days" name="payment_terms_<?php echo $gprddRow->id?>" id="payment_terms_<?php echo $gprddRow->id?>" value="45" />
                                    <?php
                                        $remainingPurchaseOrderQty = $gprddRow->qty - $gprddRow->priviousPurchaseOrderQty;
                                    ?>
                                    
    
                                    <select name="sale_tax_head_<?php echo $gprddRow->id?>" id="sale_tax_head_<?php echo $gprddRow->id?>" class="form-control">
                                        <?php echo SelectListFacades::getChartOfAccountList($m,1,0,0);?>
                                    </select>
                                </td>
                                <td class="text-center hidden">
                                    <input type="number" name="unit_<?php echo $gprddRow->id?>" id="unit_<?php echo $gprddRow->id?>" step="0.00001" placeholder="Type Unit Percent" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $gprddRow->id?>,0)" value="0">
                                </td>
                                <td>
                                    <select name="option_<?php echo $gprddRow->id;?>" id="option_<?php echo $gprddRow->id;?>" class="form-control optionPurchaseSkip" onchange="optionDisableAndEnable('<?php echo $gprddRow->id?>')" <?php echo $disableOption;?>>
                                        <option value="1">Purchse</option>
                                        <option value="2">Skip</option>
                                    </select>
                                </td>
                                <td>
                                    <?php echo $gprddRow->subItem->item_code;?> / <?php echo $gprddRow->subItem->sub_ic;?>
                                    <input type="hidden" name="subItemId_<?php echo $gprddRow->id;?>" readonly id="subItemId_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->sub_item_id;?>" class="form-control" />
                                </td>
                                <td>
                                    
                                    <select class="form-control requiredField" name="supplier_id_<?php echo $gprddRow->id;?>" id="supplier_id_<?php echo $gprddRow->id;?>">                                        
                                        <?php echo SelectListFacades::getSupplierList($m,1,$gprddRow->supplier_id);?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control requiredField" name="qoutation_no_<?php echo $gprddRow->id?>" id="qoutation_no_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->qoutation_no;?>" placeholder="Qoutation No" />
                                </td>
                                <td colspan="2">
                                    <input type="date" class="form-control requiredField" name="qoutation_date_<?php echo $gprddRow->id?>" id="qoutation_date_<?php echo $gprddRow->id?>" value="<?php echo $gprddRow->qoutation_date ?>" placeholder="Qoutation No" />
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
                                    <input type="hidden" name="location_id_<?php echo $gprddRow->id?>_1" id="location_id_<?php echo $gprddRow->id?>_1" value="<?php echo $gprddRow->location_id?>" />
                                    <?php echo $purchaseOrderData->location->location_name;?>
                                </td>
                                <td class="text-center">
                                    <?php echo CommonFacades::stockLocationWiseSum($gprddRow->category_id,$gprddRow->subItem->sub_item_id,$purchaseOrderData->location->id);?>
                                </td>
                                <td class="text-center" style="display: flex; justify-content: space-around;">
                                    <input type="number" style="width: 45%;" name="purchase_order_qty_<?php echo $gprddRow->id?>_1" id="purchase_order_qty_<?php echo $gprddRow->id?>_1" step="0.01" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $gprddRow->id?>,1),validatePurchaseOrderQtyAgainstPurchaseRequest(<?php echo $gprddRow->id?>,1,<?php echo $remainingPurchaseOrderQty+$gprddRow->purchase_order_qty;?>)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="<?php echo $gprddRow->purchase_order_qty ?>">
                                    <input type="hidden" name="remaining_purchase_order_qty_<?php echo $gprddRow->id?>" id="remaining_purchase_order_qty_<?php echo $gprddRow->id?>" value="<?php echo $remainingPurchaseOrderQty;?>" readonly />
                                    <input type="hidden" name="purchase_request_qty_<?php echo $gprddRow->id?>_1" id="purchase_request_qty_<?php echo $gprddRow->id?>_1" step="0.001" placeholder="Type Purchase Order Qty" class="form-control requiredField" onchange="makesubtotalamount(<?php echo $gprddRow->id?>,1)" max="<?php //echo $remainingPurchaseOrderQty;?>" value="<?php echo $remainingPurchaseOrderQty+$gprddRow->purchase_order_qty;?>">
                                    <input disabled class="form-control" style="width: 45%;" type="text" value="{{ CommonFacades::checkPriviousReceiveQtyPurchaseRequestWise($gprddRow->category_id, $gprddRow->sub_item_id, $gprddRow->purchase_request_no) }}">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="unit_price_<?php echo $gprddRow->id?>_1" id="unit_price_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Unit Price" class="form-control requiredField unit_price" onchange="makesubtotalamount(<?php echo $gprddRow->id?>,1)" value="<?php echo $gprddRow->unit_price/$purchaseOrderData->payment_type_rate?>">
                                </td>
                                <td class="text-center">

                                    <input type="number" readonly name="sub_total_<?php echo $gprddRow->id?>_1" id="sub_total_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Sub Total" class="form-control requiredField yesSubTotalAmount" value="<?php echo $gprddRow->sub_total/$purchaseOrderData->payment_type_rate ?>">

                                </td>
                                <td class="text-center hidden" colspan="2">
                                    <input type="number" readonly name="sub_total_with_persent_<?php echo $gprddRow->id?>_1" id="sub_total_with_persent_<?php echo $gprddRow->id?>_1" step="0.00001" placeholder="Type Sub Total With Persent" class="form-control requiredField" value="<?php echo $gprddRow->sub_total_with_persent ?>">
                                </td>
                            </tr>
                        </tbody>
                        <?php $counter++;?>
                        
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
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12"></div>
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
                                            <option {{ $purchaseOrderData->custom_tax_percent == '18' ? 'selected' : '' }} value="2-1-1-1-13-1,18">GST 18% Payable</option>                                                                                                                                    
                                        @endif
                                        @if (getSessionCompanyId() == 2)                                                                    
                                            <option {{ $purchaseOrderData->custom_tax_percent == '5' ? 'selected' : '' }} value="2-1-1-1-13-1,5">VAT 5% Payable</option>                                                                                                                                    
                                            <option {{ $purchaseOrderData->custom_tax_percent == '13' ? 'selected' : '' }} value="2-1-1-1-13-2,13">13% Payable</option>                                                                                                                                    
                                            <option {{ $purchaseOrderData->custom_tax_percent == '18' ? 'selected' : '' }} value="2-1-1-1-13-3,18">18% Payable</option>                                                                                                                                    
                                        @endif   
                                        <option {{ $purchaseOrderData->custom_tax_percent == '17' ? 'selected' : '' }} value="2-1-1-1-13-2,17">GST 17% Payable</option>
                                        <option {{ $purchaseOrderData->custom_tax_percent == '16' ? 'selected' : '' }} value="2-1-1-1-13-3,16">PRA Tax Output (16%)</option>
                                        <option {{ $purchaseOrderData->custom_tax_percent == '10' ? 'selected' : '' }} value="2-1-1-1-13-4,10">SRB Output 10% Payable</option>            
                                        <option {{ $purchaseOrderData->custom_tax_percent == '13' ? 'selected' : '' }} value="2-1-1-1-13-6,13">GST 13% payable</option>            
                                        <option {{ $purchaseOrderData->custom_tax_percent == '10' ? 'selected' : '' }} value="2-1-1-1-13-7,10">GST 10% payable</option>
                                        <option {{ $purchaseOrderData->tax_type == 'manual' ? 'selected' : '' }} value="2-1-1-1-13-8,manual">GST Manual</option>                
                                    </select>
                                    <input class="form-control" type="hidden" name="tax_type"  id="tax_type" value="{{$purchaseOrderData->tax_type}}" />
                                    <input onKeyup="calculateTaxAmount()" class="form-control" style="width:120px;" type="@if($purchaseOrderData->tax_type != 'manual') hidden @endif" name="manual_tax" value="{{ $purchaseOrderData->custom_tax_percent }}" id="manual_tax" />
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
                                    <input type="number" name="po_discount" id="po_discount" onchange="calculateGrandTotalAmount()" value="{{$purchaseOrderData->po_discount}}" class="form-control" min="0" step="any">
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
        {{ Form::submit('Submit', ['class' => 'btn btn-success','id' => '']) }}
        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("select").select2();
    });
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
            //alert('Done');
            var subTotal = purchase_order_qty * unit_price;
            var sub_total_with_persent = parseInt(subTotal) * parseInt(unit) / parseInt('100');
            $('#sub_total_'+param1+'_'+param2+'').val(subTotal);
            $('#sub_total_with_persent_'+param1+'_'+param2+'').val(sub_total_with_persent);
			updateOverAllDebitAmount();
            //overalltotalsection();
        }else{
            //alert('Empty Value');
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
        var startAccountYear = $("#startAccountYearDMYFormat").val();
        var endAccountYear = $("#endAccountYearDMYFormat").val();
        $(".fromDateDatePicker").datepicker({
            showAnim: "slideDown",
            dateFormat: "dd-mm-yy",
            maxDate: endAccountYear,
            minDate: startAccountYear
        });
    });
</script>


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
        $(document).on('change', '.unit_price', function(){
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
        
        });
        // function calculateTaxAmount(){
            // var totalAmount = $('#total_amount').text();
            // var makePercentage = (18*totalAmount) / 100;
            // $('#tax_amount').html(makePercentage);
            // $('#grand_amount').html(parseInt(totalAmount)+parseInt(makePercentage));
        // }
        var sum = 0; 
        $('.yesSubTotalAmount').each(function() {                
            sum += Number($(this).val());
        }); 
        $('#total_amount').html(sum);
        calculateTaxAmount();
        $(function () {
            $("select").select2();
        });
        function loadPurchaseOrderDetailByPRNo(){
            var prNo = $('#purchase_request_no').val();
            var m = '<?php echo $m?>';
            var pageType = '<?php echo Input::get('pageType')?>';
            var parentCode = '<?php echo Input::get('parentCode')?>';
            if(prNo == ''){
                alert('Please Select Purchase Request No');
                $('.loadPurchaseOrderDetailSection').html('');
            }else{
                $('.loadPurchaseOrderDetailSection').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                $.ajax({
                    url: '<?php echo url('/')?>/stmfal/makeFormPurchaseOrderDetailByPRNo',
                    type: "GET",
                    data: { prNo:prNo,pageType:pageType,parentCode:parentCode},
                    success:function(data) {
                        $('.loadPurchaseOrderDetailSection').html(data);
                        //disableInputFormDateAccountYear();
                        $('#submit-btn-abc').prop('disabled', false);
                    }
                });
            }
        }
        function saleTaxEnableAndDisable(paramOne){
        //alert(paramOne);
        if($('input[name="sale_tax_head_checkbox_'+paramOne+'"]').prop("checked") == true){
            //alert("Checkbox is checked.");
            $('#sale_tax_head_checkbox_'+paramOne+'').val('1');
            $('#unit_'+paramOne+'').prop('disabled', false);
            $('#sale_tax_head_'+paramOne+'').prop('disabled', false);
            
            //document.getElementById("name").disabled = true;
        }
        else if($('input[name="sale_tax_head_checkbox_'+paramOne+'"]').prop("checked") == false){
            //alert("Checkbox is unchecked.");
            $('#sale_tax_head_checkbox_'+paramOne+'').val('2');
            $('#unit_'+paramOne+'').val('0');
            $('#sub_total_with_persent_'+paramOne+'').val('0');
            $('#unit_'+paramOne+'').prop('disabled', true);
            $('#sale_tax_head_'+paramOne+'').prop('disabled', true);
        }
     }
     function optionDisableAndEnable(paramOne){
            var optionValue = $('#option_'+paramOne+'').val();
            var totalGPRDDRow = $('#totalGPRDDRow').val();
            
            if(optionValue == 2){
                if(totalGPRDDRow == 1){
                    alert('Something Wrong! Atleast one item in Purchase Order...');
                    return false;
                }else{
                    var totalGPRDDRow = parseInt(totalGPRDDRow) - parseInt(1);
                    // $('.displayRowPRow_'+paramOne+' :input').prop("disabled", true);
                    $('.displayRowPRow_'+paramOne+' :input').prop("disabled", false);
                    // console.log(paramOne);
                    $('#supplier_id_'+paramOne).prop("disabled", true);
                    $('#sub_total_'+paramOne+'_1').prop("disabled", true);
                    // $('#option_'+paramOne+'').removeClass("optionPurchaseSkip");
                    $('#option_'+paramOne+'').addClass("optionPurchaseSkip");
                    // $('.displayRowPRow_'+paramOne+' :input').removeClass("requiredField");
                    $('.displayRowPRow_'+paramOne+' :input').addClass("requiredField");
                    
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
        function validatePurchaseOrderQtyAgainstPurchaseRequest(paramOne,paramTwo,paramThree){
            var purchaseOrderQty = $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val();
            var purchaseRequestQty = paramThree;
            if(parseInt(purchaseOrderQty) > parseInt(purchaseRequestQty)){
            alert('Something Went Wrong! Your Purchase Order Qty is Greater than Remaining Purchase Request Qty...');
                $('#purchase_order_qty_'+paramOne+'_'+paramTwo+'').val(purchaseRequestQty);
                return false;
            }
        }
        function calculateTaxAmount(){            
            var totalAmount = $('#total_amount').text();
            var tax_input = $('#sales_tax_id').val();
            var tax_input_array = tax_input.split(',');
            var tax_acc_code = tax_input_array[0];
            var tax_percent = tax_input_array[1];
            if (tax_percent == 'manual' && tax_acc_code=='2-1-1-1-13-8') {
                $('#manual_tax').attr({'type': 'text'});
                $('#tax_type').val('manual');
                tax_percent=$('#manual_tax').val();
            } else {
                $('#manual_tax').val(0);
                $('#tax_type').val('');
                $('#manual_tax').attr({'type': 'hidden'});
            }
            //console.log(tax_percent, tax_acc_code);
            var makePercentage = (tax_percent*totalAmount) / 100;
            var tax_percent_amount = makePercentage;
            //alert(makePercentage);
            $('#tax_amount').html(makePercentage);        
            $('#tax_percent').val(tax_percent);
            $('#sales_tax_acc_code').val(tax_acc_code);
            $('#net_amount').html(parseFloat(totalAmount)+parseFloat(makePercentage));
            calculateGrandTotalAmount();
            //$('#grand_amount').html(parseInt(totalAmount)+parseInt(makePercentage));
        }
        function calculateGrandTotalAmount(){
            var netAmount = $('#net_amount').text();
            var poDiscountAmount = $('#po_discount').val();
            
            var grandTotal = Number(netAmount) - Number(poDiscountAmount);
            $('#grand_amount').html(grandTotal);
        }
    </script>
    