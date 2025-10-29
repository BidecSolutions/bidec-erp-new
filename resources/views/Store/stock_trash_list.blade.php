<?php

use App\Helpers\CommonHelper;
use App\Helpers\ReuseableCode;
$accType = Auth::user()->acc_type;
if($accType == 'client'){
    $m = getSessionCompanyId();
}else{
    // $m = Auth::user()->company_id;
    $m = getSessionCompanyId();
}
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');

$view=true;
$edit=true;
$delete=true;
$export=true;
// $view=ReuseableCode::check_rights(42);
// $edit=ReuseableCode::check_rights(43);
// $delete=ReuseableCode::check_rights(44);
// $export=ReuseableCode::check_rights(237);

// $AccYearDate = DB::connection('mysql')->table('company')->select('accyearfrom','accyearto')->where('id', $m)->first();
// $AccYearFrom = $AccYearDate->accyearfrom;
// $AccYearTo = $AccYearDate->accyearto;
?>

@extends('layouts.default')

@section('content')
    <div class="well_N">
    <div class="dp_sdw">    
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <?php echo CommonHelper::displayPrintButtonInBlade('printIssuanceVoucherList','','1');?>
                            <?php if($export == true):?>
                        <?php echo CommonHelper::displayExportButton('issuanceVoucherList','','1')?>
                        <?php endif;?>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">Stock Transfer List</span>
                                </div>
                            </div>

                            <div class="lineHeight">&nbsp;</div>
                            <div class="row">

                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>From Date</label>
                                    <input type="Date" name="FromDate" id="FromDate" min="<?php // echo $AccYearFrom ?>" max="<?php // echo $AccYearTo; ?>" value="<?php echo $currentMonthStartDate;?>" class="form-control" />
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <label>To Date</label>
                                    <input type="Date" name="ToDate" id="ToDate" min="<?php // echo $AccYearFrom ?>" max="<?php // echo $AccYearTo; ?>" value="<?php echo $currentMonthEndDate;?>" class="form-control" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">
                                    <input type="button" value="View Range Wise Data Filter" class="btn btn-sm btn-primary" onclick="GetBrvsDateAndAccontWise();" style="margin-top: 32px;" />
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>

                            <div id="printDemandVoucherList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <?php echo CommonHelper::headerPrintSectionInPrintView($m);?>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="data">
                                                        <div class="table-responsive" >
                                                            <table class="table table-bordered sf-table-list" id="issuanceVoucherList">
                                                                <thead>
                                                                <th class="text-center">S.No</th>
                                                                <th class="text-center">Stock Transfer No.</th>
                                                                <th class="text-center">Stock Transfer Date</th>
                                                                <th class="text-center">Desription</th>
                                                                {{-- <th class="text-center">Status</th> --}}
                                                                <th class="text-center hidden-print">Action</th>
                                                                </thead>

                                                                <tbody id="ShowHide">

                                                                <?php
                                                                CommonHelper::companyDatabaseConnection($m);
                                                                $MasterData = DB::table('stock_trashes')->where('status', '=', 1)->orderBy('id', 'desc')->get();
                                                                CommonHelper::reconnectMasterDatabase();

                                                                $Counter = 1;
                                                                $paramOne = "store/viewStockTrashDetail?m=".$m;
                                                                $paramThree = "View Issuance Detail";

                                                                foreach($MasterData as $row):
                                                                $edit_url= url('/store/editStockTransferForm/'.$row->id.'/'.$row->tr_no.'?m='.$m);
                                                                ?>
                                                                <tr class="text-center" id="RemoveTr<?php echo $row->id?>">
                                                                    <td><?php echo $Counter++;?></td>
                                                                    <td><?php echo strtoupper($row->tr_no);?></td>
                                                                    <td><?php echo CommonHelper::changeDateFormat($row->tr_date);?></td>
                                                                    <td><?php echo strtoupper($row->description);?></td>
                                                                    {{-- <td class="{{$row->id}}">@if($row->tr_status==1) Pending @else Approve @endif</td> --}}
                                                                    <td>
                                                                        @if($view==true)
                                                                        <button onclick="showDetailModelOneParamerter('<?php echo $paramOne?>','<?php echo $row->tr_no;?>','View Stock Trash Detail')"   type="button" class="btn btn-success btn-xs">View</button>
                                                                        @endif
                                                                        
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach;?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hidden qrCodeDiv">
                                        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate('View Purchase Demand Voucher List'))!!} ">
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
    <script !src="">
        //issuanceDataFilter();

        function GetBrvsDateAndAccontWise()
        {
            var FromDate = $('#FromDate').val();
            var ToDate = $('#ToDate').val();
            var m = '<?php echo $m?>';
            $('#ShowHide').html('<tr><td colspan="14"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');

            $.ajax({
                url: '/stdc/getStockTransferDataAjax',
                type: 'Get',
                data: {FromDate: FromDate,ToDate:ToDate,m:m},

                success: function (response) {
                    $('#ShowHide').html(response);
                }
            });
        }

        function DeleteStockTransfer(Id,TrNo,TrStatus)
        {
            if (confirm('Are You Sure ? You want to delete this recored...!')) {
                var m = '<?php echo $m?>';
                if(TrStatus == 2)
                {
                    if (confirm('Stock qty will roll back...!'))
                    {}
                    else{
                        return false;
                    }
                }

                //$('#data').html('<tr><td colspan="14"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
                $.ajax({
                    url: '/pdc/deleteStockTransfer',
                    type: 'Get',
                    data: {Id: Id,TrNo:TrNo,m:m},

                    success: function (response)
                    {
                        $('#RemoveTr'+response).remove();
                    }
                });
            }
            else {}
        }
    </script>

@endsection