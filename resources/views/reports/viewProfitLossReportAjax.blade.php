@php
    // Initialize counters
    $counterRev = 1;
    $counterExp = 1;
    $counterSal = 1;
    $counterCOGS = 1;

    // Totals
    $sumRevenue = $revenues->sum('total_revenue');
    $sumExpense = $expenses->sum('total_expense');
    $sumCOGS = $cogs->sum('total_cogs');
    $sumSale = $sales->sum('total_sale');

    // Gross Profit & Net Profit
    $grossProfit = $sumSale - $sumCOGS;
    $netProfit   = ($sumRevenue + $grossProfit) - $sumExpense;
@endphp

<div class="container-fluid">

    <!-- Summary Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card summary-card revenue h-100">
                <div class="card-body text-center">
                    <i class="fas fa-sack-dollar fa-2x mb-2"></i>
                    <h6>Total Revenue</h6>
                    <h2 class="fw-bold">{{ number_format($sumRevenue, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card expense h-100">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                    <h6>Total Expenses</h6>
                    <h2 class="fw-bold">{{ number_format($sumExpense, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card {{ $grossProfit >= 0 ? 'profit' : 'loss' }} h-100">
                <div class="card-body text-center">
                    <i class="fas {{ $grossProfit >= 0 ? 'fa-chart-line' : 'fa-exclamation-triangle' }} fa-2x mb-2"></i>
                    <h6>Gross {{ $grossProfit >= 0 ? 'Profit' : 'Loss' }}</h6>
                    <h2 class="fw-bold">{{ number_format($grossProfit, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card {{ $netProfit >= 0 ? 'profit' : 'loss' }} h-100">
                <div class="card-body text-center">
                    <i class="fas {{ $netProfit >= 0 ? 'fa-coins' : 'fa-exclamation-circle' }} fa-2x mb-2"></i>
                    <h6>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h6>
                    <h2 class="fw-bold">{{ number_format($netProfit, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-pie text-primary"></i> Profit & Loss Breakdown</span>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#plChart">
                Toggle Chart
            </button>
        </div>
        <div class="collapse show" id="plChart">
            <div class="card-body">
                <canvas id="plChartCanvas" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-arrow-up text-success"></i> Sales</strong>
            <span class="badge bg-success">Total: {{ number_format($sumSale, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Account Name</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sal)
                            <tr>
                                <td class="text-center">{{ $counterSal++ }}</td>
                                <td>{{ $sal->name }}</td>
                                <td class="text-end text-success">{{ number_format($sal->total_sale, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No sale records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- COGS Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-boxes text-warning"></i> COGS</strong>
            <span class="badge bg-warning">Total: {{ number_format($sumCOGS, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Account Name</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cogs as $c)
                            <tr>
                                <td class="text-center">{{ $counterCOGS++ }}</td>
                                <td>{{ $c->name }}</td>
                                <td class="text-end text-warning">{{ number_format($c->total_cogs, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No COGS records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-12">
            <div class="card final-card {{ $grossProfit >= 0 ? 'profit' : 'loss' }}">
                <div class="card-body text-center py-4">
                    <h3 class="fw-bold">Gross {{ $grossProfit >= 0 ? 'Profit' : 'Loss' }}</h3>
                    <h1 class="display-5 fw-bold">{{ number_format($grossProfit, 2) }}</h1>
                    <p class="mb-0">Period: <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-credit-card text-danger"></i> Expenses</strong>
            <span class="badge bg-danger">Total: {{ number_format($sumExpense, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark sticky-top">
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
                            <tr><td colspan="3" class="text-center text-muted">No expense records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <strong><i class="fas fa-hand-holding-usd text-primary"></i> Revenue</strong>
            <span class="badge bg-primary">Total: {{ number_format($sumRevenue, 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-dark sticky-top">
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
                            <tr><td colspan="3" class="text-center text-muted">No revenue records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Final Gross & Net -->
    <div class="row g-3">
        <div class="col-md-12">
            <div class="card final-card {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                <div class="card-body text-center py-4">
                    <h3 class="fw-bold">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h3>
                    <h1 class="display-5 fw-bold">{{ number_format($netProfit, 2) }}</h1>
                    <p class="mb-0">Period: <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .summary-card {
        border: none;
        border-radius: 1rem;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        transition: transform .2s;
    }
    .summary-card:hover { transform: translateY(-5px); }
    .summary-card.revenue { background: linear-gradient(135deg,#28a745,#20c997); }
    .summary-card.expense { background: linear-gradient(135deg,#dc3545,#ff6b6b); }
    .summary-card.profit  { background: linear-gradient(135deg,#198754,#20c997); }
    .summary-card.loss    { background: linear-gradient(135deg,#dc3545,#ff6b6b); }
    .final-card {
        border-radius: 1rem;
        margin-top: 2rem;
        box-shadow: 0 4px 14px rgba(0,0,0,.2);
        animation: fadeIn 1s ease-in-out;
        color: #fff;
    }
    .final-card.profit { background: linear-gradient(135deg,#28a745,#20c997); }
    .final-card.loss   { background: linear-gradient(135deg,#dc3545,#ff6b6b); }
    @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('plChartCanvas').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Revenue','Expenses','COGS','Gross Profit','Net Profit'],
            datasets: [{
                data: [
                    {{ $sumRevenue }},
                    {{ $sumExpense }},
                    {{ $sumCOGS }},
                    {{ $grossProfit }},
                    {{ $netProfit }}
                ],
                backgroundColor: ['#0d6efd','#dc3545','#ffc107','#20c997','#6f42c1'],
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ctx.label + ': ' + ctx.formattedValue
                    }
                }
            }
        }
    });
</script>
@endpush
