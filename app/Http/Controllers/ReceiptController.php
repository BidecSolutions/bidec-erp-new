<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ReceiptRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReceiptController extends Controller
{
    private $page;
    private $receiptRepository;
    public function __construct(ReceiptRepositoryInterface $receiptRepository)
    {
        $this->page = 'Finance.Receipts.';
        $this->receiptRepository = $receiptRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $receipts =  $this->receiptRepository->allReceipts($request->all());
            return view($this->page.'indexAjax',compact('receipts'));    
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'voucher_type' => 'required',
            'rv_date' => 'required',
            'slip_no' => 'required',
            'receipt_to' => '',
            'cheque_no' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'cheque_date' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'description' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'amount' => 'required'
        ]);
        $this->receiptRepository->storeReceipt($data);

        return redirect()->route('receipts.index')->with('message', 'Receipt Created Successfully');
    }

    public function approveReceiptVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the receipt's status
        DB::table('receipts')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['rv_status' => 2]);

        // Retrieve receipt details for processing
        $receiptDetails = DB::table('receipts')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();
        // Retrieve receipt data details for processing
        $receiptDataDetails = DB::table('receipt_data')->where('receipt_id', $id)->get();

        // Ensure receipt data is updated before processing transactions
        DB::table('receipt_data')->where('receipt_id', $id)->update(['rv_status' => 2]);

        // Prepare transaction data for bulk insert
        $transactions = [];
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $username = Auth::user()->name;

        foreach ($receiptDataDetails as $rddRow) {
            $transactions[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'acc_id' => $rddRow->acc_id,
                'particulars' => $rddRow->description,
                'opening_bal' => 2,
                'debit_credit' => $rddRow->debit_credit,
                'amount' => $rddRow->amount,
                'voucher_id' => $id,
                'record_data_id' => $rddRow->id,
                'voucher_type' => 3,
                'v_date' => $receiptDetails->rv_date ?? $currentDate,
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

    public function receiptVoucherRejectAndRepost(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('receipts')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['rv_status' => $value]);
        DB::table('receipt_data')->where('receipt_id',$id)->update(['rv_status' => $value]);
        echo 'Done';
    }

    public function receiptVoucherActiveAndInactive(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('receipts')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['status' => $value]);
        DB::table('receipt_data')->where('receipt_id',$id)->update(['status' => $value]);
        echo 'Done';
    }

    public function deleteReceiptVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $getReceiptDetail = DB::table('receipts')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();

        // Update the journal voucher's status
        DB::table('receipts')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->delete();
        
        DB::table('receipt_data')->where('receipt_id', $id)->delete();
        DB::table('purchase_sale_invoices')->where('id',$getPaymentDetail->si_id)->where('invoice_type',2)->update(['payment_receipt_status' => 1]);

        echo 'Done';
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $receiptDetail = $this->receiptRepository->findReceipt($request->get('id'));
        return view($this->page.'viewReceiptDetail',compact('receiptDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $receiptDetail = $this->receiptRepository->findReceipt($id);
        return view($this->page.'edit',compact('receiptDetail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'voucher_type' => 'required',
            'rv_date' => 'required',
            'slip_no' => 'required',
            'receipt_to' => 'required',
            // Conditionally required fields
            'cheque_no' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'cheque_date' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'description' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'amount' => 'required'
        ]);

        date_default_timezone_set("Asia/Karachi");

        // Prepare the data to be updated
        $data1 = [
            'company_id' => Session::get('company_id'),
            'company_location_id' => Session::get('company_location_id'),
            'rv_date' => $data['rv_date'],
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'slip_no' => $data['slip_no'],
            'voucher_type' => $data['voucher_type'],
            'receipt_to' => $data['receipt_to'],
            'cheque_no' => $data['cheque_no'],
            'cheque_date' => $data['cheque_date'],
            'description' => $data['description'],
            'username' => Auth::user()->name,
            'rv_status' => 1
        ];

        // Update the receipts table
        DB::table('receipts')
            ->where('id', $id)  // Use the $id to find the correct record
            ->update($data1);

        // Update receipt data for debit
        $data2 = [
            'acc_id' => $data['debit_account_id'],
            'description' => $data['description'],
            'debit_credit' => 1,
            'amount' => $data['amount'],
            'time' => date("H:i:s"),
            'date' => date("Y-m-d"),
            'username' => Auth::user()->name,
            'rv_status' => 1
        ];

        // Update the receipt_data for the debit account
        DB::table('receipt_data')
            ->where('receipt_id', $id)
            ->where('debit_credit', 1)
            ->update($data2);

        // Update receipt data for credit
        $data3 = [
            'acc_id' => $data['credit_account_id'],
            'description' => $data['description'],
            'debit_credit' => 2,
            'amount' => $data['amount'],
            'time' => date("H:i:s"),
            'date' => date("Y-m-d"),
            'username' => Auth::user()->name,
            'rv_status' => 1
        ];

        // Update the receipt_data for the credit account
        DB::table('receipt_data')
            ->where('receipt_id', $id)
            ->where('debit_credit', 2)
            ->update($data3);

        // Return a success response or redirect as needed
        return redirect()->route('receipts.index')->with('success', 'Receipt updated successfully.');
    }

    public function reverseReceiptVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the journal voucher's status
        DB::table('receipts')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['rv_status' => 1]);
        
        DB::table('receipt_data')->where('receipt_id', $id)->update(['rv_status' => 1]);
        DB::table('transaction')
            ->where('company_id',$companyId)
            ->where('company_location_id',$companyLocationId)
            ->where('voucher_id',$id)
            ->where('voucher_type',3)
            ->delete();
        echo 'Done';

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt)
    {
        //
    }
}
