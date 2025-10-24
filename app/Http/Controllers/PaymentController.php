<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    private $page;
    private $paymentRepository;
    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->page = 'Finance.Payments.';
        $this->paymentRepository = $paymentRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $payments =  $this->paymentRepository->allPayments($request->all());
            return view($this->page.'indexAjax',compact('payments'));    
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
            'pv_date' => 'required',
            'slip_no' => 'required',
            'paid_to' => 'required',
            // Conditionally required fields
            'cheque_no' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'cheque_date' => $request->input('voucher_type') == 1 ? 'nullable' : 'required',
            'description' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'amount' => 'required'
        ]);
        $this->paymentRepository->storePayment($data);

        return redirect()->route('payments.index')->with('message', 'Payment Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $paymentDetail = $this->paymentRepository->findPayment($request->get('id'));
        return view($this->page.'viewPaymentDetail',compact('paymentDetail'));
    }

    public function approvePaymentVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the payment's status
        DB::table('payments')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['pv_status' => 2]);

        // Retrieve payment details for processing
        $paymentDetails = DB::table('payments')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();
        // Retrieve payment data details for processing
        $paymentDataDetails = DB::table('payment_data')->where('payment_id', $id)->get();

        // Ensure payment data is updated before processing transactions
        DB::table('payment_data')->where('payment_id', $id)->update(['pv_status' => 2]);

        // Prepare transaction data for bulk insert
        $transactions = [];
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $username = Auth::user()->name;

        foreach ($paymentDataDetails as $pddRow) {
            $transactions[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'acc_id' => $pddRow->acc_id,
                'particulars' => $pddRow->description,
                'opening_bal' => 2,
                'debit_credit' => $pddRow->debit_credit,
                'amount' => $pddRow->amount,
                'voucher_id' => $id,
                'record_data_id' => $pddRow->id,
                'voucher_type' => 2,
                'v_date' => $paymentDetails->pv_date ?? $currentDate,
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

    public function paymentVoucherRejectAndRepost(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('payments')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['pv_status' => $value]);
        DB::table('payment_data')->where('payment_id',$id)->update(['pv_status' => $value]);
        echo 'Done';
    }

    public function paymentVoucherActiveAndInactive(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('payments')->where('id',$id)->where('company_id',$companyId)->where('company_location_id',$companyLocationId)->update(['status' => $value]);
        DB::table('payment_data')->where('payment_id',$id)->update(['status' => $value]);
        echo 'Done';
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $paymentDetail = $this->paymentRepository->findPayment($id);
        return view($this->page.'edit',compact('paymentDetail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'voucher_type' => 'required',
            'pv_date' => 'required',
            'slip_no' => 'required',
            'paid_to' => 'required',
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
            'pv_date' => $data['pv_date'],
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'slip_no' => $data['slip_no'],
            'voucher_type' => $data['voucher_type'],
            'paid_to' => $data['paid_to'],
            'cheque_no' => $data['cheque_no'],
            'cheque_date' => $data['cheque_date'],
            'description' => $data['description'],
            'username' => Auth::user()->name,
            'pv_status' => 1
        ];

        // Update the payments table
        DB::table('payments')
            ->where('id', $id)  // Use the $id to find the correct record
            ->update($data1);

        // Update payment data for debit
        $data2 = [
            'acc_id' => $data['debit_account_id'],
            'description' => $data['description'],
            'debit_credit' => 1,
            'amount' => $data['amount'],
            'time' => date("H:i:s"),
            'date' => date("Y-m-d"),
            'username' => Auth::user()->name,
            'pv_status' => 1
        ];

        // Update the payment_data for the debit account
        DB::table('payment_data')
            ->where('payment_id', $id)
            ->where('debit_credit', 1)
            ->update($data2);

        // Update payment data for credit
        $data3 = [
            'acc_id' => $data['credit_account_id'],
            'description' => $data['description'],
            'debit_credit' => 2,
            'amount' => $data['amount'],
            'time' => date("H:i:s"),
            'date' => date("Y-m-d"),
            'username' => Auth::user()->name,
            'pv_status' => 1
        ];

        // Update the payment_data for the credit account
        DB::table('payment_data')
            ->where('payment_id', $id)
            ->where('debit_credit', 2)
            ->update($data3);

        // Return a success response or redirect as needed
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function reversePaymentVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the journal voucher's status
        DB::table('payments')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['pv_status' => 1]);
        
        DB::table('payment_data')->where('payment_id', $id)->update(['pv_status' => 1]);
        DB::table('transaction')
            ->where('company_id',$companyId)
            ->where('company_location_id',$companyLocationId)
            ->where('voucher_id',$id)
            ->where('voucher_type',2)
            ->delete();
        echo 'Done';

    }

    public function deletePaymentVoucher(Request $request)
    {
        $companyId         = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id                = $request->input('id');

        DB::transaction(function () use ($id, $companyId, $companyLocationId) {
            $payment = DB::table('payments')
                ->where([
                    'id'                 => $id,
                    'company_id'         => $companyId,
                    'company_location_id'=> $companyLocationId
                ])
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw new \Exception("Payment not found.");
            }
            $paymentData = DB::table('payment_data')
                ->where('payment_id', $id)
                ->where('debit_credit', 1)
                ->first();
            DB::table('payments')
                ->where('id', $id)
                ->delete();

            DB::table('payment_data')
                ->where('payment_id', $id)
                ->delete();
            if ($payment->pi_voucher_type == 1) {
                if ($paymentData) {
                    $purchaseSaleInvoice = DB::table('purchase_sale_invoices')
                        ->where('id', $payment->pi_id)
                        ->lockForUpdate()
                        ->first();

                    if ($purchaseSaleInvoice) {
                        DB::table('purchase_sale_invoices')
                            ->where('id', $payment->pi_id)
                            ->where('invoice_type', 1)
                            ->update([
                                'remaining_amount'       => $purchaseSaleInvoice->remaining_amount + $paymentData->amount,
                                'payment_receipt_status' => 1
                            ]);
                    }
                }
            }else {
                $invoices = DB::table('payment_data_for_multiple_invoices as pdfmi')
                    ->join('purchase_sale_invoices as psi', 'pdfmi.pi_id', '=', 'psi.id')
                    ->where('pdfmi.payment_id', $payment->id)
                    ->select(
                        'pdfmi.id as pdfmi_id',
                        'pdfmi.pi_id',
                        'pdfmi.paid_amount',
                        'psi.remaining_amount'
                    )
                    ->lockForUpdate()
                    ->get();

                foreach ($invoices as $invoice) {
                    // Restore invoice amounts
                    DB::table('purchase_sale_invoices')
                        ->where('id', $invoice->pi_id)
                        ->where('invoice_type', 1)
                        ->update([
                            'remaining_amount'       => $invoice->remaining_amount + $invoice->paid_amount,
                            'payment_receipt_status' => 1
                        ]);

                    // Delete mapping row
                    DB::table('payment_data_for_multiple_invoices')
                        ->where('id', $invoice->pdfmi_id)
                        ->delete();
                }
            }
        });

        echo 'Done';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
