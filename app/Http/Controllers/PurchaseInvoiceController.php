<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\JournalVoucher;
use App\Models\PurchaseSaleInvoice;

class PurchaseInvoiceController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'purchase-invoice.';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $purchaseInvoices = DB::table('purchase_sale_invoices as psi')
                ->select('psi.*', 'jv.jv_no', 's.name as supplier_name')
                ->join('journal_vouchers as jv', 'psi.jv_id', '=', 'jv.id')
                ->join('suppliers as s', 'psi.supplier_id', '=', 's.id')
                ->when($status, function ($query, $status) {
                    return $query->where('psi.status', $status);
                })
                ->when($companyId, function ($query, $companyId) {
                    return $query->where('psi.company_id', $companyId);
                })
                ->when($companyLocationId, function ($query, $companyLocationId) {
                    return $query->where('psi.company_location_id', $companyLocationId);
                })
                ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                    return $query->whereBetween('psi.invoice_date', [$fromDate, $toDate]);
                })
                ->where('psi.invoice_type',1)
                ->orderByDesc('psi.id')
                ->get();

            return view($this->page . 'indexAjax', compact('purchaseInvoices'));
        }

        return view($this->page . 'index');
    }

    /**
     * Show the form for creating a new resource.
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

        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Define file paths for JSON files
        $jsonFiles = [
            'suppliers' => storage_path('app/json_files/suppliers.json'),
        ];

        // Ensure all necessary JSON files exist
        foreach ($jsonFiles as $key => $filePath) {
            if (!file_exists($filePath)) {
                generate_json($key); // Generate the missing JSON file
            }
        }

        // Load data from JSON files
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['suppliers' => $suppliers] = $data;

        $suppliers = array_filter($suppliers, fn($s) =>
            $s['company_id'] == $companyId &&
            $s['company_location_id'] == $companyLocationId
        );

        $purchaseInvoiceSetting = DB::table('invoices_payments_receipts_settings as iprs')
            ->join('chart_of_accounts as coa','coa.id','=','iprs.acc_id')
            ->select('coa.*')
            ->where('iprs.type',1)
            ->where('iprs.option_id',1)
            ->where('iprs.company_id',$companyId)
            ->where('iprs.company_location_id',$companyLocationId)
            ->get();

        return view($this->page . 'create', compact('suppliers','purchaseInvoiceSetting'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Common session & auth values
        $companyId         = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $username          = Auth::user()->name ?? 'system';
        $currentDate       = now()->toDateString();
        $currentTime       = now()->format('H:i:s');

        // Validation
        $data = $request->validate([
            'pi_date'          => 'required|date',
            'slip_no'          => 'nullable|string',
            'description'      => 'required|string',
            'supplier_id'      => 'required|integer',
            'debit_account_id' => 'required|integer',
            'amount'           => 'required|numeric',
        ]);

        // Step 1: Create Journal Voucher
        $journalVoucher = new JournalVoucher();
        $journalVoucher->company_id          = $companyId;
        $journalVoucher->company_location_id = $companyLocationId;
        $journalVoucher->jv_date             = $data['pi_date'];
        $journalVoucher->jv_no               = JournalVoucher::VoucherNo();
        $journalVoucher->slip_no             = $data['slip_no'] ?? '-';
        $journalVoucher->voucher_type        = 2;
        $journalVoucher->description         = $data['description'];
        $journalVoucher->username            = $username;
        $journalVoucher->status              = 1;
        $journalVoucher->jv_status           = 1;
        $journalVoucher->date                = $currentDate;
        $journalVoucher->time                = $currentTime;
        $journalVoucher->approve_username    = $username;
        $journalVoucher->approve_date        = $currentDate;
        $journalVoucher->approve_time        = $currentTime;
        $journalVoucher->delete_username     = '-';
        $journalVoucher->save();

        // Step 2: Insert Purchase Invoice
        $purchaseSaleInvoice = new PurchaseSaleInvoice();
        $purchaseSaleInvoice->invoice_type = 1;
        $purchaseSaleInvoice->invoice_date = $data['pi_date'];
        $purchaseSaleInvoice->invoice_no = PurchaseSaleInvoice::VoucherNo(1);
        $purchaseSaleInvoice->slip_no = $data['slip_no'] ?? '-';
        $purchaseSaleInvoice->supplier_id = $data['supplier_id'];
        $purchaseSaleInvoice->debit_account_id = $data['debit_account_id'];
        $purchaseSaleInvoice->amount = $data['amount'];
        $purchaseSaleInvoice->remaining_amount = $data['amount'];
        $purchaseSaleInvoice->description = $data['description'];
        $purchaseSaleInvoice->voucher_status = 1;
        $purchaseSaleInvoice->jv_id = $journalVoucher->id;
        $purchaseSaleInvoice->save();
        
        // Step 3: Supplier Account
        $supplier = DB::table('suppliers')->find($data['supplier_id']);

        // Journal Voucher Data (Debit & Credit)
        $jvData = [
            [
                'journal_voucher_id' => $journalVoucher->id,
                'acc_id'             => $data['debit_account_id'],
                'description'        => $data['description'],
                'debit_credit'       => 1, // Debit
                'amount'             => $data['amount'],
            ],
            [
                'journal_voucher_id' => $journalVoucher->id,
                'acc_id'             => $supplier->acc_id,
                'description'        => $data['description'],
                'debit_credit'       => 2, // Credit
                'amount'             => $data['amount'],
            ]
        ];

        foreach ($jvData as &$row) {
            $row['jv_status']       = 2;
            $row['time']            = $currentTime;
            $row['date']            = $currentDate;
            $row['status']          = 1;
            $row['username']        = $username;
            $row['approve_username']= $username;
            $row['delete_username'] = '-';
        }

        DB::table('journal_voucher_data')->insert($jvData);

        // Redirect
        return redirect()
            ->route('purchase-invoice.index')
            ->with('message', 'Purchase Invoice Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');

        $purchaseInvoiceDetail = \DB::table('purchase_sale_invoices as psi')
            ->select(
                'psi.id',
                'psi.invoice_type',
                'psi.invoice_no',
                'psi.slip_no',
                'psi.amount',
                'psi.description',
                'psi.status',
                'psi.voucher_status',
                's.name as supplier_name',
                'jv.jv_no',
                'jv.jv_date',
                'c.name as company_name',
                'cl.name as company_location_name'
            )
            ->join('suppliers as s', 'psi.supplier_id', '=', 's.id')
            ->join('journal_vouchers as jv', 'psi.jv_id', '=', 'jv.id')
            ->join('companies as c', 'psi.company_id', '=', 'c.id')
            ->join('company_locations as cl', 'psi.company_location_id', '=', 'cl.id')
            ->where('psi.id', $id)
            ->first();

        return view($this->page . 'viewPurchaseInvoiceDetail', compact('purchaseInvoiceDetail'));
    }

    public function approvePurchaseInvoiceVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        // Update the payment's status
        DB::table('purchase_sale_invoices')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['voucher_status' => 2]);

        $purchaseInvoiceDetail = DB::table('purchase_sale_invoices')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();


        DB::table('journal_vouchers')->where([
            'id' => $purchaseInvoiceDetail->jv_id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['jv_status' => 2]);

        
        // Retrieve payment data details for processing
        $paymentDataDetails = DB::table('journal_voucher_data')->where('journal_voucher_id', $purchaseInvoiceDetail->jv_id)->get();
        
        // Ensure payment data is updated before processing transactions
        DB::table('journal_voucher_data')->where('journal_voucher_id', $purchaseInvoiceDetail->jv_id)->update(['jv_status' => 2]);

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
                'voucher_id' => $purchaseInvoiceDetail->jv_id,
                'record_data_id' => $pddRow->id,
                'voucher_type' => 5,
                'v_date' => $purchaseInvoiceDetail->invoice_date,
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Find purchase invoice by ID
        $purchaseInvoice = PurchaseSaleInvoice::where('id', $id)
            ->where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->firstOrFail();

        // Define file paths for JSON files
        $jsonFiles = [
            'suppliers' => storage_path('app/json_files/suppliers.json'),
        ];

        // Ensure JSON files exist
        foreach ($jsonFiles as $key => $filePath) {
            if (!file_exists($filePath)) {
                generate_json($key);
            }
        }

        // Load data from JSON
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['suppliers' => $suppliers] = $data;

        // Filter suppliers based on company + location
        $suppliers = array_filter($suppliers, fn($s) =>
            $s['company_id'] == $companyId &&
            $s['company_location_id'] == $companyLocationId
        );
        $purchaseInvoiceSetting = DB::table('invoices_payments_receipts_settings as iprs')
            ->join('chart_of_accounts as coa','coa.id','=','iprs.acc_id')
            ->select('coa.*')
            ->where('iprs.type',1)
            ->where('iprs.option_id',1)
            ->where('iprs.company_id',$companyId)
            ->where('iprs.company_location_id',$companyLocationId)
            ->get();

        return view($this->page . 'edit', compact('purchaseInvoice', 'suppliers','purchaseInvoiceSetting'));
    }

    public function reversePurchaseInvoiceVoucher(Request $request){
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        $purchaseInvoiceDetail = DB::table('purchase_sale_invoices')->where('id',$id)->first();

        DB::table('purchase_sale_invoices')->where([
            'id' => $id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->update(['voucher_status' => 1]);

        DB::table('journal_vouchers')->where('id', $purchaseInvoiceDetail->jv_id)->update(['jv_status' => 1]);
        DB::table('journal_voucher_data')->where('journal_voucher_id', $purchaseInvoiceDetail->jv_id)->update(['jv_status' => 1]);
        DB::table('transaction')
            ->where('company_id',$companyId)
            ->where('company_location_id',$companyLocationId)
            ->where('voucher_id',$purchaseInvoiceDetail->jv_id)
            ->where('voucher_type',5)
            ->delete();
        echo 'Done';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Common session & auth values
        $companyId         = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $username          = Auth::user()->name ?? 'system';
        $currentDate       = now()->toDateString();
        $currentTime       = now()->format('H:i:s');

        // Validation
        $data = $request->validate([
            'pi_date'          => 'required|date',
            'slip_no'          => 'nullable|string',
            'description'      => 'required|string',
            'supplier_id'      => 'required|integer',
            'debit_account_id' => 'required|integer',
            'amount'           => 'required|numeric',
        ]);

        // Step 1: Find Purchase Invoice
        $purchaseSaleInvoice = PurchaseSaleInvoice::where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->findOrFail($id);

        // Step 2: Update Journal Voucher
        $journalVoucher = JournalVoucher::findOrFail($purchaseSaleInvoice->jv_id);
        $journalVoucher->jv_date          = $data['pi_date'];
        $journalVoucher->slip_no          = $data['slip_no'] ?? '-';
        $journalVoucher->description      = $data['description'];
        $journalVoucher->username         = $username;
        $journalVoucher->approve_username = $username;
        $journalVoucher->approve_date     = $currentDate;
        $journalVoucher->approve_time     = $currentTime;
        $journalVoucher->save();

        // Step 3: Update Purchase Invoice
        $purchaseSaleInvoice->invoice_date      = $data['pi_date'];
        $purchaseSaleInvoice->slip_no           = $data['slip_no'] ?? '-';
        $purchaseSaleInvoice->supplier_id       = $data['supplier_id'];
        $purchaseSaleInvoice->debit_account_id  = $data['debit_account_id'];
        $purchaseSaleInvoice->amount            = $data['amount'];
        $purchaseSaleInvoice->remaining_amount = $data['amount'];
        $purchaseSaleInvoice->description       = $data['description'];
        $purchaseSaleInvoice->save();

        // Step 4: Update Journal Voucher Data (Delete old & Insert new)
        DB::table('journal_voucher_data')
            ->where('journal_voucher_id', $journalVoucher->id)
            ->delete();

        $supplier = DB::table('suppliers')->find($data['supplier_id']);

        $jvData = [
            [
                'journal_voucher_id' => $journalVoucher->id,
                'acc_id'             => $data['debit_account_id'],
                'description'        => $data['description'],
                'debit_credit'       => 1, // Debit
                'amount'             => $data['amount'],
            ],
            [
                'journal_voucher_id' => $journalVoucher->id,
                'acc_id'             => $supplier->acc_id,
                'description'        => $data['description'],
                'debit_credit'       => 2, // Credit
                'amount'             => $data['amount'],
            ]
        ];

        foreach ($jvData as &$row) {
            $row['jv_status']       = 2;
            $row['time']            = $currentTime;
            $row['date']            = $currentDate;
            $row['status']          = 1;
            $row['username']        = $username;
            $row['approve_username']= $username;
            $row['delete_username'] = '-';
        }

        DB::table('journal_voucher_data')->insert($jvData);

        // Redirect
        return redirect()
            ->route('purchase-invoice.index')
            ->with('message', 'Purchase Invoice Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
