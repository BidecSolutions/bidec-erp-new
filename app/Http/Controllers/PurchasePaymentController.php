<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PurchasePaymentController extends Controller
{
    private $page;
    public function __construct()
    {
        $this->page = 'Finance.Purchase-Payments.';
    }

    public function index(Request $request){
        if($request->ajax()){
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $filterVoucherType = $request->input('filterVoucherType');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $purchasePayments = Payment::status($status) // Apply the status scope
                ->when($filterVoucherType != '', function ($query) use ($filterVoucherType) {
                    return $query->where('voucher_type', $filterVoucherType);
                })
                ->with([
                    'payment_data' => function ($query) {
                        $query->select('payment_data.payment_id', 'payment_data.amount', 'payment_data.acc_id','payment_data.debit_credit') // Specify columns from payment_data table
                            ->with('account:id,name'); // Eager load the account relationship
                    }
                ])
                ->orderBy('pv_date', 'asc')
                ->whereBetween('pv_date',[$fromDate,$toDate])
                ->where('company_id',$companyId)
                ->where('company_location_id',$companyLocationId)
                ->whereIn('entry_option', [2,3])
                ->get();
            return view($this->page.'indexAjax',compact('purchasePayments'));    
        }
        return view($this->page.'index');
    }

    public function create()
    {
        $companyId = session('company_id');
        $companyLocationId = session('company_location_id');

        // Pending Purchase Orders / GRNs
        $pendingPOs = DB::table('purchase_order_datas as pod')
            ->join('purchase_orders as po', 'pod.purchase_order_id', '=', 'po.id')
            ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
            ->leftJoin('grn_datas as gd', 'po.id', '=', 'gd.po_id')
            ->leftJoin('good_receipt_notes as grn', 'gd.good_receipt_note_id', '=', 'grn.id')
            ->where('pod.payment_status', 1)
            ->where('po.po_status', 2)
            ->where('po.status', 1)
            ->where('po.company_id', $companyId)
            ->where('po.company_location_id', $companyLocationId)
            ->distinct()
            ->get([
                'po.id',
                'po.po_no',
                'po.po_date',
                's.name as supplier_name',
                'grn.grn_no',
                'grn.grn_date'
            ]);

        // Purchase Invoices
        $purchaseInvoices = DB::table('purchase_sale_invoices as pi')
            ->join('suppliers as s', 'pi.supplier_id', '=', 's.id')
            ->where('pi.company_id', $companyId)
            ->where('pi.company_location_id', $companyLocationId)
            ->where('pi.payment_receipt_status', 1)
            ->where('pi.voucher_status', 2)
            ->where('pi.status', 1)
            ->where('pi.invoice_type',1)
            ->select('pi.id', 'pi.invoice_no', 'pi.invoice_date', 's.name as supplier_name','pi.amount','pi.remaining_amount')
            ->get();

        return view($this->page.'create', compact('pendingPOs', 'purchaseInvoices'));
    }

    public function loadPurchasePaymentVoucherDetailByPONo(Request $request){
        // Get the PO ID, company ID, and company location ID from the request and session
        $poId = $request->input('poId');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        
        // Fetch the item summary list dynamically using the PO ID, company ID, and location ID
        $itemSummaryList = DB::select(
            'SELECT c.name as category_name, p.name as product_name, s.name as size_name, pod.qty, pod.unit_price, pod.sub_total
            FROM purchase_order_datas as pod
            INNER JOIN product_variants as pv ON pod.product_variant_id = pv.id
            INNER JOIN products as p ON pv.product_id = p.id
            INNER JOIN categories as c ON p.category_id = c.id
            INNER JOIN sizes as s ON pv.size_id = s.id
            WHERE pod.purchase_order_id = :poId
            AND pod.company_id = :companyId
            AND pod.company_location_id = :companyLocationId',
            ['poId' => $poId, 'companyId' => $companyId, 'companyLocationId' => $companyLocationId]
        );
    
        // Fetch the payment summary list dynamically
        $paymentSummaryList = DB::select(
            'SELECT p.pv_no, p.pv_date, coa.name as account_head, pd.amount
            FROM payments as p
            INNER JOIN payment_data as pd ON p.id = pd.payment_id
            INNER JOIN chart_of_accounts as coa ON pd.acc_id = coa.id
            WHERE pd.debit_credit = 2
            AND p.po_id = :poId
            AND p.company_id = :companyId
            AND p.company_location_id = :companyLocationId',
            ['poId' => $poId, 'companyId' => $companyId, 'companyLocationId' => $companyLocationId]
        );
        $supplierDetail = DB::table('suppliers as s')
            ->join('purchase_orders as po','po.supplier_id','=','s.id')
            ->where('po.id',$poId)
            ->select('s.name','s.acc_id')
            ->first();
    
        // Return the view with the dynamic data
        return view($this->page . 'loadPurchasePaymentVoucherDetailByPONo', [
            'itemSummaryList' => $itemSummaryList,
            'paymentSummaryList' => $paymentSummaryList,
            'supplierDetail' => $supplierDetail
        ]);
    }

    public function loadPurchasePaymentVoucherDetailByInvoiceId(Request $request)
    {
        $invoiceIds = (array) $request->input('invoiceIds', []); // Always array
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        if (empty($invoiceIds)) {
            return back()->withErrors(['No invoice(s) selected.']);
        }

        $invoiceId = $invoiceIds[0];

        $detailSupplierAndInvoice = DB::table('suppliers as s')
            ->join('purchase_sale_invoices as pi', 'pi.supplier_id', '=', 's.id')
            ->where('pi.id', $invoiceId)
            ->select('s.name', 's.acc_id', 'pi.amount', 'pi.invoice_no', 'pi.invoice_date','pi.remaining_amount')
            ->first();

        // Fetch purchase invoice settings (common for both cases)
        $purchaseInvoiceSetting = DB::table('invoices_payments_receipts_settings as iprs')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'iprs.acc_id')
            ->where([
                ['iprs.type', 1],
                ['iprs.option_id', 2],
                ['iprs.company_id', $companyId],
                ['iprs.company_location_id', $companyLocationId],
            ])
            ->select('coa.*')
            ->get();

        // ✅ Case 1: Single Invoice
        if (count($invoiceIds) === 1) {
            return view($this->page . 'loadPurchasePaymentVoucherDetailByInvoiceId', [
                'detailSupplierAndInvoice' => $detailSupplierAndInvoice,
                'purchaseInvoiceSetting'   => $purchaseInvoiceSetting,
            ]);
        }

        $detailInvoices = DB::table('purchase_sale_invoices as pi')
            ->whereIn('pi.id', $invoiceIds)
            ->select('pi.id', 'pi.invoice_no', 'pi.slip_no', 'pi.invoice_date', 'pi.amount')
            ->get();

        return view($this->page . 'loadPurchasePaymentVoucherDetailByMultipleInvoices', [
            'purchaseInvoiceSetting'   => $purchaseInvoiceSetting,
            'detailInvoices'           => $detailInvoices,
            'detailSupplierAndInvoice' => $detailSupplierAndInvoice
        ]);
    }

    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entry_option'       => 'required|in:1,2',
            'voucher_type'       => 'required|in:1,2',
            'pv_date'            => 'required|date',
            'slip_no'            => 'nullable|string',
            'paid_to'            => 'nullable|string',
            'description'        => 'required|string',
            'debit_account_id'   => 'required|exists:chart_of_accounts,id',
            'credit_account_id'  => 'required|exists:chart_of_accounts,id',
            'amount'             => 'required|numeric|min:0',
            'remaining_amount'   => 'nullable|numeric|min:0',
        ]);

        // Conditional validations
        $validator->sometimes('po_id', 'required', function ($input) {
            return $input->entry_option == 1;
        });
        $validator->sometimes('invoice_ids', 'required', function ($input) {
            return $input->entry_option == 2;
        });
        $validator->sometimes('cheque_no', 'required|string', function ($input) {
            return $input->voucher_type == 2;
        });
        $validator->sometimes('cheque_date', 'required|date', function ($input) {
            return $input->voucher_type == 2;
        });
        $data = $validator->validate();

        // Prepare common fields to be used in both payment and payment_data tables
        $currentDate = now(); // Using Laravel's helper to get current datetime
        $username = Auth::user()->name;
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $pvNo = Payment::VoucherNo($request->input('voucher_type'));

        // Start a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Insert Payment Record
            $entryOption = $request->input('entry_option');
            $piVoucherType = $request->input('pi_voucher_type');
            $invoiceId = 0;
            if ($piVoucherType == 1) {
                $invoiceIds = (array) $request->input('invoice_ids', []);
                $invoiceId  = $invoiceIds[0] ?? 0;
            }
            $paymentData = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'pv_date' => $request->input('pv_date'),
                'date' => $currentDate->toDateString(),
                'time' => $currentDate->toTimeString(),
                'po_id' => $request->input('po_id') ?? 0,
                'pi_voucher_type' => $piVoucherType,
                'pi_id' => $invoiceId,
                'pv_no' => $pvNo,
                'slip_no' => $request->input('slip_no') ?? '-',
                'voucher_type' => $request->input('voucher_type'),
                'entry_option' => $entryOption == 1 ? 2 : ($entryOption == 2 ? 3 : null),
                'paid_to' => $request->input('paid_to') ?? '-',
                'cheque_no' => $request->input('cheque_no'),
                'cheque_date' => $request->input('cheque_date'),
                'description' => $request->input('description'),
                'username' => $username,
            ];

            // Insert payment record and get the inserted ID
            $paymentId = DB::table('payments')->insertGetId($paymentData);

            // Prepare and insert debit and credit payment data
            $paymentDataEntries = [
                [
                    'payment_id' => $paymentId,
                    'acc_id' => $request->input('debit_account_id'),
                    'description' => $request->input('description'),
                    'debit_credit' => 1, // Debit
                    'amount' => $request->input('amount'),
                    'time' => $currentDate->toTimeString(),
                    'date' => $currentDate->toDateString(),
                    'username' => $username
                ],
                [
                    'payment_id' => $paymentId,
                    'acc_id' => $request->input('credit_account_id'),
                    'description' => $request->input('description'),
                    'debit_credit' => 2, // Credit
                    'amount' => $request->input('amount'),
                    'time' => $currentDate->toTimeString(),
                    'date' => $currentDate->toDateString(),
                    'username' => $username
                ]
            ];

            // Insert both debit and credit records in one go using DB::table()
            DB::table('payment_data')->insert($paymentDataEntries);

            if($request->input('entry_option') == 2){
                if ($request->input('pi_voucher_type') == 1) {
                    DB::table('purchase_sale_invoices')
                        ->where('id', $invoiceId)
                        ->where('company_id', $companyId)
                        ->where('company_location_id', $companyLocationId)
                        ->update([
                            'remaining_amount' => ($request->input('remaining_amount') - $request->input('amount'))
                        ]);

                    if ($request->input('amount') == $request->input('remaining_amount')) {
                        DB::table('purchase_sale_invoices')
                            ->where('id', $invoiceId)
                            ->where('company_id', $companyId)
                            ->where('company_location_id', $companyLocationId)
                            ->update(['payment_receipt_status' => 2]);
                    }
                } else {
                    $paymentAmount = (float) $request->input('amount', 0); // e.g. 5000
                    $invoiceIds    = (array) $request->input('invoice_ids', []);

                    DB::transaction(function () use ($paymentAmount, $invoiceIds, $companyId, $companyLocationId, $paymentId) {
                        $purchaseSaleInvoices = DB::table('purchase_sale_invoices')
                            ->whereIn('id', $invoiceIds)
                            ->where('company_id', $companyId)
                            ->where('company_location_id', $companyLocationId)
                            ->orderBy('invoice_date', 'asc')
                            ->lockForUpdate()
                            ->get();

                        $remainingPayment = $paymentAmount;

                        foreach ($purchaseSaleInvoices as $invoice) {
                            if ($remainingPayment <= 0) {
                                break; // jab amount khatam ho jaye
                            }

                            $applyAmount = min($invoice->remaining_amount, $remainingPayment);

                            // Insert record in payment_data_for_multiple_invoices
                            DB::table('payment_data_for_multiple_invoices')->insert([
                                'payment_id'  => $paymentId,
                                'pi_id'       => $invoice->id,
                                'paid_amount' => $applyAmount,
                                'status' => 1,
                                'created_by' => auth()->user()->name,
                                'created_date' => date('Y-m-d')
                            ]);

                            // Naya remaining amount
                            $newRemaining = $invoice->remaining_amount - $applyAmount;

                            $updateData = [
                                'remaining_amount' => $newRemaining,
                            ];

                            if ($newRemaining == 0) {
                                $updateData['payment_receipt_status'] = 2; // fully paid
                            }

                            DB::table('purchase_sale_invoices')
                                ->where('id', $invoice->id)
                                ->where('company_id', $companyId)
                                ->where('company_location_id', $companyLocationId)
                                ->update($updateData);

                            // Baki payment kam karo
                            $remainingPayment -= $applyAmount;
                        }
                    });
                }
                
            }else{
                // Check and update purchase order status if amounts match
                if ($request->input('amount') == $request->input('remaining_amount')) {
                    DB::table('purchase_orders')
                        ->where('id', $request->input('po_id'))
                        ->where('company_id', $companyId)
                        ->where('company_location_id', $companyLocationId)
                        ->update(['payment_status' => 2]);

                    DB::table('purchase_order_datas')
                        ->where('purchase_order_id', $request->input('po_id'))
                        ->where('company_id', $companyId)
                        ->where('company_location_id', $companyLocationId)
                        ->update(['payment_status' => 2]);
                }
            }
            

            // Commit the transaction
            DB::commit();

            // Return the view after successfully storing the data
            return redirect()->route('purchase-payments.index')->with('message', 'Purchase Payment Created Successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            // Handle the error (log or return error response)
            return back()->withErrors(['error' => 'An error occurred while creating the payment.']);
        }
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $paymentDetail = Payment::find($id);
        return view($this->page.'viewPurchasePaymentDetail',compact('paymentDetail'));
    }
}
