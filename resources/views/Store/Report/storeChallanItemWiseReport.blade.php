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
                        <input type="hidden" name="functionName" id="functionName" value="store/report/ajax/storeChallanItemWiseReport" readonly="readonly" class="form-control" />
                        <input type="hidden" name="tbodyId" id="tbodyId" value="filterStoreChallanVoucherList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                        <input type="hidden" name="filterType" id="filterType" value="storeChallanList" readonly="readonly" class="form-control" />
                        <input type="hidden" name="pageTypeTwo" id="pageTypeTwo" value="<?php echo Input::get('pageType');?>" readonly="readonly" class="form-control" />
                        <input type="hidden" name="parentCode" id="parentCode" value="<?php echo Input::get('parentCode');?>" readonly="readonly" class="form-control" />
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="well">
                                    <div class="row">
                                        <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',2,2);?>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                            <label>Select Voucher Status</label>
                                            <select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
                                                <?php echo CommonFacades::voucherStatusSelectList();?>
                                            </select>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                            <input type="button" value="View" class="btn btn-sm btn-danger" onclick="updateStartEndField(),viewRangeWiseDataFilter()" style="margin-top: 32px;" />
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
                                                        return DB::connection('tenant')->table('header_footer_setting')->where('company_id','=',$m)->first();
                                                    });
                                                    if(empty($headerFooterSetting)){
                                                        CommonFacades::headerPrintSectionInPrintView($m);
                                                    }else{
                                                        $getDefaultInvoiceFormat = DB::connection('tenant')->table('default_invoice_format')->where('company_id','=',$m)->where('invoice_format_name','=','All List Header')->first();
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
                                                                    <th class="text-center">Challan No.</th>
                                                                    <th class="text-center">Location</th>
                                                                    <th class="text-center">Department / Sub Department</th>
                                                                    <th class="text">Project</th>
                                                                    <th class="text">Item Name</th>
                                                                    <th class="text">Issue Qty.</th>
                                                                    <th class="text">Received Qty.</th>
                                                                    <th class="text-center">M.R.No.</th>
                                                                    <th class="text-center">M.R.Date</th>
                                                                    <th class="text-center">S.C.No.</th>
                                                                    <th class="text-center">S.C.Date</th>                                                                    
                                                                    <th class="text-center">S.C.Status</th>                                                                                                                                        
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
@endsection