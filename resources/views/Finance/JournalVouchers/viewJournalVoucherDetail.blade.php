@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = array(
        'type' => 3,
        'id' => $journalVoucherDetail->id,
        'status' => $journalVoucherDetail->status,
        'voucher_type_status' => $journalVoucherDetail->jv_status
    );
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintJournalVoucherDetail','','1');?>
        @if($journalVoucherDetail->voucher_type == 1)
            {{CommonHelper::getButtonsforPaymentAndReceiptAndJournalVouchers($data)}}
        @endif
        @if($journalVoucherDetail->status == 1 && $journalVoucherDetail->jv_status == 2 && $journalVoucherDetail->voucher_type == 1)
            <button class="btn btn-xs btn-danger" onclick="reverseFinanceVoucher('3', '<?php echo $journalVoucherDetail->id?>', '<?php echo $journalVoucherDetail->status?>', '<?php echo $journalVoucherDetail->jv_status?>')">Reverse Voucher</button>&nbsp;
        @endif
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="well">
    <div class="row" id="PrintJournalVoucherDetail">
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
                                        <th>J.V.No.</th>
                                        <td>{{$journalVoucherDetail->jv_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>J.V.Date</th>
                                        <td>{{CommonHelper::changeDateformat($journalVoucherDetail->jv_date)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Slip No.</th>
                                        <td>{{$journalVoucherDetail->slip_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>Voucher Type</th>
                                        <td>{{ $journalVoucherDetail->voucher_type == 1 ? 'Normal' : ($journalVoucherDetail->voucher_type == 2 ? 'Purchase' : 'Sale') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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

                        @foreach($journalVoucherDetail->journal_voucher_data as $key => $row)
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