@php
    $counter = 1;
    $totalAmount = 0;
    $fromDate = $filterData['fromDate'];
    $toDate = $filterData['toDate'];
    use App\Helpers\CommonHelper;
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <strong><span>From Date: </span>{{CommonHelper::changeDateFormat($fromDate)}} Between <span>To Date: </span>{{CommonHelper::changeDateFormat($toDate)}}</strong>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Account Name</th>
                        <th class="text-center hide">Debit</th>
                        <th class="text-center hide">Credit</th>
                        <th class="text-center">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($viewReceivableSummaryList as $vrslRow)
                        @php
                            $totalAmount += ($vrslRow->debitAmount - $vrslRow->creditAmount);
                            $param = $vrslRow->acc_id.'<*>'.$fromDate.'<*>'.$toDate.'<*>'.$vrslRow->account_name;
                        @endphp
                        
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td onclick="showDetailModelOneParamerter('reports/viewAccountWiseReceivableSummary','<?php echo $param;?>','View <?php echo $vrslRow->account_name?> Ledger Summary')">{{ $vrslRow->account_name }}</td>
                            <td class="text-right hide">{{$vrslRow->debitAmount}}</td>
                            <td class="text-right hide">{{$vrslRow->creditAmount}}</td>
                            <td class="text-right">{{$vrslRow->debitAmount - $vrslRow->creditAmount}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">{{$totalAmount}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
