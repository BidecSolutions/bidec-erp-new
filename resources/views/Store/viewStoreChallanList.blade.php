<?php

$accType = Auth::user()->acc_type;
$m;
?>

@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">

    <style type="text/css">
        a {cursor: pointer;}
    </style>
    <div class="well_N">
	    <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    <?php echo CommonFacades::displayPrintButtonInBlade('printStoreChallanVoucherList','','1');?>
                    <?php echo CommonFacades::displayExportButton('storeChallanVoucherList','','1')?>
                </div>
                <div class="lineHeight">&nbsp;</div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <span class="subHeadingLabelClass">View Store Challan Voucher List</span>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <input type="hidden" name="functionName" id="functionName" value="stdc/filterStoreChallanVoucherList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="tbodyId" id="tbodyId" value="filterStoreChallanVoucherList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                        <input type="hidden" name="filterType" id="filterType" value="storeChallanList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageTypeTwo" id="pageTypeTwo" value="<?php echo Input::get('pageType');?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="parentCode" id="parentCode" value="<?php echo Input::get('parentCode');?>" readonly="readonly" class="form-control" />
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>Location</label>
                                            <select name="filterLocationId" id="filterLocationId" class="form-control">
                                                <?php echo SelectListFacades::getLocationList($m,0,0);?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>Project</label>
                                            <select name="filterProjectId" id="filterProjectId" class="form-control">
                                                <?php echo SelectListFacades::getProjectList($m,0,0);?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>Department / Sub Department</label>
                                            <select name="filterSubDepartmentId" id="filterSubDepartmentId" class="form-control">
                                                <?php echo SelectListFacades::getSubDepartmentList($m,0,0);?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>Select Voucher Status</label>
                                            <select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
                                                <?php echo CommonFacades::voucherStatusSelectList();?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',3,2);?>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">
                                            <input type="button" value="View" class="btn btn-sm btn-danger" onclick="updateStartEndField(),viewRangeWiseDataFilter()" style="margin-top: 32px;" />
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
                                                <select name="selectAFilterType" id="selectAFilterType"
                                                    class="form-control"
                                                    onchange="optionEnableAndDisableAdvanceFilterField()">
                                                    <option value="material_request_no">Store Challan No</option>
                                                    <option value="MR_no">M.R No</option>
                                                    <option value="material_request_date">Store Challan Date</option>
                                                    <option value="username">Username</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 selectAFilterValueDiv"
                                                id="selectAFilterValueDiv"></div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                                <input type="button" value="Enter" class="btn btn-sm btn-success"
                                                    onclick="viewAdvanceSearchFilterAgainstPurchaseOrder();"
                                                    style="margin-top: 25px;" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div id="printStoreChallanVoucherList">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <div id="headerTwo">
                                                <?php 
                                                    $headerFooterSetting = Cache::rememberForever('cacheHeaderFooterSetting_'.$m.'',function() use ($m){
                                                        return DB::table('header_footer_setting')->where('company_id','=',$m)->first();
                                                    });
                                                    if(empty($headerFooterSetting)){
                                                        CommonFacades::headerPrintSectionInPrintView($m);
                                                    }else{
                                                        $getDefaultInvoiceFormat = DB::table('default_invoice_format')->where('company_id','=',$m)->where('invoice_format_name','=','All List Header')->first();
                                                        if(empty($getDefaultInvoiceFormat)){
                                                            CommonFacades::headerPrintSectionInPrintView($m);
                                                        }else{
                                                            CommonFacades::headerPrintSectionInPrintViewInvoice($m,$getDefaultInvoiceFormat->invoice_header);
                                                        }
                                                    }
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered sf-table-list" id="storeChallanVoucherList">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">S.No</th>
                                                                    <th class="text-center">Location</th>
                                                                    <th class="text-center">Department / Sub Department</th>
                                                                    <th class="text">Project</th>
                                                                    <th class="text-center">M.R.No.</th>
                                                                    <th class="text-center">M.R.Date</th>
                                                                    <th class="text-center">S.C.No.</th>
                                                                    <th class="text-center">S.C.Date</th>
                                                                    <th class="text-center">Remarks</th>
                                                                    <th class="text-center">Created Detail</th>
                                                                    <th class="text-center">S.C.Status</th>
                                                                    <th class="text-center">Approve Detail</th>
                                                                    <th class="text-center">Receive Detail</th>
                                                                    <th class="text-center hidden-print">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="filterStoreChallanVoucherList"></tbody>
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
    <script>
        $(function () {
            $("select").select2();
        });
        
        function updateRecordLimitStoreChallanList(paramOne,paramTwo){
			$('#startRecordNo').val(paramOne);
			$('#endRecordNo').val(paramTwo);
			viewRangeWiseDataFilter();
		}
    </script>
    <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
    <script>
        $(function() {
            $("select").select2();
            $(document).on('keyup', '#selectAFilterValue', function(e) {
                if (e.keyCode == 13) {
                    viewAdvanceSearchFilterAgainstPurchaseOrder();

                }
            });
        });

        function optionEnableAndDisableAdvanceFilterField() {
            console.log('optionEnableAndDisableAdvanceFilterField');
            $('#selectAFilterValueDiv').html();
            var selectAFilterType = $('#selectAFilterType').val();
            if (selectAFilterType == 'material_request_no') {
                var field =
                    '<label>Store Challan No</label><input type="text" name="selectAFilterValue" id="selectAFilterValue" value="" placeholder="Store Challan No" class="form-control" />';
            } else if (selectAFilterType == 'material_request_date') {
                var field =
                    '<label>Store Challan Date</label><input type="date" name="selectAFilterValue" id="selectAFilterValue" value="<?php echo date('Y-m-d'); ?>" placeholder="Store Challan Date" class="form-control" />';
            }   else if (selectAFilterType == 'MR_no') {
                var field =
                '<label>M.R no</label><input type="text" name="selectAFilterValue" id="selectAFilterValue" value="" placeholder="M.R No" class="form-control" />';
            }
             else if (selectAFilterType == 'username') {
                var field =
                    '<label>Username</label><input type="text" name="selectAFilterValue" id="selectAFilterValue" value="" placeholder="username" class="form-control" />';
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
					showDetailModelOneParamerter('stdc/viewAdvanceSearchFilterAgainstStoreChallan',data,'View Advance Search Filter Data Against Store Challan Voucher Detail');
				}
			}
    </script>
@endsection