<?php
use App\Helpers\CommonHelper;
use App\Helpers\PurchaseHelper;
use App\Helpers\ReuseableCode;
// $approve=ReuseableCode::check_rights(213);
$id = $_GET['id'];
$m = getSessionCompanyId();
$currentDate = date('Y-m-d');
$companyList = DB::connection('mysql')->table('company')->where('status','=','1')->where('id','!=',$m)->get();
CommonHelper::companyDatabaseConnection($m);
$MasterData = DB::table('stock_trashes')->where('tr_no','=',$id)->get();
CommonHelper::reconnectMasterDatabase();
foreach ($MasterData as $row)
{
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
    @if(true)
      {{-- @if ($row->tr_status==1)
                <button onclick="approve({{$row->id}})" type="button" id="approve" class="btn btn-success btn-xs">Approve</button>
          @endif --}}
        @endif
          <?php CommonHelper::displayPrintButtonInView('printDemandVoucherVoucherDetail','LinkHide','1');?>
    </div>

</div>
<div style="line-height:5px;">&nbsp;</div>
<div class="row" id="printDemandVoucherVoucherDetail">
    <?php //echo PurchaseHelper::displayApproveDeleteRepostButtonTwoTable($m,$row->demand_status,$row->status,$row->demand_no,'demand_no','demand_status','status','demand','demand_data');?>
    <?php //echo Form::open(array('url' => 'pad/updateDemandDetailandApprove?m='.$m.'','id'=>'updateDemandDetailandApprove'));?>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
    <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
    <input type="hidden" name="demandNo" value="<?php echo $id; ?>">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 well">
        <div class="">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label style="border-bottom:2px solid #000 !important;">Printed On Date&nbsp;:&nbsp;</label><label style="border-bottom:2px solid #000 !important;"><?php echo CommonHelper::changeDateFormat(date('Y-m-d'));$x = date('Y-m-d');
                                echo ' '.'('.date('D', strtotime($x)).')';?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h3 style="text-align: center;">Stock Trash</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div style="line-height:5px;">&nbsp;</div>
            <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                    <table  class="table table-bordered table-striped table-condensed tableMargin">
                        <tbody>
                        <tr>
                            <td>Stock Trash No.</td>
                            <td class="text-center"><?php echo strtoupper($row->tr_no);?></td>
                        </tr>
                        <tr>
                            <td>Stock Trash Date</td>
                            <td class="text-center"><?php echo CommonHelper::changeDateFormat($row->tr_date);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table  class="table table-bordered table-striped table-condensed tableMargin">
                        <thead>
                        <tr>
                            <th class="text-center" style="width:50px;">S.No</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Location From</th>
                            <th class="text-center">Transfer Qty</th>
                            <th class="text-center">Description</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        CommonHelper::companyDatabaseConnection($m);
                        $DetailData = DB::table('stock_trash_datas')->where('tr_no','=',$id)->get();
                        CommonHelper::reconnectMasterDatabase();
                        $counter = 1;
                        $totalCountRows = count($DetailData);
                        foreach ($DetailData as $row1){
                        ?>
                        <tr>
                            <td class="text-center">
                                <?php echo $counter++;?>

                            </td>
                            <td  id="{{$row1->id}}" title="{{$row1->item_id}}">

                                <?php $accType = Auth::user()->acc_type;
                                if($accType == 'client'):
                                ?>
                                <a class="LinkHide" href="<?php echo url('/') ?>/store/fullstockReportView?pageType=&&parentCode=97&&m=<?php echo Session::get('run_company');?>&&sub_item_id=<?php echo $row1->item_id; ?>&&warehouse_id=<?php echo $row1->warehouse_from; ?>" target="_blank">
                                    <?php
                                    echo CommonHelper::get_item_name($row1->item_id);
                                    ?>
                                </a>
                                <?php else:?>
                                    <?php
                                        echo CommonHelper::get_item_name($row1->item_id);
                                    ?>
                                    <?php endif;?>


                            </td>
                            <td>
                                {{ CommonFacades::get_location_name($row1->warehouse_from) }}
                            </td>
                            <td class="text-center"><?php echo number_format($row1->qty,2);?></td>

                            <td><textarea readonly class="form-control">{{$row1->desc}}</textarea></td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="line-height:8px;">&nbsp;</div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h6>Remarks: <?php echo strtoupper($row->description); ?></h6>
                    </div>
                </div>
                <style>
                    .signature_bor {
                        border-top:solid 1px #CCC;
                        padding-top:7px;
                    }
                </style>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:40px;">
                    <div class="container-fluid">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                <h6 class="signature_bor">Prepared By: </h6>
                                <b>   <p>{{ optional(getUserDetail($row->created_by))->name }}</p></b>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                <h6 class="signature_bor">Reviewed By: </h6>
                                <b>   <p></p></b>
                            </div>
                            {{-- <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                <h6 class="signature_bor">Checked By:</h6>
                                <b>   <p></p></b>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                <h6 class="signature_bor">Approved By:</h6>
                                <b>  <p></p></b>
                            </div> --}}

                        </div>
                    </div>
                </div>


            </div>
            <!--
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right hidden qrCodeDiv">
                <img src="data:image/png;base64, { !! base64_encode(QrCode::format('png')->size(200)->generate('View Demand Voucher Detail'))!!} ">
            </div>
            <!-->
        </div>
    </div>


    <?php }?>

    <?php echo Form::close();?>
</div>
<script type="text/javascript">

    function approve(id)
    {
        $('#approve').prop('disabled', true);
        $.ajax({
            url : '{{url('/stdc/approve_transfer')}}',
            type: "GET",
            data :{id:id},
            success:function(data){
                //alert(data); return false;
                if (data=='0')
                {
                    $('#showDetailModelOneParamerter').modal('toggle');
                    $('.'+id).text('Approve');
                }
                else
                {
                    alert(data);
                    $("#"+data).css("background-color", "red")
                }




            }
        });
    }
</script>

