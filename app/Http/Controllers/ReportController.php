<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChartOfAccount;
use App\Models\PayableAndReceivableReportSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    protected $page;
    public function __construct()
    {
        $this->page = 'reports.';
    }

    public function viewReceivableReport(Request $request){
        if($request->ajax()){
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $filterData = [
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ];

            $receivableSettingDetail = PayableAndReceivableReportSetting::where('company_id', $companyId)
                ->where('company_location_id', $companyLocationId)
                ->where('option_id', 2)
                ->first();

            $receivableAccountId = $receivableSettingDetail ? $receivableSettingDetail->acc_id : null;
            $receivableAccountDetail = DB::table('chart_of_accounts')
                ->where('id',$receivableAccountId)
                ->where('company_id',$companyId)
                ->where('company_location_id',$companyLocationId)
                ->first();

            $viewReceivableSummaryList = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    't.acc_id',
                    'coa.name as account_name',
                    DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as debitAmount'),
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as creditAmount')
                )
                ->where('coa.parent_code', $receivableAccountDetail->code)
                ->where('t.v_date', '<', $toDate)
                ->where('t.company_id',$companyId)
                ->where('t.company_location_id',$companyLocationId)
                ->groupBy('t.acc_id', 'coa.name')
                ->havingRaw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) != SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END)')
                ->get();


            
            return view($this->page . 'viewReceivableReportAjax',compact('viewReceivableSummaryList','filterData'));
        }

        return view($this->page . 'viewReceivableReport');
    }

    public function viewPayableReport(Request $request){
        if($request->ajax()){
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $filterData = [
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ];

            $payableSettingDetail = PayableAndReceivableReportSetting::where('company_id', $companyId)
                ->where('company_location_id', $companyLocationId)
                ->where('option_id', 1)
                ->first();

            $payableAccountId = $payableSettingDetail ? $payableSettingDetail->acc_id : null;
            $payableAccountDetail = DB::table('chart_of_accounts')->where('id',$payableAccountId)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->first();

            $viewPayableSummaryList = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    't.acc_id',
                    'coa.name as account_name',
                    DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as debitAmount'),
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as creditAmount')
                )
                ->where('coa.parent_code', $payableAccountDetail->code)
                ->where('t.v_date', '<', $toDate)
                ->where('t.company_id',$companyId)
                ->where('t.company_location_id',$companyLocationId)
                ->groupBy('t.acc_id', 'coa.name')
                ->havingRaw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) != SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END)')
                ->get();


            
            return view($this->page . 'viewPayableReportAjax',compact('viewPayableSummaryList','filterData'));
        }

        return view($this->page . 'viewPayableReport');
    }

    public function viewAccountWiseReceivableSummary(Request $request){
        $param = $request->input('id');
        $parts = explode('<*>', $param);
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        // Access the values
        $accId = $parts[0];
        $fromDate = $parts[1];
        $toDate = $parts[2];
        $accountName = $parts[3];

        $filterData = [
            'accountName' => $accountName,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ];

        $selectedAccountDetail = DB::table('chart_of_accounts as coa')
            ->select('coa.*')
            ->selectSub(function ($query) {
                $query->from('chart_of_accounts as coa2')
                    ->select('coa2.ledger_type')
                    ->whereColumn('coa.level1', 'coa2.code')
                    ->limit(1);
            }, 'ledgerType')
            ->where('coa.id', $accId)
            ->first();

        // Prepare the opening balance query
        $makeOpeningBalance = DB::table('transaction')
            ->selectRaw(
                'SUM(CASE WHEN debit_credit = 1 THEN amount ELSE 0 END) as debitAmount,' .
                'SUM(CASE WHEN debit_credit = 2 THEN amount ELSE 0 END) as creditAmount'
            )
            ->where('acc_id', $accId)
            ->where('v_date', '<', $fromDate)
            ->first();

        Log::info(json_encode($makeOpeningBalance));
        // Prepare the transaction list query
        $transactionList = DB::table('transaction as t')
            ->leftJoin('payments as p', function ($join) {
                $join->on('t.voucher_id', '=', 'p.id')
                    ->where('t.voucher_type', '=', 2);
            })
            ->leftJoin('receipts as r', function ($join) {
                $join->on('t.voucher_id', '=', 'r.id')
                    ->where('t.voucher_type', '=', 3);
            })
            ->leftJoin('journal_vouchers as jv', function ($join) {
                $join->on('t.voucher_id', '=', 'jv.id')
                    ->whereIn('t.voucher_type', [1, 4]); // Correct way
            })
            ->select(
                't.*',
                'p.pv_no as payment_voucher_no',
                'p.pv_date as payment_voucher_date',
                'p.description as payment_voucher_description',
                'p.slip_no as payment_slip_no',
                'r.rv_no as receipt_voucher_no',
                'r.rv_date as receipt_voucher_date',
                'r.description as receipt_voucher_description',
                'r.slip_no as receipt_slip_no',
                'jv.jv_no as journal_voucher_no',
                'jv.jv_date as journal_voucher_date',
                'jv.description as journal_voucher_description',
                'jv.slip_no as journal_slip_no'
            )
            ->where('t.acc_id', $accId)
            ->whereBetween('t.v_date', [$fromDate, $toDate])
            ->OrderBy('t.v_date','ASC')
            ->get();

        Log::info(json_encode($transactionList));

        return view($this->page . 'viewLedgerReportAjax', compact('makeOpeningBalance', 'transactionList','selectedAccountDetail','filterData'));
    }

    public function viewAccountWisePayableSummary(Request $request){
        $param = $request->input('id');
        $parts = explode('<*>', $param);
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        // Access the values
        $accId = $parts[0];
        $fromDate = $parts[1];
        $toDate = $parts[2];
        $accountName = $parts[3];

        $filterData = [
            'accountName' => $accountName,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ];

        $selectedAccountDetail = DB::table('chart_of_accounts as coa')
            ->select('coa.*')
            ->selectSub(function ($query) {
                $query->from('chart_of_accounts as coa2')
                    ->select('coa2.ledger_type')
                    ->whereColumn('coa.level1', 'coa2.code')
                    ->limit(1);
            }, 'ledgerType')
            ->where('coa.id', $accId)
            ->first();

        // Prepare the opening balance query
        $makeOpeningBalance = DB::table('transaction')
            ->selectRaw(
                'SUM(CASE WHEN debit_credit = 1 THEN amount ELSE 0 END) as debitAmount,' .
                'SUM(CASE WHEN debit_credit = 2 THEN amount ELSE 0 END) as creditAmount'
            )
            ->where('acc_id', $accId)
            ->where('v_date', '<', $fromDate)
            ->first();

        Log::info(json_encode($makeOpeningBalance));
        // Prepare the transaction list query
        $transactionList = DB::table('transaction as t')
            ->leftJoin('payments as p', function ($join) {
                $join->on('t.voucher_id', '=', 'p.id')
                    ->where('t.voucher_type', '=', 2);
            })
            ->leftJoin('receipts as r', function ($join) {
                $join->on('t.voucher_id', '=', 'r.id')
                    ->where('t.voucher_type', '=', 3);
            })
            ->leftJoin('journal_vouchers as jv', function ($join) {
                $join->on('t.voucher_id', '=', 'jv.id')
                    ->whereIn('t.voucher_type', [1, 4]); // Correct way
            })
            ->select(
                't.*',
                'p.pv_no as payment_voucher_no',
                'p.pv_date as payment_voucher_date',
                'p.description as payment_voucher_description',
                'r.rv_no as receipt_voucher_no',
                'r.rv_date as receipt_voucher_date',
                'r.description as receipt_voucher_description',
                'jv.jv_no as journal_voucher_no',
                'jv.jv_date as journal_voucher_date',
                'jv.description as journal_voucher_description'
            )
            ->where('t.acc_id', $accId)
            ->whereBetween('t.v_date', [$fromDate, $toDate])
            ->OrderBy('t.v_date','ASC')
            ->get();

        Log::info(json_encode($transactionList));

        return view($this->page . 'viewLedgerReportAjax', compact('makeOpeningBalance', 'transactionList','selectedAccountDetail','filterData'));
    }

    public function viewLedgerReport(Request $request)
    {
        if ($request->ajax()) {
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $accId = $request->input('acc_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $accountName = $request->input('account_name');

            $filterData = [
                'accountName' => $accountName,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ];

            $selectedAccountDetail = DB::table('chart_of_accounts as coa')
                ->select('coa.*')
                ->selectSub(function ($query) {
                    $query->from('chart_of_accounts as coa2')
                        ->select('coa2.ledger_type')
                        ->whereColumn('coa.level1', 'coa2.code')
                        ->limit(1);
                }, 'ledgerType')
                ->where('coa.id', $accId)
                ->first();

            // Prepare the opening balance query
            $makeOpeningBalance = DB::table('transaction')
                ->selectRaw(
                    'SUM(CASE WHEN debit_credit = 1 THEN amount ELSE 0 END) as debitAmount,' .
                    'SUM(CASE WHEN debit_credit = 2 THEN amount ELSE 0 END) as creditAmount'
                )
                ->where('acc_id', $accId)
                ->where('v_date', '<', $fromDate)
                ->first();

            Log::info(json_encode($makeOpeningBalance));
            // Prepare the transaction list query
            $transactionList = DB::table('transaction as t')
                ->leftJoin('payments as p', function ($join) {
                    $join->on('t.voucher_id', '=', 'p.id')
                        ->where('t.voucher_type', '=', 2);
                })
                ->leftJoin('receipts as r', function ($join) {
                    $join->on('t.voucher_id', '=', 'r.id')
                        ->where('t.voucher_type', '=', 3);
                })
                ->leftJoin('journal_vouchers as jv', function ($join) {
                    $join->on('t.voucher_id', '=', 'jv.id')
                        ->whereIn('t.voucher_type', [1, 4]); // Correct way
                })
                ->select(
                    't.*',
                    'p.pv_no as payment_voucher_no',
                    'p.pv_date as payment_voucher_date',
                    'p.description as payment_voucher_description',
                    'p.slip_no as payment_slip_no',
                    'r.rv_no as receipt_voucher_no',
                    'r.rv_date as receipt_voucher_date',
                    'r.description as receipt_voucher_description',
                    'r.slip_no as receipt_slip_no',
                    'jv.jv_no as journal_voucher_no',
                    'jv.jv_date as journal_voucher_date',
                    'jv.description as journal_voucher_description',
                    'jv.slip_no as journal_slip_no'
                )
                ->where('t.acc_id', $accId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->OrderBy('t.v_date','ASC')
                ->get();

            Log::info(json_encode($transactionList));

            return view($this->page . 'viewLedgerReportAjax', compact('makeOpeningBalance', 'transactionList','selectedAccountDetail','filterData'));
        }
        return view($this->page . 'viewLedgerReport');
    }

    public function viewMonthlySummaryReport(Request $request)
    {
        if ($request->ajax()) {
            $monthYear = $request->input('monthYear');
            $getTeacherDetail = DB::table('students as s')
                ->join('classes as c', 'c.teacher_id', '=', 's.teacher_id')
                ->select(
                    'e.emp_name',
                    DB::raw('COUNT(s.id) as no_of_students'),
                    'c.class_no',
                    'c.class_name',
                    'd.department_name'
                )
                ->where('s.company_id', Session::get('company_id'))
                ->where('s.company_location_id', Session::get('company_location_id'))
                ->groupBy('e.emp_name', 'c.class_no', 'c.class_name', 'd.department_name')
                ->get();
            return view($this->page . 'viewMonthlySummaryReportAjax', compact('monthYear', 'getTeacherDetail'));
        }
        return view($this->page . 'viewMonthlySummaryReport');
    }

    public function viewStockReport(Request $request)
    {
        // Define file paths for JSON files
        $jsonFiles = [
            'products' => storage_path('app/json_files/products.json'),
            'product_variants' => storage_path('app/json_files/product_variants.json'),
            'categories' => storage_path('app/json_files/categories.json'),
            'brands' => storage_path('app/json_files/brands.json'),
            'sizes' => storage_path('app/json_files/sizes.json'),
        ];

        // Ensure all necessary JSON files exist
        foreach ($jsonFiles as $key => $filePath) {
            if (!file_exists($filePath)) {
                generate_json($key); // Generate the missing JSON file
            }
        }

        // Load data from JSON files
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes] = $data;

        // Optimize the relationship building by indexing categories, brands, and sizes by their IDs
        $categoryMap = array_column($categories, 'name', 'id');
        $brandMap = array_column($brands, 'name', 'id');
        $sizeMap = array_column($sizes, 'name', 'id');

        // Attach related data (variants, category names, brand names, and size names) to products
        $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
            // Attach variants to each product
            $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);

            // Assign category, brand, and size names
            $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
            $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';

            // For each variant, assign the size name
            foreach ($product['variants'] as &$variant) {
                $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
            }

            return $product;
        }, $products);

        // Apply status filter if provided
        $products = array_filter($products, fn($product) => $product['status'] == 1);

        if ($request->ajax()) {
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $productVariantId = $request->input('filter_product_variant_id');

            // Validate Product Variant Exists
            $productDetail = DB::table('product_variants as pv')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->join('sizes as s', 'pv.size_id', '=', 's.id')
                ->where('pv.id', $productVariantId)
                ->select('p.id as product_id')
                ->first();

            if (!$productDetail) {
                return response()->json(['error' => 'Product variant not found'], 404);
            }

            // Get Stock Summary with Parameterized Query
            $stockSummary = DB::select("
                SELECT 
                    p.name AS product_name, 
                    s.name AS size_name, 
                    CASE 
                        WHEN f.status = 1 THEN 'Sales' 
                        WHEN f.status = 2 THEN 'Purchase' 
                        WHEN f.status = 3 THEN 'Transfer Qty' 
                        WHEN f.status = 4 THEN 'Purchase Return'
                        WHEN f.status = 5 THEN 'Sale Return'
                        ELSE 'Unknown' 
                    END AS type,
                    sup.name AS supplier_name,
                    cus.name AS customer_name,
                    cl.name AS company_location_name,
                    f.*
                FROM faras AS f
                INNER JOIN products AS p ON f.product_id = p.id
                INNER JOIN product_variants AS pv ON f.product_variant_id = pv.id
                INNER JOIN sizes AS s ON pv.size_id = s.id
                LEFT JOIN suppliers AS sup ON f.supplier_id = sup.id
                LEFT JOIN customers AS cus ON f.customer_id = cus.id
                LEFT JOIN company_locations AS cl ON f.to_company_location_id = cl.id
                WHERE f.company_id = ? 
                AND f.company_location_id = ? 
                AND f.product_id = ? 
                AND f.product_variant_id = ?",
                [$companyId, $companyLocationId, $productDetail->product_id, $productVariantId]
            );

            return view($this->page . 'viewStockReportAjax', compact('stockSummary'));
        }
        return view($this->page . 'viewStockReport', compact('products'));
    }

    public function viewTrialBalanceReport(Request $request)
    {
        if ($request->ajax()) {
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $account_id = $request->input('acc_id');
            $accountName = DB::table('chart_of_accounts')
                ->where('id', $account_id)
                ->value('name');
            // Query transactions grouped by account for the date range.
            $trialBalance = DB::table('transaction as t')
                ->select(
                    't.acc_id',
                    DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as total_debit'),
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_credit')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->groupBy('t.acc_id')
                ->get();

            // Fetch account details for each account ID in the trial balance.
            $accountIds = $trialBalance->pluck('acc_id')->toArray();
            $accounts = DB::table('chart_of_accounts')
                ->whereIn('id', $accountIds)
                ->pluck('name', 'id'); // key = id, value = account name

            Log::info("Trial Balance Data: " . json_encode($trialBalance));
            $filterData = [
                'accountName' => $accountName,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ];
            return view($this->page . 'viewTrialBalanceReportAjax', compact('trialBalance', 'accounts', 'fromDate', 'toDate', 'filterData'));
        }
        return view($this->page . 'viewTrialBalanceReport');
    }

    public function viewProfitLossReport(Request $request)
    {
        if ($request->ajax()) {
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // Fetch all relevant acc codes (joined with chart_of_accounts)
            $settings = DB::table('profit_and_loss_report_settings as bsrs')
                ->join('chart_of_accounts as coa', 'bsrs.acc_id', '=', 'coa.id')
                ->where('bsrs.company_id', $companyId)
                ->where('bsrs.company_location_id', $companyLocationId)
                ->whereIn('bsrs.acc_type', [1,2,3,4])
                ->select('bsrs.*', 'coa.code')
                ->get()
                ->groupBy('acc_type');

            $revenueCodes = isset($settings[1]) ? $settings[1]->pluck('code')->toArray() : [];
            $expenseCodes = isset($settings[2]) ? $settings[2]->pluck('code')->toArray() : [];
            $costOfGoodSoldCodes = isset($settings[3]) ? $settings[3]->pluck('code')->toArray() : [];
            $salesCodes = isset($settings[4]) ? $settings[4]->pluck('code')->toArray() : [];
            

            $revenues = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    'coa.id',
                    'coa.name',
                    // For revenue transactions, assuming credits increase revenue
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_revenue')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($revenueCodes) {
                    foreach ($revenueCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();

            $expenses = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    'coa.id',
                    'coa.name',
                    // For expense transactions, debits increase expense
                    DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as total_expense')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($expenseCodes) {
                    foreach ($expenseCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();
            
            $cogs = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    'coa.id',
                    'coa.name',
                    DB::raw('SUM(CASE WHEN t.debit_credit = 1 THEN t.amount ELSE 0 END) as total_cogs')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($costOfGoodSoldCodes) {
                    foreach ($costOfGoodSoldCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();

            $sales = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    'coa.id',
                    'coa.name',
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_sale')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($salesCodes) {
                    foreach ($salesCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();

            $totalRevenue = $revenues->sum('total_revenue');
            $totalExpense = $expenses->sum('total_expense');
            $totalCOGS = $expenses->sum('total_cogs');
            $totalSale = $expenses->sum('total_sale');
            $netProfit = $totalRevenue - $totalExpense;

            return view($this->page . 'viewProfitLossReportAjax', compact(
                'revenues', 
                'expenses',
                'cogs',
                'sales',
                'totalRevenue', 
                'totalExpense', 
                'netProfit',
                'fromDate',
                'toDate',
                'totalCOGS',
                'totalSale'
            ));
        }
        return view($this->page . 'viewProfitLossReport');
    }

    public function viewProfitLossReportTwo(Request $request)
    {
        if ($request->ajax()) {
            $companyId = $request->input('company_id');
            $companyLocationId = $request->input('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // Fetch all relevant acc codes (joined with chart_of_accounts)
            $settings = DB::table('profit_and_loss_report_settings as bsrs')
                ->join('chart_of_accounts as coa', 'bsrs.acc_id', '=', 'coa.id')
                ->where('bsrs.company_id', $companyId)
                ->where('bsrs.company_location_id', $companyLocationId)
                ->whereIn('bsrs.acc_type', [1, 2])
                ->select('bsrs.*', 'coa.code')
                ->get()
                ->groupBy('acc_type');

            $revenueCodes = isset($settings[1]) ? $settings[1]->pluck('code')->toArray() : [];
            $expenseCodes = isset($settings[2]) ? $settings[2]->pluck('code')->toArray() : [];

            

            // --- Revenue Accounts (Assuming revenue accounts have codes starting with "4") ---
            $revenues = DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select(
                    'coa.id',
                    'coa.name',
                    // For revenue transactions, assuming credits increase revenue
                    DB::raw('SUM(CASE WHEN t.debit_credit = 2 THEN t.amount ELSE 0 END) as total_revenue')
                )
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($revenueCodes) {
                    foreach ($revenueCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
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
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function($query) use ($expenseCodes) {
                    foreach ($expenseCodes as $code) {
                        $query->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();
            
            // --- Profit & Loss by Product (your SQL query) ---
            $productWiseProfitLoss = DB::table('faras as f')
                ->select(
                    'f.product_id',
                    'p.name',
                    DB::raw('ROUND(SUM(CASE WHEN f.status = 2 THEN f.amount END) / NULLIF(SUM(CASE WHEN f.status = 2 THEN f.qty END), 0), 2) as avg_purchase_rate'),
                    DB::raw('SUM(CASE WHEN f.status = 2 THEN f.qty END) as total_purchase_qty'),
                    DB::raw('SUM(CASE WHEN f.status = 2 THEN f.amount END) as total_purchase_amount'),
                    DB::raw('ROUND(SUM(CASE WHEN f.status = 1 THEN f.amount END) / NULLIF(SUM(CASE WHEN f.status = 1 THEN f.qty END), 0), 2) as avg_sale_rate'),
                    DB::raw('SUM(CASE WHEN f.status = 1 THEN f.qty END) as total_sale_qty'),
                    DB::raw('SUM(CASE WHEN f.status = 1 THEN f.amount END) as total_sale_amount'),
                    DB::raw('ROUND(SUM(CASE WHEN f.status = 1 THEN f.amount END) - ((SUM(CASE WHEN f.status = 2 THEN f.amount END) / NULLIF(SUM(CASE WHEN f.status = 2 THEN f.qty END), 0)) * SUM(CASE WHEN f.status = 1 THEN f.qty END)), 2) as profit_loss'),
                    DB::raw('ROUND(((SUM(CASE WHEN f.status = 1 THEN f.amount END) - ((SUM(CASE WHEN f.status = 2 THEN f.amount END) / NULLIF(SUM(CASE WHEN f.status = 2 THEN f.qty END), 0)) * SUM(CASE WHEN f.status = 1 THEN f.qty END))) / NULLIF(SUM(CASE WHEN f.status = 1 THEN f.qty END), 0)), 2) as profit_per_unit')
                )
                ->join('products as p','f.product_id','=','p.id')
                ->whereBetween('f.created_date', [$fromDate, $toDate])
                ->groupBy('f.product_id','p.name')
                ->havingRaw('SUM(CASE WHEN f.status = 1 THEN f.qty END) > 0')
                ->get();

            $totalRevenue = $revenues->sum('total_revenue');
            $totalExpense = $expenses->sum('total_expense');
            $netProfit = $totalRevenue - $totalExpense;

            return view($this->page . 'viewProfitLossReportAjaxTwo', compact(
                'revenues', 
                'expenses', 
                'totalRevenue', 
                'totalExpense', 
                'netProfit',
                'fromDate',
                'toDate',
                'productWiseProfitLoss'
            ));
        }
        return view($this->page . 'viewProfitLossReportTwo');
    }
}
