<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\City;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'suppliers.';
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
            ->where('option_id', 3)
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
        $filePath = storage_path('app/json_files/cities.json');

        // Check if the JSON file exists
        if (!file_exists($filePath)) {
            // If the file doesn't exist, generate the JSON file
            generate_json('cities');
        }

        // Read the data from the JSON file
        $cities = json_decode(file_get_contents($filePath), true);
        $cities = array_filter($cities, function ($city) {
            return $city['status'] == 1;
        });

        return view($this->page . 'create', compact('chartOfAccountList', 'cities'));
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $jsonFiles = [
                'suppliers' => storage_path('app/json_files/suppliers.json'),
                'chart_of_accounts' => storage_path('app/json_files/chart_of_accounts.json'),
                'cities' => storage_path('app/json_files/cities.json'),
            ];

            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['suppliers' => $suppliers, 'chart_of_accounts' => $chart_of_accounts, 'cities' => $cities] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'code', 'id');
            $cityMap = array_column($cities, 'city_name', 'id');

            // Attach related data (variants, category names, brand names, and size names) to products
            $suppliers = array_map(function ($supplier) use ($chartOfAccountMap, $cityMap) {

                // Assign category, brand, and size names
                $supplier['code'] = $chartOfAccountMap[$supplier['acc_id']] ?? '-';
                $supplier['city_name'] = $cityMap[$supplier['city_id']] ?? '-';

                return $supplier;
            }, $suppliers);

            // Apply status filter if provided
            if ($status) {
                $suppliers = array_filter($suppliers, fn($supplier) => $supplier['status'] == $status);
            }

            $suppliers = array_filter($suppliers, function ($supplier) use ($companyId, $companyLocationId) {
                return $supplier['company_id'] == $companyId;
            });

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($suppliers, 'Suppliers Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('suppliers'));
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
                'ntn_no' => 'nullable|string',
                'strn_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',
                'physical_address' => 'nullable|string',
                'cnic_no' => 'nullable|string',
                'mobile_no' => 'nullable|string',
                'phone_no' => 'nullable|string',
                'email_address' => 'nullable|string',
                'bank_name' => 'nullable|string',
                'account_title' => 'nullable|string',
                'account_no' => 'nullable|string'
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

            $supplier = new Supplier();

            $supplier->acc_id = $accId;
            $supplier->name = $validatedData['name'];
            $supplier->ntn_no = $validatedData['ntn_no'] ?? '-';
            $supplier->strn_no = $validatedData['strn_no'] ?? '-';
            $supplier->city_id = $validatedData['city_id'];
            $supplier->physical_address = $validatedData['physical_address'] ?? '-';
            $supplier->cnic_no = $validatedData['cnic_no'] ?? '-';
            $supplier->mobile_no = $validatedData['mobile_no'] ?? '-';
            $supplier->phone_no = $validatedData['phone_no'] ?? '-';
            $supplier->email_address = $validatedData['email_address'] ?? '-';
            $supplier->bank_name = $validatedData['bank_name'] ?? '-';
            $supplier->account_title = $validatedData['account_title'] ?? '-';
            $supplier->account_no = $validatedData['account_no'] ?? '-';
            $supplier->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('suppliers');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Suppliers Created Successfully');
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
        $supplier = DB::table('suppliers as s')
            ->join('chart_of_accounts as coa', 's.acc_id', '=', 'coa.id')
            ->select('s.*', 'coa.parent_code','coa.code') // or specify fields you need
            ->where('s.id',$id)
            ->first();

        $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            ->where('option_id', 3)
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

        $isUsedInTransactions = DB::table('payment_data')->where('acc_id', $supplier->acc_id)->exists() ||
            DB::table('receipt_data')->where('acc_id', $supplier->acc_id)->exists() ||
            DB::table('journal_voucher_data')->where('acc_id', $supplier->acc_id)->exists() || 
            DB::table('chart_of_account_settings')->where('acc_id', $supplier->acc_id)->exists() || 
            DB::table('chart_of_accounts')->where('parent_code', $supplier->code)->exists();


        // Fetch related data for dropdowns
        $cities = City::all();

        // Return the edit view with data
        return view('suppliers.edit', compact('supplier', 'chartOfAccountList', 'cities','isUsedInTransactions'));
    }

    public function update(Request $request, $id)
    {
        
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'acc_id' => 'required',
                'old_acc_id' => 'nullable',
                'supplier_acc_id' => 'required',
                'name' => 'required|string|max:255',
                'ntn_no' => 'nullable|string|max:50',
                'strn_no' => 'nullable|string|max:50',
                'city_id' => 'required',
                'physical_address' => 'nullable|string|max:255',
                'cnic_no' => 'nullable|string|max:50',
                'mobile_no' => 'nullable|string|max:50',
                'phone_no' => 'nullable|string|max:50',
                'email_address' => 'nullable|max:255',
                'bank_name' => 'nullable|string|max:255',
                'account_title' => 'nullable|string|max:255',
                'account_no' => 'nullable|string|max:50',
            ]);

            if($validatedData['acc_id'] == $validatedData['old_acc_id']){
                DB::table('chart_of_accounts')->where('id',$validatedData['supplier_acc_id'])->update(['name' => $validatedData['name']]);
                $accId = $validatedData['supplier_acc_id'];
                generate_json('chart_of_accounts');
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
                $data1['username']          = Auth::user()->name;
                $data1['date']            = date("Y-m-d");
                $data1['time']            = date("H:i:s");
                $data1['ledger_type'] = $getAccountDetail->ledger_type;
                $data1['operational'] = 2;
                DB::table('chart_of_accounts')->where('id',$validatedData['supplier_acc_id'])->update($data1);
                generate_json('chart_of_accounts');
                $accId = $validatedData['supplier_acc_id'];
    
            }

            // Find the supplier by ID or fail
            $supplier = Supplier::findOrFail($id);

            // Update the database
            $supplier->acc_id = $accId;
            $supplier->name = $validatedData['name'];
            $supplier->ntn_no = $validatedData['ntn_no'] ?? '-';
            $supplier->strn_no = $validatedData['strn_no'] ?? '-';
            $supplier->city_id = $validatedData['city_id'] ?? '-';
            $supplier->physical_address = $validatedData['physical_address'] ?? '-';
            $supplier->cnic_no = $validatedData['cnic_no'] ?? '-';
            $supplier->mobile_no = $validatedData['mobile_no'] ?? '-';
            $supplier->phone_no = $validatedData['phone_no'] ?? '-';
            $supplier->email_address = $validatedData['email_address'] ?? '-';
            $supplier->bank_name = $validatedData['bank_name'] ?? '-';
            $supplier->account_title = $validatedData['account_title'] ?? '-';
            $supplier->account_no = $validatedData['account_no'] ?? '-';
            $supplier->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('suppliers');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Supplier Updated Successfully');
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
