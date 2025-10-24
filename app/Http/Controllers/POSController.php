<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Fara;
use App\Models\JournalVoucher;
use App\Models\Receipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class POSController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'pos.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterProducts(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'categories' => 'array', // Ensure categories is an array
            'categories.*' => 'integer' // Ensure each category ID is an integer
        ]);

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

        // Apply category filter if provided
        if (!empty($request->categories)) {
            $products = array_filter($products, function ($product) use ($request) {
                return in_array($product['category_id'], $request->categories);
            });
        }

        // Apply status filter (only active products)
        $products = array_filter($products, fn($product) => $product['status'] == 1);

        // Return a partial view to update the product list dynamically
        return view($this->page . 'filter-product-list', compact('products'))->render();
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
            $cartOrders = DB::table('carts as c')
                ->select(
                    'c.*',
                    'cus.name as customer_name'
                )
                ->join('customers as cus', 'c.customer_id', '=', 'cus.id')
                ->where('c.company_id', $companyId)
                ->where('c.company_location_id', $companyLocationId)
                ->whereBetween('c.order_date', [$fromDate, $toDate]);

            if ($status) {
                $cartOrders->where('c.status', $status);
            }

            $cartOrders = $cartOrders->get();

            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('cartOrders'));
            }

            // Return JSON response for API requests
            return jsonResponse($cartOrders, 'Cart Orders Retrieved Successfully', 'success', 200);
        }

        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function create()
    {
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
        $customerList = DB::table('customers')->where('company_id', $companyId)->get();
        $categoryList = DB::table('categories as c')
            ->join('products as p', 'c.id', '=', 'p.category_id')
            ->select('c.id', 'c.name', 'c.icon_image', DB::raw('COUNT(p.id) AS product_count'))
            ->groupBy('c.id', 'c.name', 'c.icon_image')
            ->havingRaw('COUNT(p.id) > 0')
            ->get();

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
        return view($this->page . 'create', compact('products', 'customerList', 'categoryList'));
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $username = Auth::user()->name;
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        // Validate the input
        $data = $request->validate([
            'orderDate' => 'required',
            'customerId' => 'required|exists:customers,id',
            'paymentType' => 'required|in:1,2',
            'paymentAccount' => 'required',
            'totalAmount' => 'required|numeric',
            'paymentAmount' => 'required|numeric',
            'changeAmount' => 'required|numeric',
            'cart' => 'required|array',
            'cart.*.productId' => 'required|exists:products,id',
            'cart.*.variantId' => 'nullable|exists:product_variants,id',
            'cart.*.name' => 'required|string',
            'cart.*.price' => 'required|numeric',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.discount' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $customerDetail = DB::table('customers')->where('id', $request->input('customerId'))->first();
            $customerAccId = $customerDetail->acc_id;

            $paymentAccountId = $request->input('paymentAccount');

            // Create Cart instance
            $cart = new Cart();
            $orderNo = Cart::VoucherNo();
            $cart->order_no = $orderNo;
            $cart->order_date = $request->input('orderDate');
            $cart->customer_id = $request->input('customerId');
            $cart->total_amount = $request->input('totalAmount');
            $cart->payment_amount = $request->input('paymentAmount');
            $cart->payment_type = $request->input('paymentType');
            $cart->change_amount = $request->input('changeAmount');
            $cart->save(); // Save Cart

            // Insert CartItems
            foreach ($request->input('cart') as $cartItemData) {

                $cartItem = new CartItem();
                $cartItem->cart_id = $cart->id;
                $cartItem->product_id = $cartItemData['productId'];
                $cartItem->variant_id = $cartItemData['variantId'] ?? null;
                $cartItem->name = $cartItemData['name'];
                $cartItem->price = $cartItemData['price'];
                $cartItem->qty = $cartItemData['qty'];
                $cartItem->discount = $cartItemData['discount'] ?? 0;
                $cartItem->save(); // Save each CartItem

                $fara = new Fara();
                $fara->process_type = 1;
                $fara->customer_id = $request->input('customerId');
                $fara->status = 1;
                $fara->main_table_id = $cart->id;
                $fara->main_table_data_id = $cartItem->id;
                $fara->order_no = $orderNo;
                $fara->order_date = $request->input('orderDate');
                $fara->product_id = $cartItemData['productId'];
                $fara->product_variant_id = $cartItemData['variantId'] ?? null;
                $fara->qty = $cartItemData['qty'];
                $fara->rate = $cartItemData['price'];
                $fara->amount = $cartItemData['qty'] * $cartItemData['price'];
                $fara->remarks = '-';

                $fara->save();
            }

            // Create Journal Voucher
            $journalVoucher = new JournalVoucher();
            $journalVoucher->company_id = $companyId;
            $journalVoucher->company_location_id = $companyLocationId;
            $journalVoucher->jv_date = $request->input('orderDate');
            $journalVoucher->jv_no = JournalVoucher::VoucherNo();
            $journalVoucher->slip_no = $orderNo;
            $journalVoucher->voucher_type = 3;
            $journalVoucher->description = $orderNo . ' - ' . $request->input('orderDate');
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

            DB::table('journal_voucher_data')->insert([
                'journal_voucher_id' => $journalVoucherId,
                'acc_id' => $customerAccId,
                'description' => $orderNo . ' - ' . $request->input('orderDate'),
                'debit_credit' => 1, //Debit
                'amount' => $request->input('totalAmount'),
                'jv_status' => 2,
                'time' => $currentTime,
                'date' => $currentDate,
                'status' => 1,
                'username' => $username,
                'approve_username' => $username,
                'delete_username' => '-'
            ]);

            $creditProductCategoryAccountDetail = DB::select('SELECT 
                c.acc_id, 
                SUM(ci.price * ci.qty) AS total_amount
            FROM cart_items AS ci
            INNER JOIN products AS p ON ci.product_id = p.id
            INNER JOIN categories AS c ON p.category_id = c.id
            WHERE ci.cart_id = ?
            GROUP BY c.acc_id', [$cart->id]);
            foreach ($creditProductCategoryAccountDetail as $cpcadRow) {
                DB::table('journal_voucher_data')->insert([
                    'journal_voucher_id' => $journalVoucherId,
                    'acc_id' => $cpcadRow->acc_id,
                    'description' => $orderNo . ' - ' . $request->input('orderDate'),
                    'debit_credit' => 2, //Credit
                    'amount' => $cpcadRow->total_amount,
                    'jv_status' => 2,
                    'time' => $currentTime,
                    'date' => $currentDate,
                    'status' => 1,
                    'username' => $username,
                    'approve_username' => $username,
                    'delete_username' => '-'
                ]);
            }

            $journalVoucherDataDetails = DB::table('journal_voucher_data')->where('journal_voucher_id', $journalVoucherId)->get();
            $transactions = [];
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
                    'voucher_type' => 4,
                    'v_date' => $request->input('orderDate') ?? $currentDate,
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



            //Receipt Voucher
            $rvNo = Receipt::VoucherNo(1);
            $data1['company_id'] = $companyId;
            $data1['company_location_id'] = $companyLocationId;
            $data1['rv_date'] = $request->input('orderDate') ?? $currentDate;
            $data1['date'] = $currentDate;
            $data1['time'] = $currentTime;
            $data1['rv_no'] = $rvNo;
            $data1['slip_no'] = $orderNo;
            $data1['voucher_type'] = 1;
            $data1['rv_type'] = 2;
            $data1['receipt_to'] = $username;
            $data1['description'] = $orderNo . ' - ' . $request->input('orderDate');
            $data1['username'] = $username;
            $data1['approve_username'] = $username;
            $data1['rv_status'] = 2;
            $receiptId = DB::table('receipts')->insertGetId($data1);

            $data2['receipt_id'] = $receiptId;
            $data2['acc_id'] = $paymentAccountId;
            $data2['description'] = $orderNo . ' - ' . $request->input('orderDate');
            $data2['debit_credit'] = 1;  //Debit
            $data2['amount'] = $request->input('totalAmount');
            $data2['time'] = $currentTime;
            $data2['date'] = $currentDate;
            $data2['username'] = $username;
            $data2['rv_status'] = 2;
            $data2['approve_username'] = $username;

            DB::table('receipt_data')->insert($data2);

            $data3['receipt_id'] = $receiptId;
            $data3['acc_id'] = $customerAccId;
            $data3['description'] = $orderNo . ' - ' . $request->input('orderDate');
            $data3['debit_credit'] = 2; //Credit
            $data3['amount'] = $request->input('totalAmount');
            $data3['time'] = $currentTime;
            $data3['date'] = $currentDate;
            $data3['username'] = $username;
            $data3['rv_status'] = 2;
            $data3['approve_username'] = $username;

            DB::table('receipt_data')->insert($data3);

            $receiptDataDetails = DB::table('receipt_data')->where('receipt_id', $receiptId)->get();
            $rvTransactions = [];
            foreach ($receiptDataDetails as $rddRow) {
                $rvTransactions[] = [
                    'company_id' => $companyId,
                    'company_location_id' => $companyLocationId,
                    'acc_id' => $rddRow->acc_id,
                    'particulars' => $rddRow->description,
                    'opening_bal' => 2,
                    'debit_credit' => $rddRow->debit_credit,
                    'amount' => $rddRow->amount,
                    'voucher_id' => $receiptId,
                    'record_data_id' => $rddRow->id,
                    'voucher_type' => 3,
                    'v_date' => $request->input('orderDate') ?? $currentDate,
                    'date' => $currentDate,
                    'time' => $currentTime,
                    'username' => $username,
                    'status' => 1
                ];
            }

            // Insert all transactions at once
            if (!empty($rvTransactions)) {
                DB::table('transaction')->insert($rvTransactions);
            }


            //die;

            DB::commit(); // Commit Transaction

            return response()->json([
                'success' => true,
                'message' => 'Cart and Cart Items created successfully.',
                'cart' => $cart
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction

            Log::error('Cart creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function loadAccountsDependPaymentType(Request $request)
    {
        if ($request->paymentType == 1) {
            $tableName = 'cash_accounts';
        } else if ($request->paymentType == 2) {
            $tableName = 'bank_accounts';
        }

        $accountList = DB::table($tableName)->where('status', 1)->get();
        $data = '<label>Payment Account</label><select class="form-control" name="paymentAccount" id="paymentAccount" >';
        $data .= '<option value="">Select Account</option>';
        foreach ($accountList as $alRow) {
            $data .= '<option value="' . $alRow->acc_id . '">' . $alRow->name . '</option>';
        }
        $data .= '</select>';
        return $data;
    }
    public function getTodaySales()
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $today = now()->format('Y-m-d');

        $totalSales = Cart::where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->whereDate('order_date', $today)
            ->sum('total_amount');

        $orderCount = Cart::where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->whereDate('order_date', $today)
            ->count();

        return response()->json([
            'success' => true,
            'total_sales' => $totalSales,
            'order_count' => $orderCount,
            'period' => 'Today'
        ]);
    }

    public function getLastMonthSales()
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        $startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');

        $totalSales = Cart::where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_amount');

        $orderCount = Cart::where('company_id', $companyId)
            ->where('company_location_id', $companyLocationId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->count();

        return response()->json([
            'success' => true,
            'total_sales' => $totalSales,
            'order_count' => $orderCount,
            'period' => 'Last Month'
        ]);
    }

    public function show(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');

        try {
            // Retrieve main order details with payment_type included
            $order = DB::table('carts as c')
                ->join('customers as cus', 'c.customer_id', '=', 'cus.id')
                ->where('c.id', $id)
                ->where('c.company_id', $companyId)
                ->where('c.company_location_id', $companyLocationId)
                ->select(
                    'c.*',
                    'cus.name as customer_name',
                    'cus.mobile_no as customer_phone',
                    'cus.physical_address as customer_address',
                )
                ->first();

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            // Retrieve order items with product and variant names
            $cartItems = DB::table('cart_items as ci')
                ->join('products as p', 'ci.product_id', '=', 'p.id')
                ->leftJoin('product_variants as pv', 'ci.variant_id', '=', 'pv.id')
                ->leftJoin('sizes as s', 'pv.size_id', '=', 's.id') // join with sizes
                ->where('ci.cart_id', $id)
                ->select(
                    'ci.*',
                    'p.name as product_name',
                    's.name as size_name'
                )
                ->get();

            // Retrieve related financial data
            $journalVoucher = DB::table('journal_vouchers')
                ->where('slip_no', $order->order_no)
                ->first();

            $receipt = DB::table('receipts')
                ->where('slip_no', $order->order_no)
                ->first();

            // Prepare response data
            $orderDetails = [
                'order' => $order,
                'items' => $cartItems,
                'journal_voucher' => $journalVoucher,
                'receipt' => $receipt,
            ];

            // Return appropriate response format
            if ($request->ajax() || $this->isApi) {
                Log::info('Order Retrieved:');
                Log::info(json_encode($orderDetails));

                return $this->isApi
                    ? jsonResponse($orderDetails, 'Order Retrieved Successfully', 'success', 200)
                    : webResponse($this->page, 'showAjax', compact('orderDetails'));
            }

            return view($this->page . 'show', compact('orderDetails'));

        } catch (\Exception $e) {
            Log::error('Order retrieval error: ' . $e->getMessage());

            if ($this->isApi) {
                return jsonResponse([], $e->getMessage(), 'error', 404);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
