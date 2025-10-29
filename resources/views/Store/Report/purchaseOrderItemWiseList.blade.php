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
                        <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseOrderVoucherList', '', '1'); ?>
                        <?php echo CommonFacades::displayExportButton('purchaseOrderVoucherList', '', '1'); ?>
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
                            <input type="hidden" name="functionName" id="functionName" value="store/report/purchaseOrderItemWiseReport" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseOrderItemWiseReport" readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                            <input type="hidden" name="filterType" id="filterType" value="purchaseOrderItemWiseReport" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageTypeTwo" id="pageTypeTwo" value="<?php echo Input::get('pageType');?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="parentCode" id="parentCode" value="<?php echo Input::get('parentCode');?>" readonly="readonly" class="form-control" />

                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Supplier</label>
                                    <select name="filterSupplierId" id="filterSupplierId" class="form-control">
                                        <?php echo SelectListFacades::getSupplierList($m,0, isset($_GET['filterSupplierId']) ? $_GET['filterSupplierId'] : 0);?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Invoice No.</label>
                                    <input class="form-control" type="text" name="invoiceNumber" value="{{ isset($_GET['invoiceNumber']) ? $_GET['invoiceNumber'] : old('invoiceNumber')}}">
                                </div>
                                <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',2,1);?>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>Select Voucher Status</label>
                                    <select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
                                        <?php echo CommonFacades::voucherStatusSelectList(); ?>
                                    </select>
                                </div>

                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                <input type="button" value="View" class="btn btn-sm btn-success" onclick="updateStartEndField(),viewRangeWiseDataFilter()" style="margin-top: 25px;" />
                                </div>
                            </div>
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
                                                                        <th class="text-center">P.O. No.</th>
                                                                        <th class="text-center">P.O. Date</th>
                                                                        <th class="text-center">P.R. No.</th>
                                                                        <th class="text-center">P.R. Date</th>
                                                                        <th class="text-center">Invoice No.</th>
                                                                        <th class="text-center">Part Code</th>
                                                                        <th class="text-center">Item Name</th>
                                                                        <th class="text-center">Item Price</th>
                                                                        <th class="text-center">Item PR.Qty</th>
                                                                        <th class="text-center">Item PO.Qty</th>
                                                                        <th class="text-center">Item Amount</th>
                                                                        <th class="text-center">Expense Amount</th>
                                                                        <th class="text-center">Total Item Amount</th>
                                                                        <th class="text-center">Location Name</th>
                                                                        <th class="text-center">Supplier Name</th>
                                                                        <th class="text-center">Department</th>
                                                                        <th class="text-center">Sub Department</th>
                                                                        <th class="text-center">Project</th>                                                                        
                                                                        <th class="text-center">P.O Status</th>                                                                                                                                                                                                                  
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="filterPurchaseOrderItemWiseReport">               
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
        function updateRecordLimitPurchaseOrderItemWiseList(paramOne,paramTwo){
			$('#startRecordNo').val(paramOne);
			$('#endRecordNo').val(paramTwo);
			viewRangeWiseDataFilter();
		}
    </script>
@endsection
