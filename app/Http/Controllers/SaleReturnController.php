<?php
namespace App\Http\Controllers;

use App\Models\JournalVoucher;
use App\Models\Receipt;
use App\Models\ReturnSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SaleReturnController extends Controller
{
    protected $isApi;
    protected $page;

    public function __construct(Request $request)
    {
        $this->isApi = $request->is('api/*');
        $this->page = 'return-sales.';
    }
    public function index(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $query = DB::table('return_sales as rs')
                ->join('customers as c', 'rs.customer_id', '=', 'c.id')
                ->join('carts as o', 'rs.cart_id', '=', 'o.id')
                ->select(
                    'rs.*',
                    'c.name as customer_name',
                    'o.order_no as order_no'
                )
                ->where('rs.company_id', $companyId)
                ->where('rs.company_location_id', $companyLocationId);

            if ($fromDate && $toDate) {
                $query->whereBetween('rs.return_sale_date', [$fromDate, $toDate]);
            }

            if (!is_null($status)) {
                $query->where('rs.status', $status);
            }

            $returnSales = $query->get();

            if ($request->ajax()) {
                $html = view('return-sales.partials.indexAjax', compact('returnSales'))->render();
                return response()->json(['success' => true, 'html' => $html], 200);
            }

            return jsonResponse($returnSales, 'Return Sales Retrieved Successfully', 'success', 200);
        }

        return view('return-sales.index');
    }
    public function create(Request $request)
    {
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Fetch only completed orders that have items available for return
        $saleReturns = DB::table('carts')
            ->join('customers', 'customers.id', '=', 'carts.customer_id')
            ->select('carts.id as cart_id', 'carts.order_no', 'customers.id as customer_id', 'customers.name as customer_name')
            ->where('carts.company_id', $companyId)
            ->where('carts.company_location_id', $companyLocationId)
            ->where('carts.status', 1) // Completed orders
            ->where('carts.returned', 2) // Not yet returned
            ->get();

        return view("{$this->page}create", compact('saleReturns'));
    }
    public function loadOrderDetails(Request $request)
    {
        $cartId = $request->input('cart_id');

        $order = DB::table('carts')
            ->join('customers', 'customers.id', '=', 'carts.customer_id')
            ->where('carts.id', $cartId)
            ->first();

        if (!$order) {
            return response()->json(['html' => '<p class="text-danger">Order not found.</p>']);
        }

        // Fetch items in the selected order with available quantity for return
        $cartItems = DB::table('cart_items')
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->join('product_variants', 'product_variants.id', '=', 'cart_items.variant_id') // Join product variants
            ->join('sizes', 'sizes.id', '=', 'product_variants.size_id') // Join sizes to get the size name
            ->where('cart_items.cart_id', $cartId)
            ->whereRaw('cart_items.qty > cart_items.returned_qty') // Only items with quantity available for return
            ->select(
                'cart_items.*',
                'products.name as product_name',
                'sizes.name as size_name' // Get the size name
            )
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['html' => '<p class="text-danger">No items available for return.</p>']);
        }

        $html = view('return-sales.partials.order-items', [
            'order' => $order,
            'cartItems' => $cartItems,
        ])->render();

        return response()->json(['html' => $html]);
    }
    public function store(Request $request)
    {
        // Start a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Fetch company and location data from session
            log::info(json_encode($request->all()));
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $createdBy = auth()->user()->name; // or any user-related field, assuming authenticated

            // Get data from the request
            $ordercustomerId = $request->input('order_id_customer_id');
            // Remove spaces and split by "<>"
            $parts = preg_split('/\s*<>\s*/', $ordercustomerId);

            $orderId = $parts[0];
            $customerId = $parts[1];
            $reason = $request->input('reason');
            $returnQtys = $request->input('return_qtys'); // The array of returned quantities
            $includeProduct = $request->input('include_product'); // The checkbox selection

            // Validate the return quantities to make sure they are positive and within bounds
            if (empty($returnQtys) || !is_array($returnQtys)) {
                return redirect()->back()->withErrors('No items selected for return.');
            }

            // Create the return sale entry
            $returnSale = new ReturnSales();
            $returnSale->cart_id = $orderId;
            $returnSale->customer_id = $customerId;
            $returnSale->return_sale_no = ReturnSales::VoucherNo();
            $returnSale->return_sale_date = now();
            $returnSale->reason = $reason;
            $returnSale->return_sale_status = 1;
            $returnSale->save();
            $returnSaleId = $returnSale->id;
          

            // Loop through the selected cart items and save return items
            foreach ($returnQtys as $itemId => $returnQty) {
                if (isset($includeProduct[$itemId]) && $includeProduct[$itemId] == 1 && $returnQty > 0) {
                    // Fetch cart item details
                    $cartItem = DB::table('cart_items')
                        ->where('cart_items.id', $itemId)
                        ->first();
                    log:
                    info(json_encode($cartItem));
                    // Calculate the amount (assuming unit price is available in cart_item)
                    $amount = $returnQty * $cartItem->price;

                    // Insert into return_sale_items table
                    DB::table('return_sale_items')->insert([
                        'company_id' => $companyId,
                        'company_location_id' => $companyLocationId,
                        'return_sale_id' => $returnSaleId,
                        'cart_id' => $orderId,
                        'cart_item_id' => $cartItem->id,
                        'return_qty' => $returnQty,
                        'unit_price' => $cartItem->price,
                        'amount' => $amount,
                        'remarks' => $request->input('remarks', ''),
                        'created_by' => $createdBy,
                        'created_date' => now(),
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect back with success message
            return redirect()->route('sales-return.index')->with('success', 'Sale return created successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error message
            Log::error('Error creating sale return: ' . $e->getMessage());

            // Return with error message
            return redirect()->back()->withErrors('An error occurred while creating the sale return.');
        }
    }
    public function show(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);

        $returnSaleId = $request->input('id');
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Fetch Return Sale header details along with customer and order info
        $returnSaleDetail = DB::table('return_sales as rs')
            ->join('customers as c', 'rs.customer_id', '=', 'c.id')
            ->join('carts as o', 'rs.cart_id', '=', 'o.id')
            ->select(
                'rs.*',
                'c.name as customer_name',
                'o.order_no as original_order_no',
                'o.created_date as original_order_date'
            )
            ->where('rs.id', $returnSaleId)
            ->where('rs.company_id', $companyId)
            ->where('rs.company_location_id', $companyLocationId)
            ->first();

        if (!$returnSaleDetail) {
            return response()->json(['error' => 'Return Sale not found'], 404);
        }

        // Fetch associated Return Sale items with product and variant info
        $returnSaleItems = DB::table('return_sale_items as rsi')
            ->join('cart_items as ci', 'rsi.cart_item_id', '=', 'ci.id')
            ->join('products', 'ci.product_id', '=', 'products.id')
            ->join('product_variants', 'ci.variant_id', '=', 'product_variants.id')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.id')
            ->select(
                'rsi.id',
                'rsi.return_sale_id',
                'rsi.cart_id',
                'rsi.cart_item_id',
                'rsi.return_qty',
                'rsi.unit_price',
                'rsi.amount',
                'rsi.remarks',
                'products.name as product_name',
                'sizes.name as size_name',
                'ci.qty as ordered_qty',
                'ci.returned_qty as previously_returned_qty'
            )
            ->where('rsi.return_sale_id', $returnSaleId)
            ->where('rsi.company_id', $companyId)
            ->where('rsi.company_location_id', $companyLocationId)
            ->get();

        // Attach the item details to the returnSaleDetail object
        $returnSaleDetail->returnItems = $returnSaleItems;
        log::info(json_encode($returnSaleDetail));
        log::info(json_encode($returnSaleItems));

        // Return the view with the fetched details
        return view('return-sales.viewReturnSaleDetail', compact('returnSaleDetail', 'returnSaleItems'));
    }

    public function returnSaleApprove(Request $request, $returnSaleId)
    {
        DB::beginTransaction();
        try {
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $username = auth()->user()->name;
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');

            // Fetch return sale details
            $returnSale = DB::table('return_sales')
                ->where('id', $returnSaleId)
                ->where('return_sale_status', 1)
                ->first();

            if (!$returnSale) {
                throw new \Exception('Return sale not found or already processed');
            }

            // Get return items and original cart
            $returnItems = DB::table('return_sale_items')
                ->join('cart_items', 'return_sale_items.cart_item_id', '=', 'cart_items.id')
                ->where('return_sale_items.return_sale_id', $returnSaleId)
                ->select(
                    'return_sale_items.*',
                    'cart_items.variant_id',
                    'cart_items.product_id'  // Also get product_id if needed
                )
                ->get();

            $originalCart = DB::table('carts')
                ->where('id', $returnSale->cart_id)
                ->first();

            // 1. Update inventory and cart items
            foreach ($returnItems as $item) {


                // Update cart item returned quantity
                DB::table('cart_items')
                    ->where('id', $item->cart_item_id)
                    ->increment('returned_qty', $item->return_qty);
            }

            // 2. Financial Reversals
            $totalReturnAmount = $returnItems->sum('amount');
            $customer = DB::table('customers')->find($returnSale->customer_id);

            // Get original payment account from receipt
            $originalReceipt = DB::table('receipts')
                ->where('slip_no', $originalCart->order_no)
                ->first();

            $paymentAccountId = DB::table('receipt_data')
                ->where('receipt_id', $originalReceipt->id)
                ->where('debit_credit', 1)
                ->value('acc_id');

            // Create Journal Voucher (Sales Return)
            $jv = new JournalVoucher();
            $jvData = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'jv_date' => $currentDate,
                'jv_no' => JournalVoucher::VoucherNo(),
                'slip_no' => $originalCart->order_no . '-RET',
                'voucher_type' => 4, // Return
                'description' => "Return for Order {$originalCart->order_no}",
                'username' => $username,
                'status' => 1,
                'jv_status' => 2,
                'date' => $currentDate,
                'time' => $currentTime,
                'approve_username' => $username,
                'approve_date' => $currentDate,
                'approve_time' => $currentTime,
            ];
            $jvId = DB::table('journal_vouchers')->insertGetId($jvData);

            // Journal Entries
            $creditAccounts = DB::select("
            SELECT c.acc_id, SUM(rsi.amount) AS total_amount
            FROM return_sale_items rsi
            JOIN cart_items ci ON ci.id = rsi.cart_item_id
            JOIN products p ON p.id = ci.product_id
            JOIN categories c ON c.id = p.category_id
            WHERE rsi.return_sale_id = ?
            GROUP BY c.acc_id
        ", [$returnSaleId]);

            foreach ($creditAccounts as $account) {
                DB::table('journal_voucher_data')->insert([
                    'journal_voucher_id' => $jvId,
                    'acc_id' => $account->acc_id,
                    'description' => "Sales Return - {$originalCart->order_no}",
                    'debit_credit' => 1, // Debit (reverse original credit)
                    'amount' => $account->total_amount,
                    'jv_status' => 2,
                    'time' => $currentTime,
                    'date' => $currentDate,
                    'status' => 1,
                    'username' => $username,
                    'approve_username' => $username,
                ]);
            }

            // Customer Credit
            DB::table('journal_voucher_data')->insert([
                'journal_voucher_id' => $jvId,
                'acc_id' => $customer->acc_id,
                'description' => "Customer Credit - {$originalCart->order_no}",
                'debit_credit' => 2, // Credit (reverse original debit)
                'amount' => $totalReturnAmount,
                'jv_status' => 2,
                'time' => $currentTime,
                'date' => $currentDate,
                'status' => 1,
                'username' => $username,
                'approve_username' => $username,
            ]);

            // Create Receipt Voucher (Refund)
            $rvData = [
                'company_id' => $companyId,
                'company_location_id' => $companyLocationId,
                'rv_date' => $currentDate,
                'rv_no' => Receipt::VoucherNo(1),
                'slip_no' => $originalCart->order_no . '-REF',
                'voucher_type' => $originalReceipt->voucher_type,
                'rv_type' => 2, // Sale Return
                'receipt_to' => $customer->name,
                'description' => "Refund for Return {$returnSale->return_sale_no}",
                'username' => $username,
                'approve_username' => $username,
                'rv_status' => 2,
                'date' => $currentDate,
                'time' => $currentTime,
                'status' => 1,
            ];
            $rvId = DB::table('receipts')->insertGetId($rvData);

            // Receipt Entries
            DB::table('receipt_data')->insert([
                [
                    'receipt_id' => $rvId,
                    'acc_id' => $customer->acc_id,
                    'description' => "Customer Refund - {$returnSale->return_sale_no}",
                    'debit_credit' => 1, // Debit (customer account)
                    'amount' => $totalReturnAmount,
                    'rv_status' => 2,
                    'time' => $currentTime,
                    'date' => $currentDate,
                    'username' => $username,
                    'approve_username' => $username,
                ],
                [
                    'receipt_id' => $rvId,
                    'acc_id' => $paymentAccountId,
                    'description' => "Refund Payment - {$returnSale->return_sale_no}",
                    'debit_credit' => 2, // Credit (cash/bank account)
                    'amount' => $totalReturnAmount,
                    'rv_status' => 2,
                    'time' => $currentTime,
                    'date' => $currentDate,
                    'username' => $username,
                    'approve_username' => $username,
                ]
            ]);

            // 3. Update return status
            DB::table('return_sales')
                ->where('id', $returnSaleId)
                ->update(['return_sale_status' => 2]);

            // 4. FARA entries
            foreach ($returnItems as $item) {
                DB::table('faras')->insert([
                    'company_id' => $companyId,
                    'company_location_id' => $companyLocationId,
                    'process_type' => 1, // Return
                    'status' => 5, // Sales Return
                    'customer_id' => $returnSale->customer_id,
                    'main_table_id' => $returnSale->id,
                    'main_table_data_id' => $item->id,
                    'return_order_no' => $returnSale->return_sale_no,
                    'return_order_date' => $currentDate,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->variant_id,
                    'qty' => $item->return_qty,
                    'rate' => $item->unit_price,
                    'amount' => $item->amount,
                    'remarks' => 'Approved Return',
                    'created_by' => $username,
                    'created_date' => $currentDate,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Return approval failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Return approval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            // Update the status directly using query builder
            DB::table('return_sales')
                ->where('id', $id)
                ->update(['status' => 2]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Sale marked as inactive successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while deactivating the Return Sale.'], 500);
        }
    }

    public function status($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            // Update the status directly using query builder
            DB::table('return_sales')
                ->where('id', $id)
                ->update(['status' => 1]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Sale marked as active successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Sale.'], 500);
        }
    }

    public function returnSaleReject($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            log::info($id);
            // Update the status directly using query builder
            DB::table('return_sales')
                ->where('id', $id)
                ->update(['return_sale_status' => 3]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Sale marked as rehjected successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Sale.'], 500);
        }
    }
    public function returnSaleRepost($id)
    {
        // Begin transaction
        DB::beginTransaction();

        try {
            log::info($id);
            // Update the status directly using query builder
            DB::table('return_sales')
                ->where('id', $id)
                ->update(['return_sale_status' => 1]);

            // Commit transaction
            DB::commit();

            return response()->json(['success' => 'Return Sale marked as pending successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while activating the Return Sale.'], 500);
        }
    }

    public function edit($id)
    {
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }

        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');

        // Fetch return sale
        $returnSale = DB::table('return_sales as rs')
            ->join('customers as c', 'rs.customer_id', '=', 'c.id')
            ->join('carts as o', 'rs.cart_id', '=', 'o.id')
            ->select(
                'rs.*',
                'c.name as customer_name',
                'o.order_no as order_no'
            )
            ->where('rs.id', $id)
            ->where('rs.company_id', $companyId)
            ->where('rs.company_location_id', $companyLocationId)
            ->first();

        if (!$returnSale) {
            return redirect()->route('sales-return.index')->withErrors('Return sale not found.');
        }

        // Get return items
        $returnItems = DB::table('return_sale_items as rsi')
            ->join('cart_items as ci', 'rsi.cart_item_id', '=', 'ci.id')
            ->join('products', 'ci.product_id', '=', 'products.id')
            ->join('product_variants', 'ci.variant_id', '=', 'product_variants.id')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.id')
            ->select(
                'rsi.*',
                'products.name as product_name',
                'sizes.name as size_name',
                'ci.qty as original_qty',
                'ci.returned_qty as previously_returned_qty'
            )
            ->where('rsi.return_sale_id', $id)
            ->get();

        return view("{$this->page}edit", compact('returnSale', 'returnItems'));
    }
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');

            $id = $request->input('id');
            $reason = $request->input('reason');
            $returnQtys = $request->input('return_qtys');
            $includeProduct = $request->input('include_product');

            if (empty($returnQtys) || !is_array($returnQtys)) {
                return redirect()->back()->withErrors('No items selected for return.');
            }

            // Update the return sale record
            DB::table('return_sales')
                ->where('id', $id)
                ->update([
                    'reason' => $reason,
                ]);

            // Delete old return items
            DB::table('return_sale_items')
                ->where('return_sale_id', $id)
                ->delete();

            // Re-insert updated return items
            foreach ($returnQtys as $itemId => $returnQty) {
                if (isset($includeProduct[$itemId]) && $includeProduct[$itemId] == 1 && $returnQty > 0) {
                    $cartItem = DB::table('cart_items')
                        ->where('id', $itemId)
                        ->first();

                    $amount = $returnQty * $cartItem->price;

                    DB::table('return_sale_items')->insert([
                        'company_id' => $companyId,
                        'company_location_id' => $companyLocationId,
                        'return_sale_id' => $id,
                        'cart_id' => $cartItem->cart_id,
                        'cart_item_id' => $cartItem->id,
                        'return_qty' => $returnQty,
                        'unit_price' => $cartItem->price,
                        'amount' => $amount,
                        'remarks' => $request->input('remarks', ''),
                        'created_by' => 'System',
                        'created_date' => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sales-return.index')->with('success', 'Sale return updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sale return: ' . $e->getMessage());
            return redirect()->back()->withErrors('An error occurred while updating the sale return.');
        }
    }



}
