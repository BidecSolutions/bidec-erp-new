<?php

$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');
?>

@extends('layouts.default')

@section('content')
    <div class="well">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseRequestVoucherList','','1');?>
                        <?php echo CommonFacades::displayExportButton('purchaseRequestVoucherList','','1')?>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">View Purchase Request Voucher List</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <input type="hidden" name="functionName" id="functionName" value="stdc/filterPurchaseRequestVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseRequestVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                            <input type="hidden" name="filterType" id="filterType" value="1" readonly="readonly" class="form-control" />

                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>Sub Department</label>
                                    <input type="hidden" readonly name="selectSubDepartmentId" id="selectSubDepartmentId" class="form-control clearable" value="">
                                    <input list="selectSubDepartment" name="selectSubDepartment" id="selectSubDepartmentTwo" class="form-control clearable">
                                    <?php echo CommonFacades::subDepartmnetSelectList($m);?>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>From Date</label>
                                    <input type="Date" name="fromDate" id="fromDate" max="<?php echo $current_date;?>" value="<?php echo $currentMonthStartDate;?>" class="form-control" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-center"><label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    <input type="text" readonly class="form-control text-center" value="Between" /></div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>To Date</label>
                                    <input type="Date" name="toDate" id="toDate" max="<?php echo $current_date;?>" value="<?php echo $currentMonthEndDate;?>" class="form-control" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>Select Voucher Status</label>
                                    <select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
                                        <?php echo CommonFacades::voucherStatusSelectList();?>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">
                                    <input type="button" value="View Filter Data" class="btn btn-sm btn-danger" onclick="viewRangeWiseDataFilter();" style="margin-top: 32px;" />
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printDemandVoucherList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered sf-table-list">
                                                                <thead>
                                                                <th class="text-center">S.No</th>
                                                                <th class="text-center">Purchase Request No.</th>
                                                                <th class="text-center">Purchase Request Date</th>
                                                                <th class="text-center">Requested Sub Department</th>
                                                                <th class="text-center">Requested Date</th>
                                                                <th class="text-center">Purchase Request Status</th>
                                                                <th class="text-center">Created By</th>
                                                                <th class="text-center hidden-print">Action</th>
                                                                </thead>
                                                                <tbody id="filterPurchaseRequestVoucherList"></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hidden qrCodeDiv">
                                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate('View Approve Store Purchase Request Voucher List'))!!} ">
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
@endsection