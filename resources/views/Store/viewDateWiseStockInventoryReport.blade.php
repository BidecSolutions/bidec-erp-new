<?php

$accType = Auth::user()->acc_type;
$m;


$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');
?>

@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    <div class="well">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">View Date Wise Stock Inventory Report</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <input type="hidden" name="functionName" id="functionName" value="stdc/filterViewDateWiseStockInventoryReport" readonly="readonly" class="form-control" />
                            <input type="hidden" name="tbodyId" id="tbodyId" value="filterViewDateWiseStockInventoryReport" readonly="readonly" class="form-control" />
                            <input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
                            <input type="hidden" name="pageType" id="pageType" value="2" readonly="readonly" class="form-control" />
                            <div class="row">
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                    <label class="sf-label">Category / Sub Item</label>
                                    <span class="rflabelsteric"><strong>*</strong></span>
                                    <select class="form-control requiredField" name="paramOne" id="paramOne" onchange="viewFilterDateWiseStockInventoryReport()">
                                        <option value="">Select Category / Sub Item</option>
                                        @foreach($categorys as $key => $y)
                                            <optgroup label="{{ $y->main_ic}}" value="{{ $y->id}}">
                                                <?php
                                                CommonFacades::companyDatabaseConnection($m);
                                                $subItems = DB::select('select `id`,`sub_ic` from `subitem` where `company_id` = '.$m.' and `main_ic_id` ='.$y->id.'');
                                                CommonFacades::reconnectMasterDatabase();
                                                ?>
                                                @foreach($subItems as $key2 => $y2)
                                                    <option value="{{ $y->id.'^'.$y2->id}}">{{ $y2->sub_ic}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>To Date</label>
                                    <input type="Date" name="paramTwo" id="paramTwo" max="<?php echo $current_date;?>" onchange="viewFilterDateWiseStockInventoryReport()" value="<?php echo $current_date;?>" class="form-control" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right">
                                    <?php echo CommonFacades::displayPrintButtonInBlade('printViewDateWiseStockInventoryReport','margin-top: 32px','0');?>
                                    <input type="button" value="View Range Wise Data Filter" class="btn btn-sm btn-danger" onclick="viewFilterDateWiseStockInventoryReport();" style="margin-top: 32px;" />
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printViewDateWiseStockInventoryReport">
                                <?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="filterViewDateWiseStockInventoryReport">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hidden qrCodeDiv">
                                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate('Date Wise Stock Inventory Report'))!!} ">
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
    </script>
    <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
@endsection