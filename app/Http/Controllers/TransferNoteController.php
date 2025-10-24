<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferNote;
use App\Models\TransferNoteData;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use function Laravel\Prompts\error;

class TransferNoteController extends Controller
{
    protected $isApi;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'transfer-notes.';
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
        $companyLocations = DB::table('company_locations')->where('company_id', Session::get('company_id'))->get();

        return view($this->page . 'create', compact('products', 'companyLocations'));
    }

    public function store(Request $request)
    {
        // print_r($request->all());
        // die;
        try {
            $validatedData = $request->validate([
                'tn_date' => 'required|date',
                'description' => 'nullable|string|max:255',
                'tnDataArray' => 'required|array',
                'tnDataArray.*' => 'required|integer',
                'locationId_*' => 'required|integer',
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
            // Insert data into PurchaseOrder
            $transferNote = new TransferNote();
            $transferNote->transfer_note_no = TransferNote::VoucherNo();
            $transferNote->transfer_note_date = $request->tn_date;
            $transferNote->description = $request->description;
            $transferNote->save();

            // Insert data into TransferNoteData
            foreach ($request->tnDataArray as $key => $tnData) {
                $index = $key + 1; // Assuming data starts from index 1

                $transferNoteData = new TransferNoteData();

                $transferNoteData->transfer_note_id = $transferNote->id;
                $transferNoteData->to_company_id = Session::get('company_id');
                $transferNoteData->to_company_location_id = $request->input('locationId_' . $index);
                $transferNoteData->product_variant_id = $request->input('productId_' . $index);
                $transferNoteData->send_qty = $request->input('qty_' . $index);
                $transferNoteData->remarks = $request->input('remarks_' . $index);
                $transferNoteData->receive_qty = 0;
                $transferNoteData->save();
            }

            //Commit transaction
            DB::commit();

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Transfer Note Created Successfully');
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
                generate_json($key);
            }
        }

        // Load data from JSON files
        $data = array_map(fn($path) => json_decode(file_get_contents($path), true), $jsonFiles);
        ['products' => $products, 'product_variants' => $variants, 'categories' => $categories, 'brands' => $brands, 'sizes' => $sizes] = $data;

        // Optimize the relationship building by indexing categories, brands, and sizes by their IDs
        $categoryMap = array_column($categories, 'name', 'id');
        $brandMap = array_column($brands, 'name', 'id');
        $sizeMap = array_column($sizes, 'name', 'id');

        // Fetch the transfer note and its data
        $transferNote = TransferNote::findOrFail($id);
        $transferNoteData = TransferNoteData::where('transfer_note_id', $id)->get();

        // Attach related data to products
        $products = array_map(function ($product) use ($variants, $categoryMap, $brandMap, $sizeMap) {
            $product['variants'] = array_filter($variants, fn($variant) => $variant['product_id'] == $product['id']);
            $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
            $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';

            foreach ($product['variants'] as &$variant) {
                $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
            }

            return $product;
        }, $products);

        // Apply status filter if provided
        $products = array_filter($products, fn($product) => $product['status'] == 1);
        $companyLocations = DB::table('company_locations')->where('company_id', Session::get('company_id'))->get();

        // Pass data to the view
        return view($this->page . 'edit', compact('products', 'companyLocations', 'transferNote', 'transferNoteData'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'tn_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'tnDataArray' => 'required|array',
            'tnDataArray.*' => 'required|integer',
            'locationId' => 'array',
            'locationId.*' => 'required|integer',
            'productId' => 'array',
            'productId.*' => 'required|integer',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $transferNote = TransferNote::findOrFail($id);
            $transferNote->transfer_note_date = $request->tn_date;
            $transferNote->description = $request->description;
            $transferNote->save();

            $existingData = TransferNoteData::where('transfer_note_id', $id)->get()->keyBy('id');
            Log::info("First transaction is complete");
            // Prepare to track updated IDs
            $updatedIds = [];

            // Update existing data and add new entries
            foreach ($request->tnDataArray as $key => $tnData) {
                $index = $key + 1; // Assuming data starts from index 1
                $dataId = $request->input('dataId_' . $index);
                Log::info('The Loop of entry the old or new is started');

                if ($dataId && $existingData->has($dataId)) {
                    // Update existing entry
                    $transferNoteData = $existingData[$dataId];
                    $transferNoteData->to_company_location_id = $request->input('locationId_' . $index);
                    $transferNoteData->product_variant_id = $request->input('productId_' . $index);
                    $transferNoteData->send_qty = $request->qty[$key] ?? 0;
                    $transferNoteData->remarks = $request->remarks[$key] ?? '-';
                    $transferNoteData->save();

                    $updatedIds[] = $dataId;
                    Log::info('Old Data Updated');
                } else {
                    // Add new entry
                    $newTransferNoteData = new TransferNoteData();
                    $newTransferNoteData->transfer_note_id = $transferNote->id;
                    $newTransferNoteData->to_company_id = Session::get('company_id');
                    $newTransferNoteData->to_company_location_id = $request->input('locationId_' . $index);
                    $newTransferNoteData->product_variant_id = $request->input('productId_' . $index);
                    $newTransferNoteData->send_qty = $request->input('qty_' . $index, 0);
                    $newTransferNoteData->remarks = $request->input('remarks_' . $index, '-');
                    $newTransferNoteData->receive_qty = 0;
                    $newTransferNoteData->save();


                    if ($dataId) {
                        $updatedIds[] = $dataId;
                    }
                    Log::info('New Data Inserted');
                }
            }

            // Delete data entries that were removed in the update
            $toDelete = $existingData->keys()->diff($updatedIds)->toArray();
            if (!empty($toDelete)) {
                TransferNoteData::whereIn('id', $toDelete)->delete();
            }
            Log::info('Existing Data IDs: ' . json_encode($existingData->keys()->toArray()));
            Log::info('Updated IDs: ' . json_encode($updatedIds));
            Log::info('IDs to Delete: ' . json_encode($toDelete));
            

            // Commit transaction
            DB::commit();
            Log::info('Its Done');

            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Transfer Note Updated Successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating.'])->withInput();
        }
    }


    public function viewReceiptDetail(Request $request)
    {
        $companyLocationId = Session::get('company_location_id');
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);

        $transferNoteId = $request->id;

        // Fetch the transfer note details with the supplier name
        $transferNote = DB::table('transfer_notes')
            ->select(
                'transfer_notes.*'
            )
            ->where('transfer_notes.id', $transferNoteId)
            ->first();

        if (!$transferNote) {
            return response()->json(['error' => 'Transfer Note not found'], 404);
        }

        // Attach transfer note data to the main object
        $transferNote->transferNoteData = DB::table('transfer_note_datas as tnd')
            ->join('company_locations as cl', 'tnd.to_company_location_id', '=', 'cl.id')
            ->join('product_variants as pv', 'tnd.product_variant_id', '=', 'pv.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('tnd.transfer_note_id', $transferNoteId)
            ->select('p.name as product_name', 'pv.amount as product_variant_amount', 's.name as size_name', 'cl.name as company_location_name', 'tnd.id', 'tnd.to_company_id', 'tnd.to_company_location_id', 'tnd.send_qty', 'tnd.receive_qty', 'tnd.remarks', 'tnd.tnd_status')
            ->where('tnd.to_company_location_id', $companyLocationId)
            ->get();

        // Fetch related purchase order details for display
        $transferNoteDetails = $transferNote->transferNoteData;

        // Return the view with the purchase order details
        return view($this->page . 'viewTransferNoteReceiptDetail', compact('transferNote', 'transferNoteDetails'));
    }

    public function show(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
        ]);

        $transferNoteId = $request->id;

        // Fetch the transfer note details with the supplier name
        $transferNote = DB::table('transfer_notes')
            ->select(
                'transfer_notes.*'
            )
            ->where('transfer_notes.id', $transferNoteId)
            ->first();

        if (!$transferNote) {
            return response()->json(['error' => 'Transfer Note not found'], 404);
        }

        // Attach transfer note data to the main object
        $transferNote->transferNoteData = DB::table('transfer_note_datas as tnd')
            ->join('company_locations as cl', 'tnd.to_company_location_id', '=', 'cl.id')
            ->join('product_variants as pv', 'tnd.product_variant_id', '=', 'pv.id')
            ->join('sizes as s', 'pv.size_id', '=', 's.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('tnd.transfer_note_id', $transferNoteId)
            ->select('p.name as product_name', 'pv.amount as product_variant_amount', 's.name as size_name', 'cl.name as company_location_name', 'tnd.id', 'tnd.to_company_id', 'tnd.to_company_location_id', 'tnd.send_qty', 'tnd.receive_qty', 'tnd.remarks', 'tnd.tnd_status')
            ->get();

        // Fetch related purchase order details for display
        $transferNoteDetails = $transferNote->transferNoteData;

        // Return the view with the purchase order details
        return view($this->page . 'viewTransferNoteDetail', compact('transferNote', 'transferNoteDetails'));
    }

    public function updateTransferNotesReceiptDetail(Request $request)
    {
        $idsArray = $request->input('idsArray');
        $faraData = [];
        $username = Auth::user()->name;
        $currentDate = date('Y-m-d');
        foreach ($idsArray as $id) {
            $receive_qty = $request->input('receive_qty_' . $id, 0);
            $return_qty = $request->input('return_qty_' . $id, 0);
            if ($receive_qty == 0 && $return_qty == 0) {
            } else {
                TransferNoteData::where('id', $id)->update([
                    'receive_qty' => $receive_qty,
                    'return_qty' => $return_qty,
                    'tnd_status' => 2
                ]);

                $getTransferDataDetail = DB::table('transfer_note_datas as tnd')
                    ->join('transfer_notes as tn', 'tnd.transfer_note_id', '=', 'tn.id')
                    ->join('product_variants as pv', 'tnd.product_variant_id', '=', 'pv.id')
                    ->select('tnd.*', 'pv.product_id', 'tn.company_id', 'tn.company_location_id', 'tn.transfer_note_no', 'tn.transfer_note_date')
                    ->where('tnd.id', $id)
                    ->first();
                $faraData[] = [
                    'company_id' => $getTransferDataDetail->company_id,
                    'company_location_id' => $getTransferDataDetail->company_location_id,
                    'to_company_id' => $getTransferDataDetail->to_company_id,
                    'to_company_location_id' => $getTransferDataDetail->to_company_location_id,
                    'process_type' => 1,
                    'status' => 3,
                    'main_table_id' => $getTransferDataDetail->transfer_note_id,
                    'main_table_data_id' => $id,
                    'transfer_note_no' => $getTransferDataDetail->transfer_note_no,
                    'transfer_note_date' => $getTransferDataDetail->transfer_note_date,
                    'product_id' => $getTransferDataDetail->product_id,
                    'product_variant_id' => $getTransferDataDetail->product_variant_id,
                    'qty' => $getTransferDataDetail->receive_qty,
                    'remarks' => $getTransferDataDetail->remarks,
                    'created_by' => $username,
                    'created_date' => $currentDate
                ];
            }
        }
        if ($faraData) {
            DB::table('faras')->insert($faraData);
        }
        echo 'Done';
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
            $transferNotes = DB::table('transfer_notes as tn')
                ->select(
                    'tn.id',
                    'tn.company_id',
                    'tn.company_location_id',
                    'tn.transfer_note_no',
                    'tn.transfer_note_date',
                    'tn.description',
                    'tn.tn_status',
                    'tn.status',
                    'tn.created_date',
                    'tn.created_by',

                )
                ->whereBetween('tn.transfer_note_date', [$fromDate, $toDate])
                ->where('tn.company_id', $companyId);
            if ($status) {
                $transferNotes = $transferNotes->where('tn.status', $status);
            }

            $transferNotes = $transferNotes->get();

            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('transferNotes'));
            }

            // Return JSON response for API requests
            return jsonResponse($transferNotes, 'Transfer Notes Retrieved Successfully', 'success', 200);
        }

        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function status($id)
    {
        $transferNote = TransferNote::find($id);
        $transferNote->status = 1;
        $transferNote->save();
        return response()->json(['success' => 'Transfer Note marked as active successfully!']);
    }
    public function destroy($id)
    {
        $transferNote = TransferNote::find($id);
        $transferNote->status = 2;
        $transferNote->save();
        return response()->json(['success' => 'Transfer Note marked as inactive successfully!']);
    }


    public function approveTransferNoteVoucher(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('transfer_notes')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['tn_status' => 2]);
        echo 'Done';
    }

    public function transferNoteVoucherRejectAndRepost(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('transfer_notes')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['tn_status' => $value]);
        echo 'Done';
    }

    public function transferNoteVoucherActiveAndInactive(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $voucherTypeStatus = $request->input('voucherTypeStatus');
        $value = $request->input('value');

        DB::table('transfer_notes')->where('id', $id)->where('company_id', $companyId)->where('company_location_id', $companyLocationId)->update(['status' => $value]);
        echo 'Done';
    }
}
