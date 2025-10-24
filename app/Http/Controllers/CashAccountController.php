<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CashAccountController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'cash-accounts.';
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
                'cash_accounts' => storage_path('app/json_files/cash_accounts.json'),
                'chart_of_accounts' => storage_path('app/json_files/chart_of_accounts.json'),
            ];

            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['cash_accounts' => $cash_accounts, 'chart_of_accounts' => $chart_of_accounts] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'code', 'id');
            
            // Attach related data (variants, category names, brand names, and size names) to products
            $cashAccounts = array_map(function ($cashAccount) use ($chartOfAccountMap) {

                // Assign category, brand, and size names
                $cashAccount['code'] = $chartOfAccountMap[$cashAccount['acc_id']] ?? '-';

                return $cashAccount;
            }, $cash_accounts);

            // Apply status filter if provided
            if ($status) {
                $cashAccounts = array_filter($cashAccounts, fn($cashAccount) => $cashAccount['status'] == $status);
            }

            $cashAccounts = array_filter($cashAccounts, function ($cashAccount) use ($companyId, $companyLocationId) {
                return $cashAccount['company_id'] == $companyId && $cashAccount['company_location_id'] == $companyLocationId;
            });

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($cashAccounts, 'Cash Accounts Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('cashAccounts'));
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

            $cashAccount = new CashAccount();

            $cashAccount->acc_id = $accId;
            $cashAccount->name = $validatedData['name'];
            $cashAccount->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('cash_accounts');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Cash Accounts Created Successfully');
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
        // Fetch the cash account data by ID
        $cashAccount = CashAccount::findOrFail($id);

        // Fetch related data for dropdowns
        $chartOfAccountList = ChartOfAccount::all();

        // Return the edit view with data
        return view('cash-accounts.edit', compact('cashAccount', 'chartOfAccountList'));
    }

    public function update(Request $request, $id)
    {
        print_r($request->all());
        // try {
        //     // Validate the request data
        //     $validatedData = $request->validate([
        //         'acc_id' => 'required',
        //         'name' => 'required|string|max:255',
        //     ]);

        //     // Find the cash account by ID or fail
        //     $cashAccount = CashAccount::findOrFail($id);

        //     // Update the database
        //     $cashAccount->acc_id = $validatedData['acc_id'];
        //     $cashAccount->name = $validatedData['name'];
        //     $cashAccount->save();

        //     // Call a helper function to regenerate JSON if applicable
        //     generate_json('cash_accounts');

        //     // Redirect with success message
        //     return redirect()
        //         ->route($this->page . 'index')
        //         ->with('success', 'Cash Account Updated Successfully');
        // } catch (\Exception $e) {
        //     // Handle unexpected errors
        //     return redirect()
        //         ->back()
        //         ->withInput()
        //         ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        // }
    }


    public function destroy($id)
    {
        try {
            // Find the cash account by ID or fail
            $cashAccount = CashAccount::findOrFail($id);

            // Delete the cash account
            $cashAccount->status = 2;

            $cashAccount->save();
            // Call a helper function to regenerate JSON if applicable
            generate_json('cash_accounts');

            // Redirect with success message
            return response()->json(['success' => 'Cash Account marked as inactive successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An error occurred while deactivating the cash account.'], 500);
        }
    }
    public function status($id)
    {
        try {
            $cashAccount = CashAccount::findOrFail($id);
            $cashAccount->status = 1;
            $cashAccount->save();
            generate_json('cash_accounts');

            return response()->json(['success' => 'Cash Account marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the cash account.'], 500);
        }
    }
}
