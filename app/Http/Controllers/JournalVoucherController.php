<?php

namespace App\Http\Controllers;

use App\Models\JournalVoucher;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\JournalVoucherRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class JournalVoucherController extends Controller
{
    private $journalVoucherRepository;
    private $page;
    public function __construct(JournalVoucherRepositoryInterface $journalVoucherRepository)
    {
        $this->page = 'Finance.JournalVouchers.';
        $this->journalVoucherRepository = $journalVoucherRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $journalVouchers =  $this->journalVoucherRepository->allJournalVouchers($request->all());
            return view($this->page.'indexAjax',compact('journalVouchers'));    
        }
        return view($this->page.'index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->page.'create');
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       $validatedData = $request->validate([
            'voucher_type' => 'required|numeric',
            'jv_date' => 'required|date',
            'slip_no' => 'required|string',
            'description' => 'required|string',
            'jvsDataSection_1' => 'required|array|min:2',
        ]);

        DB::beginTransaction();

        try {
            // Set timezone
            date_default_timezone_set("Asia/Karachi");

            // Update into journal_vouchers
            DB::table('journal_vouchers')->where('id',$id)->update([
                'company_id'          => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
                'jv_date'             => $validatedData['jv_date'],
                'slip_no'             => $validatedData['slip_no'],
                'voucher_type'        => $validatedData['voucher_type'],
                'description'         => $validatedData['description'],
                'username'            => Auth::user()->name,
                'date'                => date("Y-m-d"),
                'time'                => date("H:i:s"),
            ]);
            DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->delete();
            $totalDebit = 0;
            $totalCredit = 0;

            // Process each detail row
            foreach ($request->jvsDataSection_1 as $index) {
                $accountId = $request->input("account_id_1_$index");
                $description = $request->input("description_1_$index");
                $debit = floatval($request->input("d_amount_1_$index")) ?: 0;
                $credit = floatval($request->input("c_amount_1_$index")) ?: 0;

                // Validate that either debit or credit is present (but not both)
                if ($debit > 0 && $credit > 0) {
                    throw new \Exception("Both debit and credit cannot be filled at the same time for row $index.");
                }
                if ($debit == 0 && $credit == 0) {
                    throw new \Exception("Either debit or credit must be filled for row $index.");
                }

                $entry = [
                    'journal_voucher_id' => $id,
                    'acc_id'             => $accountId,
                    'description'        => $description,
                    'debit_credit'       => $debit > 0 ? 1 : 2,
                    'amount'             => $debit > 0 ? $debit : $credit,
                    'username'           => Auth::user()->name,
                    'date'               => date("Y-m-d"),
                    'time'               => date("H:i:s"),
                ];

                DB::table('journal_voucher_data')->insert($entry);

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            // Final validation: totals must match
            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new \Exception("Debit and Credit totals do not match. Debit: $totalDebit, Credit: $totalCredit");
            }

            DB::commit();

            return redirect()->route('journalvouchers.index')->with('message', 'Journal Voucher Updated Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'voucher_type' => 'required|numeric',
            'jv_date' => 'required|date',
            'slip_no' => 'required|string',
            'description' => 'required|string',
            'jvsDataSection_1' => 'required|array|min:2',
        ]);

        DB::beginTransaction();

        try {
            // Set timezone
            date_default_timezone_set("Asia/Karachi");

            // Generate JV No
            $jvNo = JournalVoucher::VoucherNo(); // Assuming this method exists

            // Insert into journal_vouchers
            $jvId = DB::table('journal_vouchers')->insertGetId([
                'company_id'          => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
                'jv_date'             => $validatedData['jv_date'],
                'jv_no'               => $jvNo,
                'slip_no'             => $validatedData['slip_no'],
                'voucher_type'        => $validatedData['voucher_type'],
                'description'         => $validatedData['description'],
                'username'            => Auth::user()->name,
                'date'                => date("Y-m-d"),
                'time'                => date("H:i:s"),
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            // Process each detail row
            foreach ($request->jvsDataSection_1 as $index) {
                $accountId = $request->input("account_id_1_$index");
                $description = $request->input("description_1_$index");
                $debit = floatval($request->input("d_amount_1_$index")) ?: 0;
                $credit = floatval($request->input("c_amount_1_$index")) ?: 0;

                // Validate that either debit or credit is present (but not both)
                if ($debit > 0 && $credit > 0) {
                    throw new \Exception("Both debit and credit cannot be filled at the same time for row $index.");
                }
                if ($debit == 0 && $credit == 0) {
                    throw new \Exception("Either debit or credit must be filled for row $index.");
                }

                $entry = [
                    'journal_voucher_id' => $jvId,
                    'acc_id'             => $accountId,
                    'description'        => $description,
                    'debit_credit'       => $debit > 0 ? 1 : 2,
                    'amount'             => $debit > 0 ? $debit : $credit,
                    'username'           => Auth::user()->name,
                    'date'               => date("Y-m-d"),
                    'time'               => date("H:i:s"),
                ];

                DB::table('journal_voucher_data')->insert($entry);

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            // Final validation: totals must match
            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new \Exception("Debit and Credit totals do not match. Debit: $totalDebit, Credit: $totalCredit");
            }

            DB::commit();

            return redirect()->route('journalvouchers.index')->with('message', 'Journal Voucher Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $journalVoucherDetail = $this->journalVoucherRepository->findJournalVoucher($request->get('id'));
        return view($this->page.'viewJournalVoucherDetail',compact('journalVoucherDetail'));
    }

    public function approveJournalVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the journal voucher's status
        DB::table('journal_vouchers')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['jv_status' => 2]);

        // Retrieve journal voucher details for processing
        $journalVoucherDetails = DB::table('journal_vouchers')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();
        // Retrieve journal voucher data details for processing
        $journalVoucherDataDetails = DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->get();

        // Ensure payment data is updated before processing transactions
        DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->update(['jv_status' => 2]);

        // Prepare transaction data for bulk insert
        $transactions = [];
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $username = Auth::user()->name;

        foreach ($journalVoucherDataDetails as $jvddRow) {
            $transactions[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'acc_id' => $jvddRow->acc_id,
                'particulars' => $jvddRow->description,
                'opening_bal' => 2,
                'debit_credit' => $jvddRow->debit_credit,
                'amount' => $jvddRow->amount,
                'voucher_id' => $id,
                'record_data_id' => $jvddRow->id,
                'voucher_type' => 1,
                'v_date' => $journalVoucherDetails->jv_date ?? $currentDate,
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
        echo 'Done';

    }

    public function journalVoucherRejectAndRepost(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('journal_vouchers')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['jv_status' => $value]);
        DB::table('journal_voucher_data')->where('journal_voucher_id',$id)->update(['jv_status' => $value]);
        echo 'Done';
    }

    public function journalVoucherActiveAndInactive(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('journal_vouchers')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['status' => $value]);
        DB::table('journal_voucher_data')->where('journal_voucher_id',$id)->update(['status' => $value]);
        echo 'Done';
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $journalVoucher = DB::table('journal_vouchers')->where('id', $id)->first();
    $journalVoucherData = DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->get();

    $debitEntry = $journalVoucherData->where('debit_credit', 1)->first();
    $creditEntry = $journalVoucherData->where('debit_credit', 2)->first();

    return view($this->page.'edit', compact('journalVoucher', 'debitEntry', 'creditEntry','journalVoucherData'));
}


    

    public function reverseJournalVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the journal voucher's status
        DB::table('journal_vouchers')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['jv_status' => 1]);
        
        DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->update(['jv_status' => 1]);
        DB::table('transaction')
            ->where('company_id',$companyId)
            ->where('company_location_id',$companyLocationId)
            ->where('voucher_id',$id)
            ->where('voucher_type',1)
            ->delete();
        echo 'Done';

    }
    

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(JournalVoucher $journalVoucher)
    // {
    //     //
    // }
}
