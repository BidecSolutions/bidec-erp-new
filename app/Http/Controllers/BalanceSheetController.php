<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;
use Session;

class BalanceSheetController extends Controller
{

    public function create(){
        $company_id = session('company_id');
        $company_location_id = session('company_location_id');
        $mainAccountsList = DB::table('chart_of_accounts')->where('parent_code',0)->where('company_id',$company_id)->where('company_location_id',$company_location_id)->where('status',1)->get();
        return view('balance-sheet-report-settings.create',compact('mainAccountsList'));
    }

    public function balanceSheetReportSettingStore(Request $request){
        // Validation rules
        $validator = Validator::make($request->all(), [
            'acc_id'   => 'required|array',
            'acc_type' => 'required|array',
            'acc_id.*'   => 'required|integer|distinct',
            'acc_type.*' => 'required|integer|in:0,1,2,3', // Assuming acc_type must be 0,1,2 or 3
        ]);

        // Validation failed
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $accIds = $request->input('acc_id');
        $accTypes = $request->input('acc_type');

        // Ensure both arrays are the same length
        if (count($accIds) !== count($accTypes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'acc_id and acc_type array counts do not match.'
            ], 422);
        }

        // Save to DB
        foreach ($accIds as $index => $accId) {
            // Skip if acc_type is 0
            if ((int)$accTypes[$index] === 0) {
                continue;
            }
        
            DB::table('balance_sheet_report_settings')->updateOrInsert(
                ['acc_id' => $accId],
                [
                    'acc_type' => $accTypes[$index],
                    'created_by' => Auth::user()->name,
                    'created_date' => now()->toDateString(),
                    'company_id' => Session::get('company_id'),
                    'company_location_id' => Session::get('company_location_id')
                ]
            );
        }

        return view('balance-sheet-report-settings.index');
    }

    public function balanceSheetReportSettingIndex(Request $request){
        if($request->ajax()){
            $balanceSheetReportSettingsList =  DB::table('balance_sheet_report_settings as bsrs')
                ->join('chart_of_accounts as coa','bsrs.acc_id','=','coa.id')
                ->select('bsrs.*','coa.name')
                ->where('bsrs.company_id',Session::get('company_id'))
                ->where('bsrs.company_location_id',Session::get('company_location_id'))
                ->get();

            return view('balance-sheet-report-settings.indexAjax', compact('balanceSheetReportSettingsList'));
        }
        return view('balance-sheet-report-settings.index');
    }

    public function index(Request $request)
    {
        $company_id = session('company_id');
        $company_location_id = session('company_location_id');

        $from = $request->input('from') ?? Carbon::now()->startOfYear()->toDateString();
        $to = $request->input('to') ?? Carbon::now()->toDateString();
        // Fetch all relevant acc codes (joined with chart_of_accounts)
        $settings = DB::table('balance_sheet_report_settings as bsrs')
            ->join('chart_of_accounts as coa', 'bsrs.acc_id', '=', 'coa.id')
            ->where('bsrs.company_id', $company_id)
            ->where('bsrs.company_location_id', $company_location_id)
            ->whereIn('bsrs.acc_type', [1, 2, 3])
            ->select('bsrs.*', 'coa.code')
            ->get()
            ->groupBy('acc_type');

        $assetCodes     = isset($settings[1]) ? $settings[1]->pluck('code')->toArray() : [];
        $liabilityCodes = isset($settings[2]) ? $settings[2]->pluck('code')->toArray() : [];
        $equityCodes    = isset($settings[3]) ? $settings[3]->pluck('code')->toArray() : [];

        // Fetch all relevant acc codes (joined with chart_of_accounts)
        $settingsTwo = DB::table('profit_and_loss_report_settings as bsrs')
            ->join('chart_of_accounts as coa', 'bsrs.acc_id', '=', 'coa.id')
            ->where('bsrs.company_id', $company_id)
            ->where('bsrs.company_location_id', $company_location_id)
            ->whereIn('bsrs.acc_type', [1, 2])
            ->select('bsrs.*', 'coa.code')
            ->get()
            ->groupBy('acc_type');

        $revenueCodes = isset($settingsTwo[1]) ? $settingsTwo[1]->pluck('code')->toArray() : [];
        $expenseCodes = isset($settingsTwo[2]) ? $settingsTwo[2]->pluck('code')->toArray() : [];


        // --- Revenue Accounts (Assuming revenue accounts have codes starting with "4") ---
        $revenues = DB::table('transaction as t')
            ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
            ->select(
                'coa.id',
                'coa.name',
                // For revenue transactions, assuming credits increase revenue
                DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_revenue')
            )
            ->where('t.company_id', $company_id)
            ->whereBetween('t.v_date', [$from, $to])
            ->where(function($query) use ($revenueCodes) {
                foreach ($revenueCodes as $code) {
                    $query->orWhere('coa.level1', 'like', $code);
                }
            })
            ->groupBy('coa.id', 'coa.name')
            ->orderBy('coa.code', 'ASC')
            ->get();

        // --- Expense Accounts (Assuming expense accounts have codes starting with "5") ---
        $expenses = DB::table('transaction as t')
            ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
            ->select(
                'coa.id',
                'coa.name',
                // For expense transactions, debits increase expense
                DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as total_expense')
            )
            ->where('t.company_id', $company_id)
            ->whereBetween('t.v_date', [$from, $to])
            ->where(function($query) use ($expenseCodes) {
                foreach ($expenseCodes as $code) {
                    $query->orWhere('coa.level1', 'like', $code);
                }
            })
            ->groupBy('coa.id', 'coa.name')
            ->orderBy('coa.code', 'ASC')
            ->get();
        
        $totalRevenue = $revenues->sum('total_revenue');
        $totalExpense = $expenses->sum('total_expense');
        $netProfit = $totalRevenue - $totalExpense;

        $assets     = $this->getAccountsWithBalance($company_id, $company_location_id, $assetCodes, $from, $to);
        $liabilities = $this->getAccountsWithBalance($company_id, $company_location_id, $liabilityCodes, $from, $to);
        $equities    = $this->getAccountsWithBalance($company_id, $company_location_id, $equityCodes, $from, $to);

        return view('reports.balance-sheet.index', compact('assets', 'liabilities', 'equities', 'from', 'to','netProfit'));
    }

    private function getAccountsWithBalance($company_id, $company_location_id, array $level1Ids, $from, $to)
    {
        return DB::table('chart_of_accounts as coa')
            ->leftJoin('transaction as t', function ($join) use ($from, $to) {
                $join->on('coa.id', '=', 't.acc_id')
                    ->whereBetween('t.v_date', [$from, $to])
                    ->where('t.status', 1);
            })
            ->select(
                'coa.id',
                'coa.code',
                'coa.name',
                'coa.level1',
                'coa.level2',
                'coa.level3',
                'coa.level4',
                'coa.level5',
                'coa.level6',
                'coa.level7',
                'coa.parent_code',
                DB::raw("SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as total_debit"),
                DB::raw("SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_credit")
            )
            ->where('coa.company_id', $company_id)
            ->where('coa.company_location_id', $company_location_id)
            ->where('coa.status', 1)
            ->whereIn('coa.level1', $level1Ids)
            ->groupBy('coa.id', 'coa.code', 'coa.name','coa.level1','coa.level2','coa.level2','coa.level3','coa.level4','coa.level5','coa.level6','coa.level7','coa.parent_code')
            ->orderBy('coa.code', 'ASC')
            ->get();
    }
}
