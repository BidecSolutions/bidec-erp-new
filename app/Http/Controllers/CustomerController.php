<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\City;
use App\Models\Customer;
use Illuminate\Http\Request;
use DB;
use Session;
use Auth;

class CustomerController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'customers.';
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
            ->where('option_id', 2)
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
                ->where('coas.option_id', 2)
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
                'customers' => storage_path('app/json_files/customers.json'),
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
            ['customers' => $customers, 'chart_of_accounts' => $chart_of_accounts, 'cities' => $cities] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'code', 'id');
            $cityMap = array_column($cities, 'city_name', 'id');

            // Attach related data (variants, category names, brand names, and size names) to products
            $customers = array_map(function ($customer) use ($chartOfAccountMap, $cityMap) {

                // Assign category, brand, and size names
                $customer['code'] = $chartOfAccountMap[$customer['acc_id']] ?? '-';
                $customer['city_name'] = $cityMap[$customer['city_id']] ?? '-';

                return $customer;
            }, $customers);

            // Apply status filter if provided
            if ($status) {
                $customers = array_filter($customers, fn($customer) => $customer['status'] == $status);
            }

            $customers = array_filter($customers, function ($customer) use ($companyId) {
                return $customer['company_id'] == $companyId;
            });

            

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($customers, 'Customers Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('customers'));
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
                'city_id' => 'required|exists:cities,id',
                'physical_address' => 'nullable|string',
                'mobile_no' => 'nullable|string',
                'email_address' => 'nullable|string',
            ]);

            // Check if the customer already exists based on the unique fields
            $existingCustomer = Customer::where('mobile_no', $validatedData['mobile_no'])
                ->where('email_address', $validatedData['email_address'])
                ->where('name', $validatedData['name'])
                ->first();

            if ($existingCustomer) {
                return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                'error' => 'A customer with this mobile number or email address or name already exists.'
                ]);
            }

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
            $data1['coa_type'] = 2;
            $data1['name'] = $validatedData['name'];
            $data1['parent_code'] = $validatedData['acc_id'];
            $data1['username']          = Auth::user()->name;
            $data1['date']            = date("Y-m-d");
            $data1['time']            = date("H:i:s");
            $accId = DB::table('chart_of_accounts')->insertGetId($data1);
            generate_json('chart_of_accounts');

            $customer = new Customer();

            $customer->acc_id = $accId;
            $customer->name = $validatedData['name'];
            $customer->city_id = $validatedData['city_id'];
            $customer->physical_address = $validatedData['physical_address'] ?? '-';
            $customer->mobile_no = $validatedData['mobile_no'] ?? '-';
            $customer->email_address = $validatedData['email_address'] ?? '-';
            $customer->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('customers');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Customers Created Successfully');
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
        // Fetch the customer data by ID
        $customer = DB::table('customers as c')
            ->join('chart_of_accounts as coa', 'c.acc_id', '=', 'coa.id')
            ->select('c.*', 'coa.parent_code','coa.code') // or specify fields you need
            ->where('c.id',$id)
            ->first();

        $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            ->where('option_id', 2)
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
                ->where('coas.option_id', 2)
                ->where('coas.company_id', Session::get('company_id'))
                ->where('coas.company_location_id', Session::get('company_location_id'))->get();
        }

        $isUsedInTransactions = DB::table('payment_data')->where('acc_id', $customer->acc_id)->exists() ||
            DB::table('receipt_data')->where('acc_id', $customer->acc_id)->exists() ||
            DB::table('journal_voucher_data')->where('acc_id', $customer->acc_id)->exists() || 
            DB::table('chart_of_account_settings')->where('acc_id', $customer->acc_id)->exists() || 
            DB::table('chart_of_accounts')->where('parent_code', $customer->code)->exists();

        $cities = City::all();

        // Return the edit view with data
        return view('customers.edit', compact('customer', 'chartOfAccountList', 'cities', 'isUsedInTransactions'));
    }


    public function update(Request $request, $id)
    {
        try{
            // Validate the request data
        $validatedData = $request->validate([
            'acc_id' => 'nullable',
            'old_acc_id' => 'nullable',
            'customer_acc_id' => 'required',
            'name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:50',
            'email_address' => 'nullable|string|max:255',
            'city_id' => 'required',
            'physical_address' => 'nullable|string|max:255',
        ]);

        if($validatedData['acc_id'] == $validatedData['old_acc_id']){
            DB::table('chart_of_accounts')->where('id',$validatedData['customer_acc_id'])->update(['name' => $validatedData['name']]);
            $accId = $validatedData['customer_acc_id'];
        }else{

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
            DB::table('chart_of_accounts')->where('id',$validatedData['customer_acc_id'])->update($data1);
            generate_json('chart_of_accounts');
            $accId = $validatedData['customer_acc_id'];

        }

        // Fetch the customer data by ID
        $customer = Customer::findOrFail($id);

        // Update the customer data
        $customer->acc_id = $accId;
        $customer->name = $validatedData['name'];
        $customer->mobile_no = $validatedData['mobile_no'] ?? '-';
        $customer->email_address = $validatedData['email_address'] ?? '-';
        $customer->city_id = $validatedData['city_id'];
        $customer->physical_address = $validatedData['physical_address'] ?? '-';
        $customer->save();

        // Call a helper function to regenerate JSON if applicable
        generate_json('customers');

        // Redirect with success message
        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer Updated Successfully');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function destroy($id) {
        try {
            // Fetch the customer data by ID
            $customer = Customer::findOrFail($id);

            // Delete the customer data
            $customer->status = 2;
            $customer->save();

            DB::table('chart_of_accounts')->where('id',$customer->acc_id)->update(['status' => 2]);
            generate_json('chart_of_accounts');
            // Call a helper function to regenerate JSON if applicable
            generate_json('customers');

            // Redirect with success message
            return response()->json(['success' => 'Customer marked as inactive successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An error occurred while deactivating the brand.'], 500);
        }
    }
    public function status($id) {
        try {
            // Fetch the customer data by ID
            $customer = Customer::findOrFail($id);

            // Update the status
            $customer->status = 1;
            $customer->save();

            DB::table('chart_of_accounts')->where('id',$customer->acc_id)->update(['status' => 1]);
            generate_json('chart_of_accounts');
            // Call a helper function to regenerate JSON if applicable
            generate_json('customers');

            // Return success response
            return response()->json(['success' => 'Customer marked as Active successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An error occurred while deactivating the brand.'], 500);
        }
    }
}
