<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Session;

class SalesReportController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Define file paths for JSON files
    //     $jsonFiles = [
    //         'products' => storage_path('app/json_files/products.json'),
    //         'product_variants' => storage_path('app/json_files/product_variants.json'),
    //         'categories' => storage_path('app/json_files/categories.json'),
    //         'brands' => storage_path('app/json_files/brands.json'),
    //         'sizes' => storage_path('app/json_files/sizes.json'),
    //     ];

    //     // Ensure all necessary JSON files exist
    //     foreach ($jsonFiles as $key => $filePath) {
    //         if (!file_exists($filePath)) {
    //             generate_json($key); // Generate the missing JSON file
    //         }
    //     }

    //     // Load data from JSON files
    //     $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
    //     ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes] = $data;

    //     // Optimize the relationship building by indexing categories, brands, and sizes by their IDs
    //     $categoryMap = array_column($categories, 'name', 'id');
    //     $brandMap = array_column($brands, 'name', 'id');
    //     $sizeMap = array_column($sizes, 'name', 'id');

    //     // Attach related data (variants, category names, brand names, and size names) to products
    //     $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
    //         // Attach variants to each product
    //         $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);

    //         // Assign category, brand, and size names
    //         $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
    //         $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';

    //         // For each variant, assign the size name
    //         foreach ($product['variants'] as &$variant) {
    //             $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
    //         }

    //         return $product;
    //     }, $products);

    //     // Apply status filter if provided
    //     $products = array_filter($products, fn($product) => $product['status'] == 1);

    //     // Get the filter parameters
    //     $companyId = $request->input('company_id') ?? Session::get('company_id');
    //     $companyLocationId = $request->input('company_location_id') ?? Session::get('company_location_id');
    //     $fromDate = $request->input('from_date') ?? date('Y-m-d', strtotime('-30 days'));
    //     $toDate = $request->input('to_date') ?? date('Y-m-d');
    //     $productVariantId = $request->input('filter_product_variant_id');

    //     // Query the Faras, carts, and cart_items tables for sales-related transactions (status 1 = Sales only)
    //     $salesData = DB::select("
    //         SELECT 
    //             p.name AS product_name, 
    //             s.name AS size_name, 
    //             f.qty AS sale_qty,
    //             f.order_date,
    //             f.customer_id,
    //             cus.name AS customer_name,
    //             jv.jv_no AS voucher_name, 
    //             jvd.acc_id AS account_id,
    //             coa.name AS account_name
    //         FROM faras AS f
    //         INNER JOIN products AS p ON f.product_id = p.id
    //         INNER JOIN product_variants AS pv ON f.product_variant_id = pv.id
    //         INNER JOIN sizes AS s ON pv.size_id = s.id
    //         LEFT JOIN customers AS cus ON f.customer_id = cus.id
    //         LEFT JOIN carts AS c ON f.main_table_id = c.id
    //         LEFT JOIN cart_items AS ci ON c.id = ci.cart_id
    //         LEFT JOIN journal_vouchers AS jv ON c.order_no = jv.slip_no  -- Corrected join condition
    //         LEFT JOIN journal_voucher_data AS jvd ON jv.id = jvd.journal_voucher_id
    //         LEFT JOIN chart_of_accounts AS coa ON jvd.acc_id = coa.id
    //         WHERE f.company_id = ? 
    //           AND f.company_location_id = ?
    //           AND f.status = 1  -- Only Sales transactions
    //           AND f.order_date BETWEEN ? AND ?
    //           " . ($productVariantId ? "AND f.product_variant_id = ?" : ""),
    //         [
    //             $companyId,
    //             $companyLocationId,
    //             $fromDate,
    //             $toDate,
    //             $productVariantId
    //         ]
    //     );

    //     // If the request is AJAX, return the data in JSON format for the view
    //     if ($request->ajax()) {
    //         return view('reports.sales-report.indexAjax', compact('salesData'));
    //     }

    //     // If it's not an AJAX request, return the standard view with product details
    //     return view('reports.sales-report.index', compact('salesData', 'fromDate', 'toDate', 'companyId', 'companyLocationId', 'products'));
    // }

    public function index(Request $request)
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

        // Set company and location from session
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Default date range (last 30 days) if not provided
        $fromDate = $request->input('from_date') ?? date('Y-m-d', strtotime('-30 days'));
        $toDate = $request->input('to_date') ?? date('Y-m-d');

        // Optional filter by product_variant_id if provided
        $productVariantId = $request->input('filter_product_variant_id');

        // Build the SQL query to get sales details.
        // Note: f.status = 1 means Sales.
        // We are not selecting f.voucher_name because that column does not exist.
        // Instead, we include the order_no field which holds the voucher number.
        $query = "
            SELECT 
                f.rate as unit_price,
                f.amount as amount,
                p.name AS product_name,
                s.name AS size_name,
                f.qty AS sale_qty,
                f.order_date,
                f.customer_id,
                cus.name AS customer_name,
                f.order_no AS order_no,
                jvd.acc_id AS account_id,
                jv.jv_no as jv_no,
                coa.name AS account_name
            FROM faras AS f
            INNER JOIN products AS p ON f.product_id = p.id
            INNER JOIN product_variants AS pv ON f.product_variant_id = pv.id
            INNER JOIN sizes AS s ON pv.size_id = s.id
            LEFT JOIN customers AS cus ON f.customer_id = cus.id
            LEFT JOIN carts AS c ON f.main_table_id = c.id
            LEFT JOIN journal_vouchers AS jv ON c.order_no = jv.slip_no
            LEFT JOIN journal_voucher_data AS jvd ON jv.id = jvd.journal_voucher_id
            LEFT JOIN chart_of_accounts AS coa ON jvd.acc_id = coa.id
            WHERE f.company_id = ?
              AND f.company_location_id = ?
              AND f.status = 1
              AND f.order_date BETWEEN ? AND ?
        ";

        $bindings = [$companyId, $companyLocationId, $fromDate, $toDate];

        if ($productVariantId) {
            $query .= " AND f.product_variant_id = ?";
            $bindings[] = $productVariantId;
        }

        $salesData = DB::select($query, $bindings);

        Log::info(json_encode($salesData));

        // If AJAX request, return a partial view with the data, otherwise load the full report view
        if ($request->ajax()) {
            return view('reports.sales-report.indexAjax', compact('salesData'));
        }

        return view('reports.sales-report.index', compact('salesData', 'fromDate', 'toDate', 'companyId', 'companyLocationId', 'products'));
    }
}
