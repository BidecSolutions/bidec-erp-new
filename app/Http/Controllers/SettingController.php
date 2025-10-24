<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\PayableAndReceivableReportSetting;

class SettingController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'settings.';
    }
    
    public function create(Request $request){
        
        return view($this->page . 'create');
    }

    public function store(Request $request){
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'company_id' => 'required|integer',
                'company_location_id' => 'required|integer',
                'fee_voucher_footer_description' => 'nullable|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        $setting = new Setting();
        $setting->company_id = $request->input('company_id');
        $setting->company_location_id = $request->input('company_location_id');
        $setting->fee_voucher_footer_description = $request->input('fee_voucher_footer_description');
        $setting->save();

        return redirect()->route($this->page.'index')->with('success', 'Setting Added Successfully');
    }

    public function index(Request $request){
        return view($this->page . 'index');
    }

    public function purchaseInvoiceAndPaymentSettingCreate(Request $request){
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $chartOfAccountList = DB::table('chart_of_accounts')->where('company_id', Session::get('company_id'))->where('status', 1)->get();
        $type = '1';
        $url = route('purchase-invoice-and-payment-setting.store');
        $savedSettings = DB::table('invoices_payments_receipts_settings')
            ->where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->where('type',1)
            ->pluck('acc_id', 'option_id')
            ->toArray();
        return view('purchase-invoice-and-payment-setting.create', compact('chartOfAccountList','type','url','savedSettings'));
    }

    public function purchaseInvoiceAndPaymentSettingStore(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        try {
            // Validate request
            $validatedData = $request->validate([
                'option_id'   => 'required|array',
                'option_id.*' => 'required|integer|in:1,2,3,4',
                'acc_id'      => 'required|array',
                'acc_id.*'    => 'nullable|exists:chart_of_accounts,id',
            ]);

            foreach ($validatedData['option_id'] as $index => $optionId) {
                $accountId = $validatedData['acc_id'][$index] ?? null;

                if ($accountId) {
                    // Check if already exists
                    $exists = DB::table('invoices_payments_receipts_settings')
                        ->where('option_id', $optionId)
                        ->where('company_id', $companyId)
                        ->where('type',1)
                        ->where('company_location_id', $companyLocationId)
                        ->first();

                    if ($exists) {
                        // Update record
                        DB::table('invoices_payments_receipts_settings')
                            ->where('id', $exists->id)
                            ->update([
                                'acc_id' => $accountId,
                                'updated_by' => auth()->user()->name,
                                'updated_date' => date('Y-m-d')

                            ]);
                    } else {
                        // Insert new
                        DB::table('invoices_payments_receipts_settings')->insert([
                            'type' => 1,
                            'company_id' => $companyId,
                            'company_location_id' => $companyLocationId,
                            'option_id' => $optionId,
                            'acc_id' => $accountId,
                            'status' => 1,
                            'created_by' => auth()->user()->name,
                            'created_date' => date('Y-m-d')
                        ]);
                    }
                }
            }

            return redirect()
                ->route('purchase-invoice-and-payment-setting.create')
                ->with('success', 'Settings saved successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }

    public function saleInvoiceAndPaymentSettingCreate(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        $chartOfAccountList = DB::table('chart_of_accounts')->where('company_id', Session::get('company_id'))->where('status', 1)->get();
        $type = '2';
        $url = route('sale-invoice-and-payment-setting.store');
        $savedSettings = DB::table('invoices_payments_receipts_settings')
            ->where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->where('type',2)
            ->pluck('acc_id', 'option_id')
            ->toArray();
        return view('purchase-invoice-and-payment-setting.create', compact('chartOfAccountList','type','url','savedSettings'));
    }

    public function saleInvoiceAndPaymentSettingStore(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        try {
            // Validate request
            $validatedData = $request->validate([
                'option_id'   => 'required|array',
                'option_id.*' => 'required|integer|in:1,2,3,4',
                'acc_id'      => 'required|array',
                'acc_id.*'    => 'nullable|exists:chart_of_accounts,id',
            ]);

            foreach ($validatedData['option_id'] as $index => $optionId) {
                $accountId = $validatedData['acc_id'][$index] ?? null;

                if ($accountId) {
                    // Check if already exists
                    $exists = DB::table('invoices_payments_receipts_settings')
                        ->where('option_id', $optionId)
                        ->where('company_id', $companyId)
                        ->where('type',2)
                        ->where('company_location_id', $companyLocationId)
                        ->first();

                    if ($exists) {
                        // Update record
                        DB::table('invoices_payments_receipts_settings')
                            ->where('id', $exists->id)
                            ->update([
                                'acc_id' => $accountId,
                                'updated_by' => auth()->user()->name,
                                'updated_date' => date('Y-m-d')

                            ]);
                    } else {
                        // Insert new
                        DB::table('invoices_payments_receipts_settings')->insert([
                            'type' => 2,
                            'company_id' => $companyId,
                            'company_location_id' => $companyLocationId,
                            'option_id' => $optionId,
                            'acc_id' => $accountId,
                            'status' => 1,
                            'created_by' => auth()->user()->name,
                            'created_date' => date('Y-m-d')
                        ]);
                    }
                }
            }

            return redirect()
                ->route('sale-invoice-and-payment-setting.create')
                ->with('success', 'Settings saved successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Unexpected error: ' . $e->getMessage()]);
        }
    }


    //Start Profit and Loss Report Setting Start
        public function profitAndLossReportSettingIndex(Request $request){
            if($request->ajax()){
                $profitAndLossReportSettingsList =  DB::table('profit_and_loss_report_settings as palrs')
                    ->join('chart_of_accounts as coa','palrs.acc_id','=','coa.id')
                    ->select('palrs.*','coa.name')
                    ->where('palrs.company_id',Session::get('company_id'))
                    ->where('palrs.company_location_id',Session::get('company_location_id'))
                    ->get();
    
                return view('profit-and-loss-report-settings.indexAjax', compact('profitAndLossReportSettingsList'));
            }
            return view('profit-and-loss-report-settings.index');
        }

        public function payableAndReceivableReportSettingCreate(){
            // Return error response if accessed via API
            if ($this->isApi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid endpoint for API.',
                ], 400);
            }
            $chartOfAccountList = DB::table('chart_of_accounts')->where('company_id', Session::get('company_id'))->where('status', 1)->get();

            return view('payable-and-receivable-report-settings.create', compact('chartOfAccountList'));
        }

        public function payableAndReceivableReportSettingStore(Request $request){
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'option_id' => 'required|array',
                'option_id.*' => 'required|integer|in:1,2',
                'acc_id' => 'required|array',
                'acc_id.*' => 'nullable|exists:chart_of_accounts,id',
            ]);

            // Loop through each option_id
            foreach ($validatedData['option_id'] as $index => $optionId) {
                $accountId = $validatedData['acc_id'][$index];

                if ($accountId) {
                    // Check if a record exists
                    $payableAndReceivableReportSetting = PayableAndReceivableReportSetting::where('option_id', $optionId)
                        ->where('company_id',$companyId)
                        ->where('company_location_id',$companyLocationId)
                        ->first();

                    if ($payableAndReceivableReportSetting) {
                        // Update existing record
                        $payableAndReceivableReportSetting->acc_id = $accountId;
                        $payableAndReceivableReportSetting->save();
                    } else {
                        // Insert new record
                        $newPayableAndReceivableSetting = new PayableAndReceivableReportSetting();
                        $newPayableAndReceivableSetting->option_id = $optionId;
                        $newPayableAndReceivableSetting->acc_id = $accountId;
                        $newPayableAndReceivableSetting->save();
                    }
                }
            }

            // Call a helper function to regenerate JSON if applicable
            generate_json('payable_and_receivable_report_settings');

            // Redirect with success message
            return redirect()->route('payable-and-receivable-report-settings.index')
            ->with('success', 'Payable and Receivable Report Settings Created or Updated Successfully');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
        }

        public function payableAndReceivableReportSettingIndex(Request $request){
            if($request->ajax()){
                $payableAndReceivableReportSettingsList =  DB::table('payable_and_receivable_report_settings as parrs')
                    ->join('chart_of_accounts as coa','parrs.acc_id','=','coa.id')
                    ->select('parrs.*','coa.name')
                    ->where('parrs.company_id',Session::get('company_id'))
                    ->where('parrs.company_location_id',Session::get('company_location_id'))
                    ->get();
    
                return view('payable-and-receivable-report-settings.indexAjax', compact('payableAndReceivableReportSettingsList'));
            }
            return view('payable-and-receivable-report-settings.index');
        }

        public function profitAndLossReportSettingCreate(){
            $company_id = session('company_id');
            $company_location_id = session('company_location_id');
            $mainAccountsList = DB::table('chart_of_accounts')->where('parent_code',0)->where('company_id',$company_id)->where('company_location_id',$company_location_id)->where('status',1)->get();
            return view('profit-and-loss-report-settings.create',compact('mainAccountsList'));
        }

        public function profitAndLossReportSettingStore(Request $request){
            // Validation rules
            $validator = Validator::make($request->all(), [
                'acc_id'   => 'required|array',
                'acc_type' => 'required|array',
                'acc_id.*'   => 'required|integer|distinct',
                'acc_type.*' => 'required|integer|in:0,1,2,3,4', // Assuming acc_type must be 0,1,2,3 or 4
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
            
                DB::table('profit_and_loss_report_settings')->updateOrInsert(
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

            return view('profit-and-loss-report-settings.index');
        }
    //End Profit and Loss Report Setting End
}
