@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
			<?php echo CommonHelper::displayPrintButtonInBlade('PrintBalanceSheetReportSettingsList','','1');?>
		</div>
	</div>
	<div class="lineHeight">&nbsp;</div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form method="GET" action="{{ route('balance-sheet.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="lineHeight">&nbsp;</div>
	<div class="boking-wrp dp_sdw" id="PrintBalanceSheetReportSettingsList">
	    <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print">
                                {{CommonHelper::displayPageTitle('View Balance Sheet Report')}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <h4>Assets</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $filteredAssets = [];
                                        $level3Accounts = [];

                                        // Step 1: Identify Level 3 accounts
                                        foreach ($assets as $a) {
                                            $level = 0;
                                            for ($i = 1; $i <= 7; $i++) {
                                                if (!empty($a->{'level' . $i})) {
                                                    $level = $i;
                                                }
                                            }

                                            if ($level === 3) {
                                                $level3Accounts[$a->code] = $a->name;
                                            }
                                        }

                                        // Step 2: Process assets
                                        foreach ($assets as $a) {
                                            $level = 0;
                                            for ($i = 1; $i <= 7; $i++) {
                                                if (!empty($a->{'level' . $i})) {
                                                    $level = $i;
                                                }
                                            }

                                            $balance = $a->total_debit - $a->total_credit;

                                            if ($level <= 2) {
                                                // Show Level 1 & 2 accounts directly
                                                if (isset($filteredAssets[$a->code])) {
                                                    $filteredAssets[$a->code]['balance'] += $balance;
                                                } else {
                                                    $filteredAssets[$a->code] = [
                                                        'code' => $a->code,
                                                        'parent_code' => $a->parent_code,
                                                        'name' => $a->name,
                                                        'balance' => $balance,
                                                    ];
                                                }

                                            } elseif ($level === 3) {
                                                // Show Level 3 account (will also get child merged later)
                                                $filteredAssets[$a->code] = [
                                                    'code' => $a->code,
                                                    'name' => $a->name,
                                                    'balance' => $balance,
                                                    'parent_code' => $a->parent_code,
                                                ];

                                            } else {
                                                // Merge Level 4–7 into Level 3 parent
                                                $parentCode = $a->level1.'-'.$a->level2.'-'.$a->level3 ?? null;
                                                $parentName = $level3Accounts[$parentCode] ?? $parentCode;

                                                if ($parentCode) {
                                                    if (isset($filteredAssets[$parentCode])) {
                                                        $filteredAssets[$parentCode]['balance'] += $balance;
                                                    } else {
                                                        // Safety: in case Level 3 wasn't defined above
                                                        $filteredAssets[$parentCode] = [
                                                            'code' => $parentCode,
                                                            'name' => $parentName,
                                                            'balance' => $balance,
                                                            'parent_code' => $parentCode
                                                        ];
                                                    }
                                                }
                                            }
                                        }

                                        $total_assets = array_sum(array_column($filteredAssets, 'balance'));
                                        @endphp

                                        @php
                                            // Map all account codes to their names for easy lookup
                                            $codeNameMap = [];
                                            foreach ($assets as $a) {
                                                $codeNameMap[$a->code] = $a->name;
                                            }

                                            // Group filtered assets by parent_code
                                            $groupedAssets = [];
                                            foreach ($filteredAssets as $fa) {
                                                $groupedAssets[$fa['parent_code']][] = $fa;
                                            }
                                        @endphp

                                        @foreach ($groupedAssets as $parentCode => $group)
                                            @php
                                                $groupTotal = 0;
                                                $parentName = $codeNameMap[$parentCode] ?? '-';
                                            @endphp

                                            {{-- Parent heading --}}
                                            @if($parentCode != 0)
                                            <tr>
                                                <th colspan="3" class="bg-light">{{ $parentCode }} - {{ $parentName }}</th>
                                            </tr>
                                            @endif

                                            {{-- Print each child under the current parent --}}
                                            @foreach ($group as $fa)
                                                @php
                                                    $groupTotal += $fa['balance'];
                                                @endphp
                                                @if($fa['balance'] != '0.000')
                                                <tr>
                                                    <td>{{ $fa['code'] }} - {{ $fa['name'] }}</td>
                                                    <td class="text-end text-right">{{ number_format($fa['balance'], 2) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach

                                            {{-- Subtotal row --}}
                                            @if($parentCode != 0 && $groupTotal != '0.000')
                                            <tr>
                                                <th class="ps-4">Subtotal for {{ $parentCode }} - {{ $parentName }}</th>
                                                <th class="text-end text-right">{{ number_format($groupTotal, 2) }}</th>
                                            </tr>
                                            @endif
                                        @endforeach

                                        {{-- Grand total --}}
                                        <tr>
                                            <th>Total Assets</th>
                                            <th class="text-end text-right">{{ number_format($total_assets, 2) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Liabilities & Equity</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $filteredLiabilities = [];
                                            $level3Liabilities = [];
                                            $codeNameMap = [];
                                            $totalLiability = 0;

                                            // Step 1: Identify levels, names, and level 3 accounts
                                            foreach ($liabilities as $l) {
                                                $level = 0;
                                                for ($i = 1; $i <= 7; $i++) {
                                                    if (!empty($l->{'level' . $i})) {
                                                        $level = $i;
                                                    }
                                                }
                                                $l->level = $level;
                                                $codeNameMap[$l->code] = $l->name;

                                                if ($level === 3) {
                                                    $level3Liabilities[$l->code] = $l->name;
                                                }
                                            }

                                            // Step 2: Process and filter
                                            foreach ($liabilities as $l) {
                                                $balance = $l->total_credit - $l->total_debit;

                                                if ($l->level <= 2) {
                                                    if (isset($filteredLiabilities[$l->code])) {
                                                        $filteredLiabilities[$l->code]['balance'] += $balance;
                                                    } else {
                                                        $filteredLiabilities[$l->code] = [
                                                            'code' => $l->code,
                                                            'name' => $l->name,
                                                            'balance' => $balance,
                                                            'parent_code' => $l->parent_code,
                                                        ];
                                                    }
                                                } elseif ($l->level === 3) {
                                                    $filteredLiabilities[$l->code] = [
                                                        'code' => $l->code,
                                                        'name' => $l->name,
                                                        'balance' => $balance,
                                                        'parent_code' => $l->parent_code,
                                                    ];
                                                } else {
                                                    $parentCode = $l->level1 . '-' . $l->level2 . '-' . $l->level3;
                                                    $parentName = $level3Liabilities[$parentCode] ?? $parentCode;

                                                    if (!isset($filteredLiabilities[$parentCode])) {
                                                        $filteredLiabilities[$parentCode] = [
                                                            'code' => $parentCode,
                                                            'name' => $parentName,
                                                            'balance' => 0,
                                                            'parent_code' => $parentCode,
                                                        ];
                                                    }

                                                    $filteredLiabilities[$parentCode]['balance'] += $balance;
                                                }
                                            }

                                            $total_liabilities = array_sum(array_column($filteredLiabilities, 'balance'));

                                            // Group by parent_code for heading + subtotal
                                            $groupedLiabilities = [];
                                            foreach ($filteredLiabilities as $fl) {
                                                $groupedLiabilities[$fl['parent_code']][] = $fl;
                                            }
                                        @endphp

                                        <!-- Output: Show grouped liabilities with subtotals -->
                                        @foreach ($groupedLiabilities as $parentCode => $group)
                                            @php
                                                $groupTotal = 0;
                                                $parentName = $codeNameMap[$parentCode] ?? '-';
                                            @endphp

                                            <!-- Group Heading -->
                                             @if($parentCode != 0)
                                            <tr>
                                                <th colspan="2" class="bg-light">{{ $parentCode }} - {{ $parentName }}</th>
                                            </tr>
                                            @endif

                                            @foreach ($group as $fl)
                                                @php $groupTotal += $fl['balance']; @endphp
                                                @if($fl['balance'] != '0.000')
                                                <tr>
                                                    <td>{{ $fl['code'] }} - {{ $fl['name'] }}</td>
                                                    <td class="text-end text-right">{{ number_format($fl['balance'], 2) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach

                                            <!-- Subtotal per parent -->
                                             @if($parentCode != 0 && $groupTotal != '0.000')
                                            <tr>
                                                <th class="ps-4">Subtotal for {{ $parentCode }} - {{ $parentName }}</th>
                                                <th class="text-end text-right">{{ number_format($groupTotal, 2) }}</th>
                                            </tr>
                                            @endif
                                        @endforeach

                                        <!-- Grand total -->
                                        <tr>
                                            <th>Total Liabilities</th>
                                            <th class="text-end text-right">{{ number_format($total_liabilities, 2) }}</th>
                                        </tr>

                                                                                    @php
                                            $filteredEquities = [];
                                            $level3Equities = [];
                                            $codeNameMap = [];
                                            $totalEquity = 0;

                                            // Step 1: Determine level & mark Level 3 equities
                                            foreach ($equities as $e) {
                                                $level = 0;
                                                for ($i = 1; $i <= 7; $i++) {
                                                    if (!empty($e->{'level' . $i})) {
                                                        $level = $i;
                                                    }
                                                }
                                                $e->level = $level;
                                                $codeNameMap[$e->code] = $e->name;

                                                if ($level === 3) {
                                                    $level3Equities[$e->code] = $e->name;
                                                }
                                            }

                                            // Step 2: Process and categorize
                                            foreach ($equities as $e) {
                                                $balance = $e->total_credit - $e->total_debit;

                                                if ($e->level <= 2) {
                                                    if (isset($filteredEquities[$e->code])) {
                                                        $filteredEquities[$e->code]['balance'] += $balance;
                                                    } else {
                                                        $filteredEquities[$e->code] = [
                                                            'code' => $e->code,
                                                            'name' => $e->name,
                                                            'balance' => $balance,
                                                            'parent_code' => $e->parent_code,
                                                        ];
                                                    }
                                                } elseif ($e->level === 3) {
                                                    $filteredEquities[$e->code] = [
                                                        'code' => $e->code,
                                                        'name' => $e->name,
                                                        'balance' => $balance,
                                                        'parent_code' => $e->parent_code,
                                                    ];
                                                } else {
                                                    $parentCode = $e->level1 . '-' . $e->level2 . '-' . $e->level3;
                                                    $parentName = $level3Equities[$parentCode] ?? $parentCode;

                                                    if (!isset($filteredEquities[$parentCode])) {
                                                        $filteredEquities[$parentCode] = [
                                                            'code' => $parentCode,
                                                            'name' => $parentName,
                                                            'balance' => 0,
                                                            'parent_code' => $parentCode,
                                                        ];
                                                    }

                                                    $filteredEquities[$parentCode]['balance'] += $balance;
                                                }
                                            }

                                            $total_equities = array_sum(array_column($filteredEquities, 'balance'));

                                            // Group by parent_code
                                            $groupedEquities = [];
                                            foreach ($filteredEquities as $fe) {
                                                $groupedEquities[$fe['parent_code']][] = $fe;
                                            }
                                        @endphp

                                        <!-- Output: Equities by group with subtotals -->
                                        @foreach ($groupedEquities as $parentCode => $group)
                                            @php
                                                $groupTotal = 0;
                                                $parentName = $codeNameMap[$parentCode] ?? '-';
                                            @endphp

                                            <!-- Group Header -->
                                            @if($parentCode != 0)
                                            <tr>
                                                <th colspan="2" class="bg-light">{{ $parentCode }} - {{ $parentName }}</th>
                                            </tr>
                                            @endif

                                            @foreach ($group as $fe)
                                                @php $groupTotal += $fe['balance']; @endphp
                                                @if($fe['balance'] != '0.000')
                                                <tr>
                                                    <td>{{ $fe['code'] }} - {{ $fe['name'] }}</td>
                                                    <td class="text-end text-right">{{ number_format($fe['balance'], 2) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach

                                            <!-- Subtotal -->
                                            @if($parentCode != 0 && $groupTotal != '0.000')
                                            <tr>
                                                <th class="ps-4">Subtotal for {{ $parentCode }} - {{ $parentName }}</th>
                                                <th class="text-end text-right">{{ number_format($groupTotal, 2) }}</th>
                                            </tr>
                                            @endif
                                        @endforeach

                                        <!-- Grand Total -->
                                        <tr>
                                            <th>Total Equities</th>
                                            <th class="text-end text-right">{{ number_format($total_equities, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th>Profit and Loss</th>
                                            <td class="text-right">{{number_format($netProfit,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Liabilities & Equity</th>
                                            <th class="text-end text-right">{{ number_format(($total_liabilities + $total_equities) + $netProfit, 2) }}</th>
                                        </tr>
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
@endsection
