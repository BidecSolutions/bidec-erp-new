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
                        <th class="text-center">Debit</th>
                        <th class="text-center">Credit</th>
                        <th class="text-center">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($viewPayableSummaryList as $vpslRow)
                        @php
                            $totalAmount += ($vpslRow->creditAmount - $vpslRow->debitAmount);
                            $param = $vpslRow->acc_id.'<*>'.$fromDate.'<*>'.$toDate.'<*>'.$vpslRow->account_name;
                        @endphp
                        
                        <tr>
                            <td class="text-center">{{$counter++}}</td>
                            <td onclick="showDetailModelOneParamerter('reports/viewAccountWisePayableSummary','<?php echo $param;?>','View <?php echo $vpslRow->account_name?> Ledger Summary')">{{ $vpslRow->account_name }}</td>
                            <td class="text-right">{{$vpslRow->debitAmount}}</td>
                            <td class="text-right">{{$vpslRow->creditAmount}}</td>
                            <td class="text-right">{{$vpslRow->creditAmount - $vpslRow->debitAmount}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="4">Total</th>
                        <th class="text-right">{{$totalAmount}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
