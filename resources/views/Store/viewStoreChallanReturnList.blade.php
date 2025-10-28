<?php

$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate = date('Y-m-t');
?>

@extends('layouts.default')

@section('content')
    <div class="well_N">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="display: none;">
                        @include('Store.' . $accType . 'storeMenu')
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">View Store Challan Return Voucher List</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <input type="hidden" name="functionName" id="functionName"
                                value="stdc/filterStoreChallanReturnList" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterStoreChallanReturnList"
                                readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m; ?>"
                                readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/'); ?>"
                                readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly"
                                class="form-control" />
                            <input type="hidden" name="filterType" id="filterType" value="storeChallanList"
                                readonly="readonly" class="form-control" />
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <?php echo CommonFacades::displayDateRangeOption('fromDate', 'toDate', 3, 2); ?>

                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                                    <input type="button" value="View" class="btn btn-sm btn-success"
                                                        onclick="viewRangeWiseDataFilter();" style="margin-top: 25px;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printStoreChallanReturnList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                                                        <label style="border-bottom:2px solid #000 !important;">Printed On
                                                            Date&nbsp;:&nbsp;</label><label
                                                            style="border-bottom:2px solid #000 !important;"><?php echo CommonFacades::changeDateFormat($current_date); ?></label>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-5">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                                                                style="font-size: 30px !important; font-style: inherit;
font-family: -webkit-body; font-weight: bold;">
                                                                <?php echo CommonFacades::getCompanyName($m); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-right">
                                                        <?php $nameOfDay = date('l', strtotime($current_date)); ?>
                                                        <label style="border-bottom:2px solid #000 !important;">Printed On
                                                            Day&nbsp;:&nbsp;</label><label
                                                            style="border-bottom:2px solid #000 !important;"><?php echo '&nbsp;' . $nameOfDay; ?></label>

                                                    </div>
                                                </div>
                                                <div style="line-height:5px;">&nbsp;</div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered sf-table-list">
                                                                <thead>
                                                                    <th class="text-center">S.No</th>
                                                                    <th class="text-center">S.C.R. No.</th>
                                                                    <th class="text-center">S.C.R. Date</th>
                                                                    <th class="text-center">Slip No.</th>
                                                                    <th class="text-center">Sub Department</th>
                                                                    <th class="text-center">S.C.R Status</th>
                                                                    <th class="text-center hidden-print">Action</th>
                                                                </thead>
                                                                <tbody id="filterStoreChallanReturnList"></tbody>
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
            } else if (selectAFilterType == 'username') {
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
                showDetailModelOneParamerter('stdc/viewAdvanceSearchFilterAgainstStoreChallan',data,'View Advance Search Filter Data Against Store Challan Return Voucher Detail');
            }
        }
        function reverseStoreChallanReturnVoucher(id,voucherNo){
            $.ajax({
                url: '<?php echo url('/')?>/std/reverseStoreChallanReturnVoucher',
                type: "GET",
                data: { id:id,voucherNo:voucherNo},
                success:function(data) {
                    $('#showDetailModelOneParamerter').modal('toggle');
                    viewRangeWiseDataFilter();
                }
            });
        }
    </script>
@endsection
