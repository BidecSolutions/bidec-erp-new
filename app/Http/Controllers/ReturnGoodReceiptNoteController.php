<?php

namespace App\Http\Controllers;

use App\Models\JournalVoucher;
use App\Models\ReturnGoodReceiptNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReturnGoodReceiptNoteController extends Controller
{
    protected $isApi;
    protected $page;

    public function __construct(Request $request)
    {
        $this->isApi = $request->is('api/*');
        $this->page = 'return-good-receipt-notes.';
    }

    public function index(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Check if the request is via AJAX or API
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus'); // Optional status filter
            $fromDate = $request->input('from_date');      // Optional from date filter
            $toDate = $request->input('to_date');        // Optional to date filter

            // Build the query with a join to fetch supplier details
            $query = DB::table('return_good_receipt_notes as rgrn')
                ->join('suppliers as s', 'rgrn.supplier_id', '=', 's.id')
                ->join('good_receipt_notes as grn', 'rgrn.good_receipt_note_id', '=', 'grn.id')
                ->select('rgrn.*', 'grn.grn_no as grn_no', 's.name as supplier_name')
                ->where('rgrn.company_id', $companyId)
                ->where('rgrn.company_location_id', $companyLocationId);

            // Apply date filtering if both dates are provided
            if ($fromDate && $toDate) {
                $query->whereBetween('rgrn.return_date', [$fromDate, $toDate]);
            }

            // Apply status filtering if provided
            if (!is_null($status)) {
                $query->where('rgrn.status', $status);
            }

            $returnGRNs = $query->get();

            if ($request->ajax() || $this->isApi) {
                $html = view('return-good-receipt-notes.partials.indexAjax', compact('returnGRNs'))->render();
                return response()->json(['success' => true, 'html' => $html], 200);
            }

            // For API requests, return a JSON response
            return jsonResponse($returnGRNs, 'Return GRNs Retrieved Successfully', 'success', 200);
        }

        // For regular non-AJAX/non-API requests, return the main index view
        return view('return-good-receipt-notes.index');
    }


    public function create(Request $request)
    {
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        $grns = DB::table('good_receipt_notes')
            ->join('suppliers', 'suppliers.id', '=', 'good_receipt_notes.supplier_id')
            ->where('grn_status', 2)
            ->select('good_receipt_notes.*', 'suppliers.name as supplier_name')
            ->get();

        return view("{$this->page}create")->with('grns', $grns);
    }

    public function loadGRNDetails(Request $request)
    {
        $grnId = $request->input('grn_id');

        $grn = DB::table('good_receipt_notes')
            ->where('id', $grnId)
            ->first();

        if (!$grn) {
            return response()->json(['html' => '<p class="text-danger">GRN not found.</p>']);
        }

        $supplier = DB::table('suppliers')
            ->where('id', $grn->supplier_id)
            ->first();

        // In ReturnGoodReceiptNoteController's loadGRNDetails method
        $grnItems = DB::table('grn_datas')
            ->join('purchase_order_datas', 'purchase_order_datas.id', '=', 'grn_datas.po_data_id')
            ->join('product_variants', 'product_variants.id', '=', 'purchase_order_datas.product_variant_id')
            ->join('sizes as product_variant_sizes', 'product_variant_sizes.id', '=', 'product_variants.size_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('grn_datas.good_receipt_note_id', $grnId)
            ->select(
                'grn_datas.*',
                'products.name as product_name',
                'products.id as product_id',
                'product_variant_sizes.name as size_name',
                'purchase_order_datas.purchase_order_id as po_id' // Add this line
            )
            ->get();

        log:
        info("grnItems" . json_encode($grnItems));
        if ($grnItems->isEmpty()) {
            return response()->json(['html' => '<p class="text-danger">GRN items not found.</p>']);
        }

        $html = view('return-good-receipt-notes.partials.grn-items', [
            'grn' => $grn,
            'supplier' => $supplier,
            'grnItems' => $grnItems,
        ])->render();

        return response()->json(['html' => $html]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'good_receipt_note_id' => 'required|integer|exists:good_receipt_notes,id',
            'reason' => 'required|string',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer',
            'po_ids' => 'required|array',
            'po_ids.*' => 'required|integer',
            'po_data_ids' => 'required|array',
            'po_data_ids.*' => 'required|integer',
            'return_qtys' => 'required|array',
            'return_qtys.*' => 'required|numeric|min:0',
            'remarks' => 'nullable|array',
        ]);


        DB::beginTransaction();

        try {

            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

            // Generate a unique Return GRN number
            $grn = DB::table('good_receipt_notes')
                ->where('id', $validated['good_receipt_note_id'])
                ->first();

            if (!$grn) {
                return back()->with('error', 'GRN not found.');
            }

            // Insert the return GRN header record
            $returnGRN = new ReturnGoodReceiptNote();
            $returnGRN->good_receipt_note_id = $validated['good_receipt_note_id'];
            $returnGRN->return_grn_no = ReturnGoodReceiptNote::VoucherNo();
            $returnGRN->return_date = now();
            $returnGRN->supplier_id = $grn->supplier_id;
            $returnGRN->return_grn_status = 1;
            $returnGRN->reason = $validated['reason'];
            $returnGRN->save();

            $returnGRN->save();
            $returnGRNId = $returnGRN->id;


            // Extract all arrays from validated input
            $productIds = $validated['product_ids'];
            $poIds = $validated['po_ids'];
            $poDataIds = $validated['po_data_ids'];
            $returnQtys = $validated['return_qtys'];
            $remarks = $validated['remarks'] ?? [];

            $returnItems = [];

            // Build return items
            foreach ($productIds as $index => $productId) {
                $qty = isset($returnQtys[$index]) ? (float) $returnQtys[$index] : 0;

                if ($qty > 0) {
                    $returnItems[] = [
                        'company_id' => $companyId,
                        'company_location_id' => $companyLocationId,
                        'return_good_receipt_note_id' => $returnGRNId,
                        'po_id' => $poIds[$index] ?? 0,
                        'po_data_id' => $poDataIds[$index] ?? 0,
                        'return_qty' => $qty,
                        'unit_price' => 0, // Optional: set later if needed
                        'amount' => 0,     // Optional: set later if needed
                        'remarks' => $remarks[$index] ?? null,
                        'created_by' => auth()->id(),
                        'created_date' => now(),
                    ];
                }
            }

            // Optional: Log first item for debugging
            Log::info('Prepared return item:', $returnItems[0] ?? []);

            // Insert items if any
            if (!empty($returnItems)) {
                DB::table('return_grn_datas')->insert($returnItems);
            }

            DB::commit();

            return redirect()->route('return-good-receipt-notes.index')
                ->with('success', 'Return GRN created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return GRN store error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create Return GRN. ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            // Update the status directly using query builder
            DB::table('return_good_receipt_notes')
                ->where('id', $id)
                ->update(['status' => 2]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Good Receipt Note marked as inactive successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while deactivating the Return Good Receipt Note.'], 500);
        }
    }

    public function status($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            // Update the status directly using query builder
            DB::table('return_good_receipt_notes')
                ->where('id', $id)
                ->update(['status' => 1]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Good Receipt Note marked as active successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Good Receipt Note.'], 500);
        }
    }

    public function approveReturnGoodReceiptNoteVoucher(Request $request)
    {
        DB::beginTransaction();

        try {
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $id = $request->input('id');
            $username = Auth::user()->name;
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');

            // Update RGRN status to Approved (2)
            DB::table('return_good_receipt_notes')
                ->where([
                    ['id', '=', $id],
                    ['company_id', '=', $companyId],
                    ['company_location_id', '=', $companyLocationId]
                ])->update(['return_grn_status' => 2]);

            // Fetch RGRN details
            $rgrnDetail = DB::table('return_good_receipt_notes')
                ->where([
                    ['id', '=', $id],
                    ['company_id', '=', $companyId],
                    ['company_location_id', '=', $companyLocationId]
                ])->first();

            if (!$rgrnDetail) {
                throw new \Exception('Return Good Receipt Note not found.');
            }

            // Create Journal Voucher
            $journalVoucher = new JournalVoucher();
            $journalVoucher->company_id = $companyId;
            $journalVoucher->company_location_id = $companyLocationId;
            $journalVoucher->jv_date = $rgrnDetail->return_date;
            $journalVoucher->jv_no = JournalVoucher::VoucherNo();
            $journalVoucher->slip_no = $rgrnDetail->return_grn_no;
            $journalVoucher->voucher_type = 2; // Assuming 2 is for purchase-related
            $journalVoucher->description = $rgrnDetail->reason;
            $journalVoucher->username = $username;
            $journalVoucher->status = 1;
            $journalVoucher->jv_status = 2; // Approved
            $journalVoucher->date = $currentDate;
            $journalVoucher->time = $currentTime;
            $journalVoucher->approve_username = $username;
            $journalVoucher->approve_date = $currentDate;
            $journalVoucher->approve_time = $currentTime;
            $journalVoucher->delete_username = '-';
            $journalVoucher->save();

            $journalVoucherId = $journalVoucher->id;

            // Link JV ID to RGRN and its data
            DB::table('return_good_receipt_notes')
                ->where('id', $id)
                ->update(['jv_id' => $journalVoucherId]);

            DB::table('return_grn_datas')
                ->where('return_good_receipt_note_id', $id)
                ->update(['jv_id' => $journalVoucherId]);

            // Fetch RGRN Data with necessary joins
            $rgrnDataDetails = DB::table('return_grn_datas as rgd')
                ->join('grn_datas as gd', 'rgd.po_data_id', '=', 'gd.po_data_id')
                ->join('purchase_order_datas as pod', 'gd.po_data_id', '=', 'pod.id')
                ->join('purchase_orders as po', 'pod.purchase_order_id', '=', 'po.id')
                ->join('product_variants as pv', 'pod.product_variant_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->join('categories as c', 'p.category_id', '=', 'c.id')
                ->where('rgd.return_good_receipt_note_id', $id)
                ->select([
                    'rgd.*',
                    'pod.unit_price as po_unit_price',
                    'c.acc_id as account_id',
                    'p.id as product_id',
                    'pv.id as product_variant_id',
                    'po.po_no',
                    'po.po_date'
                ])->get();

            $faraData = [];
            $jvCreditData = [];
            $totalCreditAmount = 0;

            foreach ($rgrnDataDetails as $rgdRow) {
                $amount = $rgdRow->return_qty * $rgdRow->po_unit_price;
                $totalCreditAmount += $amount;

                // Prepare fara data
                $faraData[] = [
                    'company_id' => $companyId,
                    'company_location_id' => $companyLocationId,
                    'process_type' => 1,
                    'status' => 4, //  return grn
                    'supplier_id' => $rgrnDetail->supplier_id,
                    'main_table_id' => $id,
                    'main_table_data_id' => $rgdRow->id,
                    'po_no' => $rgdRow->po_no,
                    'po_date' => $rgdRow->po_date,
                    'rgrn_no' => $rgrnDetail->return_grn_no,
                    'rgrn_date' => $rgrnDetail->return_date,
                    'product_id' => $rgdRow->product_id,
                    'product_variant_id' => $rgdRow->product_variant_id,
                    'qty' => $rgdRow->return_qty,
                    'rate' => $rgdRow->po_unit_price,
                    'amount' => $amount, // Negative amount for return
                    'remarks' => $rgrnDetail->reason,
                    'created_by' => $username,
                    'created_date' => $currentDate
                ];

                // Prepare JV credit entries
                $jvCreditData[] = [
                    'journal_voucher_id' => $journalVoucherId,
                    'acc_id' => $rgdRow->account_id,
                    'description' => $rgrnDetail->reason,
                    'debit_credit' => 2, // Credit
                    'amount' => $amount,
                    'jv_status' => 2,
                    'time' => $currentTime,
                    'date' => $currentDate,
                    'status' => 1,
                    'username' => $username,
                    'approve_username' => $username,
                    'delete_username' => '-'
                ];
            }

            // Insert fara data
            if (!empty($faraData)) {
                DB::table('faras')->insert($faraData);
            }

            // Insert JV credit entries
            if (!empty($jvCreditData)) {
                DB::table('journal_voucher_data')->insert($jvCreditData);
            }

            // Handle supplier account
            $supplierAccount = DB::table('suppliers')
                ->where('id', $rgrnDetail->supplier_id)
                ->select('acc_id')
                ->first();

            if (!$supplierAccount) {
                throw new \Exception('Supplier account not found.');
            }

            // Insert supplier debit entry
            DB::table('journal_voucher_data')->insert([
                'journal_voucher_id' => $journalVoucherId,
                'acc_id' => $supplierAccount->acc_id,
                'description' => $rgrnDetail->reason,
                'debit_credit' => 1, // Debit
                'amount' => $totalCreditAmount,
                'jv_status' => 2,
                'time' => $currentTime,
                'date' => $currentDate,
                'status' => 1,
                'username' => $username,
                'approve_username' => $username,
                'delete_username' => '-'
            ]);

            // Prepare transactions
            $transactions = [];
            $journalVoucherData = DB::table('journal_voucher_data')
                ->where('journal_voucher_id', $journalVoucherId)
                ->get();

            foreach ($journalVoucherData as $jvData) {
                $transactions[] = [
                    'company_id' => $companyId,
                    'company_location_id' => $companyLocationId,
                    'acc_id' => $jvData->acc_id,
                    'particulars' => $jvData->description,
                    'opening_bal' => 2,
                    'debit_credit' => $jvData->debit_credit,
                    'amount' => $jvData->amount,
                    'voucher_id' => $journalVoucherId,
                    'record_data_id' => $jvData->id,
                    'voucher_type' => 1, // Journal Voucher
                    'v_date' => $rgrnDetail->return_date,
                    'date' => $currentDate,
                    'time' => $currentTime,
                    'username' => $username,
                    'status' => 1
                ];
            }

            if (!empty($transactions)) {
                DB::table('transaction')->insert($transactions);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Return GRN approved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return GRN Approval Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Approval failed: ' . $e->getMessage()], 500);
        }
    }
    public function returnGoodReceiptNoteVoucherReject($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            log::info($id);
            // Update the status directly using query builder
            DB::table('return_good_receipt_notes')
                ->where('id', $id)
                ->update(['return_grn_status' => 3]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Good Receipt Note marked as rehjected successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Good Receipt Note.'], 500);
        }
    }
    public function returnGoodReceiptNoteVoucherRepost($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            log::info($id);
            // Update the status directly using query builder
            DB::table('return_good_receipt_notes')
                ->where('id', $id)
                ->update(['return_grn_status' => 1]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Good Receipt Note marked as pending successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Good Receipt Note.'], 500);
        }
    }



    public function show(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);

        $returnGrnId = $request->input('id');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Fetch Return GRN header details along with the supplier's name and original GRN details
        $returnGrnDetail = DB::table('return_good_receipt_notes as rgrn')
            ->join('suppliers as s', 'rgrn.supplier_id', '=', 's.id')
            ->leftJoin('good_receipt_notes as grn', 'rgrn.good_receipt_note_id', '=', 'grn.id')
            ->select(
                'rgrn.*',
                's.name as supplier_name',
                'grn.grn_no as original_grn_no',
                'grn.grn_date as original_grn_date'
            )
            ->where('rgrn.id', $returnGrnId)
            ->where('rgrn.company_id', $companyId)
            ->where('rgrn.company_location_id', $companyLocationId)
            ->first();

        if (!$returnGrnDetail) {
            return response()->json(['error' => 'Return Good Receipt Note not found'], 404);
        }

        // Fetch associated Return GRN data along with related purchase order and product details
        $returnGrnDataDetails = DB::table('return_grn_datas as rgrnd')
            ->leftJoin('purchase_order_datas as pod', 'rgrnd.po_data_id', '=', 'pod.id')
            ->leftJoin('purchase_orders as po', 'pod.purchase_order_id', '=', 'po.id')
            ->leftJoin('product_variants as pv', 'pod.product_variant_id', '=', 'pv.id')
            ->leftJoin('products as p', 'pv.product_id', '=', 'p.id')
            ->leftJoin('sizes as si', 'pv.size_id', '=', 'si.id')
            ->select(
                'rgrnd.id',
                'rgrnd.return_good_receipt_note_id',
                'pod.product_variant_id as product_variant_id',
                'rgrnd.po_id',
                'rgrnd.po_data_id',
                'rgrnd.return_qty',
                'rgrnd.unit_price',
                'rgrnd.amount',
                'rgrnd.remarks',
                'po.po_no',
                'po.po_date',
                'pod.qty as po_qty',
                'pod.unit_price as po_unit_price',
                'pod.sub_total as po_sub_total',
                'si.name as size_name',
                'p.name as product_name'
            )
            ->where('rgrnd.return_good_receipt_note_id', $returnGrnId)
            ->where('rgrnd.company_id', $companyId)
            ->where('rgrnd.company_location_id', $companyLocationId)
            ->get();

        // Attach the Return GRN data details to the main header object
        $returnGrnDetail->returnGrnData = $returnGrnDataDetails;

        // Return the view with the fetched details
        return view('return-good-receipt-notes.viewReturnGrnDetail', compact('returnGrnDetail', 'returnGrnDataDetails'));
    }



    public function edit($id)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Fetch the Return GRN
        $returnGrn = DB::table('return_good_receipt_notes as rgrn')
            ->join('suppliers as s', 'rgrn.supplier_id', '=', 's.id')
            ->join('good_receipt_notes as grn', 'rgrn.good_receipt_note_id', '=', 'grn.id')
            ->select('rgrn.*', 's.name as supplier_name', 'grn.grn_no as grn_no', 'grn.id as original_grn_id')
            ->where('rgrn.id', $id)
            ->where('rgrn.company_id', $companyId)
            ->where('rgrn.company_location_id', $companyLocationId)
            ->first();

        if (!$returnGrn) {
            return redirect()->route('return-good-receipt-notes.index')
                ->with('error', 'Return GRN not found.');
        }

        // Fetch all items from the original GRN
        $originalGrnItems = DB::table('grn_datas')
            ->join('purchase_order_datas', 'purchase_order_datas.id', '=', 'grn_datas.po_data_id')
            ->join('product_variants', 'product_variants.id', '=', 'purchase_order_datas.product_variant_id')
            ->join('sizes as product_variant_sizes', 'product_variant_sizes.id', '=', 'product_variants.size_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('grn_datas.good_receipt_note_id', $returnGrn->good_receipt_note_id)
            ->select(
                'grn_datas.id as grn_data_id',
                'grn_datas.receive_qty',
                'products.name as product_name',
                'products.id as product_id',
                'product_variant_sizes.name as size_name',
                'purchase_order_datas.purchase_order_id as po_id',
                'purchase_order_datas.id as po_data_id'
            )
            ->get();

        // Fetch existing Return GRN items
        $existingReturnItems = DB::table('return_grn_datas')
            ->where('return_good_receipt_note_id', $id)
            ->get()
            ->keyBy('po_data_id'); // Key by po_data_id to match original GRN items

        // Prepare merged items
        $grnItems = [];
        foreach ($originalGrnItems as $grnItem) {
            $returnItem = $existingReturnItems->get($grnItem->po_data_id);
            $grnItems[] = (object) [
                'grn_data_id' => $grnItem->grn_data_id,
                'product_name' => $grnItem->product_name,
                'size_name' => $grnItem->size_name,
                'product_id' => $grnItem->product_id,
                'po_id' => $grnItem->po_id,
                'po_data_id' => $grnItem->po_data_id,
                'receive_qty' => $grnItem->receive_qty,
                'return_qty' => $returnItem->return_qty ?? 0,
                'remarks' => $returnItem->remarks ?? null,
                'is_included' => $returnItem ? true : false,
            ];
        }

        return view('return-good-receipt-notes.edit', compact('returnGrn', 'grnItems'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer',
            'po_ids' => 'required|array',
            'po_ids.*' => 'required|integer',
            'po_data_ids' => 'required|array',
            'po_data_ids.*' => 'required|integer',
            'return_qtys' => 'required|array',
            'return_qtys.*' => 'required|numeric|min:0',
            'remarks' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

            // Fetch the Return GRN and original GRN ID
            $returnGRN = ReturnGoodReceiptNote::where('id', $id)
                ->where('company_id', $companyId)
                ->where('company_location_id', $companyLocationId)
                ->firstOrFail();

            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

            // Update return GRN header
            $returnGRN = ReturnGoodReceiptNote::where('id', $id)
                ->where('company_id', $companyId)
                ->where('company_location_id', $companyLocationId)
                ->first();

            if (!$returnGRN) {
                return back()->with('error', 'Return GRN not found.');
            }

            $returnGRN->reason = $validated['reason'];
            $returnGRN->save();

            // Delete old return items
            DB::table('return_grn_datas')
                ->where('return_good_receipt_note_id', $id)
                ->delete();

            // Prepare updated return items
            $returnItems = [];
            foreach ($validated['product_ids'] as $poDataId => $productId) {
                $qty = $validated['return_qtys'][$poDataId] ?? 0;
                if ($qty > 0) {
                    $returnItems[] = [
                        'company_id' => $companyId,
                        'company_location_id' => $companyLocationId,
                        'return_good_receipt_note_id' => $id,
                        'po_id' => $validated['po_ids'][$poDataId],
                        'po_data_id' => $validated['po_data_ids'][$poDataId],
                        'return_qty' => $qty,
                        'unit_price' => 0,
                        'amount' => 0,
                        'remarks' => $validated['remarks'][$poDataId] ?? null,
                        'created_by' => auth()->id(),
                        'created_date' => now(),
                    ];
                }
            }

            if (!empty($returnItems)) {
                DB::table('return_grn_datas')->insert($returnItems);
            }

            DB::commit();

            return redirect()->route('return-good-receipt-notes.index')
                ->with('success', 'Return GRN updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return GRN update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update Return GRN. ' . $e->getMessage());
        }
    }


}
