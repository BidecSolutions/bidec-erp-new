<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccountSetting;
use DB;
use Session;
use Illuminate\Support\Facades\Log;

class ChartOfAccountSettingController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'chart-of-account-settings.';
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
        $chartOfAccountList = DB::table('chart_of_accounts')->where('company_id', Session::get('company_id'))->where('status', 1)->get();

        return view($this->page . 'create', compact('chartOfAccountList'));
    }

    public function index(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $jsonFiles = [
                'chart_of_account_settings' => storage_path('app/json_files/chart_of_account_settings.json'),
                'chart_of_accounts' => storage_path('app/json_files/chart_of_accounts.json'),
            ];

            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['chart_of_account_settings' => $chartOfAccountSettings, 'chart_of_accounts' => $chart_of_accounts] = $data;

            $chartOfAccountMap = array_column($chart_of_accounts, 'name', 'id');

            // Attach related data (variants, category names, brand names, and size names) to products
            $chartOfAccountSettings = array_map(function ($chartOfAccountSetting) use ($chartOfAccountMap) {

                // Assign category, brand, and size names
                $chartOfAccountSetting['account_name'] = $chartOfAccountMap[$chartOfAccountSetting['acc_id']] ?? '-';

                return $chartOfAccountSetting;
            }, $chartOfAccountSettings);

            // Filter by company_id and company_location_id
            $chartOfAccountSettings = array_filter($chartOfAccountSettings, function ($chartOfAccountSetting) use ($companyId, $companyLocationId) {
                return isset($chartOfAccountSetting['company_id']) 
                    && $chartOfAccountSetting['company_id'] == $companyId
                    && isset($chartOfAccountSetting['company_location_id']) 
                    && $chartOfAccountSetting['company_location_id'] == $companyLocationId;
            });
            // Apply status filter if provided
            if ($status) {
                $chartOfAccountSettings = array_filter($chartOfAccountSettings, fn($chartOfAccountSetting) => $chartOfAccountSetting['status'] == $status);
            }

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($chartOfAccountSettings, 'Chart of Account Settings Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('chartOfAccountSettings'));
        }

        // Handle non-AJAX and non-API requests
        return view($this->page . 'index');
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'option_id' => 'required|array',
                'option_id.*' => 'required|integer|in:1,2,3,4,5',
                'acc_id' => 'required|array',
                'acc_id.*' => 'nullable|exists:chart_of_accounts,id',
            ]);

            // Loop through each option_id
            foreach ($validatedData['option_id'] as $index => $optionId) {
                $accountId = $validatedData['acc_id'][$index];

                if ($accountId) {
                    // Check if a record exists
                    $chartOfAccountSetting = ChartOfAccountSetting::where('option_id', $optionId)
                        ->where('company_id',$companyId)
                        ->where('company_location_id',$companyLocationId)
                        ->first();

                    if ($chartOfAccountSetting) {
                        // Update existing record
                        $chartOfAccountSetting->acc_id = $accountId;
                        $chartOfAccountSetting->save();
                    } else {
                        // Insert new record
                        $newChartOfAccountSetting = new ChartOfAccountSetting();
                        $newChartOfAccountSetting->option_id = $optionId;
                        $newChartOfAccountSetting->acc_id = $accountId;
                        $newChartOfAccountSetting->save();
                    }
                }
            }

            // Call a helper function to regenerate JSON if applicable
            generate_json('chart_of_account_settings');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Chart of Account Settings Created or Updated Successfully');
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
        try {
            // Fetch the first matching setting based on the provided ID
            $settings = ChartOfAccountSetting::where('id', $id)->first();

            // Check if the record exists
            if (!$settings) {
                return redirect()->route('chart-of-account-settings.index')->withErrors(['error' => 'The requested record was not found.']);
            }

            // Retrieve the chart of accounts list
            $chartOfAccountList = DB::connection('mysql')
                ->table('chart_of_accounts')
                ->select(['id', 'name'])
                ->where('status', '=', 1)
                ->get();

            // Define the option array
            $optionArray = [
                'option1' => 'Category and Sub Category',
                'option2' => 'Customers',
                'option3' => 'Suppliers',
            ];

            return view('chart-of-account-settings.edit', compact('settings', 'chartOfAccountList', 'optionArray'));
        } catch (\Exception $e) {
            return redirect()->route('chart-of-account-settings.index')->withErrors(['error' => 'An unexpected error occurred.']);
        }
    }







    public function update(Request $request, $id)
    {
        Log::info('Request Data:', $request->all());

        $validatedData = $request->validate([
            'option_id' => 'required|array',
            'option_id.*' => 'required|integer', // Ensure integers
            'acc_id' => 'required|array',
            'acc_id.*' => 'nullable|exists:chart_of_accounts,id',
        ]);

        Log::info('Validation Passed:', $validatedData);

        foreach ($validatedData['option_id'] as $index => $optionId) {
            $accountId = $validatedData['acc_id'][$index];
            $setting = ChartOfAccountSetting::findOrFail($id);

            $setting->option_id = $optionId;
            $setting->acc_id = $accountId;
            $setting->save();
        }

        return redirect()->route('chart-of-account-settings.index')
            ->with('success', 'Settings updated successfully.');
    }



    public function destroy($id)
    {
        try {
            $chartOfAccountSetting = ChartOfAccountSetting::findOrFail($id);
            $chartOfAccountSetting->status = 0;
            $chartOfAccountSetting->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('chart_of_account_settings');

            return response()->json(['success' => 'ChartOfAccountSetting deactivated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the ChartOfAccountSetting.'], 500);
        }
    }
    public function active($id)
    {
        try {
            $chartOfAccountSetting = ChartOfAccountSetting::findOrFail($id);
            $chartOfAccountSetting->status = 1;
            $chartOfAccountSetting->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('chart_of_account_settings');

            return response()->json(['success' => 'ChartOfAccountSetting activated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while activating the ChartOfAccountSetting.'], 500);
        }
    }
}
