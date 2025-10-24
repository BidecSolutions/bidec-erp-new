<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class BankAccountController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'bank-accounts.';
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
            ->where('option_id', 5)
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
                ->where('coas.option_id', 3)
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
                'bank_accounts' => storage_path('app/json_files/bank_accounts.json'),
                'chart_of_accounts' => storage_path('app/json_files/chart_of_accounts.json'),
            ];

            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['bank_accounts' => $bank_accounts, 'chart_of_accounts' => $chart_of_accounts] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'code', 'id');
            
            // Attach related data (variants, category names, brand names, and size names) to products
            $bankAccounts = array_map(function ($bankAccount) use ($chartOfAccountMap) {

                // Assign category, brand, and size names
                $bankAccount['code'] = $chartOfAccountMap[$bankAccount['acc_id']] ?? '-';

                return $bankAccount;
            }, $bank_accounts);

            // Apply status filter if provided
            if ($status) {
                $bankAccounts = array_filter($bankAccounts, fn($bankAccount) => $bankAccount['status'] == $status);
            }

            $bankAccounts = array_filter($bankAccounts, function ($bankAccount) use ($companyId, $companyLocationId) {
                return $bankAccount['company_id'] == $companyId && $bankAccount['company_location_id'] == $companyLocationId;
            });

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($bankAccounts, 'Bank Accounts Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('bankAccounts'));
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
                'name' => 'required|string'
            ]);

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
            $data1['username']          = Auth::user()->name;
            $data1['coa_type']          = 2;
            $data1['date']            = date("Y-m-d");
            $data1['time']            = date("H:i:s");
            $data1['company_id'] = Session::get('company_id');
            $data1['company_location_id'] = Session::get('company_location_id');
            $accId = DB::table('chart_of_accounts')->insertGetId($data1);
            generate_json('chart_of_accounts');

            $bankAccount = new BankAccount();

            $bankAccount->acc_id = $accId;
            $bankAccount->name = $validatedData['name'];
            $bankAccount->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('bank_accounts');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Bank Accounts Created Successfully');
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
        // Fetch the bank account data by ID
        $bankAccount = BankAccount::findOrFail($id);

        // Fetch related data for dropdowns
        $chartOfAccountList = ChartOfAccount::all();

        // Return the edit view with data
        return view('bank-accounts.edit', compact('bankAccount', 'chartOfAccountList'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'acc_id' => 'required',
                'name' => 'required|string|max:255',
            ]);

            // Find the bank account by ID or fail
            $bankAccount = BankAccount::findOrFail($id);

            // Update the database
            $bankAccount->acc_id = $validatedData['acc_id'];
            $bankAccount->name = $validatedData['name'];
            $bankAccount->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('bank_accounts');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Bank Account Updated Successfully');
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
            // Find the bank account by ID or fail
            $bankAccount = BankAccount::findOrFail($id);

            // Delete the bank account
            $bankAccount->status = 2;

            $bankAccount->save();
            // Call a helper function to regenerate JSON if applicable
            generate_json('bank_accounts');

            // Redirect with success message
            return response()->json(['success' => 'Bank Account marked as inactive successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An error occurred while deactivating the bank account.'], 500);
        }
    }
    public function status($id)
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);
            $bankAccount->status = 1;
            $bankAccount->save();
            generate_json('bank_accounts');

            return response()->json(['success' => 'Bank Account marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the bank account.'], 500);
        }
    }
}
