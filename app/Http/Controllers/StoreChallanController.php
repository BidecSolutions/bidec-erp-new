<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Google\Service\CloudResourceManager\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StoreChallan;
use App\Models\StoreChallanData;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestData;
use App\Models\Fara;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherData;
use Illuminate\Support\Facades\Auth;

class StoreChallanController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'store-challans.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create()
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        // Check if accessed via API and return error response
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        // Fetch purchase order details with optimized query using Eloquent
        $getDepartmentList = Department::distinct()
            ->join('material_requests as mr', 'mr.department_id', '=', 'departments.id')
            ->select('mr.department_id as id',  'departments.department_name')
            ->where('mr.company_id', $companyId)
            ->where('mr.location_id', $companyLocationId)
            ->where('mr.material_request_status', 2)
            ->where('mr.status', 1)
            ->get();

        // Return view with data using with()
        return view("{$this->page}create")->with('getDepartmentList', $getDepartmentList);
    }

    public function getMaterialRequestsByDepartmentId(Request $request)
    {
        $departmentId = $request->input('departmentId');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // 🔸 Validate required field
        if (!$departmentId) {
            return response()->json([
                'success' => false,
                'message' => 'Department ID is required'
            ], 400);
        }

        // 🔸 Fetch material requests using query builder
            $materialRequests = DB::table('material_request_datas as mrd')
                ->join('material_requests as mr', 'mr.id', '=', 'mrd.material_request_id')
                ->join('product_variants as pv', 'mrd.product_variant_id', '=', 'pv.id')
                ->join('sizes as s', 'pv.size_id', '=', 's.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftJoin('store_challan_datas as scd', 'mrd.id', '=', 'scd.material_request_data_id')
                ->where('mr.department_id', $departmentId)
                ->where('mr.company_id', $companyId)
                ->where('mr.location_id', $companyLocationId)
                ->where('mr.status', 1)
                ->where('mr.material_request_status', 2)
                ->select(
                    'mrd.id',
                    'mrd.material_request_id',
                    'mr.material_request_no',
                    'mr.material_request_date',
                    'p.name as product_name',
                    's.name as size_name',
                    'pv.id as variant_id',
                    'mrd.qty as material_request_qty',
                    DB::raw('COALESCE(SUM(scd.issue_qty), 0) as previous_issue_qty')
                )
                ->groupBy(
                    'mrd.id',
                    'mrd.material_request_id',
                    'mr.material_request_no',
                    'mr.material_request_date',
                    'p.name',
                    's.name',
                    'pv.id',
                    'mrd.qty'
                )
                ->havingRaw('COALESCE(SUM(scd.issue_qty), 0) < mrd.qty')
                ->get();

        // 🔸 Handle empty results
        if ($materialRequests->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No material requests found for this department'
            ], 404);
        }

        // 🔸 Render the partial view
        $html = view('store-challans.partials.material_requests', [
            'materialRequests' => $materialRequests,
            'departmentId' => $departmentId
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        // Begin transaction
        DB::beginTransaction();
        try {
            // Insert data into PurchaseOrder
            $storeChallan = new StoreChallan();
            $storeChallan->material_request_id = $request->input('material_request_id_1');
            $storeChallan->department_id = $request->input('department_id');
            $storeChallan->store_challan_no = storeChallan::VoucherNo();
            $storeChallan->store_challan_date = $request->store_challan_date;
            $storeChallan->description = $request->description;
            $storeChallan->save();
            

         foreach ($request->mrRowsArray as $poraData) {
            $storeChallanData = new storeChallanData();
            $storeChallanData->material_request_data_id = $request->input('mr_data_id_' . $poraData);
            $storeChallanData->store_challan_id = $storeChallan->id;
            $storeChallanData->product_variant_id = $request->input('variant_id_' . $poraData);
            $storeChallanData->issue_qty = $request->input('issue_qty_' . $poraData);
            $storeChallanData->receive_qty = 0.00;
            $storeChallanData->data_description = $request->input('data_description_' . $poraData);
            $storeChallanData->save();

            $remainingQty = $request->input('remaining_qty_' . $poraData);
            $issueQty = $request->input('issue_qty_' . $poraData);

                if ($remainingQty == $issueQty) {
                    MaterialRequestData::where('id', $request->input('mr_data_id_' . $poraData))
                        ->update(['store_challan_status' => 2]);
                }
             }

            $materialRequestId = $request->input('material_request_id_1');
            $totalRows = MaterialRequestData::where('material_request_id', $materialRequestId)->count();
            $completedRows = MaterialRequestData::where('material_request_id', $materialRequestId)
                ->where('store_challan_status', 2)
                ->count();

            if ($totalRows > 0 && $totalRows == $completedRows) {
                MaterialRequest::where('id', $materialRequestId)
                    ->update(['store_challan_status' => 2]);
            }
            //Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Store Challan Created Successfully');
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }
    public function edit($id)
    {
        // Fetch the company ID and location ID from the session
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Check if accessed via API and return error response
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        // Fetch the GoodReceiptNote by ID using with() to eager load the related data
        $storeChallan = StoreChallan::with('storeChallanData') // This will now work after defining the relationship
            ->where('company_id', $companyId)
            ->where('location_id', $companyLocationId)
            ->findOrFail($id);
        // Fetch supplier list using distinct suppliers related to the current purchase orders
     
        $getDepartmentList = Department::distinct()
            ->join('material_requests as mr', 'mr.department_id', '=', 'departments.id')
            ->select('mr.department_id as id',  'departments.department_name')
            ->where('mr.company_id', $companyId)
            ->where('mr.location_id', $companyLocationId)
            ->get();


        // Fetch material request details for the selected department
        $materialRequest = MaterialRequest::where('department_id', $storeChallan->department_id)
            ->where('company_id', $companyId)
            ->where('location_id', $companyLocationId)
            ->with('materialRequestData') // Assuming this is the relationship for the purchase orders data
            ->get();

        // Return the edit view with the fetched data
        return view("{$this->page}edit")->with([
            'storeChallan' => $storeChallan,
            'getDepartmentList' => $getDepartmentList,
            'materialRequest' => $materialRequest,
        ]);
    }
    public function getMaterialRequestsForEdit(Request $request)
      {
        $deparmentId = $request->input('deparmentId');
        $storeChallanId = $request->input('storeChallanId');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        if (!$deparmentId || !$storeChallanId) {
            return response()->json(['success' => false, 'message' => 'Department ID and Store Challan Id ID are required'], 400);
        }
        // Step 1: Fetch all unassigned material
        $unassignedPOs = DB::select(
            'SELECT 
                pod.id,
                pod.purchase_order_id,
                po.po_no,
                po.po_date,
                p.name AS product_name,
                s.name AS size_name,
                pod.qty AS purchase_order_qty,
                COALESCE(SUM(grnd.receive_qty), 0) AS previous_receipt_qty
            FROM 
                purchase_order_datas AS pod
            INNER JOIN 
                purchase_orders AS po ON po.id = pod.purchase_order_id
            INNER JOIN 
                product_variants AS pv ON pod.product_variant_id = pv.id
            INNER JOIN 
                sizes AS s ON pv.size_id = s.id
            INNER JOIN 
                products AS p ON pv.product_id = p.id
            LEFT JOIN 
                grn_datas AS grnd ON pod.id = grnd.po_data_id
            WHERE 
                pod.receive_qty = 1 
                AND po.supplier_id = :supplierId
                AND po.company_id = :companyId
                AND po.company_location_id = :companyLocationId
                AND po.status = 1
                AND po.po_status = 2
                AND NOT EXISTS (
                    SELECT 1 FROM grn_datas gd WHERE gd.po_data_id = pod.id
                )
            GROUP BY 
                pod.id, pod.purchase_order_id, po.po_no, po.po_date, p.name, s.name, pod.qty
            HAVING 
                COALESCE(SUM(grnd.receive_qty), 0) < pod.qty',
            [
                'deparmentId' => $deparmentId,
                'companyId' => $companyId,
                'companyLocationId' => $companyLocationId
            ]
        );


        // Step 2: Find all assigned PO IDs from the `grn_datas` table
        $assignedPOsData = DB::select(
            'SELECT po_data_id 
            FROM grn_datas 
            WHERE good_receipt_note_id = :goodReceiptNoteId',
            ['goodReceiptNoteId' => $goodReceiptNoteId]
        );

        $assignedPOIds = array_map(fn($row) => $row->po_data_id, $assignedPOsData);

        // Step 3: Fetch assigned POs with corrected SQL
        $assignedPOs = [];
        if (!empty($assignedPOIds)) {
            $placeholders = [];
            $params = [
                'supplierId' => $supplierId,
                'companyId' => $companyId,
                'companyLocationId' => $companyLocationId,
                'goodReceiptNoteId' => $goodReceiptNoteId // Added missing parameter
            ];

            foreach ($assignedPOIds as $index => $id) {
                $paramName = 'id_' . $index;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $id;
            }

            $placeholdersStr = implode(', ', $placeholders);

            $query = "SELECT 
            pod.id,
            pod.purchase_order_id,
            po.po_no,
            po.po_date,
            p.name AS product_name,
            s.name AS size_name,
            pod.qty AS purchase_order_qty,
            grnd.quotation_no,      
            grnd.expiry_date,
            grnd.receive_qty,
            COALESCE(SUM(grnd.receive_qty), 0) AS previous_receipt_qty 
         FROM 
            purchase_order_datas AS pod
         INNER JOIN 
            purchase_orders AS po ON po.id = pod.purchase_order_id
         INNER JOIN 
            product_variants AS pv ON pod.product_variant_id = pv.id
         INNER JOIN 
            sizes AS s ON pv.size_id = s.id
         INNER JOIN 
            products AS p ON pv.product_id = p.id
         LEFT JOIN 
            grn_datas AS grnd ON pod.id = grnd.po_data_id 
                AND grnd.good_receipt_note_id = :goodReceiptNoteId
         WHERE 
            pod.receive_qty = 1 
            AND po.supplier_id = :supplierId
            AND po.company_id = :companyId
            AND po.company_location_id = :companyLocationId            
            AND po.status = 1
            AND po.po_status = 2
            AND pod.id IN ($placeholdersStr)
         GROUP BY 
            pod.id, pod.purchase_order_id, po.po_no, po.po_date, 
            p.name, s.name, pod.qty, grnd.quotation_no, grnd.expiry_date, grnd.receive_qty
         HAVING 
            COALESCE(SUM(grnd.receive_qty), 0) < pod.qty";

            $assignedPOs = DB::select($query, $params);
        }

        // Step 4: Merge results (unchanged)
        $purchaseOrders = array_merge($unassignedPOs, $assignedPOs);

        // Return rendered view (unchanged)
        $html = view('good-receipt-notes.partials.purchase_orders_edit', [
            'purchaseOrders' => $purchaseOrders,
            'assignedPOIds' => $assignedPOIds,
            'supplierId' => $supplierId,
            'goodReceiptNoteId' => $goodReceiptNoteId
        ])->render();

        return response()->json(['success' => true, 'html' => $html], 200);
    }


    /* public function update(Request $request, GoodReceiptNote $goodReceiptNote)
    {
        DB::beginTransaction();
        try {
            // Fetch the existing GRN
            $goodReceiptNote = GoodReceiptNote::findOrFail($goodReceiptNote);
            $goodReceiptNote->supplier_id = $request->input('supplier_id');
            $goodReceiptNote->grn_date = $request->grn_date;
            $goodReceiptNote->description = $request->description;
            $goodReceiptNote->save();

            // Get all submitted PO data IDs from the request
            $submittedPoDataIds = collect($request->poRowsArray)
                ->map(function ($row) use ($request) {
                    return $request->input("po_data_id_{$row}");
                })
                ->filter()
                ->toArray();
            Log::info($submittedPoDataIds);
            // Delete entries not in the submitted list
            $deleted = GoodReceiptNoteData::where('good_receipt_note_id', $goodReceiptNote->id)
                ->whereNotIn('po_data_id', $submittedPoDataIds)
                ->delete();
            if(!$deleted){
                return redirect()->back()->withErrors(['error' => 'Update failed: Delete']);
            }
            // Update or create entries
            foreach ($request->poRowsArray as $row) {
                $poDataId = $request->input("po_data_id_{$row}");
                Log::info("Processing PO Data ID: {$poDataId}");

                GoodReceiptNoteData::updateOrCreate(
                    [
                    'good_receipt_note_id' => $goodReceiptNote->id,
                    'po_data_id' => $poDataId
                ],
                    [
                        'po_id' => $request->input("purchase_order_id_{$row}"),
                        'quotation_no' => $request->input("quotation_no_{$row}"),
                        'expiry_date' => $request->input("expiry_date_{$row}"),
                        'receive_qty' => $request->input("receive_qty_{$row}"),
                    ]
                );
            }

            DB::commit();
            return redirect()->route($this->page . 'index')->with('success', 'Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("GRN Update Error: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    } */
    public function update(Request $request, GoodReceiptNote $goodReceiptNote)
    {
        DB::beginTransaction();
        try {
            // Update main GRN
            $goodReceiptNote->update([
                'supplier_id' => $request->supplier_id,
                'grn_date' => $request->grn_date,
                'description' => $request->description
            ]);

            // Get valid submitted PO data IDs
            $submittedPoDataIds = collect($request->poRowsArray)
                ->map(fn($row) => $request->input("po_data_id_{$row}"))
                ->filter()
                ->values()
                ->toArray();

            // Clean up removed entries
            GoodReceiptNoteData::where('good_receipt_note_id', $goodReceiptNote->id)
                ->whereNotIn('po_data_id', $submittedPoDataIds)
                ->delete();

            // Update/create entries
            foreach ($request->poRowsArray as $row) {
                $poDataId = $request->input("po_data_id_{$row}");

                GoodReceiptNoteData::updateOrCreate(
                    [
                        'good_receipt_note_id' => $goodReceiptNote->id,
                        'po_data_id' => $poDataId
                    ],
                    [
                        'good_receipt_note_id' => $goodReceiptNote->id, // Explicitly include
                        'po_id' => $request->input("purchase_order_id_{$row}"),
                        'quotation_no' => $request->input("quotation_no_{$row}"),
                        'expiry_date' => $request->input("expiry_date_{$row}"),
                        'receive_qty' => $request->input("receive_qty_{$row}"),
                        // Add other required fields from your error message:
                        'company_id' => auth()->user()->company_id,
                        'company_location_id' => auth()->user()->company_location_id,
                        'status' => 1,
                        'created_by' => auth()->user()->name,
                        'created_date' => now()->format('Y-m-d')
                    ]
                );
            }

            DB::commit();
            return redirect()->route($this->page . 'index')->with('success', 'Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("GRN Update Error: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function show(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);

        $goodReceiptNoteId = $request->id;

        // Fetch Good Receipt Note Details with Supplier Name
        $goodReceiptNoteDetail = DB::table('good_receipt_notes')
            ->join('suppliers', 'good_receipt_notes.supplier_id', '=', 'suppliers.id')
            ->select('good_receipt_notes.*', 'suppliers.name as supplier')
            ->where('good_receipt_notes.id', $goodReceiptNoteId)
            ->first();

        if (!$goodReceiptNoteDetail) {
            return response()->json(['error' => 'Good Receipt Note not found'], 404);
        }

        // Fetch associated GRN data along with purchase order details
        $goodReceiptNoteDataDetails = DB::table('grn_datas as grnd')
            ->join('purchase_order_datas as pod', 'grnd.po_data_id', '=', 'pod.id')
            ->join('purchase_orders as po', 'po.id', '=', 'pod.purchase_order_id')
            ->join('product_variants as pv', 'pod.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->select(
                'grnd.*',
                'po.po_no',
                'po.po_date',
                'pod.qty as po_qty',
                'pod.unit_price as po_unit_price',
                'pod.sub_total as po_sub_total',
                's.name as size_name',
                'pv.amount as product_variant_amount',
                'p.name as product_name'
            )
            ->where('grnd.good_receipt_note_id', $goodReceiptNoteId)
            ->get();

        // Attach purchase order data to the main object
        $goodReceiptNoteDetail->grnData = $goodReceiptNoteDataDetails;

        // Return the view with the purchase order details
        return view($this->page . 'viewGoodReceiptNoteDetail', compact('goodReceiptNoteDetail', 'goodReceiptNoteDataDetails'));
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // Query builder to fetch data with a join between 'good_receipt_notes' and 'suppliers' table
            $storeChallans = DB::table('store_challans as sc')
                ->join('departments as d', 'sc.department_id', '=', 'd.id')
                ->select('sc.*', 'd.department_name as department_name')
                ->where('sc.company_id', $companyId)
                ->where('sc.location_id', $companyLocationId)
                ->whereBetween('sc.store_challan_date', [$fromDate, $toDate]);

             
            // Apply status filter if provided
            if ($status) {
                $storeChallans->where('sc.status', $status);
            }

            // Execute the query and get the results
            $storeChallans = $storeChallans->get();
        // dd($storeChallans);

            // If rendering in a web view (non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('storeChallans'));
            }

            // Return JSON response for API requests
            return jsonResponse($storeChallans, 'Store Challans Retrieved Successfully', 'success', 200);
        }

        // For non-API requests, return the view for the page
        return view($this->page . 'index');
    }

    public function status($id)
    {
        try {
            // Find the GoodReceiptNote by ID
            $storeChallan = StoreChallan::find($id);

            // Check if record exists
            if (!$storeChallan) {
                Log::error("storeChallan with ID $id not found.");
                return response()->json(['error' => 'store Challan Note not found.'], 404);
            }
            DB::beginTransaction();

            // Debugging Log
            Log::info("Updating status for storeChallan ID: $id");

            // Update the status
            $storeChallan->status = 1;
            $storeChallan->save(); // Save the update

            // Commit transaction
            DB::commit();


            return response()->json(['success' => 'Store Challan marked as Active successfully!']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on failure
            Log::error("Error updating GoodReceiptNote: " . $e->getMessage()); // Log error for debugging

            return response()->json(['error' => 'An error occurred while Activating the Good Receipt Note.', 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        // Find the GoodReceiptNote by ID
        $storeChallan = StoreChallan::find($id);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete the GoodReceiptNote
            $storeChallan->status = 2;
            $storeChallan->save();
            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Store Challan  marked as inactive successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while deactivating the Good Receipt Note.'], 500);
        }
    }

    public function approveGoodReceiptNoteVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $username = Auth::user()->name;
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        // Update GRN Status
        DB::table('good_receipt_notes')
            ->where([
                ['id', '=', $id],
                ['company_id', '=', $companyId],
                ['company_location_id', '=', $companyLocationId]
            ])->update(['store_challan_status' => 2]);

        // Fetch Good Receipt Note details
        $goodReceiptNoteDetail = DB::table('good_receipt_notes')
            ->where([
                ['id', '=', $id],
                ['company_id', '=', $companyId],
                ['company_location_id', '=', $companyLocationId]
            ])->first();

        if (!$goodReceiptNoteDetail) {
            return response()->json(['message' => 'Good Receipt Note not found'], 404);
        }

        // Create Journal Voucher
        $journalVoucher = new JournalVoucher();
        $journalVoucher->company_id = $companyId;
        $journalVoucher->company_location_id = $companyLocationId;
        $journalVoucher->jv_date = $goodReceiptNoteDetail->grn_date;
        $journalVoucher->jv_no = JournalVoucher::VoucherNo();;
        $journalVoucher->slip_no = $goodReceiptNoteDetail->grn_no;
        $journalVoucher->voucher_type = 2;
        $journalVoucher->description = $goodReceiptNoteDetail->description;
        $journalVoucher->username = Auth::user()->name;
        $journalVoucher->status = 1;
        $journalVoucher->jv_status = 2;
        $journalVoucher->date = date('Y-m-d');
        $journalVoucher->time = date("H:i:s");
        $journalVoucher->approve_username = Auth::user()->name;
        $journalVoucher->approve_date = date('Y-m-d');
        $journalVoucher->approve_time = date("H:i:s");
        $journalVoucher->delete_username = '-';
        $journalVoucher->save();

        $journalVoucherId = $journalVoucher->id;

        DB::table('good_receipt_notes')
            ->where([
                ['id', '=', $id],
                ['company_id', '=', $companyId],
                ['company_location_id', '=', $companyLocationId]
            ])->update(['jv_id' => $journalVoucherId]);

        DB::table('grn_datas')
            ->where([
                ['good_receipt_note_id', '=', $id],
                ['company_id', '=', $companyId],
                ['company_location_id', '=', $companyLocationId]
            ])->update(['jv_id' => $journalVoucherId]);

        // Fetch GRN data
        $grnDataDetails = DB::table('grn_datas as grnd')
            ->join('purchase_order_datas as pod', 'grnd.po_data_id', '=', 'pod.id')
            ->join('purchase_orders as po', 'po.id', '=', 'pod.purchase_order_id')
            ->join('product_variants as pv', 'pod.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->where('grnd.good_receipt_note_id', $id)
            ->select([
                'grnd.*',
                'po.po_no',
                'po.po_date',
                'pod.qty as po_qty',
                'pod.unit_price as po_unit_price',
                'pod.sub_total as po_sub_total',
                'pod.product_variant_id',
                'pv.product_id',
                's.name as size_name',
                'pv.amount as product_variant_amount',
                'p.name as product_name',
                'c.acc_id as account_id',
                'p.acc_id as p_acc_id'
            ])->get();

        $faraData = [];
        $jvDebitData = [];
        $totalDebitAmount = 0;
        $taxAmount = $goodReceiptNoteDetail->tax_amount;
        $taxAccountId = $goodReceiptNoteDetail->tax_account_id;

        foreach ($grnDataDetails as $grndRow) {
            $amount = $grndRow->receive_qty * $grndRow->po_unit_price;
            $totalDebitAmount += $amount;

            $faraData[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'process_type' => $goodReceiptNoteDetail->process_type,
                'status' => 2,
                'supplier_id' => $goodReceiptNoteDetail->supplier_id,
                'main_table_id' => $id,
                'main_table_data_id' => $grndRow->id,
                'po_no' => $grndRow->po_no,
                'po_date' => $grndRow->po_date,
                'grn_no' => $goodReceiptNoteDetail->grn_no,
                'grn_date' => $goodReceiptNoteDetail->grn_date,
                'product_id' => $grndRow->product_id,
                'product_variant_id' => $grndRow->product_variant_id,
                'qty' => $grndRow->receive_qty,
                'rate' => $grndRow->po_unit_price,
                'amount' => $amount,
                'remarks' => $goodReceiptNoteDetail->description,
                'created_by' => $username,
                'created_date' => $currentDate
            ];

            $jvDebitData[] = [
                'journal_voucher_id' => $journalVoucherId,
                'acc_id' => $grndRow->p_acc_id,
                'description' => $goodReceiptNoteDetail->description,
                'debit_credit' => 1,
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

        if ($faraData) {
            DB::table('faras')->insert($faraData);
        }
        if ($jvDebitData) {
            DB::table('journal_voucher_data')->insert($jvDebitData);
        }

        // Fetch supplier account details
        $supplierAccountDetail = DB::table('suppliers')
            ->where('id', $goodReceiptNoteDetail->supplier_id)
            ->select('acc_id')
            ->first();

        if($taxAccountId != ''){
            DB::table('journal_voucher_data')->insert([
                'journal_voucher_id' => $journalVoucherId,
                'acc_id' => $taxAccountId,
                'description' => $goodReceiptNoteDetail->description,
                'debit_credit' => 1,
                'amount' => $taxAmount,
                'jv_status' => 2,
                'time' => $currentTime,
                'date' => $currentDate,
                'status' => 1,
                'username' => $username,
                'approve_username' => $username,
                'delete_username' => '-'
            ]);
        }

        if ($supplierAccountDetail) {
            DB::table('journal_voucher_data')->insert([
                'journal_voucher_id' => $journalVoucherId,
                'acc_id' => $supplierAccountDetail->acc_id,
                'description' => $goodReceiptNoteDetail->description,
                'debit_credit' => 2,
                'amount' => $totalDebitAmount + $taxAmount,
                'jv_status' => 2,
                'time' => $currentTime,
                'date' => $currentDate,
                'status' => 1,
                'username' => $username,
                'approve_username' => $username,
                'delete_username' => '-'
            ]);
        }
        $journalVoucherDetails = DB::table('journal_vouchers')->where([
            'id' => $journalVoucherId,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();
        $journalVoucherDataDetails = DB::table('journal_voucher_data')->where('journal_voucher_id', $journalVoucherId)->get();
        foreach ($journalVoucherDataDetails as $jvddRow) {
            $transactions[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'acc_id' => $jvddRow->acc_id,
                'particulars' => $jvddRow->description,
                'opening_bal' => 2,
                'debit_credit' => $jvddRow->debit_credit,
                'amount' => $jvddRow->amount,
                'voucher_id' => $journalVoucherId,
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

    public function goodReceiptNoteVoucherRejectAndRepost(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('store_challans')->where('id', $id)->where('company_id', $companyId)->where('location_id', $companyLocationId)->update(['store_challan_status' => $value]);
        echo 'Done';
    }

    public function goodReceiptNoteVoucherActiveAndInactive(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('good_receipt_notes')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['status' => $value]);
        echo 'Done';
    }
}
