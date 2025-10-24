<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Models\Fee;
use App\Helpers\CommonHelper;
use App\Models\JournalVoucher;
use Carbon\Carbon;

class FeeController extends Controller
{
    private $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function index(Request $request){
        if($request->ajax()){
            $studentId = $request->input('filter_student_id');
            $filter_from_date = $request->input('filter_from_date');
            $filter_to_date = $request->input('filter_to_date');

            $feesList = DB::table('fees as f')
                ->select('f.*', 's.student_name', 's.registration_no', 'spagi.father_name')
                ->join('students as s', 'f.student_id', '=', 's.id')
                ->leftJoin('student_parent_and_guardian_informations as spagi', 'spagi.student_id', '=', 's.id')
                ->whereBetween('f.month_year', [$filter_from_date, $filter_to_date])
                ->when($studentId != '', function ($q) use ($studentId){
                    return $q->where('s.id','=',$studentId);
                })
                ->get();
            return view('fees.indexAjax',compact('feesList'));
        }
        $getAllStudents = CommonHelper::get_all_students(1);
        return view('fees.index',compact('getAllStudents'));
    }

    public function generate_fee_voucher(){
        $getAllStudents = CommonHelper::get_all_students(1);
        $getAllSections = CommonHelper::get_all_sections(1);
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
        return view('fees.generate-fee-voucher',compact('getAllStudents','getAllSections','chartOfAccountList'));
    }

    public function generate_fee_voucher_store(Request $request)
    {
        $companyId     = Session::get('company_id');
        $locationId    = Session::get('company_location_id');
        $username      = Auth::user()->name ?? 'system';
        $currentDate   = now()->toDateString();
        $currentTime   = now()->format('H:i:s');
        $monthYear = $request->input('month_year'); // e.g. "2025-11"
        $monthYearDate = Carbon::parse($monthYear . '-01')->endOfMonth()->toDateString();
        $description   = $request->input('description');
        $classId       = $request->input('class_id');

        $students = DB::table('students as s')
            ->select('s.id', 's.registration_no', 'c.fee_amount')
            ->join('classes as c', 's.class_id', '=', 'c.id')
            ->where([
                ['s.company_id', $companyId],
                ['s.company_location_id', $locationId],
                ['s.class_id', $classId],
                ['s.status', 1],
            ])
            ->get();

        if ($students->isEmpty()) {
            return redirect()->route('fees.generate-fee-voucher-list')
                ->with('warning', 'No active students found for this class.');
        }

        DB::beginTransaction();
        try {
            $journalVoucher = new JournalVoucher();
            $journalVoucher->company_id          = $companyId;
            $journalVoucher->company_location_id = $locationId;
            $journalVoucher->jv_date             = $monthYearDate;
            $journalVoucher->jv_no               = JournalVoucher::VoucherNo();
            $journalVoucher->slip_no             = 'fees -' . $monthYearDate;
            $journalVoucher->voucher_type        = 4; //Generate Fee Voucher
            $journalVoucher->description         = $description;
            $journalVoucher->username            = $username;
            $journalVoucher->status              = 1;
            $journalVoucher->jv_status           = 2;
            $journalVoucher->date                = $currentDate;
            $journalVoucher->time                = $currentTime;
            $journalVoucher->approve_username    = $username;
            $journalVoucher->approve_date        = $currentDate;
            $journalVoucher->approve_time        = $currentTime;
            $journalVoucher->delete_username     = '-';
            $journalVoucher->save();

            $generateFeeVoucherId = DB::table('generate_fee_vouchers')->insertGetId([
                'company_id'          => $companyId,
                'company_location_id' => $locationId,
                'month_year'          => $monthYearDate,
                'description'         => $description,
                'class_id'            => $classId,
                'jv_id'               => $journalVoucher->id,
                'status'              => 1,
                'created_by'          => $username,
                'created_date'        => $currentDate,
            ]);

            $dataDetail  = [];
            $totalAmount = 0;

            foreach ($students as $student) {
                $totalAmount += $student->fee_amount;
                $dataDetail[] = [
                    'company_id'              => $companyId,
                    'company_location_id'     => $locationId,
                    'generate_fee_voucher_id' => $generateFeeVoucherId,
                    'student_id'              => $student->id,
                    'amount'                  => $student->fee_amount,
                    'status'                  => 1,
                    'created_by'              => $username,
                    'created_date'            => $currentDate,
                ];
            }
            DB::table('generate_fee_voucher_datas')->insert($dataDetail);

            $jvData = [
                [
                    'journal_voucher_id' => $journalVoucher->id,
                    'acc_id'             => $request->input('debit_acc_id'),
                    'description'        => $description,
                    'debit_credit'       => 1, // Debit
                    'amount'             => $totalAmount,
                ],
                [
                    'journal_voucher_id' => $journalVoucher->id,
                    'acc_id'             => $request->input('credit_acc_id'),
                    'description'        => $description,
                    'debit_credit'       => 2, // Credit
                    'amount'             => $totalAmount,
                ],
            ];

            foreach ($jvData as &$row) {
                $row = array_merge($row, [
                    'jv_status'        => 2,
                    'time'             => $currentTime,
                    'date'             => $currentDate,
                    'status'           => 1,
                    'username'         => $username,
                    'approve_username' => $username,
                    'delete_username'  => '-',
                ]);
            }
            DB::table('journal_voucher_data')->insert($jvData);

            $paymentDataDetails = DB::table('journal_voucher_data')->where('journal_voucher_id', $journalVoucher->id)->get();

            foreach ($paymentDataDetails as $pddRow) {
                $transactions[] = [
                    'company_id' => $companyId,
                    'company_location_id' => $locationId,
                    'acc_id' => $pddRow->acc_id,
                    'particulars' => $pddRow->description,
                    'opening_bal' => 2,
                    'debit_credit' => $pddRow->debit_credit,
                    'amount' => $pddRow->amount,
                    'voucher_id' => $journalVoucher->id,
                    'record_data_id' => $pddRow->id,
                    'voucher_type' => 6, //Fees Journal Voucher
                    'v_date' => $monthYearDate,
                    'date' => $currentDate,
                    'time' => $currentTime,
                    'username' => $username,
                    'status' => 1
                ];
            }

            // Insert all transactions at once
            if (!empty($transactions)) {
                DB::table('transaction')->insert($transactions);
            }

            DB::commit();

            return redirect()->route('fees.generate-fee-voucher-list')
                ->with('message', 'Generate Fee Voucher Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function generate_fee_voucher_list(Request $request){
        if($request->ajax()){
            $getGenerateFeeVoucherList = DB::table('generate_fee_vouchers as gfv')
                ->select('gfv.*')
                ->selectSub(function ($query) {
                    $query->selectRaw('SUM(gfvd.amount)')
                        ->from('generate_fee_voucher_datas as gfvd')
                        ->whereColumn('gfv.id', 'gfvd.generate_fee_voucher_id');
                }, 'totalVoucherAmount')
                ->where('gfv.company_id',Session::get('company_id'))
                ->where('gfv.company_location_id',Session::get('company_location_id'))
                ->get();
            return view('fees.generate-fee-voucher-list-ajax',compact('getGenerateFeeVoucherList'));
        }
        return view('fees.generate-fee-voucher-list');
    }

    public function viewGeneratedFeeVouchersMultiple(Request $request){
        $generatedFeeVoucherId = $request->input('id');
        $schoolId = Session::get('company_id');
        $schoolCampusId = Session::get('company_location_id');

        $generatedFeeVoucherDataList = DB::table('generate_fee_voucher_datas as gfvd')
            ->join('generate_fee_vouchers as gfv','gfvd.generate_fee_voucher_id','=','gfv.id')
            ->join('students as s','gfvd.student_id','=','s.id')
            ->join('sections as sec','s.section_id','=','sec.id')
            ->join('classes as c','sec.class_id','=','c.id')
            ->leftjoin('student_parent_and_guardian_informations as spagi', 'gfvd.student_id', '=', 'spagi.student_id')
            ->select(
                's.student_name','s.registration_no',
                'spagi.mobile_no','spagi.father_name',
                'sec.section_name','c.class_no','c.class_name',
                'gfvd.amount','gfv.month_year','gfv.description','gfvd.student_id','gfvd.id',
                DB::raw('(SELECT SUM(gfvd.amount) 
                        FROM generate_fee_voucher_datas AS gfvd 
                        WHERE gfvd.student_id = s.id) AS totalPaymentAmount'),
                DB::raw('(SELECT SUM(f2.amount) 
                        FROM fees AS f2 
                        WHERE s.id = f2.student_id) AS totalReceiptAmount'))
            ->where('gfv.id',$generatedFeeVoucherId)
            ->get();
        return view('fees.viewGeneratedFeeVouchersMultiple',compact('generatedFeeVoucherDataList'));
    }

    

    public function student_wise_fee_voucher_list(Request $request){
        $getAllStudents = CommonHelper::get_all_students(1);
        if($request->ajax()){
            $studentId = $request->input('filter_student_id');
            $startDate = $request->input('filter_from_date');
            $endDate = $request->input('filter_to_date');
            $getData = DB::table('generate_fee_voucher_datas as gfvd')
                ->join('generate_fee_vouchers as gfv', 'gfvd.generate_fee_voucher_id', '=', 'gfv.id')
                ->join('students as s', 'gfvd.student_id', '=', 's.id')
                ->leftjoin('student_parent_and_guardian_informations as spagi', 'gfvd.student_id', '=', 'spagi.student_id')
                ->select(
                    'gfvd.id',
                    'gfvd.student_id',
                    'gfv.month_year',
                    'gfv.description',
                    'gfvd.amount',
                    's.registration_no',
                    's.student_name',
                    'spagi.father_name',
                    'spagi.mobile_no'
                )
                ->whereBetween('gfv.month_year', [$startDate, $endDate])
                ->when($studentId != '', function ($q) use ($studentId){
                    return $q->where('gfvd.student_id','=',$studentId);
                })
                ->where('gfvd.company_id',Session::get('company_id'))
                ->where('gfvd.company_location_id',Session::get('company_location_id'))
                ->get();
            return view('fees.student-wise-fee-voucher-list-ajax',compact('getData'));
        }
        return view('fees.student-wise-fee-voucher-list',compact('getAllStudents'));
    }

    public function receipt_voucher_against_fees(Request $request){
        $getAllStudents = CommonHelper::get_all_students(1);
        if($request->ajax()){
            $studentId = $request->input('filter_student_id');
            $getData = DB::table('generate_fee_voucher_datas as gfvd')
                ->select('s.registration_no','s.student_name','spagi.father_name','gfvd.id','gfvd.generate_fee_voucher_id','gfvd.student_id','gfvd.amount','gfvd.fee_voucher_status','gfv.month_year','gfv.description','gfv.jv_id')
                ->join('generate_fee_vouchers as gfv','gfvd.generate_fee_voucher_id','=','gfv.id')
                ->join('students as s', 'gfvd.student_id', '=', 's.id')
                ->leftJoin('student_parent_and_guardian_informations as spagi','spagi.student_id','=','gfvd.student_id')
                ->when($studentId != '', function ($q) use ($studentId){
                    return $q->where('gfvd.student_id','=',$studentId);
                })
                ->where('gfvd.company_id',Session::get('company_id'))
                ->where('gfvd.company_location_id',Session::get('company_location_id'))
                ->where('gfvd.fee_voucher_status',1)
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
            return view('fees.receipt-voucher-against-fees-ajax',compact('getData','chartOfAccountList'));
        }
        return view('fees.receipt-voucher-against-fees',compact('getAllStudents'));
    }

    public function receipt_voucher_against_fees_store(Request $request)
    {
        $generateFeeVoucherDataIdsArray = $request->input('generate_fee_voucher_data_ids_array');
        $companyId     = Session::get('company_id');
        $locationId    = Session::get('company_location_id');
        $username      = Auth::user()->name ?? 'system';
        $currentDate   = now()->toDateString();
        $currentTime   = now()->format('H:i:s');

        DB::beginTransaction();
        try {
            $transactions = [];

            foreach ($generateFeeVoucherDataIdsArray as $gfvdia) {
                $receiptAmount        = $request->input("amount_$gfvdia");
                $studentId            = $request->input("receipt_voucher_s_id_$gfvdia");
                $generateFeeVoucherId = $request->input("receipt_voucher_gfv_id_$gfvdia");
                $feeVoucherStatus     = $request->input("fee_voucher_status_$gfvdia");
                $debitAccId           = $request->input("debit_acc_id_$gfvdia");
                $jvId                 = $request->input("jv_id_$gfvdia");

                // Process only if status == 2
                if ($feeVoucherStatus != 2) {
                    continue;
                }

                $registrationNo = Fee::RegistrationNo();
                $description    = "Receipt Fee Voucher - {$registrationNo}";

                // ✅ Create Journal Voucher
                $journalVoucher = new JournalVoucher();
                $journalVoucher->company_id          = $companyId;
                $journalVoucher->company_location_id = $locationId;
                $journalVoucher->jv_date             = $currentDate;
                $journalVoucher->jv_no               = JournalVoucher::VoucherNo();
                $journalVoucher->slip_no             = $registrationNo;
                $journalVoucher->voucher_type        = 5; // Receipt Fee Voucher
                $journalVoucher->description         = $description;
                $journalVoucher->username            = $username;
                $journalVoucher->status              = 1;
                $journalVoucher->jv_status           = 2;
                $journalVoucher->date                = $currentDate;
                $journalVoucher->time                = $currentTime;
                $journalVoucher->approve_username    = $username;
                $journalVoucher->approve_date        = $currentDate;
                $journalVoucher->approve_time        = $currentTime;
                $journalVoucher->delete_username     = '-';
                $journalVoucher->save();

                // ✅ Insert into Fees
                DB::table('fees')->insert([
                    'fee_registration_no'        => $registrationNo,
                    'company_id'                 => $companyId,
                    'company_location_id'        => $locationId,
                    'generate_fee_voucher_id'    => $generateFeeVoucherId,
                    'generate_fee_voucher_data_id' => $gfvdia,
                    'student_id'                 => $studentId,
                    'jv_id'                      => $journalVoucher->id,
                    'month_year'                 => $currentDate,
                    'amount'                     => $receiptAmount,
                    'status'                     => 1,
                    'created_by'                 => $username,
                    'created_date'               => $currentDate,
                ]);

                // ✅ Get Credit Account (from original JV)
                $creditAccDetail = DB::table('journal_voucher_data')
                    ->where('journal_voucher_id', $jvId)
                    ->where('debit_credit', 1)
                    ->first();

                // ✅ Journal Voucher Entries
                $jvData = [
                    [
                        'journal_voucher_id' => $journalVoucher->id,
                        'acc_id'             => $debitAccId,
                        'description'        => $description,
                        'debit_credit'       => 1, // Debit
                        'amount'             => $receiptAmount,
                    ],
                    [
                        'journal_voucher_id' => $journalVoucher->id,
                        'acc_id'             => $creditAccDetail->acc_id,
                        'description'        => $description,
                        'debit_credit'       => 2, // Credit
                        'amount'             => $receiptAmount,
                    ],
                ];

                foreach ($jvData as &$row) {
                    $row = array_merge($row, [
                        'jv_status'        => 2,
                        'time'             => $currentTime,
                        'date'             => $currentDate,
                        'status'           => 1,
                        'username'         => $username,
                        'approve_username' => $username,
                        'delete_username'  => '-',
                    ]);
                }
                DB::table('journal_voucher_data')->insert($jvData);

                // ✅ Transactions (build in memory, insert once after loop)
                $paymentDataDetails = DB::table('journal_voucher_data')
                    ->where('journal_voucher_id', $journalVoucher->id)
                    ->get();

                foreach ($paymentDataDetails as $pddRow) {
                    $transactions[] = [
                        'company_id'          => $companyId,
                        'company_location_id' => $locationId,
                        'acc_id'              => $pddRow->acc_id,
                        'particulars'         => $pddRow->description,
                        'opening_bal'         => 2,
                        'debit_credit'        => $pddRow->debit_credit,
                        'amount'              => $pddRow->amount,
                        'voucher_id'          => $journalVoucher->id,
                        'record_data_id'      => $pddRow->id,
                        'voucher_type'        => 7, // Receipt Fees Journal Voucher
                        'v_date'              => $currentDate,
                        'date'                => $currentDate,
                        'time'                => $currentTime,
                        'username'            => $username,
                        'status'              => 1,
                    ];
                }

                DB::table('generate_fee_voucher_datas')
                    ->where('id', $gfvdia)
                    ->update(['fee_voucher_status' => 2]);
            }

            if (!empty($transactions)) {
                DB::table('transaction')->insert($transactions);
            }

            DB::commit();
            return redirect()->route('fees.index')
                ->with('message', 'Receipt Voucher Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function receipt_voucher_list(){
        
    }

    public function create(){
        return view('fees.create');
    }

    public function addStudentFeesForm(Request $request){
        $studentId = $request->get('id');
        $student = DB::table('students as s')
            ->join('student_parent_and_guardian_informations as spagi','spagi.student_id','=','s.id')
            ->join('student_document_against_registrations as sdar','sdar.student_id','=','s.id')
            ->select('s.id','s.registration_no','s.student_name','s.date_of_admission','s.status',
            'spagi.father_name','spagi.parent_email','spagi.mobile_no','spagi.cnic_no','sdar.birth_certificate',
            'sdar.father_guardian_cnic','sdar.father_guardian_cnic_back','sdar.passport_size_photo','sdar.copy_of_last_report','d.department_name','d.department_fees','d.department_timing',
            's.class_timing','s.fees','s.concession_fees','s.consession_fees_image','e.emp_name')
            ->where('s.company_id',Session::get('company_id'))
            ->where('s.company_location_id',Session::get('company_location_id'))
            ->where('s.id',$studentId)
            ->first();
        return view('fees.addStudentFeesForm',compact('student'));
    }

    public function addStudentFeesDetail(Request $request){
        $data = $request->validate([
            'student_id' => 'required',
            'month_year' => 'required',
            'amount' => 'required'
        ]);

        $registrationNo = Fee::RegistrationNo();
        $data['fee_registration_no'] = $registrationNo;
        $data['company_id'] = Session::get('company_id');
        $data['company_location_id'] = Session::get('company_location_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        Fee::insert($data);
        return view('fees.index');
    }

    

    public function show(Request $request){
        $feeId = $request->input('id');
        $schoolId = Session::get('company_id');
        $schoolCampusId = Session::get('company_location_id');
        $feesDetail = DB::table('fees as f')
            ->join('students as s', 'f.student_id', '=', 's.id')
            ->leftjoin('student_parent_and_guardian_informations as spagi', 's.id', '=', 'spagi.student_id')
            ->join('sections as sec','s.section_id','=','sec.id')
            ->join('classes as c','sec.class_id','=','c.id')
            ->select(
                'f.id',
                'f.month_year',
                'f.amount',
                'f.status',
                'f.created_by',
                'f.created_date',
                'f.fee_registration_no',
                's.student_name',
                's.registration_no',
                'spagi.father_name',
                'spagi.mother_name',
                'spagi.father_qualification',
                'spagi.mother_qualification',
                'spagi.cnic_no',
                'spagi.mobile_no',
                'sec.section_name',
                'c.class_no','c.class_name',
                DB::raw('(SELECT SUM(gfvd.amount) 
                        FROM generate_fee_voucher_datas AS gfvd 
                        WHERE gfvd.student_id = s.id) AS totalPaymentAmount'),
                DB::raw('(SELECT SUM(f2.amount) 
                        FROM fees AS f2 
                        WHERE s.id = f2.student_id) AS totalReceiptAmount')
            )
            ->where('f.id', $feeId)
            ->where('f.company_id', $schoolId)
            ->where('f.company_location_id', $schoolCampusId)
            ->first();
        return view('fees.viewFeeDetail',compact('feesDetail'));
    }

    public function viewGenerateFeeVoucherDetail(Request $request){
        $rvId = $request->input('id');
        $schoolId = Session::get('company_id');
        $getData = DB::table('generate_fee_voucher_datas as gfvd')
                ->join('generate_fee_vouchers as gfv', 'gfvd.generate_fee_voucher_id', '=', 'gfv.id')
                ->join('students as s', 'gfvd.student_id', '=', 's.id')
                ->leftjoin('student_parent_and_guardian_informations as spagi', 'gfvd.student_id', '=', 'spagi.student_id')
                ->select(
                    'gfv.id',
                    'gfvd.student_id',
                    'gfv.month_year',
                    'gfv.description',
                    'gfvd.amount',
                    's.registration_no',
                    's.student_name',
                    'spagi.father_name',
                    'spagi.mobile_no',
                    DB::raw('(SELECT SUM(gfvd.amount) 
                        FROM generate_fee_voucher_datas AS gfvd 
                        WHERE gfvd.student_id = s.id) AS totalPaymentAmount'),
                    DB::raw('(SELECT SUM(f2.amount) 
                            FROM fees AS f2 
                            WHERE s.id = f2.student_id) AS totalReceiptAmount')
                )
                ->where('gfvd.id',$rvId)
                ->where('gfvd.company_id',Session::get('company_id'))
                ->where('gfvd.company_location_id',Session::get('company_location_id'))
                ->first();
        return view('fees.viewGenerateFeeVoucherDetail',compact('getData'));
    }

    public function student_wise_generated_fee_voucher_list(Request $request){
        try {
            // Validate input data for filters
            $data = $request->validate([
                'fee_voucher_status' => 'nullable|string|in:1,2', // Status filter
                'student_id' => 'nullable|integer|exists:students,id', // Student filter
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Build query with filters
         $generatedFeeVoucherQuery = DB::table('generate_fee_voucher_datas as gfvd')
            ->select('gfvd.*','s.registration_no','s.student_name','gfv.section_id','gfv.month_year','gfv.description','sec.section_name') 
            ->join('generate_fee_vouchers as gfv','gfvd.generate_fee_voucher_id','=','gfv.id')
            ->join('students as s','gfvd.student_id','=','s.id')
            ->join('sections as sec','gfv.section_id','=','sec.id')
             ->where('gfvd.company_id', $request->input('company_id'))
             ->where('gfvd.company_location_id', $request->input('company_location_id'))
             ->when(!empty($data['fee_voucher_status']), function ($query) use ($data) {
                 $query->where('gfvd.fee_voucher_status', $data['fee_voucher_status']);
             })
             ->when(!empty($data['student_id']), function ($query) use ($data) {
                $query->where('gfvd.student_id', $data['student_id']);
            });
     
         // Get the records and their count
         $generatedFeeVouchers = $generatedFeeVoucherQuery->get();
         $totalRecords = $generatedFeeVoucherQuery->count();
     
         // Return response
         return response()->json([
             'status' => 'success',
             'message' => 'Generated Fee Vouchers Retrieved Successfully',
             'data' => $generatedFeeVouchers,
             'total_records' => $totalRecords,
         ], 200);
    }
}
