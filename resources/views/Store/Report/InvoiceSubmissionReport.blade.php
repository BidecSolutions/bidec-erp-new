<?php
    $accType = Auth::user()->acc_type;
    $m;
    $current_date = date('Y-m-d');
    $currentMonthStartDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-01');
    $currentMonthEndDate =  isset($_GET['toDate']) ? $_GET['toDate'] :date('Y-m-t');
?>

@extends('layouts.default')

@section('content')
<script src="{{ URL::asset('assets/custom/js/customMainFunction.js') }}"></script>
    <div class="well_N">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseOrderVoucherList','','1');?>
                        <?php echo CommonFacades::displayExportButton('purchaseOrderVoucherList','','1')?>
                        
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">View Invoice Submission Report</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <form id="list_data" method="get" action="{{ route('store.report.invoiceSubmissionReport') }}">
                            <input type="hidden" name="functionName" id="functionName" value="stdc/filterPurchaseOrderVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseOrderVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                            <input type="hidden" name="filterType" id="filterType" value="PurchaseOrderList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageTypeTwo" id="pageTypeTwo" value="<?php echo Input::get('pageType');?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="parentCode" id="parentCode" value="<?php echo Input::get('parentCode');?>" readonly="readonly" class="form-control" />
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
                                                <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',3,2);?>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                    <label>Select Payment Status</label>
                                                    <select name="payment_status" id="payment_status" class="form-control">
                                                        <option value="">Select Option</option>
                                                        <option value="Paid">Paid</option>
                                                        <option value="Unpaid">Unpaid</option>
                                                        <option value="Partial Paid">Partial Paid</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                                    <input type="button" value="View" class="btn btn-sm btn-success" onclick="get_ajax_data();" style="margin-top: 25px;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printPurchaseOrderVoucherList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <?php echo CommonFacades::headerPrintSectionInPrintView($m); ?>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered sf-table-list" id="purchaseOrderVoucherList">
                                                                <thead>
                                                                <tr>
																	<th class="text-center">S.No</th>
																	<th class="text-center">Invoice Type</th>
																	<th class="text-center">Vendor Name</th>
																	<th class="text-center">Item Detail</th>
																	<th class="text-center">Invoice No</th>
                                                                    <th class="text-center">Amount in Dollar</th>
                                                                    <th class="text-center">Amount in DHS</th>
                                                                    <th class="text-center">Amount in PKR</th>
																	<!-- <th class="text-center">Privious Paid Amount</th> -->
																	<th class="text-center">Invoice Date</th>
                                                                    <th class="text-center">Submited Date</th>
                                                                    <th class="text-center">PO Detail</th>
                                                                    <th class="text-center">Batch No</th>
                                                                    <th class="text-center">PR Detail</th>
                                                                    <th class="text-center">Project</th>
                                                                    <th class="text-center">Payment Status</th>
                                                                    <!-- <th class="text-center">New Pay Amount</th> -->
                                                                    <th class="text-center">Remarks</th>
                                                                    <th class="text-center">Finance Status</th>
																
                                                                    
                                                                </tr>
                                                                </thead>
                                                                <tbody id="data">               
                                                                </tbody>
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
    </div>
    <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
    <script>
        // function updateRecordLimitPurchaseOrderItemWiseList(paramOne,paramTwo){
		// 	$('#startRecordNo').val(paramOne);
		// 	$('#endRecordNo').val(paramTwo);
		// 	viewRangeWiseDataFilter();
		// }

        function get_ajax_data(){
            var form = $('#list_data');
            var actionUrl = form.attr('action');
            $('#data').html('<tr><td colspan="100"><div class="loader"></div></td></tr>');
            $.ajax({
                type: "get",
                url: actionUrl,
                data: form.serialize(),
                success:function(data) {
                    $('#data').html(data);
                }
            });
        }
        get_ajax_data();
    </script>
@endsection
