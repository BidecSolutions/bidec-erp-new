<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestData;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MaterialRequestController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'material-requests.';
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
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes, 'departments' => $departments] = $data;

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

        return view($this->page . 'create', compact('products', 'departments'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'mr_date' => 'required|date',
                'main_description' => 'nullable|string|max:255',
                'department_id' => 'required|integer',
                'mrDataArray' => 'required|array',
                'mrDataArray.*' => 'required|integer',
                'productId_*' => 'required|integer',
                'qty_*' => 'required|numeric',
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
            $materialRequest = new MaterialRequest();
            $materialRequest->material_request_no = MaterialRequest::VoucherNo();
            $materialRequest->material_request_date = $request->mr_date;
            $materialRequest->main_description = $request->main_description;
            $materialRequest->department_id = $request->department_id;
            $materialRequest->save();

            // Insert data into MaterialRequestData
            foreach ($request->mrDataArray as $key => $mrData) {
                $index = $key + 1; // Assuming data starts from index 1

                $materialRequestData = new MaterialRequestData();
                $materialRequestData->material_request_id = $materialRequest->id;
                $materialRequestData->product_variant_id = $request->input('productId_' . $index);
                $materialRequestData->qty = $request->input('qty_' . $index);
                $materialRequestData->save();
            }

            //Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Material Request Created Successfully');
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
            $materialRequest = MaterialRequest::findOrFail($id);
            $materialRequestData = MaterialRequestData::where('material_request_id', $id)->get();

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
            return view($this->page . 'edit', compact('materialRequest', 'materialRequestData', 'products', 'payment_types', 'suppliers', 'departments'));
        } catch (\Exception $e) {
            return redirect()->route($this->page . 'index')->withErrors(['error' => 'The Request Was not found']);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'mr_date' => 'required|date',
                'main_description' => 'nullable|string|max:255',
                'department_id' => 'required|integer',
                'mrDataArray' => 'required|array',
                'productId_*' => 'required|integer',
                'qty_*' => 'required|numeric',
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
            $materialRequest = MaterialRequest::findOrFail($id);
            $materialRequest->material_request_date = $request->mr_date;
            $materialRequest->main_description = $request->main_description;
            $materialRequest->department_id = $request->department_id;
            $materialRequest->save();

            // Delete old associated MaterialRequestData entries
            MaterialRequestData::where('material_request_id', $materialRequest->id)->delete();
            $mrDataArray = is_string($request->mrDataArray) ? json_decode($request->mrDataArray, true) : $request->mrDataArray;
            if (!is_array($mrDataArray)) {
                return redirect()->back()->withErrors(['error' => 'Invalid mrDataArray format.']);
            }
            Log::info($mrDataArray);
            foreach ($mrDataArray as $mrData) {
                $materialRequestData = MaterialRequestData::where('material_request_id', $materialRequest->id)
                    ->where('product_variant_id', $mrData['product_id'])
                    ->first();
                Log::info($materialRequestData);
              if ($materialRequestData) {
                    // If the record exists, update it
                    $materialRequestData->product_variant_id = $request->input('productId_' . $index);
                    $materialRequestData->qty = $request->input('qty_' . $index);
                    $materialRequestData->save();
                } else {
                    // If the record does not exist, create a new entry
                        $materialRequestData = new MaterialRequestData();
                        $materialRequestData->material_request_id = $materialRequest->id;
                        $materialRequestData->product_variant_id = $mrData['product_id'];
                        $materialRequestData->qty = $mrData['qty'];
                        $materialRequestData->save();
                }
            }
            // Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Material Request Updated Successfully');
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

        $materialRequestId = $request->id;

        // Fetch the purchase order details with the supplier name
        $materialRequest = DB::table('material_requests')
            ->join('departments', 'material_requests.department_id', '=', 'departments.id')
            ->select(
                'material_requests.*',
                'departments.department_name as department'
            )
            ->where('material_requests.id', $materialRequestId)
            ->first();

        if (!$materialRequest) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        }

        // Attach purchase order data to the main object
        $materialRequest->materialRequestData = DB::table('material_request_datas as mrd')
            ->join('product_variants as pv', 'mrd.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->select('mrd.*', 
            'pv.amount as product_variant_amount',
            'p.name as product_name')
            ->where('material_request_id', $materialRequestId)
            ->get();

        // Fetch related purchase order details for display
        $materialRequestDetails = $materialRequest->materialRequestData;

        // Return the view with the purchase order details
        return view($this->page . 'viewMaterialRequestDetail', compact('materialRequest', 'materialRequestDetails'));
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
            $materialRequests = DB::table('material_requests as mr')
                ->select(
                    'mr.id',
                    'mr.company_id',
                    'mr.location_id',
                    'mr.material_request_no',
                    'mr.material_request_date',
                    'mr.main_description',
                    'mr.department_id',
                    'mr.status',
                    'mr.material_request_status',
                    'mr.created_date',
                    'mr.created_by',
                    'd.department_name as department_name',

                )
                ->join('departments as d', 'mr.department_id', '=', 'd.id')
                ->whereBetween('mr.material_request_date', [$fromDate, $toDate])
                ->where('mr.company_id', $companyId)
                ->where('mr.location_id', $companyLocationId);
            if ($status) {
                $materialRequests = $materialRequests->where('mr.status', $status);
            }

            $materialRequests = $materialRequests->get();

            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('materialRequests'));
            }

            // Return JSON response for API requests
            return jsonResponse($materialRequests, 'Material Request Retrieved Successfully', 'success', 200);
        }

        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function status($id)
    {
        $materialRequest = MaterialRequest::find($id);
        $materialRequest->status = 1;
        $materialRequest->save();
        return response()->json(['success' => 'Material Request marked as active successfully!']);
    }
    public function destroy($id)
    {
        $materialRequest = MaterialRequest::find($id);
        $materialRequest->status = 2;
        $materialRequest->save();
        return response()->json(['success' => 'Material Request marked as inactive successfully!']);
    }

    public function approveMaterialRequestVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('material_requests')->where('id', $id)->where('company_id', $companyId)->where('location_id', $companyLocationId)->update(['material_request_status' => 2]);
        echo 'Done';
    }

    public function MaterialRequestVoucherRejectAndRepost(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('material_requests')->where('id', $id)->where('company_id', $companyId)->where('location_id', $companyLocationId)->update(['material_request_status' => $value]);
        echo 'Done';
    }

    public function materialRequestVoucherActiveAndInactive(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('material_requests')->where('id', $id)->where('company_id', $companyId)->where('location_id', $companyLocationId)->update(['status' => $value]);
        echo 'Done';
    }
    public function getLastPurchasePrice($productVariantId)
{
    $lastPrice = DB::table('material_request_datas')
        ->where('product_variant_id', $productVariantId)
        ->orderByDesc('id')
        ->value('unit_price');

    return response()->json(['price' => $lastPrice ?? 0]);
}


}
