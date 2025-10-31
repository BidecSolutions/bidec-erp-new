<?php

namespace App\Http\Controllers;
use App\Models\Department;
use Google\Service\CloudResourceManager\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BOM;
use App\Models\BOMData;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestData;
use App\Models\Fara;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherData;
use Illuminate\Support\Facades\Auth;

class BOMController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'bom.';
    }
    
    /**
     * Display a listing of the resource.
     */
 public function index(Request $request)
{
    $status = $request->input('filterStatus');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $companyId = Session::get('company_id');
    $companyLocationId = Session::get('company_location_id');

    $query = DB::table('boms as b')
        ->select(
            'b.id',
            'b.bom_no',
            'b.bom_date',
            'b.remarks',
            'b.status',
            'b.created_date',
            'b.created_by',
            'b.finish_product_id',
            'b.bom_status',
            'p.name as finish_product'
        )
        ->join('products as p', 'b.finish_product_id', '=', 'p.id');

    if ($fromDate && $toDate) {
        $query->whereBetween('b.bom_date', [$fromDate, $toDate]);
    }

    if ($status) {
        $query->where('b.status', $status);
    }

    // Only add company filters if these columns exist
    // $query->where('b.company_id', $companyId)->where('b.location_id', $companyLocationId);

    $boms = $query->get();

    if (!$this->isApi) {
        return webResponse($this->page, 'indexAjax', compact('boms'));
    }

    return jsonResponse($boms, 'BOM Retrieved Successfully', 'success', 200);
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
      
       $rawProducts = array_filter($products, fn($product) => $product['product_type'] == 1);
       $finishProducts = array_filter($products, fn($product) => $product['product_type'] == 2);

        return view($this->page . 'create', compact('rawProducts' ,'finishProducts' ,'products', 'payment_types', 'suppliers', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */

    
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'bom_date' => 'required|date',
                'remarks' => 'nullable|string|max:255',
                'finish_product_id' => 'required|integer',
                'bomDataArray' => 'required|array',
                'bomDataArray.*' => 'required|integer',
                'rowProductId_*' => 'required|integer',
                'qty_*' => 'required|numeric',
                'description' => 'nullable|string|max:255',

            ]);

            // Proceed with your logic if validation passes.
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Begin transaction
        // DB::beginTransaction();
      
        try {
            $bom = new BOM();
            $bom->bom_date = $request->bom_date;
            $bom->bom_no = BOM::VoucherNo();
            $bom->remarks = $request->remarks;
            $bom->finish_product_id = $request->finish_product_id;
            $bom->save();

            // Insert data into bomData
            foreach ($request->bomDataArray as $key => $bData) {
        
                $index = $key + 1; // Assuming data starts from index 1
                $bomData = new BOMData();
                $bomData->bom_id = $bom->id;
                $bomData->row_product_id = $request->input('rowProductId_' . $index);
                $bomData->qty = $request->input('qty_' . $index);
                $bomData->description = $request->input('description_' . $index);
                $bomData->save();
            }

            // Commit transaction
            DB::commit();
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'BOM Created Successfully');
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BOM $bOM)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
        public function edit($id)
        {
            // If API request → invalid endpoint
            if ($this->isApi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid endpoint for API.',
                ], 400);
            }

            try {
                // 🔹 Fetch BOM and its details
                $bom = BOM::findOrFail($id);
                $bomData = BOMData::where('bom_id', $id)->get();

                // 🔹 Define JSON file paths
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

                // 🔹 Ensure all required JSON files exist
                foreach ($jsonFiles as $key => $filePath) {
                    if (!file_exists($filePath)) {
                        generate_json($key);
                    }
                }

                // 🔹 Load data from JSON files
                $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
                [
                    'products' => $products,
                    'product_variants' => $variants,
                    'categories' => $categories,
                    'brands' => $brands,
                    'sizes' => $sizes,
                    'payment_types' => $payment_types,
                    'suppliers' => $suppliers,
                    'departments' => $departments
                ] = $data;

                // 🔹 Create lookup maps
                $categoryMap = array_column($categories, 'name', 'id');
                $brandMap = array_column($brands, 'name', 'id');
                $sizeMap = array_column($sizes, 'name', 'id');

                // 🔹 Attach relationships and names
                $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
                    $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);
                    $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
                    $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';
                    foreach ($product['variants'] as &$variant) {
                        $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
                    }
                    return $product;
                }, $products);

                // 🔹 Filter only active products
                $products = array_filter($products, fn($product) => $product['status'] == 1);

                // 🔹 Separate into raw and finish products
                $rawProducts = array_filter($products, fn($product) => $product['product_type'] == 1);
                $finishProducts = array_filter($products, fn($product) => $product['product_type'] == 2);

                // 🔹 Pass data to edit view
                return view($this->page . 'edit', compact('bom', 'bomData', 'rawProducts', 'finishProducts', 'products', 'payment_types', 'suppliers', 'departments'));

            } catch (\Exception $e) {
                return redirect()->route($this->page . 'index')
                    ->withErrors(['error' => 'The requested BOM was not found.']);
            }
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BOM $bOM)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BOM $bOM)
    {
        //
    }
}
