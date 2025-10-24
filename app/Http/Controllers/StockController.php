<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use DB;

class StockController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'stocks.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function openTraceStockModel()
    {

        // Define file paths for JSON files
        $jsonFiles = [
            'products' => storage_path('app/json_files/products.json'),
            'product_variants' => storage_path('app/json_files/product_variants.json'),
            'categories' => storage_path('app/json_files/categories.json'),
            'brands' => storage_path('app/json_files/brands.json'),
            'sizes' => storage_path('app/json_files/sizes.json'),
        ];

        // Ensure all necessary JSON files exist
        foreach ($jsonFiles as $key => $filePath) {
            if (!file_exists($filePath)) {
                generate_json($key); // Generate the missing JSON file
            }
        }

        // Load data from JSON files
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes] = $data;

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

        return view($this->page . 'openTraceStockModel', compact('products'));
    }

    public function loadTraceStockDetail(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $filterProductVariantId = $request->input('filter_product_variant_id');

        // Get Product Details
        $productDetail = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->where('pv.id', $filterProductVariantId)
            ->select('p.id as product_id', 'p.name as product_name', 's.name as size_name')
            ->first();

        if (!$productDetail) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }

        // Summary Query: Get Stock Details
        $stockSummary = DB::table('faras as f')
            ->selectRaw('COALESCE(SUM(CASE WHEN f.status = 2 THEN f.qty END), 0) as purchaseQty')
            ->selectRaw('COALESCE(SUM(CASE WHEN f.status = 3 AND f.to_company_id = ? AND f.to_company_location_id = ? THEN f.qty END), 0) as transferReceiveQty', [$companyId, $companyLocationId])
            ->selectRaw('COALESCE(SUM(CASE WHEN f.status = 3 AND f.company_id = ? AND f.company_location_id = ? THEN f.qty END), 0) as transferQty', [$companyId, $companyLocationId])
            ->selectRaw('COALESCE(SUM(CASE WHEN f.status = 1 THEN f.qty END), 0) as saleQty')
            ->where('f.product_id', $productDetail->product_id)
            ->where('f.product_variant_id', $filterProductVariantId)
            ->first();

        Log::info(json_encode($stockSummary));

        // Calculate Current Balance
        $currentBalance = ($stockSummary->purchaseQty + $stockSummary->transferReceiveQty) - ($stockSummary->transferQty + $stockSummary->saleQty);

        // Build Response Data
        $data = '<div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Product Variant</th>
                            <th class="text-center">Purchase Qty</th>
                            <th class="text-center">Transfer Receive Qty</th>
                            <th class="text-center">Transfer Qty</th>
                            <th class="text-center">Sale Qty</th>
                            <th class="text-center">Current Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>' . $productDetail->product_name . '</td>
                            <td>' . $productDetail->size_name . '</td>
                            <td class="text-center">' . $stockSummary->purchaseQty . '</td>
                            <td class="text-center">' . $stockSummary->transferReceiveQty . '</td>
                            <td class="text-center">' . $stockSummary->transferQty . '</td>
                            <td class="text-center">' . $stockSummary->saleQty . '</td>
                            <td class="text-center">' . $currentBalance . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>';
        return $data;
    }
}
