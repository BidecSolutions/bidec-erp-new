<?php
    $accType = Auth::user()->acc_type;
    $m = getSessionCompanyId();
    $current_date = date('Y-m-d');
?>

@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="{{ URL::asset('assets/datePicker/jquery-ui.js') }}"></script>
    <div class="well_N">
	    <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseOrderVoucherList','','1');?>
                    <?php echo CommonFacades::displayExportButton('purchaseOrderVoucherList','','1')?>
                    <?php echo CommonFacades::createFormLinkForList($m,'add',Input::get('parentCode'),'store/createPurchaseOrderForm')?>
                </div>
                <div class="lineHeight">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <span class="subHeadingLabelClass">View Purchase Order Voucher List</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <input type="hidden" name="functionName" id="functionName" value="stdc/filterPurchaseOrderVoucherList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseOrderVoucherList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                        <input type="hidden" name="filterType" id="filterType" value="PurchaseOrderList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageTypeTwo" id="pageTypeTwo" value="<?php echo Input::get('pageType');?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="parentCode" id="parentCode" value="<?php echo Input::get('parentCode');?>" readonly="readonly" class="form-control" />
						<div class="row">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
								<div class="panel">
									<div class="panel-body">
										<div class="row">
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<label>Select Location</label>
												<select name="filterLocationId" id="filterLocationId" class="form-control">
                                                    <?php echo SelectListFacades::getLocationList($m,0,0);?>
												</select>
											</div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label>Department / Sub Department</label>
                                                <select name="filterSubDepartmentId" id="filterSubDepartmentId" class="form-control">
                                                    <?php echo SelectListFacades::getSubDepartmentList($m,0,0);?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label>Project</label>
                                                <select name="filterProjectId" id="filterProjectId" class="form-control">
                                                    <?php echo SelectListFacades::getProjectList($m,0,0);?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label>Supplier</label>
                                                <select name="filterSupplierId" id="filterSupplierId" class="form-control">
                                                    <?php echo SelectListFacades::getSupplierList($m,0,0);?>
                                                </select>
                                            </div>
										</div>
										<div class="row">
                                            <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',5,2);?>
										</div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label>Select Quotation Status</label>
												<select name="selectQuotationStatus" id="selectQuotationStatus" class="form-control">
													<option value="0">All</option>
                                                    <option value="1">Quotation Not Generated</option>
                                                    <option value="2">Quotation Generated</option>
												</select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<label>Select Voucher Status</label>
												<select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
													<option value="1">Pending Vouchers</option>
													<option value="2">Approved Vouchers</option>
													<option value="3">Deleted Vouchers</option>
													<option value="4">Completed Vouchers</option>
													<option value="0">All Vouchers</option>
												</select>
											</div>
											<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
												<input type="button" value="View" class="btn btn-sm btn-success" onclick="viewRangeWiseDataFilter();" style="margin-top: 25px;" />
											</div>
                                        </div>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
													<label>Select Filter Column Type</label>
													<select name="selectAFilterType" id="selectAFilterType" class="form-control" onchange="optionEnableAndDisableAdvanceFilterField()">
														<option value="purchase_order_no">P.O. No</option>
														<option value="purchase_order_date">P.O. Date</option>
														<option value="purchase_request_no">P.R. No</option>
														<option value="purchase_request_date">P.R. Date</option>
														<option value="supplier_id">Supplier Name</option>
													</select>
												</div>
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 selectAFilterValueDiv" id="selectAFilterValueDiv"></div>
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
													<input type="button" value="Enter" class="btn btn-sm btn-success" onclick="viewAdvanceSearchFilterAgainstPurchaseOrder();" style="margin-top: 25px;" />
												</div>
											</div>
                                    </div>
                                </div>
							</div>
						</div>
                        
                        <div class="lineHeight">&nbsp;</div>
                        <div id="printPurchaseOrderVoucherList">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="table-responsive scrollme">
                                                        <table class="table table-bordered customTable" id="purchaseOrderVoucherList">
                                                            <thead>
																<tr>
																	<th class="text-center">S.No</th>
																	<th class="text-center">P.O. No. / Date</th>
																	<th class="text-center">P.R. No. / Date</th>
																	<th class="text-center">Location Name</th>
																	<th class="text-center">Supplier Name</th>
                                                                    <th class="text-center">Department</th>
                                                                    <th class="text-center">Sub Department</th>
                                                                    <th class="text-center">Project</th>
																	<th class="text-center">Remarks</th>
																	<th class="text-center">Created Detail</th>
																	<th class="text-center">P.O Status</th>
                                                                    <th class="text-center">Additional Remarks</th>
                                                                    <th class="text-center">Approved Detail</th>
                                                                    <th class="text-center">All P.O. Item Status</th>
																	<th class="text-center hidden-print">Action</th>
																</tr>
                                                            </thead>
                                                            <tbody id="filterPurchaseOrderVoucherList"></tbody>
                                                        </table>
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
            </div>
        </div>
    </div>
    
@endsection
@section('custom-js-end')
<script>
        $(function () {
            $("select").select2();
            $(document).on('keyup', '#selectAFilterValue', function(e){
                if(e.keyCode == 13)
                {                    
                    viewAdvanceSearchFilterAgainstPurchaseOrder();
                    
                }
            });
        });
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
		
		
		function viewAdvanceSearchFilterAgainstPurchaseOrder(){
			var selectAFilterValue = $('#selectAFilterValue').val();
			var selectAFilterType  = $('#selectAFilterType').val();
			if(selectAFilterValue == ''){
				alert('Something Wrong! Please Select or Fill Input Field and Filter Data.');
				return false;
			}else{
				data = selectAFilterType+'<*>'+selectAFilterValue;
				showDetailModelOneParamerter('stdc/viewAdvanceSearchFilterAgainstPurchaseOrder',data,'View Advance Search Filter Data Against Purchase Order Detail');
			}
		}
		var startAccountYear = $('#startAccountYearDMYFormat').val();
        var endAccountYear = $('#endAccountYearDMYFormat').val();
        $( ".datePicker" ).datepicker({
            showAnim: "slideDown",
            dateFormat: "dd-mm-yy"
        });
        function updateRecordLimitPurchaseOrderList(paramOne,paramTwo){
			$('#startRecordNo').val(paramOne);
			$('#endRecordNo').val(paramTwo);
			viewRangeWiseDataFilter();
		}
        function optionEnableAndDisableAdvanceFilterField(){
            console.log('optionEnableAndDisableAdvanceFilterField');
				$('#selectAFilterValueDiv').html();
				var selectAFilterType = $('#selectAFilterType').val();
				if(selectAFilterType == 'purchase_order_no'){
					var field = '<label>P.O. No</label><input type="text" name="selectAFilterValue" id="selectAFilterValue" value="" placeholder="P.O. No" class="form-control" />';
				}else if(selectAFilterType == 'purchase_order_date'){
					var field = '<label>P.O. Date</label><input type="date" name="selectAFilterValue" id="selectAFilterValue" value="<?php echo date('Y-m-d')?>" placeholder="P.O. Date" class="form-control" />';
				}else if(selectAFilterType == 'purchase_request_no'){
					var field = '<label>P.R. No</label><input type="text" name="selectAFilterValue" id="selectAFilterValue" value="" placeholder="P.R. No" class="form-control" />';
				}else if(selectAFilterType == 'purchase_request_date'){
					var field = '<label>P.R. Date</label><input type="date" name="selectAFilterValue" id="selectAFilterValue" value="<?php echo date('Y-m-d')?>" placeholder="P.R. Date" class="form-control" />';
				}else if(selectAFilterType == 'supplier_id'){
					var field = '<label>Supplier Name</label><select name="selectAFilterValue" id="selectAFilterValue" class="form-control"><?php echo SelectListFacades::getSupplierList($m,0,0);?></select>';
				}
				$('#selectAFilterValueDiv').html(field);
				$("select").select2();
			}
			optionEnableAndDisableAdvanceFilterField();
			
			function viewAdvanceSearchFilterAgainstPurchaseOrder(){
				var selectAFilterValue = $('#selectAFilterValue').val();
				var selectAFilterType  = $('#selectAFilterType').val();
				if(selectAFilterValue == ''){
					alert('Something Wrong! Please Select or Fill Input Field and Filter Data.');
					return false;
				}else{
					data = selectAFilterType+'<*>'+selectAFilterValue;
					showDetailModelOneParamerter('stdc/viewAdvanceSearchFilterAgainstPurchaseOrder',data,'View Advance Search Filter Data Against Purchase Order Detail');
				}
			}
    </script>
    <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
@endsection