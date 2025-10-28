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
                        <form id="list_data" method="get" action="{{ route('store.viewInvoiceSubmissionList') }}">
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
                                            <?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="table-responsive scrollme">
                                                        <table class="table table-bordered customTable" id="purchaseOrderVoucherList">
                                                            <thead>
																<tr>
																	<th class="text-center" rowspan="2">S.No</th>
																	<th class="text-center" rowspan="2">Invoice Type</th>
																	<th class="text-center" rowspan="2">Vendor Name</th>
																	<th class="text-center" rowspan="2">Item Detail</th>
																	<th class="text-center" rowspan="2">Invoice No</th>
                                                                    <th class="text-center" rowspan="2">Amount in Dollar</th>
                                                                    <th class="text-center" rowspan="2">Amount in DHS</th>
                                                                    <th class="text-center" rowspan="2">Amount in PKR</th>
																	<th class="text-center" rowspan="2">Privious Paid Amount</th>
																	<th class="text-center" rowspan="2">Invoice Date</th>
                                                                    <th class="text-center" rowspan="2">Submited Date</th>
                                                                    <th class="text-center" rowspan="2">PO Detail</th>
                                                                    <th class="text-center" rowspan="2">Batch No</th>
                                                                    <th class="text-center" rowspan="2">PR Detail</th>
                                                                    <th class="text-center" rowspan="2">Project</th>
                                                                    <th class="text-center" rowspan="2">Payment Status</th>
                                                                    <th colspan="3" class="text-center">New Pay Detail</th>
                                                                    <th class="text-center" rowspan="2">Finance Status</th>
																</tr>
                                                                <tr>
                                                                    <th class="text-center">New Pay Amount</th>
                                                                    <th class="text-center">Remarks</th>
                                                                    <th class="text-center">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="data"></tbody>
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
        });
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
        function updatePurchaseOrderDetailForInvoiceSubmit(poId,fieldName){   
            var value = $('#'+fieldName+'_'+poId+'').val();
            
            $.ajax({
                url: '<?php echo url('/')?>/pad/updatePurchaseOrderDetailForInvoiceSubmit',
                type: "GET",
                data: { columnValue:value,poId:poId,columnName:fieldName},
                success:function(data) {
                    get_ajax_data();
                }
            });
        }
        function updateNewPayDetail(poId){
            var new_pay = $('#new_pay_'+poId+'').val();
            var remarks = $('#remarks_'+poId+'').val();
            var payment_status= $('#payment_status_'+poId+'').val();
            $.ajax({
                url: '<?php echo url('/')?>/pad/updateNewPayDetail',
                type: "GET",
                data: { new_pay:new_pay,poId:poId,remarks:remarks,payment_status:payment_status},
                success:function(data) {
                    get_ajax_data();
                }
            });
        }
    </script>
    <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
@endsection