<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\TaxAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TaxAccountsController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'tax-accounts.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create()
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            ->where('option_id', 6)
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->first();
        if (empty($chartOfAccountSettingDetail)) {
            $chartOfAccountList = DB::table('chart_of_accounts')
                ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'))
                ->where('status', 1)->get();
        } else {
            $chartOfAccountList = DB::table('chart_of_account_settings as coas')
                ->join('chart_of_accounts as coa', 'coas.acc_id', '=', 'coa.id')
                ->select('coas.acc_id', 'coa.name', 'coa.code')
                ->where('coas.option_id', 6)
                ->where('coas.company_id', Session::get('company_id'))
                ->where('coas.company_location_id', Session::get('company_location_id'))->get();
        }
        return view($this->page . 'create', compact('chartOfAccountList'));
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $jsonFiles = [
                'tax_accounts' => storage_path('app/json_files/tax_accounts.json'),
                'chart_of_accounts' => storage_path('app/json_files/chart_of_accounts.json'),
            ];

            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['tax_accounts' => $tax_accounts, 'chart_of_accounts' => $chart_of_accounts] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'code', 'id');

            // Attach related data (variants, category names, brand names, and size names) to products
            $tax_accounts = array_map(function ($tax_account) use ($chartOfAccountMap) {

                // Assign category, brand, and size names
                $tax_account['code'] = $chartOfAccountMap[$tax_account['acc_id']] ?? '-';

                return $tax_account;
            }, $tax_accounts);

            // Apply status filter if provided
            if ($status) {
                $tax_accounts = array_filter($tax_accounts, fn($tax_account) => $tax_account['status'] == $status);
            }

            $tax_accounts = array_filter($tax_accounts, function ($tax_account) use ($companyId, $companyLocationId) {
                return $tax_account['company_id'] == $companyId;
            });

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($tax_accounts, 'Tax Accounts Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('tax_accounts'));
        }

        // Handle non-AJAX and non-API requests
        return view($this->page . 'index');
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'acc_id' => 'required|exists:chart_of_accounts,code',
                'name' => 'required|string',
                'percent_value' => 'nullable|string'
            ]);
            $getAccountDetail = DB::table('chart_of_accounts')->where('code',$validatedData['acc_id'])->first();
            $chartOfAccount = new ChartOfAccount();
            $code = ChartOfAccount::GenerateAccountCode($validatedData['acc_id']);

            $level_array = explode('-', $code);
            $counter = 1;
            foreach ($level_array as $level):
                $data1['level' . $counter] = $level;
                $counter++;
            endforeach;
            $data1['company_id'] = Session::get('company_id');
            $data1['company_location_id'] = Session::get('company_location_id');
            $data1['code'] = $code;
            $data1['name'] = $validatedData['name'];
            $data1['parent_code'] = $validatedData['acc_id'];
            $data1['coa_type'] = 2;
            $data1['username']          = Auth::user()->name;
            $data1['date']            = date("Y-m-d");
            $data1['time']            = date("H:i:s");
            $data1['operational'] = 2;
            $data1['ledger_type'] = $getAccountDetail->ledger_type;
            $data1['company_id'] = Session::get('company_id');
            $data1['company_location_id'] = Session::get('company_location_id');
            $accId = DB::table('chart_of_accounts')->insertGetId($data1);
            generate_json('chart_of_accounts');

            $taxAccount = new TaxAccount();

            $taxAccount->acc_id = $accId;
            $taxAccount->name = $validatedData['name'];
            $taxAccount->percent_value = $validatedData['percent_value'] ?? '-';
            $taxAccount->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('tax_accounts');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Tax Accounts Created Successfully');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function edit($id)
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        // Fetch the supplier data by ID
        $taxAccount = DB::table('tax_accounts as ta')
            ->join('chart_of_accounts as coa', 'ta.acc_id', '=', 'coa.id')
            ->select('ta.*', 'coa.parent_code','coa.code') // or specify fields you need
            ->where('ta.id',$id)
            ->first();

        $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            ->where('option_id', 6)
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->first();
        if (empty($chartOfAccountSettingDetail)) {
            $chartOfAccountList = DB::table('chart_of_accounts')
                ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'))
                ->where('status', 1)->get();
        } else {
            $chartOfAccountList = DB::table('chart_of_account_settings as coas')
                ->join('chart_of_accounts as coa', 'coas.acc_id', '=', 'coa.id')
                ->select('coas.acc_id', 'coa.name', 'coa.code')
                ->where('coas.option_id', 6)
                ->where('coas.company_id', Session::get('company_id'))
                ->where('coas.company_location_id', Session::get('company_location_id'))->get();
        }

        $isUsedInTransactions = DB::table('payment_data')->where('acc_id', $taxAccount->acc_id)->exists() ||
            DB::table('receipt_data')->where('acc_id', $taxAccount->acc_id)->exists() ||
            DB::table('journal_voucher_data')->where('acc_id', $taxAccount->acc_id)->exists() || 
            DB::table('chart_of_account_settings')->where('acc_id', $taxAccount->acc_id)->exists() || 
            DB::table('chart_of_accounts')->where('parent_code', $taxAccount->code)->exists();

        

        // Return the edit view with data
        return view('tax-accounts.edit', compact('taxAccount', 'chartOfAccountList', 'isUsedInTransactions'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'acc_id' => 'required',
                'old_acc_id' => 'nullable',
                'taxAccount_acc_id' => 'required',
                'name' => 'required|string|max:255',
                'percent_value' => 'nullable|string',
            ]);

            if($validatedData['acc_id'] == $validatedData['old_acc_id']){
                DB::table('chart_of_accounts')->where('id',$validatedData['taxAccount_acc_id'])->update(['name' => $validatedData['name']]);
                $accId = $validatedData['taxAccount_acc_id'];
            }else{
                $getAccountDetail = DB::table('chart_of_accounts')->where('code',$validatedData['acc_id'])->first();
                $code = ChartOfAccount::GenerateAccountCode($validatedData['acc_id']);
    
                $level_array = explode('-', $code);
                $counter = 1;
                foreach ($level_array as $level):
                    $data1['level' . $counter] = $level;
                    $counter++;
                endforeach;
                $data1['company_id'] = Session::get('company_id');
                $data1['company_location_id'] = Session::get('company_location_id');
                $data1['code'] = $code;
                $data1['coa_type'] = 2;
                $data1['name'] = $validatedData['name'];
                $data1['parent_code'] = $validatedData['acc_id'];
                $data1['operational'] = 2;
                $data1['ledger_type'] = $getAccountDetail->ledger_type;
                $data1['username']          = Auth::user()->name;
                $data1['date']            = date("Y-m-d");
                $data1['time']            = date("H:i:s");
                DB::table('chart_of_accounts')->where('id',$validatedData['taxAccount_acc_id'])->update($data1);
                generate_json('chart_of_accounts');
                $accId = $validatedData['supplier_acc_id'];
    
            }

            // Find the supplier by ID or fail
            $taxAccount = TaxAccount::findOrFail($id);

            // Update the database
            $taxAccount->acc_id = $accId;
            $taxAccount->name = $validatedData['name'];
            $taxAccount->percent_value = $validatedData['percent_value'];
            $taxAccount->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('tax_accounts');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Tax Account Updated Successfully');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }


    public function destroy($id)
    {
        try {
            // Find the supplier by ID or fail
            $supplier = Supplier::findOrFail($id);

            // Delete the supplier
            $supplier->status = 2;
            $supplier->save();
            
            DB::table('chart_of_accounts')->where('id',$supplier->acc_id)->update(['status' => 2]);
            generate_json('chart_of_accounts');
            // Call a helper function to regenerate JSON if applicable
            generate_json('suppliers');

            // Redirect with success message
            return response()->json(['success' => 'Supplier marked as inactive successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An error occurred while deactivating the supplier.'], 500);
        }
    }
    public function status($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->status = 1;
            $supplier->save();

            DB::table('chart_of_accounts')->where('id',$supplier->acc_id)->update(['status' => 1]);
            generate_json('chart_of_accounts');

            generate_json('suppliers');

            return response()->json(['success' => 'Supplier marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the supplier.'], 500);
        }
    }
}
