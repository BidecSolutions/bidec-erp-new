@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $openingBalance = $makeOpeningBalance->debitAmount - $makeOpeningBalance->creditAmount;
    if ($openingBalance < 0) {
        $openingBalance = $openingBalance * -1;
    }
    $totalDebitAmount = 0;
    $totalCreditAmount = 0;
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <strong><span>Account Name: </span>{{$filterData['accountName']}} & <span>From Date: </span>{{CommonHelper::changeDateformat($filterData['fromDate'])}} Between <span>To Date: </span>{{CommonHelper::changeDateformat($filterData['toDate'])}}</strong>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Voucher Type</th>
                        <th class="text-center" style="width:100px">Voucher No</th>
                        <th class="text-center" style="width:100px">Voucher Date</th>
                        <th class="text-center" style="width:100px">Slip No</th>
                        <th class="text-center">Particular</th>
                        <th class="text-center" style="width:100px">Debit</th>
                        <th class="text-center" style="width:100px">Credit</th>
                        <th class="text-center" style="width:100px">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="8" class="text-center">Opening Balance</th>
                        <th class="text-right">{{$openingBalance}}</th>
                    </tr>
                    @foreach($transactionList as $tlRow)
                        @php
                            // Determine the voucher type and its corresponding fields
                            $voucherType = '';
                            $voucherNo = '';
                            $voucherDate = '';
                            $slipNo = '';
                            $mainDescription = '';
                            $modelLink = '';
                            $dAmount = 0;
                            $cAmount = 0;

                            switch ($tlRow->voucher_type) {
                                case 1:
                                    $voucherType = 'J.V.';
                                    $voucherNo = $tlRow->journal_voucher_no;
                                    $voucherDate = $tlRow->journal_voucher_date;
                                    $mainDescription = $tlRow->journal_voucher_description;
                                    $modelLink = 'journalvouchers';
                                    $slipNo = $tlRow->journal_slip_no;
                                    break;
                                case 2:
                                    $voucherType = 'P.V.';
                                    $voucherNo = $tlRow->payment_voucher_no;
                                    $voucherDate = $tlRow->payment_voucher_date;
                                    $mainDescription = $tlRow->payment_voucher_description;
                                    $modelLink = 'payments';
                                    $slipNo = $tlRow->payment_slip_no;
                                    break;
                                case 3:
                                    $voucherType = 'R.V.';
                                    $voucherNo = $tlRow->receipt_voucher_no;
                                    $voucherDate = $tlRow->receipt_voucher_date;
                                    $mainDescription = $tlRow->receipt_voucher_description;
                                    $modelLink = 'receipts';
                                    $slipNo = $tlRow->receipt_slip_no;
                                    break;
                                case 4:
                                    $voucherType = 'S.J.V.';
                                    $voucherNo = $tlRow->journal_voucher_no;
                                    $voucherDate = $tlRow->journal_voucher_date;
                                    $mainDescription = $tlRow->journal_voucher_description;
                                    $modelLink = 'journalvouchers';
                                    $slipNo = $tlRow->journal_slip_no;
                                    break;
                            }
                            if($tlRow->debit_credit == 1){
                                $dAmount = $tlRow->amount;
                                $totalDebitAmount += $dAmount;
                                if($selectedAccountDetail->ledgerType == 2){
                                    $openingBalance -= $dAmount;
                                }else{
                                    $openingBalance += $dAmount;
                                }
                                
                            }

                            if($tlRow->debit_credit == 2){
                                $cAmount = $tlRow->amount;
                                $totalCreditAmount += $cAmount;
                                if($selectedAccountDetail->ledgerType == 2){
                                    $openingBalance += $cAmount;
                                }else{
                                    $openingBalance -= $cAmount;
                                }
                                
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td class="text-center">{{ $voucherType }}</td>
                            <td class="text-center" onclick="showDetailModelOneParamerter('finance/<?php echo $modelLink?>/show','<?php echo $tlRow->voucher_id;?>','View <?php echo $voucherType?> Detail')">{{ $voucherNo }}</td>
                            <td class="text-center">{{ CommonHelper::changeDateformat($voucherDate) }}</td>
                            <td class="text-center">{{ $slipNo }}</td>
                            <td>
                                @if($mainDescription != '-')
                                    {{ $mainDescription }}
                                @else
                                    {{ $tlRow->particulars }}
                                @endif
                            </td>
                            <td class="text-right">{{$dAmount}}</td>
                            <td class="text-right">{{$cAmount}}</td>
                            <td class="text-right">{{$openingBalance}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="6" class="text-center">Current Balance</th>
                        <th class="text-right">{{$totalDebitAmount}}</th>
                        <th class="text-right">{{$totalCreditAmount}}</th>
                        <th class="text-right">{{$openingBalance}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
