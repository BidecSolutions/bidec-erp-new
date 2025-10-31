<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CustomAuthController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }
    public function set_user_db_id(Request $request)
    {
        $company_id = $request->company_id;
        $company_name = $request->company_name;
        $company_code = $request->company_code;
        $company_location_id = $request->company_location_id;
        $company_location_name = $request->company_location_name;

        $request->session()->put('company_id', $company_id);
        $request->session()->put('company_name', $company_name);
        $request->session()->put('company_code', $company_code);
        $request->session()->put('company_location_id', $company_location_id);
        $request->session()->put('company_location_name', $company_location_name);
        return redirect()->intended('/dashboard');;
    }
    public function customLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            Log::info('Login successful for username: ' . $request->username);

            // Clear any custom session data
            Session::put('run_company', '');

            $userDetail = Auth::user();
            $companyIds = $userDetail->company_id;
            $accType = $userDetail->acc_type;

            // If the account type is 'owner' or 'superadmin'
            if (in_array($accType, ['client','owner', 'superadmin'])) {
                // Exploding company IDs and selecting the first one if only one exists
                $explodeCompanyIds = explode("<*>", $companyIds);
                $companyId = (count($explodeCompanyIds) == 1) ? $explodeCompanyIds[0] : '';
                if (!empty($companyId)) {
                    // Determine the school campus ID
                    $companyLocationId = '';
                    if ($userDetail->emp_type_multiple_campus == 2) {
                        $schoolCampusIdsArray = is_string($userDetail->school_campus_ids_array)
                            ? json_decode($userDetail->school_campus_ids_array, true) // Decode the JSON string to array
                            : $userDetail->school_campus_ids_array;
                        // Select school campus ID if only one campus exists
                        $companyLocationId = isset($schoolCampusIdsArray[0]['school_campus_id']) ? $schoolCampusIdsArray[0]['school_campus_id'] : '';
                    } else {
                        // Use the default campus ID
                        $companyLocationId = $userDetail->company_location_id;
                    }

                    if (in_array($accType, ['client','owner'])) {
                        $companyLocationDetail = DB::table('company_locations')->where('company_id', $companyId)->first();
                        $companyLocationId = $companyLocationDetail->id;
                    }


                    // Retrieve company and school campus details if companyId and schoolCampusId are available
                    if ($companyId) {
                        $companyDetail = DB::table('companies')->where('id', $companyId)->first();
                        if ($companyDetail) {
                            Session::put('company_name', $companyDetail->name);
                            Session::put('company_code', $companyDetail->company_code);
                        }
                    }

                    if ($companyLocationId) {
                        $companyLocationDetail = DB::table('company_locations')->where('id', $companyLocationId)->first();
                        if ($companyLocationDetail) {
                            Session::put('company_location_name', $companyLocationDetail->name);
                        }
                    }

                    // Store session values
                    Session::put('company_location_id', $companyLocationId);
                    Session::put('company_id', $companyId);
                }
            }

            // Redirect based on account type
            switch ($accType) {
                case 'parent':
                    return redirect()->intended('/parents/dashboard');
                case 'user':
                    return $this->updateSessionDetail($request);
                case 'superadmin':
                    return redirect()->intended('/superadmin/dashboard');
                default:
                    return redirect()->intended('dashboard');
            }
        }
        return redirect("login")->withSuccess('Login details are not valid');
    }
    public function registration()
    {
        return view('auth.register');
    }
    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data = $request->all();
        $check = $this->create($data);
        return redirect("dashboard")->withSuccess('You have signed-in');
    }
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
    public function dashboard(Request $request)
    {
        if (!$request->ajax()) {
            return Auth::check()
                ? view('dashboard.dashboard')
                : redirect('login')->withErrors('You are not allowed to access this page.');
        }

        $companyId = Session::get('company_id');
        $companyLocationId = $request->input('companyLocation');
        $fromDate = $request->input('fromDate') ?? now()->startOfMonth()->toDateString();
        $toDate = $request->input('toDate') ?? now()->endOfMonth()->toDateString();

        // 🔹 Get Report Settings (Codes by Type)
        $settings = DB::table('profit_and_loss_report_settings as bsrs')
            ->join('chart_of_accounts as coa', 'bsrs.acc_id', '=', 'coa.id')
            ->where('bsrs.company_id', $companyId)
            ->when($companyLocationId, fn($q) => $q->where('bsrs.company_location_id', $companyLocationId))
            ->whereIn('bsrs.acc_type', [1, 2, 3, 4])
            ->select('bsrs.acc_type', 'coa.code')
            ->get()
            ->groupBy('acc_type');

        $getCodes = fn($type) => $settings[$type]->pluck('code')->toArray() ?? [];

        $revenueCodes = $settings->get(1)?->pluck('code')->toArray() ?? [];
        $expenseCodes = $settings->get(2)?->pluck('code')->toArray() ?? [];
        $cogsCodes    = $settings->get(3)?->pluck('code')->toArray() ?? [];
        $salesCodes   = $settings->get(4)?->pluck('code')->toArray() ?? [];

        // 🔹 Helper for Summary Calculation
        $getSummary = function (array $codes, int $creditCondition) use ($companyId, $fromDate, $toDate) {
            if (empty($codes)) return collect();

            return DB::table('transaction as t')
                ->join('chart_of_accounts as coa', 't.acc_id', '=', 'coa.id')
                ->select('coa.id', 'coa.name',
                    DB::raw("SUM(CASE WHEN t.debit_credit = {$creditCondition} THEN t.amount ELSE 0 END) as total"))
                ->where('t.company_id', $companyId)
                ->whereBetween('t.v_date', [$fromDate, $toDate])
                ->where(function ($q) use ($codes) {
                    foreach ($codes as $code) {
                        $q->orWhere('coa.level1', 'like', $code);
                    }
                })
                ->groupBy('coa.id', 'coa.name')
                ->get();
        };

        // 🔹 Summary Data
        $revenues = $getSummary($revenueCodes, 2);
        $expenses = $getSummary($expenseCodes, 1);
        $cogs = $getSummary($cogsCodes, 1);
        $sales = $getSummary($salesCodes, 2);

        $sumRevenue = $revenues->sum('total');
        $sumExpense = $expenses->sum('total');
        $sumCOGS = $cogs->sum('total');
        $sumSale = $sales->sum('total');
        $grossProfit = $sumSale - $sumCOGS;
        $netProfit = ($sumRevenue + $grossProfit) - $sumExpense;

        // 🔹 Common Aggregation Function
        $aggregate = function (string $table, string $joinTable, string $foreignKey, array $filters) 
            use ($companyId, $companyLocationId, $fromDate, $toDate) {

            $query = DB::table("{$table} as t")
                ->join("{$joinTable} as d", "t.id", "=", "d.{$foreignKey}")
                ->where('t.company_id', $companyId)
                ->when($companyLocationId, fn($q) => $q->where('t.company_location_id', $companyLocationId))
                ->whereBetween('t.date', [$fromDate, $toDate])
                ->where('t.status', 1)
                ->where('d.debit_credit', 1);

            foreach ($filters as $key => $value) {
                $query->where("t.{$key}", $value);
            }

            return $query->selectRaw('COUNT(t.id) as total_count, COALESCE(SUM(d.amount),0) as total_amount')->first();
        };

        // 🔹 Payment / Receipt Summary
        $pendingPayment  = $aggregate('payments', 'payment_data', 'payment_id', ['pv_status' => 1, 'entry_option' => 1]);
        $approvedPayment = $aggregate('payments', 'payment_data', 'payment_id', ['pv_status' => 2, 'entry_option' => 1]);
        $pendingReceipt  = $aggregate('receipts', 'receipt_data', 'receipt_id', ['rv_status' => 1, 'rv_type' => 1]);
        $approvedReceipt = $aggregate('receipts', 'receipt_data', 'receipt_id', ['rv_status' => 2, 'rv_type' => 1]);

        // 🔹 Purchase & Sales Summary
        $invoiceSummary = function (int $type) use ($companyId, $companyLocationId, $fromDate, $toDate) {
            return DB::table('purchase_sale_invoices as psi')
                ->where('psi.company_id', $companyId)
                ->when($companyLocationId, fn($q) => $q->where('psi.company_location_id', $companyLocationId))
                ->whereBetween('psi.invoice_date', [$fromDate, $toDate])
                ->where('psi.invoice_type', $type)
                ->selectRaw('COUNT(id) as total_count, COALESCE(SUM(amount),0) as total_amount, COALESCE(SUM(remaining_amount),0) as total_remaining')
                ->first();
        };

        $purchaseSummary = $invoiceSummary(1);
        $salesSummary = $invoiceSummary(2);

        // ✅ Return AJAX-rendered view
        return view('dashboard.dashboardAjax', compact(
            'fromDate', 'toDate',
            'pendingPayment', 'approvedPayment', 'pendingReceipt', 'approvedReceipt',
            'purchaseSummary', 'salesSummary',
            'sumRevenue', 'sumExpense', 'sumCOGS', 'sumSale', 'grossProfit', 'netProfit',
            'revenues', 'expenses', 'cogs', 'sales'
        ));
    }



    public function getTopSellingProducts(Request $request)
    {
        $year = $request->input('year', date('Y')); // Default to current year if not provided
        $schoolCampusId = $request->input('schoolCampusId');

        if (!$schoolCampusId) {
            return response()->json(['error' => 'User session expired. Please log in again.'], 401);
        }
        if (!$year) {
            return response()->json(['error' => 'Year parameter is required'], 400);
        }

        $topSellingProducts = DB::table('cart_items as ci')
            ->join('products as p', 'ci.product_id', '=', 'p.id')
            ->join('carts as c', 'ci.cart_id', '=', 'c.id')
            ->select('p.name', DB::raw('SUM(ci.qty) as total_sold'))
            ->where('c.status', 1) // Only confirmed sales
            ->whereRaw('YEAR(c.created_date) = ?', [$year]) // Filter by year using created_date
            ->where('c.company_location_id', $schoolCampusId) // Filter by location
            ->groupBy('p.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json($topSellingProducts);
    }
    public function getWeeklySalesAndPurchases(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $week = $request->input('week', date('W'));
        $schoolCampusId = $request->input('schoolCampusId');

        $weeklySalesQuery = DB::table('carts')
            ->where('status', 1)
            ->whereYear('created_date', $year)
            ->whereRaw("WEEKOFYEAR(created_date) = ?", [$week])
            ->selectRaw('DAYNAME(created_date) as day, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");

        if ($schoolCampusId) {
            $weeklySalesQuery->where('company_location_id', $schoolCampusId);
        }
        $weeklySales = $weeklySalesQuery->get();

        $weeklyPurchasesQuery = DB::table('grn_datas as g')
            ->join('purchase_order_datas as p', 'g.po_data_id', '=', 'p.id')
            ->whereYear('g.created_date', $year)
            ->whereRaw("WEEKOFYEAR(g.created_date) = ?", [$week])
            ->selectRaw('DAYNAME(g.created_date) as day, SUM(g.receive_qty * p.unit_price) as total')
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");

        if ($schoolCampusId) {
            $weeklyPurchasesQuery->where('g.company_location_id', $schoolCampusId);
        }
        $weeklyPurchases = $weeklyPurchasesQuery->get();

        return response()->json(['weeklySales' => $weeklySales, 'weeklyPurchases' => $weeklyPurchases]);
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

    public function forgetPasswordForm()
    {
        return view('auth.forgetPasswordForm');
    }

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        $token = Str::random(64);

        $data = array(
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        );
        $user = User::where('email', $request->email)->first();

        PasswordResetToken::where('email', $request->email)->delete();
        PasswordResetToken::insert($data);

        $array['view'] = 'email.forgetPassword';
        $array['subject'] = 'One Time PIN (OTP) for login on new device';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['user'] = $user;
        $array['token'] = $token;
        try {
            Mail::to($request->email)->queue(new ForgetPasswordMail($array));
            return Redirect('login');
            //return $this->sendResponse($data, 'We have e-mailed your password reset link!');
            //return Redirect('/resetPasswordForm/'.$token.'');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function resetPasswordForm($token)
    {
        return view('auth.resetPasswordForm', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        try {



            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => ['required', 'confirmed', 'min:8'],
                'password_confirmation' => 'required'
            ]);



            $updatePassword = PasswordResetToken::where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

            if (!$updatePassword) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Token',
                ]);
            }

            $user = User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);
            $user2 = User::where([['email', '=', $request->email]])->first();

            PasswordResetToken::where(['email' => $request->email])->delete();
            return Redirect('login');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function change_password_form()
    {
        return view('auth.change_password_form');
    }

    public function changePassword(Request $request)
    {
        try {
            $id = $request->input('id');
            $user = User::find($id);
            $input = $request->all();
            $validator = Validator::make($input, [
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $user->password = Hash::make($input['password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password Changed successfully',
                'data' => null,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function changePasswordTwo(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Check if the old password matches the user's current password
            $user = Auth::user(); // Assuming the user is authenticated
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password is incorrect');
            }

            // Update the user's password
            $user->sgpe = $request->password;
            $user->password = Hash::make($request->password);
            $user->save();

            // Return success message
            return redirect()->back()->with('success', 'Password changed successfully');
        } catch (\Throwable $th) {
            // Handle exceptions and return error response
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
