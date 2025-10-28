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

    $view = true;
    $edit = true;
    $delete = true;
    $export = true;
?>

@extends('layouts.default')
@section('content')
    <div class="well_N">
        <div class="dp_sdw">
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
                                <label>Voucher Type</label>
                                <select class="form-control" name="voucher_type" id="voucher_type">
                                    <option value="">Select Option</option>
                                    <option value="1">Stock Transfer</option>
                                    <option value="2">Unit Transfer</option>
                                </select>
                            </div>
                            <?php echo CommonFacades::displayDateRangeOption('fromDate','toDate',2,1);?>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                <input type="button" value="View" class="btn btn-sm btn-success" onclick="updateStartEndField(),getStockTransferDataDetail()" style="margin-top: 25px;" />
                            </div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div id="printDemandVoucherList">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php echo CommonHelper::headerPrintSectionInPrintView($m);?>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="data">
                                            <div class="table-responsive" >
                                                <table class="table table-bordered sf-table-list" id="issuanceVoucherList">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">S.No</th>
                                                            <th class="text-center">Stock Transfer No.</th>
                                                            <th class="text-center">Stock Transfer Date</th>
                                                            <th class="text-center">Voucher Type</th>
                                                            <th class="text-center">Desription</th>
                                                            <th class="text-center hidden-print">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="ShowHide">
                                                        <?php /*?><?php
                                                            CommonHelper::companyDatabaseConnection($m);
                                                                $MasterData = DB::table('stock_transfers')->where('status', '=', 1)->orderBy('id', 'desc')->get();
                                                            CommonHelper::reconnectMasterDatabase();
                                                            $Counter = 1;
                                                            $paramOne = "store/viewStockTransferDetail?m=".$m;
                                                            $paramThree = "View Issuance Detail";
                                                            foreach($MasterData as $row):
                                                            $edit_url = url('/store/editStockTransferForm/'.$row->id.'/'.$row->tr_no.'?m='.$m);
                                                        ?>
                                                                <tr class="text-center" id="RemoveTr<?php echo $row->id?>">
                                                                    <td><?php echo $Counter++;?></td>
                                                                    <td><?php echo strtoupper($row->tr_no);?></td>
                                                                    <td><?php echo CommonHelper::changeDateFormat($row->tr_date);?></td>
                                                                    <td><?php echo strtoupper($row->description);?></td>
                                                                    <td>
                                                                        @if($view==true)
                                                                            <button onclick="showDetailModelOneParamerter('<?php echo $paramOne?>','<?php echo $row->tr_no;?>','View Stock Transfer Detail')"   type="button" class="btn btn-success btn-xs">View</button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                        <?php 
                                                            endforeach;
                                                        ?><?php */?>
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
    <script !src="">
        function getStockTransferDataDetail(){
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var startRecordNo = $('#startRecordNo').val();
            var endRecordNo = $('#endRecordNo').val();
            var voucherType = $('#voucher_type').val();
            var m = '<?php echo $m?>';
            $('#ShowHide').html('<tr><td colspan="100"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
            $.ajax({
                url: '<?php echo url('/')?>/store/stock_transfer_list',
                type: 'Get',
                data: {fromDate: fromDate,toDate:toDate,m:m,startRecordNo:startRecordNo,endRecordNo:endRecordNo,voucherType:voucherType},
                success: function (response) {
                    $('#ShowHide').html(response);
                }
            });
        }
        getStockTransferDataDetail();

        function updateRecordLimitStockTransferListList(paramOne,paramTwo){
			$('#startRecordNo').val(paramOne);
			$('#endRecordNo').val(paramTwo);
			getStockTransferDataDetail();
		}

        function DeleteStockTransfer(Id,TrNo,TrStatus){
            if (confirm('Are You Sure ? You want to delete this recored...!')) {
                var m = '<?php echo $m?>';
                if(TrStatus == 2){
                    if (confirm('Stock qty will roll back...!')){
                    }else{
                        return false;
                    }
                }
                $.ajax({
                    url: '/pdc/deleteStockTransfer',
                    type: 'Get',
                    data: {Id: Id,TrNo:TrNo,m:m},
                    success: function (response){
                        $('#RemoveTr'+response).remove();
                    }
                });
            }else{}
        }
    </script>
@endsection