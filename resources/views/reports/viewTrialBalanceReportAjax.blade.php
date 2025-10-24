@php
    $counter = 1;
    use App\Helpers\CommonHelper;
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <strong><span>Account Name: </span>{{ $filterData['accountName'] }} & <span>From Date:
            </span>{{ CommonHelper::changeDateFormat($filterData['fromDate']) }} Between <span>To Date: </span>{{ CommonHelper::changeDateFormat($filterData['toDate']) }}</strong>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th class="text-center">Account Name</th>
                <th class="text-center">Total Debit</th>
                <th class="text-center">Total Credit</th>
                <th class="text-center">Net Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalDebit = 0;
                $grandTotalCredit = 0;
            @endphp
            @foreach ($trialBalance as $tb)
                @php
                    $accountName = isset($accounts[$tb->acc_id]) ? $accounts[$tb->acc_id] : 'N/A';
                    $netBalance = $tb->total_debit - $tb->total_credit;
                    $grandTotalDebit += $tb->total_debit;
                    $grandTotalCredit += $tb->total_credit;
                @endphp
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>{{ $accountName }}</td>
                    <td class="text-right">{{ number_format($tb->total_debit, 2) }}</td>
                    <td class="text-right">{{ number_format($tb->total_credit, 2) }}</td>
                    <td class="text-right">{{ number_format($netBalance, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-center"><strong>Grand Total</strong></td>
                <td class="text-right"><strong>{{ number_format($grandTotalDebit, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($grandTotalCredit, 2) }}</strong></td>
                <td class="text-right">
                    <strong>{{ number_format($grandTotalDebit - $grandTotalCredit, 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
</div>
