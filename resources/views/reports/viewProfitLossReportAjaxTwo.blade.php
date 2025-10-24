@php
    // Initialize counters
    $counterRev = 1;
    $counterExp = 1;

    // Aggregate product values
    $totalPurchaseQty    = $productWiseProfitLoss->sum('total_purchase_qty');
    $totalSaleQty        = $productWiseProfitLoss->sum('total_sale_qty');
    $totalPurchaseAmount = $productWiseProfitLoss->sum('total_purchase_amount');
    $totalSaleAmount     = $productWiseProfitLoss->sum('total_sale_amount');
    $totalProfitLoss     = $productWiseProfitLoss->sum('profit_loss');

    // Sum of revenues & expenses
    $sumRevenue = $revenues->sum('total_revenue');
    $sumExpense = $expenses->sum('total_expense');

    // Final Net Profit/Loss
    $netProfit = ($totalProfitLoss + $sumRevenue) - $sumExpense;
@endphp

<div class="container-fluid">

    <!-- Summary Section -->
    <div class="row g-3 mb-4">
        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                    <h6 class="fw-bold">Total Revenue</h6>
                    <h2>{{ number_format($sumRevenue, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Expenses -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-danger text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                    <h6 class="fw-bold">Total Expenses</h6>
                    <h2>{{ number_format($sumExpense, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Product Profit/Loss -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 {{ $totalProfitLoss >= 0 ? 'bg-primary text-white' : 'bg-warning text-dark' }} h-100">
                <div class="card-body text-center">
                    <i class="fas {{ $totalProfitLoss >= 0 ? 'fa-box' : 'fa-exclamation-circle' }} fa-2x mb-2"></i>
                    <h6 class="fw-bold">Product {{ $totalProfitLoss >= 0 ? 'Profit' : 'Loss' }}</h6>
                    <h2>{{ number_format($totalProfitLoss, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Net Profit/Loss -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 {{ $netProfit >= 0 ? 'bg-dark text-white' : 'bg-secondary text-white' }} h-100">
                <div class="card-body text-center">
                    <i class="fas {{ $netProfit >= 0 ? 'fa-coins' : 'fa-exclamation-circle' }} fa-2x mb-2"></i>
                    <h6 class="fw-bold">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h6>
                    <h2>{{ number_format($netProfit, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Chart -->
    <div class="text-end mb-3">
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#plChart">
            <i class="fas fa-chart-pie"></i> Show Chart
        </button>
    </div>

    <div class="collapse mb-4" id="plChart">
        <div class="card shadow-sm">
            <div class="card-body">
                <canvas id="plChartCanvas" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Product Wise Profit/Loss -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong><i class="fas fa-box"></i> Product Wise Profit & Loss</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Product Name</th>
                            <th class="text-end">Avg Purchase Rate</th>
                            <th class="text-end">Avg Sale Rate</th>
                            <th class="text-end">Purchase Qty</th>
                            <th class="text-end">Sale Qty</th>
                            <th class="text-end">Total Purchase</th>
                            <th class="text-end">Total Sale</th>
                            <th class="text-end">Profit/Loss</th>
                            <th class="text-end">Profit/Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productWiseProfitLoss as $index => $pl)
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td>{{ $pl->name }}</td>
                                <td class="text-end">{{ number_format($pl->avg_purchase_rate, 2) }}</td>
                                <td class="text-end">{{ number_format($pl->avg_sale_rate, 2) }}</td>
                                <td class="text-end">{{ $pl->total_purchase_qty }}</td>
                                <td class="text-end">{{ $pl->total_sale_qty }}</td>
                                <td class="text-end">{{ number_format($pl->total_purchase_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($pl->total_sale_amount, 2) }}</td>
                                <td class="text-end {{ $pl->profit_loss >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($pl->profit_loss, 2) }}
                                </td>
                                <td class="text-end">{{ number_format($pl->profit_per_unit, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No product sale records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($productWiseProfitLoss->count() > 0)
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end">{{ $totalPurchaseQty }}</th>
                                <th class="text-end">{{ $totalSaleQty }}</th>
                                <th class="text-end">{{ number_format($totalPurchaseAmount, 2) }}</th>
                                <th class="text-end">{{ number_format($totalSaleAmount, 2) }}</th>
                                <th class="text-end {{ $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totalProfitLoss, 2) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-arrow-up text-success"></i> Revenue</strong>
            <span class="badge bg-success">Total: {{ number_format($sumRevenue, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Account Name</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($revenues as $rev)
                            <tr>
                                <td class="text-center">{{ $counterRev++ }}</td>
                                <td>{{ $rev->name }}</td>
                                <td class="text-end text-success">{{ number_format($rev->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No revenue records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expense Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-arrow-down text-danger"></i> Expenses</strong>
            <span class="badge bg-danger">Total: {{ number_format($sumExpense, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Account Name</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $exp)
                            <tr>
                                <td class="text-center">{{ $counterExp++ }}</td>
                                <td>{{ $exp->name }}</td>
                                <td class="text-end text-danger">{{ number_format($exp->total_expense, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No expense records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Final Net Profit / Loss -->
    <div class="card shadow-sm border-0 {{ $netProfit >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
        <div class="card-body text-center py-4">
            <h3 class="fw-bold">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h3>
            <h1 class="display-5">{{ number_format($netProfit, 2) }}</h1>
            <p class="mb-0">Period: <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong></p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('plChartCanvas').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Product Profit/Loss', 'Revenue', 'Expenses'],
            datasets: [{
                data: [{{ $totalProfitLoss }}, {{ $sumRevenue }}, {{ $sumExpense }}],
                backgroundColor: ['#0d6efd', '#198754', '#dc3545']
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush
