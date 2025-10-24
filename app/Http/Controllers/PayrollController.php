<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Auth;
use App\Models\JournalVoucher;
use Carbon\Carbon;
class PayrollController extends Controller
{
    public function create(Request $request){
        $normalAllowance = DB::table('allowance_type')->where('type',1)->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->get();
        $additionalAllowance = DB::table('allowance_type')->where('type',2)->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->get();
        $deductionType = DB::table('deduction_type')->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->get();
        if($request->ajax()){
            $filterEmployeeType = $request->input('filterEmployeeType');
            $filterJobType = $request->input('filterJobType');
            $filterEmploymentStatus = $request->input('filterEmploymentStatus');
            $month_year = $request->input('month_year');

            $data['filterEmployeeType'] = $filterEmployeeType;
            $data['filterJobType'] = $filterJobType;
            $data['filterEmploymentStatus'] = $filterEmploymentStatus;
            $data['month_year'] = $month_year;

            
            $employeeList = DB::table('employees as e')
                ->leftJoin('employee_allowance_detail as ead', 'e.id', '=', 'ead.employee_id')
                ->select('e.id','e.emp_no','e.emp_name','e.basic_salary', DB::raw('MAX(ead.id) AS eadId'))
                ->where('e.emp_type', $filterEmployeeType)
                ->where('e.company_id',Session::get('company_id'))
                ->where('e.company_location_id',Session::get('company_location_id'))
                ->groupBy('e.id','e.emp_no','e.emp_name','e.basic_salary')
                ->get();

            // $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            //     ->where('option_id', 3)
            //     ->where('company_id', Session::get('company_id'))
            //     ->where('company_location_id', Session::get('company_location_id'))->first();
            // if (empty($chartOfAccountSettingDetail)) {
                $chartOfAccountList = DB::table('chart_of_accounts')
                    ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
                    ->where('company_id', Session::get('company_id'))
                    ->where('company_location_id', Session::get('company_location_id'))
                    ->where('status', 1)->get();
            // } else {
            //     $chartOfAccountList = DB::table('chart_of_account_settings as coas')
            //         ->join('chart_of_accounts as coa', 'coas.acc_id', '=', 'coa.id')
            //         ->select('coas.acc_id', 'coa.name', 'coa.code')
            //         ->where('coas.option_id', 3)
            //         ->where('coas.company_id', Session::get('company_id'))
            //         ->where('coas.company_location_id', Session::get('company_location_id'))->get();
            // }
            
            return view('payroll.createAjax',compact('employeeList','normalAllowance','additionalAllowance','deductionType','data','chartOfAccountList'));    
        }
        return view('payroll.create',compact('normalAllowance','additionalAllowance','deductionType'));
    }

    public function index(Request $request){
        if($request->ajax()){
            $filterEmployeeType = $request->input('filterEmployeeType');
            $filter_from_month_year = $request->input('filter_from_month_year');
            $filter_to_month_year = $request->input('filter_to_month_year');
            $filterJobType = $request->input('filterJobType');
            $filterEmploymentStatus = $request->input('filterEmploymentStatus');
            $getPayrollList = DB::table('employee_payroll_detail as epd')
                ->join('employee_payroll_data_detail as epdd', 'epd.id', '=', 'epdd.epd_id')
                ->select(
                    'epd.id',
                    'epd.employee_type_id',
                    'epd.month_year',
                    DB::raw('SUM(epdd.basic_salary) as total_basic_salary'),
                    DB::raw('SUM(epdd.total_allowance) as total_allowance'),
                    DB::raw('SUM(epdd.total_additional_allowance) as total_additional_allowance'),
                    DB::raw('SUM(epdd.gross_salary) as total_gross_salary'),
                    DB::raw('SUM(epdd.total_deduction) as total_deduction'),
                    DB::raw('SUM(epdd.net_salary) as total_net_salary')
                )
                ->whereBetween('epd.month_year', [$filter_from_month_year, $filter_to_month_year])
                ->when($filterEmployeeType != '', function ($q) use ($filterEmployeeType){
                    return $q->where('epd.employee_type_id','=',$filterEmployeeType);
                })
                ->where('company_id',Session::get('company_id'))
                ->groupBy('epd.id', 'epd.month_year','epd.employee_type_id')
                ->get();
            return view('payroll.indexAjax',compact('getPayrollList'));    
        }
        return view('payroll.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $empArray    = $request->input('emp_array');
            $companyId   = Session::get('company_id');
            $locationId  = Session::get('company_location_id');
            $username    = Auth::user()->name ?? 'system';
            $currentDate = now()->toDateString();
            $currentTime = now()->format('H:i:s');

            // Parent Payroll Record
            $epdId = DB::table('employee_payroll_detail')->insertGetId([
                'month_year'           => $request->input('month_year'),
                'employee_type_id'     => $request->input('filterEmployeeType'),
                'job_type_id'          => $request->input('filterJobType'),
                'employment_status_id' => $request->input('filterEmploymentStatus'),
                'status'               => 1,
                'created_by'           => $username,
                'created_date'         => $currentDate,
                'company_id'           => $companyId,
                'company_location_id'  => $locationId,
            ]);

            foreach ($empArray as $empId) {
                // Inputs
                $empBasicSalary              = $request->input("emp_basic_salary_$empId");
                $empNormalAllowance          = $request->input("emp_normal_allowance_$empId");
                $empTotalAllowance           = $request->input("emp_total_allowance_$empId");
                $empAdditionalAllowance      = $request->input("emp_additional_allowance_$empId");
                $empTotalAdditionalAllowance = $request->input("emp_total_additional_allowance_$empId");
                $empGrossSalary              = $request->input("emp_gross_salary_$empId");
                $empDeductionAmount          = $request->input("emp_deduction_amount_$empId");
                $empTotalDeduction           = $request->input("emp_total_deduction_$empId");
                $empNetSalary                = $request->input("emp_net_salary_$empId");

                // Slip No
                $slipStr = DB::selectOne("
                    SELECT MAX(CONVERT(SUBSTR(slip_no,4,LENGTH(SUBSTR(slip_no,4))-4),SIGNED INTEGER)) reg
                    FROM employee_payroll_data_detail 
                    WHERE SUBSTR(slip_no,-4,2) = ? AND SUBSTR(slip_no,-2,2) = ?
                ", [date('m'), date('y')])->reg ?? 0;

                $slipNo = 'SAL' . ($slipStr + 1) . date('my');

                $debitAccountId  = $request->input("debit_acc_id_$empId");
                $creditAccountId = $request->input("credit_acc_id_$empId");
                $monthYear       = $request->input('month_year');
                $monthYearDate   = Carbon::parse($monthYear.'-01')->endOfMonth()->toDateString();
                $description     = "Salary $monthYear Description";

                // Journal Voucher
                $journalVoucherId = DB::table('journal_vouchers')->insertGetId([
                    'company_id'          => $companyId,
                    'company_location_id' => $locationId,
                    'jv_date'             => $monthYearDate,
                    'jv_no'               => JournalVoucher::VoucherNo(),
                    'slip_no'             => $slipNo,
                    'voucher_type'        => 6,
                    'description'         => $description,
                    'username'            => $username,
                    'status'              => 1,
                    'jv_status'           => 2,
                    'date'                => $currentDate,
                    'time'                => $currentTime,
                    'approve_username'    => $username,
                    'approve_date'        => $currentDate,
                    'approve_time'        => $currentTime,
                    'delete_username'     => '-',
                ]);

                // Payroll Detail
                $epddId = DB::table('employee_payroll_data_detail')->insertGetId([
                    'epd_id'                     => $epdId,
                    'slip_no'                    => $slipNo,
                    'emp_id'                     => $empId,
                    'basic_salary'               => $empBasicSalary,
                    'total_allowance'            => $empTotalAllowance,
                    'total_additional_allowance' => $empTotalAdditionalAllowance,
                    'gross_salary'               => $empGrossSalary,
                    'total_deduction'            => $empTotalDeduction,
                    'net_salary'                 => $empNetSalary,
                    'status'                     => 1,
                    'created_by'                 => $username,
                    'created_date'               => $currentDate,
                    'jv_id'                      => $journalVoucherId,
                ]);

                // Journal Voucher Data
                $jvData = [
                    [
                        'journal_voucher_id' => $journalVoucherId,
                        'acc_id'             => $debitAccountId,
                        'description'        => $description,
                        'debit_credit'       => 1,
                        'amount'             => $empNetSalary,
                        'jv_status'          => 2,
                        'time'               => $currentTime,
                        'date'               => $currentDate,
                        'status'             => 1,
                        'username'           => $username,
                        'approve_username'   => $username,
                        'delete_username'    => '-',
                    ],
                    [
                        'journal_voucher_id' => $journalVoucherId,
                        'acc_id'             => $creditAccountId,
                        'description'        => $description,
                        'debit_credit'       => 2,
                        'amount'             => $empNetSalary,
                        'jv_status'          => 2,
                        'time'               => $currentTime,
                        'date'               => $currentDate,
                        'status'             => 1,
                        'username'           => $username,
                        'approve_username'   => $username,
                        'delete_username'    => '-',
                    ]
                ];
                DB::table('journal_voucher_data')->insert($jvData);

                // Transactions
                $paymentDataDetails = DB::table('journal_voucher_data')
                    ->where('journal_voucher_id', $journalVoucherId)
                    ->get();

                $transactions = $paymentDataDetails->map(function($pddRow) use ($companyId, $locationId, $journalVoucherId, $monthYearDate, $username, $currentDate, $currentTime) {
                    return [
                        'company_id'          => $companyId,
                        'company_location_id' => $locationId,
                        'acc_id'              => $pddRow->acc_id,
                        'particulars'         => $pddRow->description,
                        'opening_bal'         => 2,
                        'debit_credit'        => $pddRow->debit_credit,
                        'amount'              => $pddRow->amount,
                        'voucher_id'          => $journalVoucherId,
                        'record_data_id'      => $pddRow->id,
                        'voucher_type'        => 8,
                        'v_date'              => $monthYearDate,
                        'date'                => $currentDate,
                        'time'                => $currentTime,
                        'username'            => $username,
                        'status'              => 1,
                    ];
                })->toArray();

                if (!empty($transactions)) {
                    DB::table('transaction')->insert($transactions);
                }

                // Allowances + Deductions
                if (!empty($empNormalAllowance)) {
                    foreach ($empNormalAllowance as $enaRow) {
                        DB::table('employee_payroll_allowance_detail')->insert([
                            'epdd_id'     => $epddId,
                            'type'        => 1,
                            'at_id'       => $enaRow,
                            'amount'      => $request->input("normal_allowance_{$empId}_$enaRow"),
                            'status'      => 1,
                            'created_by'  => $username,
                            'created_date'=> $currentDate,
                        ]);
                    }
                }

                if (!empty($empAdditionalAllowance)) {
                    foreach ($empAdditionalAllowance as $eaaRow) {
                        DB::table('employee_payroll_allowance_detail')->insert([
                            'epdd_id'     => $epddId,
                            'type'        => 2,
                            'at_id'       => $eaaRow,
                            'amount'      => $request->input("additional_allowance_{$empId}_$eaaRow"),
                            'status'      => 1,
                            'created_by'  => $username,
                            'created_date'=> $currentDate,
                        ]);
                    }
                }

                if (!empty($empDeductionAmount)) {
                    foreach ($empDeductionAmount as $edaRow) {
                        DB::table('employee_payroll_deduction_detail')->insert([
                            'epdd_id'     => $epddId,
                            'dt_id'       => $edaRow,
                            'amount'      => $request->input("deduction_amount_{$empId}_$edaRow"),
                            'status'      => 1,
                            'created_by'  => $username,
                            'created_date'=> $currentDate,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('payroll.index')->with('message', 'Payroll Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Payroll creation failed: '.$e->getMessage());
        }
    }
    
    public function show(Request $request){
        $id = $request->route('id');
        $viewEmployeeSalaryDetail = DB::table('employee_payroll_data_detail as epdd')
            ->join('employees as e', 'epdd.emp_id', '=', 'e.id')
            ->join('employee_payroll_detail as epd','epdd.epd_id','=','epd.id')
            ->select('epdd.*','e.emp_no','e.emp_name','e.emp_type','e.emp_father_name','e.date_of_birth','e.cnic_no','e.address','e.emp_email','e.phone_no','e.maritarial_status','e.job_type','e.employment_status','epd.month_year')
            ->where('epdd.epd_id',$id)
            ->where('epd.company_id',Session::get('company_id'))
            ->where('epd.company_location_id',Session::get('company_location_id'))
            ->get();
        return view('payroll.viewEmployeeSalaryDetail',compact('viewEmployeeSalaryDetail'));
    }
}
