<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Auth;

class ProductController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'products.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Request $request)
    {
        // Check if the request is AJAX or API
        if ($request->ajax() || $this->isApi) {
            // Retrieve status filter from the request
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

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
            if ($status) {
                $products = array_filter($products, fn($product) => $product['status'] == $status);
            }

            $products = array_filter($products, function ($product) use ($companyId, $companyLocationId) {
                return $product['company_id'] == $companyId;
            });

            // Handle AJAX or API response
            if ($this->isApi) {
                return jsonResponse($products, 'Products Retrieved Successfully', 'success', 200);
            }

            // Handle web view response
            return view($this->page . 'indexAjax', compact('products'));
        }

        // Handle non-AJAX and non-API requests
        return view($this->page . 'index');
    }

    public function create()
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        $chartOfAccountList = DB::table('chart_of_accounts')
            ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))
            ->where('status', 1)->get();
        $categories = DB::table('categories as c')
            ->leftJoin('categories as children', 'c.id', '=', 'children.parent_id')
            ->whereNull('children.id')
            ->where('c.company_id', Session::get('company_id'))
            ->where('c.company_location_id', Session::get('company_location_id'))
            ->select('c.*')
            ->get();
        $brands = Brand::where('status', 1)->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->get();
        $sizes = Size::where('status', 1)->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->get();

        return view($this->page . 'create', compact('categories', 'brands', 'sizes','chartOfAccountList'));
    }


public function store(Request $request)
{
            $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'order_number' => 'nullable|integer',
            'product' => 'nullable|image|max:2048',
            'icon' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
            'variant_size_id.*' => 'nullable|exists:sizes,id',
            'variant_amount.*' => 'nullable|numeric|min:1',
            'variant_image.*' => 'nullable|image|max:2048',
            'variant_barcode.*' => 'nullable|string|max:255',
        ]);
        try { 
            $getCategoryDetail = Category::select('categories.*', 'chart_of_accounts.code')
                ->join('chart_of_accounts', 'categories.acc_id', '=', 'chart_of_accounts.id')
                ->where('categories.id', $request->category_id)
                ->first();
            $accId = $getCategoryDetail->code;
            $code = ChartOfAccount::GenerateAccountCode($accId);
            $levelArray = explode('-', $code);
            $accountData = [
                'company_id' => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
                'code' => $code,
                'coa_type' => 2,
                'name' => $request->name,
                'parent_code' => $accId,
                'username' => Auth::user()->name,
                'date' => date("Y-m-d"),
                'time' => date("H:i:s"),
            ];

            foreach ($levelArray as $index => $level) {
                $accountData['level' . ($index + 1)] = $level;
            }

            // Insert new chart of account and get the ID
            $chartOfAccountId = DB::table('chart_of_accounts')->insertGetId($accountData);
            generate_json('chart_of_accounts');

            // Manually creating a product
            $product = new Product();
            $product->name = $request->name;
            $product->acc_id = $chartOfAccountId;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->description  =$request->description;
            if ($request->filled('order_number')) {
                $product->order_number = $request->order_number;
            } else {
                $lastOrder = Product::where('acc_id', $accId)->max('order_number');
                $product->order_number = $lastOrder ? $lastOrder + 1 : 1;
            }
            // Ensure the storage directory exists
            $productImagePath = 'product_images';
            Storage::disk('public')->makeDirectory($productImagePath);

            $product->product_image = 'storage/app/public/' .$this->handleFileUpload($request, 'product', $productImagePath) ?: '';
            $product->icon_image = 'storage/app/public/' .$this->handleFileUpload($request, 'icon', $productImagePath) ?: '';
            $product->cover_image = 'storage/app/public/' .$this->handleFileUpload($request, 'cover_image', $productImagePath) ?: '';
            $product->save();
           
        // Handling variants manually
        if($request->has_variants == 1){
            foreach ($request->variant_size_id as $index => $sizeId) {
                if ($sizeId) {
                    $variant = new ProductVariant();
                    $variant->product_id = $product->id;
                    $variant->size_id = $sizeId;
                    $variant->amount = $request->variant_amount[$index];
                    $variant->variant_barcode = $request->variant_barcode[$index];

                    if ($request->hasFile("variant_image.$index")) {
                        $variant->variant_image = 'storage/app/public/' . $this->handleFileUpload($request, "variant_image.$index", $productImagePath);
                    }
                    $variant->save();
                }
            }
        }else{
                $variant = new ProductVariant();
                $variant->product_id = $product->id;
                $variant->amount = $request->sell_price;
                $variant->variant_barcode = $request->barcode;
                $variant->variant_image = 'storage/app/public/' .$this->handleFileUpload($request, 'product', $productImagePath) ?: '';
                $variant->size_id  =$request->size_id;
                $variant->save();
        }
            generate_json('products');
            generate_json('product_variants');
            return redirect()->route($this->page . 'index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'error' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }

    /**
     * Handle file upload and return file path.
     */
    private function handleFileUpload(Request $request, $fileInputName, $destinationPath)
    {
        if (!$request->hasFile($fileInputName)) {
            Log::error("File '$fileInputName' not found in request.");
            return false;
        }

        $file = $request->file($fileInputName);

        if (!$file->isValid()) {
            Log::error("Invalid file upload: " . $fileInputName);
            return false;
        }

        $storedPath = $file->store($destinationPath, 'public');

        if (!$storedPath) {
            Log::error("File '$fileInputName' failed to store.");
            return false;
        }

        return $storedPath;
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

        // Fetch the product by ID
        $product = DB::table('products')
            ->join('chart_of_accounts as coa', 'products.acc_id', '=', 'coa.id')
            ->where('products.id', $id)
            ->select('products.*', 'coa.code','coa.parent_code','coa.id as acc_id') // yahan columns select karo
            ->first();

        if (!$product) {
            return redirect()->route($this->page . 'index')->with('error', 'Product not found.');
        }

        // Fetch variants for the product
        $variants = DB::table('product_variants')
            ->where('product_id', $id)
            ->get();
        
        $chartOfAccountList = DB::table('chart_of_accounts')
            ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))
            ->where('status', 1)->get();

        // Fetch categories, brands, and sizes
        $categories = DB::table('categories as c')
            ->leftJoin('categories as children', 'c.id', '=', 'children.parent_id')
            ->whereNull('children.id')
            ->where('c.company_id', Session::get('company_id'))
            ->where('c.company_location_id', Session::get('company_location_id'))
            ->select('c.*')
            ->get();
        $brands = DB::table('brands')->where('status', 1)->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->get();
        $sizes = DB::table('sizes')->where('status', 1)->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->get();

        return view($this->page . 'edit', compact('product', 'variants', 'categories', 'brands', 'sizes','chartOfAccountList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acc_id' => 'required|exists:chart_of_accounts,code',
            'category_id' => 'required|exists:categories,id',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'order_number' => 'required|integer',
            'product' => 'nullable|image|max:2048',
            'icon' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
            'variant_size_id.*' => 'nullable|exists:sizes,id',
            'variant_amount.*' => 'nullable|numeric',
            'variant_image.*' => 'nullable|image|max:2048',
            'variant_barcode.*' => 'nullable|string|max:255',
        ]);

        $oldParentCode = $request->old_parent_code;
        $newAccId = $request->acc_id;
        $oldAccId = $request->old_acc_id;
        try {
            // Fetch product
            $product = DB::table('products')->where('id', $id)->first();
            if (!$product) {
                return redirect()->route($this->page . 'index')->with('error', 'Product not found.');
            }

            if ($oldParentCode == $newAccId) {
                $accountData = [
                    'company_id'          => Session::get('company_id'),
                    'company_location_id' => Session::get('company_location_id'),
                    'name'                => $request->name,
                    'username'            => Auth::user()->name,
                    'date'                => date("Y-m-d"),
                    'time'                => date("H:i:s"),
                ];
                // Update existing chart_of_accounts
                DB::table('chart_of_accounts')
                    ->where('id', $oldAccId) // ya phir jahan pe condition match ho
                    ->update($accountData);
                generate_json('chart_of_accounts');
            }else{
                $accId = $request->acc_id;
                $code = ChartOfAccount::GenerateAccountCode($accId);
                $levelArray = explode('-', $code);
                $accountData = [
                    'company_id'          => Session::get('company_id'),
                    'company_location_id' => Session::get('company_location_id'),
                    'code'                => $code,
                    'coa_type'            => 2,
                    'name'                => $request->name,
                    'parent_code'         => $accId,
                    'username'            => Auth::user()->name,
                    'date'                => date("Y-m-d"),
                    'time'                => date("H:i:s"),
                ];

                foreach ($levelArray as $index => $level) {
                    $accountData['level' . ($index + 1)] = $level;
                }

                // Update existing chart_of_accounts
                DB::table('chart_of_accounts')
                    ->where('id', $oldAccId) // ya phir jahan pe condition match ho
                    ->update($accountData);
                generate_json('chart_of_accounts');
            }

            // Update product details
            $productData = [
                'name' => $request->name,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'order_number' => $request->order_number,
            ];

            // Ensure the product images directory exists
            $productImageDir = 'product_images';
            Storage::disk('public')->makeDirectory($productImageDir);

            // Handle product image upload
            if ($request->hasFile('product')) {
                $imagePath = $this->handleFileUpload($request, 'product', $productImageDir);
                if ($imagePath) {
                    $productData['product_image'] = 'storage/app/public/' . $imagePath;
                }
            }

            // Handle icon image upload
            if ($request->hasFile('icon')) {
                $imagePath = $this->handleFileUpload($request, 'icon', $productImageDir);
                if ($imagePath) {
                    $productData['icon_image'] = 'storage/app/public/' . $imagePath;
                }
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $imagePath = $this->handleFileUpload($request, 'cover_image', $productImageDir);
                if ($imagePath) {
                    $productData['cover_image'] = 'storage/app/public/' . $imagePath;
                }
            }

            DB::table('products')->where('id', $id)->update($productData);

            // Fetch existing variants
            $existingVariants = DB::table('product_variants')
                ->where('product_id', $id)
                ->get()
                ->keyBy('size_id');

            $updatedSizeIds = $request->variant_size_id ?? [];
            $deletedVariantIds = [];

            // Handle variants
            if ($updatedSizeIds) {
                foreach ($updatedSizeIds as $index => $sizeId) {
                    if ($sizeId) {
                        $variantData = [
                            'product_id' => $id,
                            'size_id' => $sizeId,
                            'amount' => $request->variant_amount[$index],
                            'variant_barcode' => $request->variant_barcode[$index],
                            'created_by' => auth()->id(),
                            'created_date' => now(),
                        ];

                        // Handle image upload
                        if ($request->hasFile("variant_image.$index")) {
                            $imagePath = $this->handleFileUpload($request, "variant_image.$index", 'product_images');
                            if ($imagePath) {
                                $variantData['variant_image'] = 'storage/app/public/' . $imagePath;
                            }
                        }
                        // Update or insert variant
                        $existingVariant = $existingVariants[$sizeId] ?? null;
                        if ($existingVariant) {
                            DB::table('product_variants')
                                ->where('id', $existingVariant->id)
                                ->update($variantData);
                        } else {
                            DB::table('product_variants')->insert($variantData);
                        }
                    }
                }
            }

            // Get IDs of variants that should be deleted
            $deletedVariantIds = DB::table('product_variants')
                ->where('product_id', $id)
                ->whereNotIn('size_id', $updatedSizeIds)
                ->pluck('id')
                ->toArray();

            // Delete related cart items first before deleting variants
            DB::table('cart_items')->whereIn('variant_id', $deletedVariantIds)->delete();

            // Delete variants from database
            DB::table('product_variants')->whereIn('id', $deletedVariantIds)->delete();

            // Update JSON files
            generate_json('products');
            generate_json('product_variants', $deletedVariantIds);

            return redirect()->route($this->page . 'index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors([
                'error' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }


    public function status($id)
    {
        try {
            $product = Product::find($id);
            $product->status = 1;
            $product->save();
            generate_json('products');
            return response()->json(['success' => 'Product marked as inactive successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the Product.'], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->status = 2;
            $product->save();
            generate_json('products');
            return response()->json(['success' => 'Product marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the Product.'], 500);
        }
    }
}
