<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Google\Service\CloudResourceManager\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DirectSaleInvoice;
use App\Models\DirectSaleInvoiceData;
use App\Models\JournalVoucher;
use App\Models\Fara;
use Exception;
use Illuminate\Support\Facades\Session;
use Auth;
use App\Helpers\CommonHelper;
class DirectSaleInvoiceController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'direct-sale-invoices.';
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
        // Define file paths for JSON files
        $jsonFiles = [
            'products' => storage_path('app/json_files/products.json'),
            'product_variants' => storage_path('app/json_files/product_variants.json'),
            'categories' => storage_path('app/json_files/categories.json'),
            'brands' => storage_path('app/json_files/brands.json'),
            'sizes' => storage_path('app/json_files/sizes.json'),
            'payment_types' => storage_path('app/json_files/payment_types.json'),
            'customers' => storage_path('app/json_files/customers.json'),
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
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes, 'payment_types' => $payment_types, 'customers' => $customers, 'departments' => $departments, 'tax_accounts' => $tax_accounts] = $data;

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
        $products = array_filter($products, function ($product) use ($companyId) {
            return $product['status'] == 1 && $product['company_id'] == $companyId;
        });

        // Optional: Filter customers and departments by company ID
        $customers = array_filter($customers, fn($customer) => $customer['company_id'] == $companyId);
        $departments = array_filter($departments, fn($dept) => $dept['company_id'] == $companyId);
        $tax_accounts = array_filter($tax_accounts, fn($ta) => $ta['company_id'] == $companyId);
        $allChartOfAccounts = CommonHelper::get_all_chart_of_account(1);

        return view($this->page . 'create', compact('products', 'payment_types', 'customers', 'departments','tax_accounts','allChartOfAccounts'));
    }

    public function productWiseAverageRateAndCurrentBalance(Request $request)
    {
        $productVariantId = $request->input('product_variant_id');
        $fromDate  = $request->input('from_date', now()->startOfMonth());
        $toDate    = $request->input('to_date', now()->endOfMonth());

        $result = DB::table('faras as f')
            ->select(
                'f.product_id',
                DB::raw('ROUND(SUM(CASE WHEN f.status = 2 THEN f.amount END) / NULLIF(SUM(CASE WHEN f.status = 2 THEN f.qty END), 0), 2) as avg_purchase_rate')
            )
            ->join('products as p', 'f.product_id', '=', 'p.id')
            ->where('f.product_variant_id', $productVariantId)
            ->whereBetween('f.created_date', [$fromDate, $toDate])
            ->groupBy('f.product_id')
            ->first();

        if ($result) {
            return response()->json([
                'success' => true,
                'avg_purchase_rate' => $result->avg_purchase_rate
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No purchase records found for this product.'
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tax_account_id'     => 'nullable|exists:chart_of_accounts,id',
            'debit_account_id'   => 'nullable|exists:chart_of_accounts,id',
            'credit_account_id'  => 'nullable|exists:chart_of_accounts,id',
            'si_date'            => 'required|date',
            'main_description'   => 'nullable|string|max:255',
            'paymentType'        => 'required|integer',
            'payment_type_rate'  => 'required|numeric',
            'customer_id'        => 'required|integer|exists:customers,id',
            'si_note'            => 'nullable|string',
            'tax_amount'         => 'nullable|numeric',
            'siDataArray'        => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $validatedData) {

                $companyId         = Session::get('company_id');
                $companyLocationId = Session::get('company_location_id');
                $username          = Auth::user()->name;
                $currentDate       = date('Y-m-d');
                $currentTime       = date('H:i:s');

                /** -----------------------
                 * Create Direct Sale Invoice
                 * ----------------------- */
                $directSaleInvoice = new DirectSaleInvoice();
                $directSaleInvoice->dsi_no             = DirectSaleInvoice::VoucherNo();
                $directSaleInvoice->dsi_date           = $validatedData['si_date'];
                $directSaleInvoice->customer_id        = $validatedData['customer_id'];
                $directSaleInvoice->si_note            = $validatedData['si_note'] ?? null;
                $directSaleInvoice->main_description   = $validatedData['main_description'] ?? null;
                $directSaleInvoice->paymentType        = $validatedData['paymentType'];
                $directSaleInvoice->payment_type_rate  = $validatedData['payment_type_rate'];
                $directSaleInvoice->tax_account_id     = $validatedData['tax_account_id'] ?? null;
                $directSaleInvoice->tax_amount         = $validatedData['tax_amount'] ?? 0;
                $directSaleInvoice->payment_receipt_status = 1;
                $directSaleInvoice->dsi_status         = 1;
                $directSaleInvoice->save();

                /** -----------------------
                 * Create Journal Voucher
                 * ----------------------- */
                $journalVoucher = new JournalVoucher();
                $journalVoucher->company_id        = $companyId;
                $journalVoucher->company_location_id = $companyLocationId;
                $journalVoucher->jv_date           = $directSaleInvoice->dsi_date;
                $journalVoucher->jv_no             = JournalVoucher::VoucherNo();
                $journalVoucher->slip_no           = $directSaleInvoice->dsi_no;
                $journalVoucher->voucher_type      = 3;
                $journalVoucher->description       = $directSaleInvoice->main_description;
                $journalVoucher->username          = $username;
                $journalVoucher->status            = 1;
                $journalVoucher->jv_status         = 1;
                $journalVoucher->date              = $currentDate;
                $journalVoucher->time              = $currentTime;
                $journalVoucher->approve_username  = $username;
                $journalVoucher->approve_date      = $currentDate;
                $journalVoucher->approve_time      = $currentTime;
                $journalVoucher->delete_username   = '-';
                $journalVoucher->save();

                // Link journal voucher with invoice
                DB::table('direct_sale_invoices')
                    ->where('id', $directSaleInvoice->id)
                    ->update(['jv_id' => $journalVoucher->id]);

                /** -----------------------
                 * Get Customer Account
                 * ----------------------- */
                $customerAccount = DB::table('customers')
                    ->where('id', $validatedData['customer_id'])
                    ->select('acc_id')
                    ->first();

                if (!$customerAccount) {
                    throw new \Exception('Customer account not found.');
                }

                /** -----------------------
                 * Process Invoice Data
                 * ----------------------- */
                $overallAvgPurAmount = 0;
                $overallSellAmount   = 0;

                $debitEntries        = [];
                $creditEntries       = [];
                $debitEntriesTwo     = [];
                $creditEntriesTwo    = [];

                foreach ($validatedData['siDataArray'] as $index => $rowId) {
                    $rowIndex = $index + 1;

                    $avgPurAmount    = (float) $request->input("averagePurchaseAmount_$rowIndex", 0);
                    $actualSellAmount = (float) $request->input("subTotal_$rowIndex", 0);

                    $overallAvgPurAmount += $avgPurAmount;
                    $overallSellAmount   += $actualSellAmount;

                    // Save invoice row
                    $directSaleInvoiceData = new DirectSaleInvoiceData();
                    $directSaleInvoiceData->direct_sale_invoice_id = $directSaleInvoice->id;
                    $directSaleInvoiceData->product_variant_id     = $request->input("productId_$rowIndex");
                    $directSaleInvoiceData->qty                   = $request->input("qty_$rowIndex");
                    $directSaleInvoiceData->rate                  = $request->input("unitPrice_$rowIndex");
                    $directSaleInvoiceData->total_amount          = $actualSellAmount;
                    $directSaleInvoiceData->save();

                    // Product account → CREDIT
                    $productAcc = DB::table('products as p')
                        ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
                        ->where('pv.id', $request->input("productId_$rowIndex"))
                        ->select('p.acc_id')
                        ->first();

                    if ($productAcc) {
                        $creditEntries[] = [
                            'journal_voucher_id' => $journalVoucher->id,
                            'acc_id'             => $productAcc->acc_id,
                            'description'        => $directSaleInvoice->main_description,
                            'debit_credit'       => 2,
                            'amount'             => $avgPurAmount,
                            'jv_status'          => 1,
                            'time'               => $currentTime,
                            'date'               => $currentDate,
                            'status'             => 1,
                            'username'           => $username,
                            'approve_username'   => $username,
                            'delete_username'    => '-'
                        ];
                    }
                }

                /** -----------------------
                 * Tax (if any) → CREDIT
                 * ----------------------- */
                $taxAccountId = $validatedData['tax_account_id'] ?? null;
                $taxAmount    = $validatedData['tax_amount'] ?? 0;

                if ($taxAccountId && $taxAmount > 0) {
                    $creditEntries[] = [
                        'journal_voucher_id' => $journalVoucher->id,
                        'acc_id'             => $taxAccountId,
                        'description'        => $directSaleInvoice->main_description,
                        'debit_credit'       => 2,
                        'amount'             => $taxAmount,
                        'jv_status'          => 1,
                        'time'               => $currentTime,
                        'date'               => $currentDate,
                        'status'             => 1,
                        'username'           => $username,
                        'approve_username'   => $username,
                        'delete_username'    => '-'
                    ];
                }

                /** -----------------------
                 * Debit & Credit Entries
                 * ----------------------- */
                $debitEntries[] = [
                    'journal_voucher_id' => $journalVoucher->id,
                    'acc_id'             => $validatedData['debit_account_id'],
                    'description'        => $directSaleInvoice->main_description,
                    'debit_credit'       => 1,
                    'amount'             => $overallAvgPurAmount,
                    'jv_status'          => 1,
                    'time'               => $currentTime,
                    'date'               => $currentDate,
                    'status'             => 1,
                    'username'           => $username,
                    'approve_username'   => $username,
                    'delete_username'    => '-'
                ];

                $debitEntriesTwo[] = [
                    'journal_voucher_id' => $journalVoucher->id,
                    'acc_id'             => $customerAccount->acc_id,
                    'description'        => $directSaleInvoice->main_description,
                    'debit_credit'       => 1,
                    'amount'             => $overallSellAmount,
                    'jv_status'          => 1,
                    'time'               => $currentTime,
                    'date'               => $currentDate,
                    'status'             => 1,
                    'username'           => $username,
                    'approve_username'   => $username,
                    'delete_username'    => '-'
                ];

                $creditEntriesTwo[] = [
                    'journal_voucher_id' => $journalVoucher->id,
                    'acc_id'             => $validatedData['credit_account_id'],
                    'description'        => $directSaleInvoice->main_description,
                    'debit_credit'       => 2,
                    'amount'             => $overallSellAmount,
                    'jv_status'          => 1,
                    'time'               => $currentTime,
                    'date'               => $currentDate,
                    'status'             => 1,
                    'username'           => $username,
                    'approve_username'   => $username,
                    'delete_username'    => '-'
                ];

                /** -----------------------
                 * Insert JV Entries
                 * ----------------------- */
                $jvEntries = array_merge($debitEntries, $creditEntries, $debitEntriesTwo, $creditEntriesTwo);
                if (!empty($jvEntries)) {
                    DB::table('journal_voucher_data')->insert($jvEntries);
                }
            });

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Direct Sale Invoice Created Successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
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

        $directSaleInvoiceId = $request->id;

        // Fetch the direct sale invoice details with the supplier name
        $directSaleInvoice = DB::table('direct_sale_invoices')
            ->join('customers', 'direct_sale_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('tax_accounts as ta','direct_sale_invoices.tax_account_id','=','ta.acc_id')
            ->select(
                'direct_sale_invoices.*',
                'customers.name as customer',
                'ta.name as tax_account_name'
            )
            ->where('direct_sale_invoices.id', $directSaleInvoiceId)
            ->first();

        if (!$directSaleInvoice) {
            return response()->json(['error' => 'Direct Sale Invoice not found'], 404);
        }

        // Attach purchase order data to the main object
        $directSaleInvoice->directSaleInvoiceData = DB::table('direct_sale_invoice_datas as dsid')
            ->join('product_variants as pv', 'dsid.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->select('dsid.*', 's.name as size_name', 'pv.amount as product_variant_amount', 'p.name as product_name')
            ->where('dsid.direct_sale_invoice_id', $directSaleInvoiceId)
            ->get();

        // Fetch related purchase order details for display
        $directSaleInvoiceDatas = $directSaleInvoice->directSaleInvoiceData;

        // Return the view with the purchase order details
        return view($this->page . 'viewDirectSaleInvoiceDetail', compact('directSaleInvoice', 'directSaleInvoiceDatas'));
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
            $directSaleInvoices = DB::table('direct_sale_invoices as dsi')
                ->select(
                    'dsi.id',
                    'dsi.company_id',
                    'dsi.company_location_id',
                    'dsi.dsi_no',
                    'dsi.dsi_date',
                    'dsi.main_description',
                    'dsi.paymentType',
                    'dsi.payment_type_rate',
                    'dsi.customer_id',
                    'dsi.si_note',
                    'dsi.dsi_status',
                    'dsi.status',
                    'dsi.created_date',
                    'dsi.created_by',
                    'c.name as customer_name',
                )
                ->join('customers as c', 'dsi.customer_id', '=', 'c.id')
                ->whereBetween('dsi.dsi_date', [$fromDate, $toDate])
                ->where('dsi.company_id',$companyId)
                ->where('dsi.company_location_id',$companyLocationId);
            if ($status) {
                $directSaleInvoices = $directSaleInvoices->where('dsi.status', $status);
            }

            $directSaleInvoices = $directSaleInvoices->get();

            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('directSaleInvoices'));
            }

            // Return JSON response for API requests
            return jsonResponse($directSaleInvoices, 'Purchase Orders Retrieved Successfully', 'success', 200);
        }

        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function status($id)
    {
        $directSaleInvoice = DirectSaleInvoice::find($id);
        $directSaleInvoice->status = 1;
        $directSaleInvoice->save();
        return response()->json(['success' => 'Direct Sale Invoice Activated Successfully', 'Direct Sale Invoice Activated Successfully']);
    }
    public function destroy($id)
    {
        $directSaleInvoice = DirectSaleInvoice::find($id);
        $directSaleInvoice->status = 2;
        $directSaleInvoice->save();
        return response()->json(['success' => 'Direct Sale Invoice Inactive Successfully', 'Direct Sale Invoice Inactive Successfully']);
    }




    public function approveDirectSaleInvoiceVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $username = Auth::user()->name;
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        // Fetch Direct Sale Invoice details
        $directSaleInvoiceDetail = DB::table('direct_sale_invoices')
            ->where([
                ['id', '=', $id],
                ['company_id', '=', $companyId],
                ['company_location_id', '=', $companyLocationId]
            ])->first();

        if (!$directSaleInvoiceDetail) {
            return response()->json(['message' => 'Direct Sale Invoice not found'], 404);
        }

        

        // Fetch Direct Sale Invoice Items
        $directSaleInvoiceDatas = DB::table('direct_sale_invoice_datas as dsid')
            ->join('product_variants as pv', 'dsid.product_variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->where('dsid.direct_sale_invoice_id', $id)
            ->select([
                'dsid.*',
                'pv.product_id',
                's.name as size_name',
                'pv.amount as product_variant_amount',
                'p.name as product_name',
                'c.acc_id as account_id'
            ])->get();

        $faraData = [];
        $totalCreditAmount = 0;
        $taxAmount = $directSaleInvoiceDetail->tax_amount;
        $taxAccountId = $directSaleInvoiceDetail->tax_account_id;

        DB::table('direct_sale_invoices')->where('id',$id)->update(['dsi_status' => 2]);
        DB::table('direct_sale_invoice_datas')->where('direct_sale_invoice_id',$id)->update(['dsi_status' => 2]);

        foreach ($directSaleInvoiceDatas as $dsidRow) {
            $amount = $dsidRow->qty * $dsidRow->rate;
            $totalCreditAmount += $amount;

            $faraData[] = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'process_type' => 3,
                'status' => 1,
                'customer_id' => $directSaleInvoiceDetail->customer_id,
                'main_table_id' => $id,
                'main_table_data_id' => $dsidRow->id,
                'order_no' => $directSaleInvoiceDetail->dsi_no,
                'order_date' => $directSaleInvoiceDetail->dsi_date,
                'product_id' => $dsidRow->product_id,
                'product_variant_id' => $dsidRow->product_variant_id,
                'qty' => $dsidRow->qty,
                'rate' => $dsidRow->rate,
                'amount' => $amount,
                'remarks' => $directSaleInvoiceDetail->main_description,
                'created_by' => $username,
                'created_date' => $currentDate
            ];
        }

        if ($faraData) {
            DB::table('faras')->insert($faraData);
        }

        // Fetch Customer Account
        $customerAccountDetail = DB::table('customers')
            ->where('id', $directSaleInvoiceDetail->customer_id)
            ->select('acc_id')
            ->first();

        DB::table('journal_vouchers')->where('id',$directSaleInvoiceDetail->jv_id)->update(['jv_status' => 2]);
        DB::table('journal_voucher_data')->where('journal_voucher_id',$directSaleInvoiceDetail->jv_id)->update(['jv_status' => 2]);

        // Fetch JV details for transactions
        $journalVoucherDetails = DB::table('journal_vouchers')->where([
            'id' => $directSaleInvoiceDetail->jv_id,
            'company_id' => $companyId,
            'company_location_id' => $companyLocationId
        ])->first();

        $journalVoucherDataDetails = DB::table('journal_voucher_data')
            ->where('journal_voucher_id', $directSaleInvoiceDetail->jv_id)
            ->orderBy('debit_credit', 'asc') // Debit (1) first
            ->get();

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
                'voucher_id' => $directSaleInvoiceDetail->jv_id,
                'record_data_id' => $jvddRow->id,
                'voucher_type' => 1,
                'v_date' => $journalVoucherDetails->jv_date ?? $currentDate,
                'date' => $currentDate,
                'time' => $currentTime,
                'username' => $username,
                'status' => 1
            ];
        }

        if (!empty($transactions)) {
            DB::table('transaction')->insert($transactions);
        }

        echo 'Done';
    }

    public function directSaleInvoiceVoucherRejectAndRepost(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('direct_sale_invoices')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['dsi_status' => $value]);
        echo 'Done';
    }

    public function directSaleInvoiceVoucherActiveAndInactive(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('direct_sale_invoices')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['status' => $value]);
        echo 'Done';
    }


}
