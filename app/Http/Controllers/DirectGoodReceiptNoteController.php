<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Google\Service\CloudResourceManager\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteData;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderData;
use Exception;
use Illuminate\Support\Facades\Session;

class DirectGoodReceiptNoteController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'direct-good-receipt-note.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            'products' => storage_path('app/json_files/products.json'),
            'product_variants' => storage_path('app/json_files/product_variants.json'),
            'categories' => storage_path('app/json_files/categories.json'),
            'brands' => storage_path('app/json_files/brands.json'),
            'sizes' => storage_path('app/json_files/sizes.json'),
            'payment_types' => storage_path('app/json_files/payment_types.json'),
            'suppliers' => storage_path('app/json_files/suppliers.json'),
            'departments' => storage_path('app/json_files/departments.json'),
            'tax_accounts' => storage_path('app/json_files/tax_accounts.json'),
        ];

        // Ensure all necessary JSON files exist
        foreach ($jsonFiles as $key => $filePath) {
            if (!file_exists($filePath)) {
                generate_json($key); // Generate the missing JSON file
            }
        }

        // Load data from JSON files
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes, 'payment_types' => $payment_types, 'suppliers' => $suppliers, 'departments' => $departments,'tax_accounts' => $tax_accounts] = $data;

        // Optimize the relationship building by indexing categories, brands, and sizes by their IDs
        $categoryMap = array_column($categories, 'name', 'id');
        $brandMap = array_column($brands, 'name', 'id');
        $sizeMap = array_column($sizes, 'name', 'id');

        $tax_accounts = array_filter($tax_accounts, fn($ta) =>
            $ta['company_id'] == $companyId &&
            $ta['company_location_id'] == $companyLocationId
        );

        $suppliers = array_filter($suppliers, fn($s) =>
            $s['company_id'] == $companyId &&
            $s['company_location_id'] == $companyLocationId
        );

        $categories = array_filter($categories, fn($c) =>
            $c['company_id'] == $companyId &&
            $c['company_location_id'] == $companyLocationId
        );

        $products = array_filter($products, fn($p) =>
            $p['company_id'] == $companyId &&
            $p['company_location_id'] == $companyLocationId
        );

        // Attach related data (variants, category names, brand names, and size names) to products
        $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
            // Attach variants to each product
            $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);

            // Assign category, brand, and size names
            $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
            $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';

            // For each variant, assign the size name
            foreach ($product['variants'] as &$variant) {
                $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
            }

            return $product;
        }, $products);

        // Apply status filter if provided
        $products = array_filter($products, fn($product) => $product['status'] == 1);

        return view($this->page . 'create', compact('products', 'payment_types', 'suppliers', 'departments','tax_accounts'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tax_account_id' => 'nullable|exists:chart_of_accounts,id',
                'grn_date' => 'required|date',
                'delivery_place' => 'required|string|max:255',
                'quotation_no' => 'required|string|max:255',
                'quotation_date' => 'required|date',
                'main_description' => 'nullable|string|max:255',
                'paymentType' => 'required|integer',
                'payment_type_rate' => 'required|numeric',
                'supplier_id' => 'required|integer',
                'po_note' => 'nullable|string',
                'tax_amount' => 'nullable|numeric',
                'poDataArray' => 'required|array',
                'poDataArray.*' => 'required|integer',
                'productId_*' => 'required|integer',
                'qty_*' => 'required|numeric',
                'unitPrice_*' => 'required|numeric',
                'subTotal_*' => 'required|numeric',
                'expiryDate_*' => 'required|date',
                
            ]);

            // Proceed with your logic if validation passes.
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Insert data into PurchaseOrder
            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder->po_no = PurchaseOrder::VoucherNo();
            $purchaseOrder->process_type = 2;
            $purchaseOrder->po_date = $request->grn_date;
            $purchaseOrder->delivery_place = $request->delivery_place;
            $purchaseOrder->invoice_quotation_no = $request->quotation_no;
            $purchaseOrder->quotation_date = $request->quotation_date;
            $purchaseOrder->main_description = $request->main_description;
            $purchaseOrder->paymentType = $request->paymentType;
            $purchaseOrder->payment_type_rate = $request->payment_type_rate;
            $purchaseOrder->supplier_id = $request->supplier_id;
            $purchaseOrder->po_note = $request->po_note;
            $purchaseOrder->po_status = 2;
            $purchaseOrder->save();

            // Insert data into PurchaseOrder
            $goodReceiptNote = new GoodReceiptNote();
            $goodReceiptNote->process_type = 2;
            $goodReceiptNote->supplier_id = $request->supplier_id;
            $goodReceiptNote->grn_no = GoodReceiptNote::VoucherNo();
            $goodReceiptNote->grn_date = $request->grn_date;
            $goodReceiptNote->description = $request->main_description;
            $goodReceiptNote->tax_account_id = $request->tax_account_id;
            $goodReceiptNote->tax_amount = $request->tax_amount;
            $goodReceiptNote->save();

            // Insert data into PurchaseOrderData
            foreach ($request->poDataArray as $key => $poData) {
                $index = $key + 1; // Assuming data starts from index 1

                $purchaseOrderData = new PurchaseOrderData();
                $purchaseOrderData->purchase_order_id = $purchaseOrder->id;
                $purchaseOrderData->product_variant_id = $request->input('productId_' . $index);
                $purchaseOrderData->qty = $request->input('qty_' . $index);
                $purchaseOrderData->unit_price = $request->input('unitPrice_' . $index);
                $purchaseOrderData->sub_total = $request->input('subTotal_' . $index);
                $purchaseOrderData->save();

                $goodReceiptNoteData = new GoodReceiptNoteData();
                $goodReceiptNoteData->good_receipt_note_id = $goodReceiptNote->id;
                $goodReceiptNoteData->po_id = $purchaseOrder->id;
                $goodReceiptNoteData->po_data_id = $purchaseOrderData->id;
                $goodReceiptNoteData->quotation_no = $request->quotation_no;
                $goodReceiptNoteData->expiry_date = $request->input('expiryDate_' . $index);
                $goodReceiptNoteData->receive_qty = $request->input('qty_' . $index);
                $goodReceiptNoteData->save();
            }

            //Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Direct Good Receipt Note Created Successfully');
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
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        try {
            // Fetch the purchase order and related data
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrderData = PurchaseOrderData::where('purchase_order_id', $id)->get();

            // Define file paths for JSON files
            $jsonFiles = [
                'products' => storage_path('app/json_files/products.json'),
                'product_variants' => storage_path('app/json_files/product_variants.json'),
                'categories' => storage_path('app/json_files/categories.json'),
                'brands' => storage_path('app/json_files/brands.json'),
                'sizes' => storage_path('app/json_files/sizes.json'),
                'payment_types' => storage_path('app/json_files/payment_types.json'),
                'suppliers' => storage_path('app/json_files/suppliers.json'),
                'departments' => storage_path('app/json_files/departments.json'),
            ];

            // Ensure all necessary JSON files exist
            foreach ($jsonFiles as $key => $filePath) {
                if (!file_exists($filePath)) {
                    generate_json($key); // Generate the missing JSON file
                }
            }

            // Load data from JSON files
            $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
            ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes, 'payment_types' => $payment_types, 'suppliers' => $suppliers, 'departments' => $departments] = $data;

            // Optimize the relationship building by indexing categories, brands, and sizes by their IDs
            $categoryMap = array_column($categories, 'name', 'id');
            $brandMap = array_column($brands, 'name', 'id');
            $sizeMap = array_column($sizes, 'name', 'id');

            $suppliers = array_filter($suppliers, fn($s) =>
                $s['company_id'] == $companyId &&
                $s['company_location_id'] == $companyLocationId
            );

            $categories = array_filter($categories, fn($c) =>
                $c['company_id'] == $companyId &&
                $c['company_location_id'] == $companyLocationId
            );

            $products = array_filter($products, fn($p) =>
                $p['company_id'] == $companyId &&
                $p['company_location_id'] == $companyLocationId
            );

            // Attach related data (variants, category names, brand names, and size names) to products
            $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
                // Attach variants to each product
                $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);

                // Assign category, brand, and size names
                $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
                $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';

                // For each variant, assign the size name
                foreach ($product['variants'] as &$variant) {
                    $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
                }

                return $product;
            }, $products);

            // Apply status filter if provided
            $products = array_filter($products, fn($product) => $product['status'] == 1);

            // Pass the data to the view
            return view($this->page . 'edit', compact('purchaseOrder', 'purchaseOrderData', 'products', 'payment_types', 'suppliers', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route($this->page . 'index')->withErrors(['error' => 'The Request Was not found']);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'po_date' => 'date',
                'delivery_place' => 'required|string|max:255',
                'quotation_no' => 'nullable|string|max:255',
                'quotation_date' => 'required|date',
                'main_description' => 'nullable|string',
                'paymentType' => 'required|integer',
                'payment_type_rate' => 'required|numeric|min:0',
                'supplier_id' => 'required|integer|exists:suppliers,id',
                'po_note' => 'nullable|string',
                'poDataArray' => 'required|array',
                'poDataArray.*.product_id' => 'required|integer|exists:product_variants,id',
                'poDataArray.*.qty' => 'required|numeric|min:1',
                'poDataArray.*.unit_price' => 'required|numeric|min:0',
                'poDataArray.*.sub_total' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Begin transaction

        DB::beginTransaction();

        try {
            // Fetch the existing Purchase Order
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            // Update PurchaseOrder details
            $purchaseOrder->po_date = $request->po_date;
            $purchaseOrder->delivery_place = $request->delivery_place;
            $purchaseOrder->invoice_quotation_no = $request->quotation_no;
            $purchaseOrder->quotation_date = $request->quotation_date;
            $purchaseOrder->main_description = $request->main_description;
            $purchaseOrder->paymentType = $request->paymentType;
            $purchaseOrder->payment_type_rate = $request->payment_type_rate;
            $purchaseOrder->supplier_id = $request->supplier_id;
            $purchaseOrder->po_note = $request->po_note;
            $purchaseOrder->save();

            // Delete old associated PurchaseOrderData entries
            PurchaseOrderData::where('purchase_order_id', $purchaseOrder->id)->delete();

            // Update the associated PurchaseOrderData entries
            foreach ($request->poDataArray as $poData) {
                $purchaseOrderData = PurchaseOrderData::where('purchase_order_id', $purchaseOrder->id)
                    ->where('product_variant_id', $poData['product_id'])
                    ->first();

                if ($purchaseOrderData) {
                    // If the record exists, update it
                    $purchaseOrderData->qty = $poData['qty'];
                    $purchaseOrderData->unit_price = $poData['unit_price'];
                    $purchaseOrderData->sub_total = $poData['sub_total'];
                    $purchaseOrderData->save();
                } else {
                    // If the record does not exist, create a new entry
                    $purchaseOrderData = new PurchaseOrderData();
                    $purchaseOrderData->purchase_order_id = $purchaseOrder->id;
                    $purchaseOrderData->product_variant_id = $poData['product_id'];
                    $purchaseOrderData->qty = $poData['qty'];
                    $purchaseOrderData->unit_price = $poData['unit_price'];
                    $purchaseOrderData->sub_total = $poData['sub_total'];
                    $purchaseOrderData->save();
                }
            }


            // Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Purchase Order Updated Successfully');
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
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
            ->leftJoin('tax_accounts as ta','good_receipt_notes.tax_account_id','=','ta.acc_id')
            ->leftJoin('tax_accounts as ta','good_receipt_notes.tax_account_id','=','ta.acc_id')
            ->select('good_receipt_notes.*', 'suppliers.name as supplier','ta.name as tax_account_name')
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
        return view($this->page . 'viewDirectGoodReceiptNoteDetail', compact('goodReceiptNoteDetail', 'goodReceiptNoteDataDetails'));
    }





    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

            // Use Query Builder to select data
            $purchaseOrders = DB::table('purchase_orders as po')
                ->select(
                    'po.id',
                    'po.company_id',
                    'po.company_location_id',
                    'po.po_no',
                    'po.po_date',
                    'po.delivery_place',
                    'po.invoice_quotation_no',
                    'po.quotation_date',
                    'po.main_description',
                    'po.paymentType',
                    'po.payment_type_rate',
                    'po.supplier_id',
                    'po.po_note',
                    'po.created_date',
                    'po.created_by',
                    's.name as supplier_name',
                    'grn.id as grn_id',
                    'grn.grn_no',
                    'grn.grn_date',
                    'grn.grn_status',
                    'grn.status',
                    'grn.created_by',
                    'grn.created_date'

                )
                ->join('grn_datas as grnd','po.id','=','grnd.po_id')
                ->join('good_receipt_notes as grn','grnd.good_receipt_note_id','=','grn.id')
                ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->whereBetween('po.po_date', [$fromDate, $toDate])
                ->where('po.process_type',2)
                ->where('po.company_id',$companyId)
                ->where('po.company_location_id',$companyLocationId);
            if ($status) {
                $purchaseOrders = $purchaseOrders->where('po.status', $status);
            }

            $purchaseOrders = $purchaseOrders->get();

            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('purchaseOrders'));
            }

            // Return JSON response for API requests
            return jsonResponse($purchaseOrders, 'Purchase Orders Retrieved Successfully', 'success', 200);
        }

        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function status($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        $purchaseOrder->status = 1;
        $purchaseOrder->save();
        return response()->json(['success' => 'Purchase Order Activated Successfully', 'Purchase Order Activated Successfully']);
    }
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        $purchaseOrder->status = 2;
        $purchaseOrder->save();
        return response()->json(['success' => 'Purchase Order Inactive Successfully', 'Purchase Order Inactive Successfully']);
    }
}
