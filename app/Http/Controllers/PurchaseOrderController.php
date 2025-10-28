<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderData;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseOrderController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'purchase-orders.';
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

        return view($this->page . 'create', compact('products', 'payment_types', 'suppliers', 'departments'));
    }

    public function store(Request $request)
    {
       
       
        try {
            $validatedData = $request->validate([
                'po_date' => 'required|date',
                // 'delivery_place' => 'required|string|max:255',
                'quotation_no' => 'nullable|string|max:255',
                // 'delivery_place' => 'required|string|max:255',
                'quotation_no' => 'nullable|string|max:255',
                'quotation_date' => 'required|date',
                'main_description' => 'nullable|string|max:255',
                'paymentType' => 'required|integer',
                'payment_type_rate' => 'required|numeric',
                'supplier_id' => 'required|integer',
                'po_note' => 'nullable|string',
                'poDataArray' => 'required|array',
                'poDataArray.*' => 'required|integer',
                'productId_*' => 'required|integer',
                'qty_*' => 'required|numeric',
                'unitPrice_*' => 'required|numeric',
                'subTotal_*' => 'required|numeric',
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
            //  dd($request);
            //  dd($request);
            // Insert data into PurchaseOrder
            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder->po_no = PurchaseOrder::VoucherNo();
            $purchaseOrder->po_date = $request->po_date;
            // $purchaseOrder->delivery_place = $request->delivery_place;
            // $purchaseOrder->delivery_place = $request->delivery_place;
            $purchaseOrder->invoice_quotation_no = $request->quotation_no;
            $purchaseOrder->quotation_date = $request->quotation_date;
            $purchaseOrder->main_description = $request->main_description;
            $purchaseOrder->paymentType = $request->paymentType;
            $purchaseOrder->payment_type_rate = $request->payment_type_rate;
            $purchaseOrder->supplier_id = $request->supplier_id;
            $purchaseOrder->po_note = $request->po_note;
            $purchaseOrder->save();

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
                // dd($purchaseOrderData);
                // dd($purchaseOrderData);
            }

            //Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Purchase Order Created Successfully');
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
                'po_date' => 'required|date',
                // 'delivery_place' => 'required|string|max:255',
                // 'delivery_place' => 'required|string|max:255',
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
            // $purchaseOrder->delivery_place = $request->delivery_place;
            // $purchaseOrder->delivery_place = $request->delivery_place;
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
            $poDataArray = is_string($request->poDataArray) ? json_decode($request->poDataArray, true) : $request->poDataArray;
            if (!is_array($poDataArray)) {
                return redirect()->back()->withErrors(['error' => 'Invalid poDataArray format.']);
            }
            Log::info($poDataArray);

            // Update the associated PurchaseOrderData entries
            foreach ($poDataArray as $poData) {
                $purchaseOrderData = PurchaseOrderData::where('purchase_order_id', $purchaseOrder->id)
                    ->where('product_variant_id', $poData['product_id'])
                    ->first();
                Log::info($poData);
                if ($purchaseOrderData) {
                    // If the record exists, update it
                    $purchaseOrderData->product_variant_id = $poData['product_id'];
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

        $purchaseOrderId = $request->id;

        // Fetch the purchase order details with the supplier name
        $purchaseOrder = DB::table('purchase_orders')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select(
                'purchase_orders.*',
                'suppliers.name as supplier'
            )
            ->where('purchase_orders.id', $purchaseOrderId)
            ->first();

        if (!$purchaseOrder) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        }

        // Attach purchase order data to the main object
        $purchaseOrder->purchaseOrderData = DB::table('purchase_order_datas as pod')
            ->join('product_variants as pv', 'pod.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->select('pod.*', 's.name as size_name', 'pv.amount as product_variant_amount', 'p.name as product_name')
            ->where('purchase_order_id', $purchaseOrderId)
            ->get();

        // Fetch related purchase order details for display
        $purchaseOrderDetails = $purchaseOrder->purchaseOrderData;

        // Return the view with the purchase order details
        return view($this->page . 'viewPurchaseOrderDetail', compact('purchaseOrder', 'purchaseOrderDetails'));
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
                    // 'po.delivery_place',
                    // 'po.delivery_place',
                    'po.invoice_quotation_no',
                    'po.quotation_date',
                    'po.main_description',
                    'po.paymentType',
                    'po.payment_type_rate',
                    'po.supplier_id',
                    'po.po_note',
                    'po.status',
                    'po.po_status',
                    'po.created_date',
                    'po.created_by',
                    's.name as supplier_name',

                )
                ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->where('po.process_type', 1)
                ->whereBetween('po.po_date', [$fromDate, $toDate])
                ->where('po.company_id', $companyId)
                ->where('po.company_location_id', $companyLocationId);
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
        return response()->json(['success' => 'Purchase Order marked as active successfully!']);
        //return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order Activated Successfully');
    }
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        $purchaseOrder->status = 2;
        $purchaseOrder->save();
        return response()->json(['success' => 'Purchase Order marked as inactive successfully!']);
    }

    public function approvePurchaseOrderVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('purchase_orders')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['po_status' => 2]);
        echo 'Done';
    }

    public function purchaseOrderVoucherRejectAndRepost(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('purchase_orders')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['po_status' => $value]);
        echo 'Done';
    }

    public function purchaseOrderVoucherActiveAndInactive(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('purchase_orders')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['status' => $value]);
        echo 'Done';
    }






}
