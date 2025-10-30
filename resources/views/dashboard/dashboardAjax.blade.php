@php
    $summaries = [
        ['icon'=>'bi-clock-history','title'=>'Pending Payments','count'=>$pendingPayment->total_count ?? 0,'amount'=>number_format($pendingPayment->total_amount ?? 0,2),'color'=>'#f59e0b'],
        ['icon'=>'bi-check-circle','title'=>'Approved Payments','count'=>$approvedPayment->total_count ?? 0,'amount'=>number_format($approvedPayment->total_amount ?? 0,2),'color'=>'#16a34a'],
        ['icon'=>'bi-hourglass-split','title'=>'Pending Receipts','count'=>$pendingReceipt->total_count ?? 0,'amount'=>number_format($pendingReceipt->total_amount ?? 0,2),'color'=>'#f97316'],
        ['icon'=>'bi-bag-check','title'=>'Approved Receipts','count'=>$approvedReceipt->total_count ?? 0,'amount'=>number_format($approvedReceipt->total_amount ?? 0,2),'color'=>'#2563eb'],
        ['icon'=>'bi-cart-check','title'=>'Purchase Invoices','count'=>$purchaseSummary->total_count ?? 0,'amount'=>number_format($purchaseSummary->total_amount ?? 0,2),'color'=>'#0891b2','remaining'=>number_format($purchaseSummary->total_remaining ?? 0,2)],
        ['icon'=>'bi-receipt','title'=>'Sales Invoices','count'=>$salesSummary->total_count ?? 0,'amount'=>number_format($salesSummary->total_amount ?? 0,2),'color'=>'#7c3aed','remaining'=>number_format($salesSummary->total_remaining ?? 0,2)],
    ];
@endphp

<style>
.dashboard-summary{background:#f9fafb;padding:25px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.dashboard-header{text-align:center;margin-bottom:30px}
.dashboard-header h4{font-weight:700;color:#1e3a8a}
.dashboard-section-title{font-size:18px;font-weight:600;color:#334155;border-left:4px solid #2563eb;padding-left:10px;margin-bottom:20px}
.dashboard-card{background:linear-gradient(135deg,#fff,#f1f5f9);border-radius:12px;box-shadow:0 3px 8px rgba(0,0,0,0.08);padding:20px 25px;display:flex;align-items:center;justify-content:space-between;position:relative;transition:.2s}
.dashboard-card:hover{transform:translateY(-4px);box-shadow:0 6px 14px rgba(37,99,235,0.2)}
.dashboard-card::before{content:"";position:absolute;top:0;left:0;width:6px;height:100%;background:var(--accent-color);border-radius:12px 0 0 12px}
.dashboard-icon{font-size:32px;margin-right:10px;opacity:0.9}
.dashboard-title{font-weight:600;color:#334155;font-size:16px}
.dashboard-value{font-size:20px;font-weight:700;color:#1e40af;margin:0;text-align:right}
.dashboard-subvalue{font-size:14px;color:#475569;font-weight:500}
@media(max-width:767px){.dashboard-card{flex-direction:column;align-items:flex-start}.dashboard-value{text-align:left;margin-top:8px}}
</style>

<div class="col-12">
  <div class="dashboard-summary">
    <div class="dashboard-header">
      <h4>📊 Dashboard Summary</h4>
      <p class="text-muted mb-1">
        <strong>From:</strong> {{ $fromDate }} &nbsp; | &nbsp; 
        <strong>To:</strong> {{ $toDate }}
      </p>
    </div>

    <h5 class="dashboard-section-title">💰 Financial Overview</h5>
    <div class="row g-4">
      @foreach ($summaries as $summary)
        <div class="col-md-6 col-lg-4">
          <div class="dashboard-card" style="--accent-color: {{ $summary['color'] }}">
            <div class="d-flex align-items-center">
              <i class="bi {{ $summary['icon'] }} dashboard-icon" style="color: {{ $summary['color'] }}"></i>
              <h6 class="dashboard-title">{{ $summary['title'] }}</h6>
            </div>
            <div class="text-end">
              <p class="dashboard-value">{{ $summary['count'] }}</p>
              <p class="dashboard-subvalue">
                {{ $summary['amount'] }}
                @isset($summary['remaining'])
                  <br><small class="text-secondary">Remaining: {{ $summary['remaining'] }}</small>
                @endisset
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <hr class="my-4">

    <div class="row g-3 mb-4">
      <div class="col-md-3"><div class="card h-100 text-center p-3"><h6>Total Revenue</h6><h2>{{ number_format($sumRevenue,2) }}</h2></div></div>
      <div class="col-md-3"><div class="card h-100 text-center p-3"><h6>Total Expense</h6><h2>{{ number_format($sumExpense,2) }}</h2></div></div>
      <div class="col-md-3"><div class="card h-100 text-center p-3"><h6>Gross Profit</h6><h2>{{ number_format($grossProfit,2) }}</h2></div></div>
      <div class="col-md-3"><div class="card h-100 text-center p-3"><h6>Net Profit</h6><h2>{{ number_format($netProfit,2) }}</h2></div></div>
    </div>
    <!-- 💳 Expense Breakdown -->
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
      <strong><i class="fas fa-credit-card text-danger"></i> Expenses</strong>
      <span class="badge bg-danger fs-6">Total: {{ number_format($sumExpense ?? 0, 2) }}</span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped table-sm mb-0 align-middle">
          <thead class="table-dark">
            <tr>
              <th class="text-center" style="width: 60px;">#</th>
              <th>Account Name</th>
              <th class="text-end">Amount (PKR)</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($expenses as $index => $exp)
              <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $exp->name }}</td>
                <td class="text-end text-danger fw-semibold">{{ number_format($exp->total, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">No expense records found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
