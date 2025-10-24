@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = array(
        'type' => 1,
        'id' => $paymentDetail->id,
        'status' => $paymentDetail->status,
        'voucher_type_status' => $paymentDetail->pv_status
    );
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintPaymentVoucherDetail','','1');?>
        <!-- {{CommonHelper::getButtonsforPaymentAndReceiptAndJournalVouchers($data)}} -->
        @if($paymentDetail->status == 1 && $paymentDetail->pv_status == 1)
            <button class="btn btn-xs btn-danger" onclick="deleteFinanceVoucher('1', '<?php echo $paymentDetail->id?>', '<?php echo $paymentDetail->status;?>', '<?php echo $paymentDetail->pv_status?>')">Delete</button>
            <button class="btn btn-xs btn-success" onclick="approveFinanceVoucher('1', '<?php echo $paymentDetail->id?>', '<?php echo $paymentDetail->status;?>', '<?php echo $paymentDetail->pv_status?>')">Approve</button>
        @endif
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="well">
    <div class="row" id="PrintPaymentVoucherDetail">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>P.V.No.</th>
                                        <td>{{$paymentDetail->pv_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>P.V.Date</th>
                                        <td>{{CommonHelper::changeDateFormat($paymentDetail->pv_date)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Slip No.</th>
                                        <td>{{$paymentDetail->slip_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>Paid To</th>
                                        <td>{{$paymentDetail->paid_to}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="floatRight">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    @if($paymentDetail->entry_option == 3)
                                        @if($paymentDetail->pi_voucher_type == 1)
                                            <tr>
                                                <th>P.I. No</th>
                                                <td>{{$paymentDetail->purchase_invoice->invoice_no}}</td>
                                            </tr>
                                            <tr>
                                                <th>P.I. Date</th>
                                                <td>{{CommonHelper::changeDateFormat($paymentDetail->purchase_invoice->invoice_date)}}</td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr>
                                            <th>P.O. No</th>
                                            <td>{{$paymentDetail->purchase_order->po_no}}</td>
                                        </tr>
                                        <tr>
                                            <th>P.O. Date</th>
                                            <td>{{CommonHelper::changeDateFormat($paymentDetail->purchase_order->po_date)}}</td>
                                        </tr>
                                    @endif
                                    @if($paymentDetail->voucher_type == 2)
                                        <tr>
                                            <th>Cheque No</th>
                                            <td>{{$paymentDetail->cheque_no}}</td>
                                        </tr>
                                        <tr>
                                            <th>Cheque Date</th>
                                            <td>{{CommonHelper::changeDateFormat($paymentDetail->cheque_date)}}</td>
                                        </tr>
                                    @endif
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

                        @foreach($paymentDetail->payment_data as $key => $row)
                            <tr>
                                <td class="text-center">{{$counter++}}</td>
                                <td>{{$row->account->name}}</td>
                                <td class="text-right">{{number_format($row->debit_credit == 1 ? $row->amount : '0',0)}}</td>
                                <td class="text-right">{{number_format($row->debit_credit == 2 ? $row->amount : '0',0)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>