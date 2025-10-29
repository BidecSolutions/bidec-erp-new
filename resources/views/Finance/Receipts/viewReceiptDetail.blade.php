@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = array(
        'type' => 2,
        'id' => $receiptDetail->id,
        'status' => $receiptDetail->status,
        'voucher_type_status' => $receiptDetail->rv_status
    );
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintReceiptVoucherDetail','','1');?>
        {{CommonHelper::getButtonsforPaymentAndReceiptAndJournalVouchers($data)}}
        @if($receiptDetail->status == 1 && $receiptDetail->rv_status == 2 && $receiptDetail->rv_type == 1)
            <button class="btn btn-xs btn-danger" onclick="reverseFinanceVoucher('2', '<?php echo $receiptDetail->id?>', '<?php echo $receiptDetail->status?>', '<?php echo $receiptDetail->rv_status?>')">Reverse Voucher</button>&nbsp;
        @endif
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="well">
    <div class="row" id="PrintReceiptVoucherDetail">
        <style>
            .floatLeft{
                width: 45%;
                float: left;
            }
            .floatRight{
                width: 45%;
                float: right;
            }
        </style>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>R.V.No.</th>
                                        <td>{{$receiptDetail->rv_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>R.V.Date</th>
                                        <td>{{CommonHelper::changeDateFormat($receiptDetail->rv_date)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Slip No.</th>
                                        <td>{{$receiptDetail->slip_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>Receipt To</th>
                                        <td>{{$receiptDetail->receipt_to}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if($receiptDetail->voucher_type == 2)
                <div class="floatRight">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Cheque No</th>
                                            <td>{{$receiptDetail->cheque_no}}</td>
                                        </tr>
                                        <tr>
                                            <th>Cheque Date</th>
                                            <td>{{CommonHelper::changeDateFormat($receiptDetail->cheque_date)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Account Name</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($receiptDetail->receipt_data as $key => $row)
                            <tr>
                                <td class="text-center">{{$counter++}}</td>
                                <td>{{$row->account->name}}</td>
                                <td class="text-right">{{$row->debit_credit == 1 ? $row->amount : '0'}}</td>
                                <td class="text-right">{{$row->debit_credit == 2 ? $row->amount : '0'}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>