<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SaleReceiptController extends Controller
{
    private $page;
    public function __construct()
    {
        $this->page = 'Finance.Sale-Receipts.';
    }

    public function index(Request $request){
        if($request->ajax()){
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $filterVoucherType = $request->input('filterVoucherType');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $saleReceipts = Receipt::status($status) // Apply the status scope
                ->when($filterVoucherType != '', function ($query) use ($filterVoucherType) {
                    return $query->where('voucher_type', $filterVoucherType);
                })
                ->with([
                    'receipt_data' => function ($query) {
                        $query->select('receipt_data.receipt_id', 'receipt_data.amount', 'receipt_data.acc_id','receipt_data.debit_credit') // Specify columns from payment_data table
                            ->with('account:id,name'); // Eager load the account relationship
                    }
                ])
                ->orderBy('rv_date', 'asc')
                ->whereBetween('rv_date',[$fromDate,$toDate])
                ->where('company_id',$companyId)
                ->where('company_location_id',$companyLocationId)
                ->whereIn('entry_option',[2,3])
                ->get();
            return view($this->page.'indexAjax',compact('saleReceipts'));    
        }
        return view($this->page.'index');
    }

    public function create() {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
    
        // Fetch pending purchase orders payments
        $getPendingDirectSaleInvoiceReceipts = DB::table('direct_sale_invoices as dsi')
            ->join('customers as c', 'dsi.customer_id', '=', 'c.id')
            ->where('dsi.payment_receipt_status', '1')
            ->where('dsi.dsi_status', '2')
            ->where('dsi.status', '1')
            ->where('dsi.company_id', $companyId)
            ->where('dsi.company_location_id', $companyLocationId)
            ->distinct()
            ->get(['dsi.id', 'dsi.dsi_no', 'dsi.dsi_date', 'c.name']);
        
        // Sale Invoices
        $saleInvoices = DB::table('purchase_sale_invoices as si')
            ->join('customers as c', 'si.customer_id', '=', 'c.id')
            ->where('si.company_id', $companyId)
            ->where('si.company_location_id', $companyLocationId)
            ->where('si.payment_receipt_status', 1)
            ->where('si.voucher_status', 2)
            ->where('si.status', 1)
            ->where('si.invoice_type',2)
            ->select('si.id', 'si.invoice_no', 'si.invoice_date', 'c.name as customer_name','si.amount')
            ->get();
        
        return view($this->page . 'create', compact('getPendingDirectSaleInvoiceReceipts','saleInvoices'));
    }

    public function loadSaleReceiptVoucherDetailByDSINO(Request $request){
        // Get the PO ID, company ID, and company location ID from the request and session
        $dsiId = $request->input('dsiId');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        
        // Fetch the item summary list dynamically using the PO ID, company ID, and location ID
        $itemSummaryList = DB::select(
            'SELECT c.name as category_name, p.name as product_name, s.name as size_name, dsid.qty, dsid.rate, dsid.total_amount
            FROM direct_sale_invoice_datas as dsid
            INNER JOIN product_variants as pv ON dsid.product_variant_id = pv.id
            INNER JOIN products as p ON pv.product_id = p.id
            INNER JOIN categories as c ON p.category_id = c.id
            INNER JOIN sizes as s ON pv.size_id = s.id
            WHERE dsid.direct_sale_invoice_id = :dsiId
            AND dsid.company_id = :companyId
            AND dsid.company_location_id = :companyLocationId',
            ['dsiId' => $dsiId, 'companyId' => $companyId, 'companyLocationId' => $companyLocationId]
        );
    
        // Fetch the payment summary list dynamically
        $receiptSummaryList = DB::select(
            'SELECT r.rv_no, r.rv_date, coa.name as account_head, rd.amount
            FROM receipts as r
            INNER JOIN receipt_data as rd ON r.id = rd.receipt_id
            INNER JOIN chart_of_accounts as coa ON rd.acc_id = coa.id
            WHERE rd.debit_credit = 1
            AND r.dsi_id = :dsiId
            AND r.company_id = :companyId
            AND r.company_location_id = :companyLocationId',
            ['dsiId' => $dsiId, 'companyId' => $companyId, 'companyLocationId' => $companyLocationId]
        );
        $customerDetail = DB::table('customers as c')
            ->join('direct_sale_invoices as dsi','dsi.customer_id','=','c.id')
            ->where('dsi.id',$dsiId)
            ->select('c.name','c.acc_id')
            ->first();
    
        // Return the view with the dynamic data
        return view($this->page . 'loadSaleReceiptVoucherDetailByDSINO', [
            'itemSummaryList' => $itemSummaryList,
            'receiptSummaryList' => $receiptSummaryList,
            'customerDetail' => $customerDetail
        ]);
    }

    public function loadSaleReceiptVoucherDetailByInvoiceId(Request $request){
        // Get the PO ID, company ID, and company location ID from the request and session
        $invoiceId = $request->input('invoiceId');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        
        // Fetch the payment summary list dynamically
        $receiptSummaryList = DB::select(
            'SELECT r.rv_no, r.rv_date, coa.name as account_head, rd.amount
            FROM receipts as r
            INNER JOIN receipt_data as rd ON r.id = rd.receipt_id
            INNER JOIN chart_of_accounts as coa ON rd.acc_id = coa.id
            WHERE rd.debit_credit = 1
            AND r.si_id = :invoiceId
            AND r.company_id = :companyId
            AND r.company_location_id = :companyLocationId',
            ['invoiceId' => $invoiceId, 'companyId' => $companyId, 'companyLocationId' => $companyLocationId]
        );
        $detailCustomerAndInvoice = DB::table('customers as c')
            ->join('purchase_sale_invoices as si','si.customer_id','=','c.id')
            ->where('si.id',$invoiceId)
            ->select('c.name','c.acc_id','si.amount')
            ->first();
        
        $saleInvoiceSetting = DB::table('invoices_payments_receipts_settings as iprs')
            ->join('chart_of_accounts as coa','coa.id','=','iprs.acc_id')
            ->select('coa.*')
            ->where('iprs.type',2)
            ->where('iprs.option_id',4)
            ->where('iprs.company_id',$companyId)
            ->where('iprs.company_location_id',$companyLocationId)
            ->get();
    
        // Return the view with the dynamic data
        return view($this->page . 'loadSaleReceiptVoucherDetailByInvoiceId', [
            'receiptSummaryList' => $receiptSummaryList,
            'detailCustomerAndInvoice' => $detailCustomerAndInvoice,
            'saleInvoiceSetting' => $saleInvoiceSetting
        ]);
    }

    public function store(Request $request)
    {
        // // Validate the incoming request data
        // $data = $request->validate([
        //     'dsi_id' => 'required',
        //     'entry_option' => 'required|in:1,2',
        //     'voucher_type' => 'required|in:1,2',
        //     'rv_date' => 'required|date',
        //     'slip_no' => 'nullable|string',
        //     'receipt_to' => 'nullable|string',
        //     'cheque_no' => $request->input('voucher_type') == 1 ? 'nullable' : 'required|string',
        //     'cheque_date' => $request->input('voucher_type') == 1 ? 'nullable|date' : 'required|date',
        //     'description' => 'required|string',
        //     'debit_account_id' => 'required|exists:chart_of_accounts,id',
        //     'credit_account_id' => 'required|exists:chart_of_accounts,id',
        //     'amount' => 'required|numeric|min:0',
        //     'remaining_amount' => 'required|numeric|min:0',
        // ]);


        $validator = Validator::make($request->all(), [
            'entry_option'       => 'required|in:1,2',
            'voucher_type'       => 'required|in:1,2',
            'rv_date'            => 'required|date',
            'slip_no'            => 'nullable|string',
            'receipt_to'            => 'nullable|string',
            'description'        => 'required|string',
            'debit_account_id'   => 'required|exists:chart_of_accounts,id',
            'credit_account_id'  => 'required|exists:chart_of_accounts,id',
            'amount'             => 'required|numeric|min:0',
            'remaining_amount'   => 'required|numeric|min:0',
        ]);

        // Conditional validations
        $validator->sometimes('dsi_id', 'required', function ($input) {
            return $input->entry_option == 1;
        });
        $validator->sometimes('invoice_id', 'required', function ($input) {
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
        $rvNo = Receipt::VoucherNo($request->input('voucher_type'));

        // // Start a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Insert Payment Record
            $entryOption = $request->input('entry_option');
            $receiptData = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'rv_date' => $request->input('rv_date'),
                'date' => $currentDate->toDateString(),
                'time' => $currentDate->toTimeString(),
                'dsi_id' => $request->input('dsi_id') ?? 0,
                'si_id' => $request->input('invoice_id') ?? 0,
                'rv_no' => $rvNo,
                'slip_no' => $request->input('slip_no') ?? '-',
                'voucher_type' => $request->input('voucher_type'),
                'entry_option' => $entryOption == 1 ? 2 : ($entryOption == 2 ? 3 : null),
                'rv_type' => $entryOption == 1 ? 2 : ($entryOption == 2 ? 3 : null),
                'receipt_to' => $request->input('receipt_to') ?? '-',
                'cheque_no' => $request->input('cheque_no'),
                'cheque_date' => $request->input('cheque_date'),
                'description' => $request->input('description'),
                'username' => $username,
            ];

            // Insert payment record and get the inserted ID
            $receiptId = DB::table('receipts')->insertGetId($receiptData);

            // Prepare and insert debit and credit payment data
            $receiptDataEntries = [
                [
                    'receipt_id' => $receiptId,
                    'acc_id' => $request->input('debit_account_id'),
                    'description' => $request->input('description'),
                    'debit_credit' => 1, // Debit
                    'amount' => $request->input('amount'),
                    'time' => $currentDate->toTimeString(),
                    'date' => $currentDate->toDateString(),
                    'username' => $username
                ],
                [
                    'payment_id' => $receiptId,
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
            DB::table('receipt_data')->insert($receiptDataEntries);

            // Check and update purchase order status if amounts match
            if($request->input('entry_option') == 2){
                if ($request->input('amount') == $request->input('remaining_amount')) {
                    DB::table('purchase_sale_invoices')
                        ->where('id', $request->input('invoice_id'))
                        ->where('company_id', $companyId)
                        ->where('company_location_id', $companyLocationId)
                        ->update(['payment_receipt_status' => 2]);
                }
            }else{
                if ($request->input('amount') == $request->input('remaining_amount')) {
                    DB::table('direct_sale_invoices')
                        ->where('id', $request->input('dsi_id'))
                        ->where('company_id', $companyId)
                        ->where('company_location_id', $companyLocationId)
                        ->update(['payment_receipt_status' => 2]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Return the view after successfully storing the data
            return redirect()->route('sale-receipts.index')->with('message', 'Sale Receipt Created Successfully');
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
        $receiptDetail = Receipt::find($id);
        return view($this->page.'viewSaleReceiptDetail',compact('receiptDetail'));
    }
}
