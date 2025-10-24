<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CashFlowStatementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id');
        $locationId = session('company_location_id');

        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Get cash/bank accounts
        $cashBankAccounts = DB::table('chart_of_account_settings')
            ->where('company_id', $companyId)
            ->where('company_location_id', $locationId)
            ->whereIn('option_id', [4, 5]) // 4 = Bank, 5 = Cash
            ->where('status', 1)
            ->pluck('acc_id');

        // Log cash/bank accounts to check the retrieved values
        Log::info('Cash/Bank Accounts:', $cashBankAccounts->toArray());

        // 2. Get transactions within the date range
        $transactions = DB::table('transaction')
            ->where('company_id', $companyId)
            ->whereBetween('v_date', [$fromDate, $toDate])
            ->where('status', 1)
            ->get();

        // Log the retrieved transactions
        Log::info('Transactions:', $transactions->toArray());

        // 3. Classify cash flows

        // Operating Inflows
        $operatingInflows = $transactions->whereIn('acc_id', $cashBankAccounts)
            ->where('debit_credit', 1) // Inflow
            ->whereIn('voucher_type', [1, 3]) // Journal or Receipts
            ->sum('amount');

        // Log operating inflows
        Log::info('Operating Inflows:', [$operatingInflows]);

        // Operating Outflows
        $operatingOutflows = $transactions->whereIn('acc_id', $cashBankAccounts)
            ->where('debit_credit', 2) // Outflow
            ->whereIn('voucher_type', [1, 2]) // Journal or Payments
            ->sum('amount');

        // Log operating outflows
        Log::info('Operating Outflows:', [$operatingOutflows]);

        $investingInflows = 0; // Placeholder — depends on account classification
        $investingOutflows = 0;

        $financingInflows = 0;
        $financingOutflows = 0;

        // Placeholder: In a real case, you classify chart_of_accounts based on purpose (Asset/Loan/Equity)
        // and check if transactions involve those accounts to determine investing/financing flows.

        $netCashFlow = ($operatingInflows - $operatingOutflows)
            + ($investingInflows - $investingOutflows)
            + ($financingInflows - $financingOutflows);

        // Log net cash flow calculation
        Log::info('Net Cash Flow:', [$netCashFlow]);

        return view('reports.cash-flow-statement.index', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'operatingInflows' => $operatingInflows,
            'operatingOutflows' => $operatingOutflows,
            'investingInflows' => $investingInflows,
            'investingOutflows' => $investingOutflows,
            'financingInflows' => $financingInflows,
            'financingOutflows' => $financingOutflows,
            'netCashFlow' => $netCashFlow,
        ]);
    }
}
