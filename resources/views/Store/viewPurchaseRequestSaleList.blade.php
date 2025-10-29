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
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="display: none;">
                        @include('Store.'.$accType.'storeMenu')
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">View Purchase Request Sale Voucher List</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <input type="hidden" name="functionName" id="functionName" value="stdc/filterPurchaseRequestSaleVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseRequestSaleVoucherList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
                            <input type="hidden" name="filterType" id="filterType" value="2" readonly="readonly" class="form-control" />

                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>Sub Department</label>
                                    <input type="hidden" readonly name="selectSubDepartmentId" id="selectSubDepartmentId" class="form-control" value="">
                                    <input list="selectSubDepartment" name="selectSubDepartment" id="selectSubDepartmentTwo" class="form-control">
                                    <?php echo CommonFacades::subDepartmnetSelectList($m);?>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>Supplier Name</label>
                                    <input type="hidden" readonly name="selectSupplierId" id="selectSupplierId" class="form-control" value="">
                                    <input list="selectSupplier" name="selectSupplier" id="selectSupplierTwo" class="form-control">
                                    <?php echo CommonFacades::supplierSelectList($m);?>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>From Date</label>
                                    <input type="Date" name="fromDate" id="fromDate" max="<?php echo $current_date;?>" value="<?php echo $currentMonthStartDate;?>" class="form-control" />
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-center"><label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
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

                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                    <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseRequestVoucherList','margin-top: 32px','1');?>
                                    <input type="button" value="View Filter Data" class="btn btn-sm btn-danger" onclick="viewRangeWiseDataFilter();" style="margin-top: 32px;" />
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printPurchaseRequestVoucherList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                                                        <label style="border-bottom:2px solid #000 !important;">Printed On Date&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo CommonFacades::changeDateFormat($current_date);?></label>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-5">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                                                                 style="font-size: 30px !important; font-style: inherit;
font-family: -webkit-body; font-weight: bold;">
                                                                <?php echo CommonFacades::getCompanyName($m);?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-right">
                                                        <?php $nameOfDay = date('l', strtotime($current_date)); ?>
                                                        <label style="border-bottom:2px solid #000 !important;">Printed On Day&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo '&nbsp;'.$nameOfDay;?></label>

                                                    </div>
                                                </div>
                                                <div style="line-height:5px;">&nbsp;</div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered sf-table-list">
                                                                <thead>
                                                                <th class="text-center">S.No</th>
                                                                <th class="text-center">P.R. No.</th>
                                                                <th class="text-center">P.R. Date</th>
                                                                <th class="text-center">Slip No.</th>
                                                                <th class="text-center">Sub Department</th>
                                                                <th class="text-center">Supplier Name</th>
                                                                <th class="text-center">P.R Status</th>
                                                                <th class="text-center hidden-print">Action</th>
                                                                </thead>
                                                                <tbody id="filterPurchaseRequestSaleVoucherList"></tbody>
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
@endsection