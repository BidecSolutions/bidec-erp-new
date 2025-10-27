<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Attribute;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $isApi;
    protected $pageTwo;
    protected $page;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'categories.';
        $this->pageTwo = 'layouts.';
    }
    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function index(Request $request)
     {
         if ($request->ajax() || $this->isApi) {
             $status = $request->input('filterStatus');
             $companyId = Session::get('company_id');
             $companyLocationId = Session::get('company_location_id');
             $filePath = storage_path('app/json_files/categories.json');
     
             // Generate JSON file if it doesn't exist
             if (!file_exists($filePath)) {
                 generate_json('categories');
             }
     
             // Read and decode the JSON file
             $categories = json_decode(file_get_contents($filePath), true) ?? [];
     
             // Create a lookup table for parent category names
             $categoryLookup = [];
             foreach ($categories as $category) {
                 $categoryLookup[$category['id']] = $category['name'];
             }
     
             // Filter categories based on company and location
             $categories = array_filter($categories, fn($category) =>
                 $category['company_id'] == $companyId
             );
     
             // Further filter by status if provided
             if ($status) {
                 $categories = array_filter($categories, fn($category) => $category['status'] == $status);
             }
     
             // Map categories to include parent category name
             $categories = array_values(array_map(function ($category) use ($categoryLookup) {
                 $category['parent_name'] = $category['parent_id'] ? ($categoryLookup[$category['parent_id']] ?? '-') : '-';
                 return $category;
             }, $categories));
     
             // Return appropriate response
             return $this->isApi 
                 ? jsonResponse($categories, 'Categories Retrieved Successfully', 'success', 200)
                 : webResponse($this->page, 'indexAjax', compact('categories'));
         }
     
         return view($this->page . 'index');
     }

    public function create(Request $request)
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
            ->where('option_id', 1)
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))->first();
        if (empty($chartOfAccountSettingDetail)) {
            $chartOfAccountList = DB::table('chart_of_accounts')
                ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'))
                ->where('status', 1)->get();
        } else {
            $chartOfAccountList = DB::table('chart_of_account_settings as coas')
                ->join('chart_of_accounts as coa', 'coas.acc_id', '=', 'coa.id')
                ->select('coas.acc_id', 'coa.name', 'coa.code')
                ->where('coas.option_id', 1)
                ->where('coas.company_id', Session::get('company_id'))
                ->where('coas.company_location_id', Session::get('company_location_id'))->get();
        }
        $categories = Category::where('parent_id', 0)->with('childCategories')->get();

        if ($request->input('type') == 'model') {
            $columnId = $request->input('columnId');
            return view($this->page . 'create', ['pageOptionType' => 'model', 'columnId' => $columnId, 'categories' => $categories,'chartOfAccountList' => $chartOfAccountList]);
        } else {
            // Ensure you're passing the correct data to the view
            return view($this->pageTwo . 'create', ['page' => $this->page, 'pageOptionType' => 'normal', 'columnId' => '', 'categories' => $categories,'chartOfAccountList' => $chartOfAccountList]);
        }
      
        return view($this->page . 'create', compact('categories','chartOfAccountList'));
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming data
            $validatedData = $request->validate([
                'acc_id' => 'required|exists:chart_of_accounts,code',
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|integer',
                'order_number' => 'required|integer',
                'banner' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'cover_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'pageOptionType' => 'required|string|in:normal,model'
            ]);

            // Determine the account ID based on parent category
            // echo $validatedData['parent_id'];
            // die;
            if (!empty($validatedData['parent_id'])) {
                $parentCategory = DB::table('categories as c')
                    ->join('chart_of_accounts as coa', 'c.acc_id', '=', 'coa.id')
                    ->where('c.id', $validatedData['parent_id'])
                    ->select('coa.code')
                    ->first();

                $accId = $parentCategory->code;
            }else{
                $accId = $validatedData['acc_id'];
            }
            

            // Generate account code
            $code = ChartOfAccount::GenerateAccountCode($accId);
            $levelArray = explode('-', $code);
            $accountData = [
                'company_id' => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
                'code' => $code,
                'coa_type' => 2,
                'name' => $validatedData['name'],
                'parent_code' => $accId,
                'username' => Auth::user()->name,
                'date' => date("Y-m-d"),
                'time' => date("H:i:s"),
            ];

            foreach ($levelArray as $index => $level) {
                $accountData['level' . ($index + 1)] = $level;
            }

            // // Insert new chart of account and get the ID
            // $chartOfAccountId = DB::table('chart_of_accounts')->insertGetId($accountData);
            // generate_json('chart_of_accounts');

            // Initialize the Category model
           $chartOfAccountId = DB::table('chart_of_accounts')->insertGetId($accountData);
        // Example: cache or log data instead of generate_json

            $category = new Category();

            // Assign basic data
            $category->name = $request->input('name');
            $category->parent_id = $request->input('parent_id');
            $category->order_number = $request->input('order_number');
            $category->acc_id = $chartOfAccountId;

            // Ensure the storage directory exists
            $categoryImagePath = 'category_images';
            Storage::disk('public')->makeDirectory($categoryImagePath);

            // File handling (banner, icon, cover_image)
            $category->banner_image = 'storage/app/public/' . $this->handleFileUpload($request, 'banner', $categoryImagePath);
            $category->icon_image = 'storage/app/public/' . $this->handleFileUpload($request, 'icon', $categoryImagePath);
            $category->cover_image = 'storage/app/public/' . $this->handleFileUpload($request, 'cover_image', $categoryImagePath);

            // Save the model to the database
            $category->save();

            // Return a response or redirect with success message
            generate_json('categories');
            return jsonWithWebResponse($category, 'Category Created Successfully', 'success', 200, $request->input('pageOptionType'), $this->page);
        } catch (\Exception $e) {
            // Log the error and handle unexpected exceptions
            Log::error('Category creation failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    /**
     * Handle file upload and return file path.
     */
    protected function handleFileUpload(Request $request, $fieldName, $path)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $fileName = time() . '_' . $file->getClientOriginalName(); // Generate a unique name for the file
            // Ensure the directory exists
            Storage::disk('public')->makeDirectory($path);

            // Store the file in the 'storage/app/public/{path}' directory
            $filePath = $file->storeAs($path, $fileName, 'public'); // Store the file in the 'public/category_images' directory
            return $path . '/' . $fileName; // Return the relative path of the uploaded file
        }
        return null;
    }


    public function edit($id)
    {
        try {
            // Retrieve the category to edit
            // $category = Category::findOrFail($id);

            $category = DB::table('categories as c')
            ->join('chart_of_accounts as coa', 'c.acc_id', '=', 'coa.id')
            ->leftJoin('chart_of_accounts as children', 'children.parent_code', '=', 'coa.code')
            ->select(
                'c.*',
                'coa.parent_code',
                'coa.code',
                DB::raw('COUNT(children.id) as children_count')
            )
            ->where('c.id', $id)
            ->groupBy('c.id', 'coa.parent_code', 'coa.code')
            ->first();

            $chartOfAccountSettingDetail = DB::table('chart_of_account_settings')
                ->where('option_id', 1)
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'))
                ->first();
        if (empty($chartOfAccountSettingDetail)) {
            $chartOfAccountList = DB::table('chart_of_accounts')
                ->select('chart_of_accounts.id as acc_id', 'chart_of_accounts.name', 'chart_of_accounts.code')
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'))
                ->where('status', 1)->get();
        } else {
            $chartOfAccountList = DB::table('chart_of_account_settings as coas')
                ->join('chart_of_accounts as coa', 'coas.acc_id', '=', 'coa.id')
                ->select('coas.acc_id', 'coa.name', 'coa.code')
                ->where('coas.option_id', 1)
                ->where('coas.company_id', Session::get('company_id'))
                ->where('coas.company_location_id', Session::get('company_location_id'))->get();
        }
            $categories = Category::with('childCategories')->get(); 
            // Log the categories and the category being edited
            Log::info('Editing Category:', ['category' => $category, 'categories' => $categories]);

            // Return the edit view with category and categories for parent options
            return view('categories.edit', compact('category', 'categories', 'chartOfAccountList'));
        } catch (\Exception $e) {
            Log::error('Category edit failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['error' => 'An error occurred while retrieving the category.']);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'acc_id'          => 'required',
                'old_acc_id'      => 'nullable',
                'category_acc_id' => 'required',
                'name'            => 'required|string|max:255',
                'parent_id'       => 'nullable|integer',
                'order_number'    => 'nullable|integer',
                'banner_image'    => 'nullable|image|mimes:png,jpg,jpeg',
                'icon_image'      => 'nullable|image|mimes:png,jpg,jpeg',
                'cover_image'     => 'nullable|image|mimes:png,jpg,jpeg',
            ]);

            // --- Chart of Account Update ---
            if ($validatedData['acc_id'] == $validatedData['old_acc_id']) {
                DB::table('chart_of_accounts')
                    ->where('id', $validatedData['category_acc_id'])
                    ->update(['name' => $validatedData['name']]);
            } else {
                $getAccountDetail = DB::table('chart_of_accounts')
                    ->where('code', $validatedData['acc_id'])
                    ->first();

                $code = ChartOfAccount::GenerateAccountCode($validatedData['acc_id']);

                $levels = explode('-', $code);
                $data1 = [];
                foreach ($levels as $i => $level) {
                    $data1['level' . ($i + 1)] = $level;
                }

                $data1 = array_merge($data1, [
                    'company_id'          => Session::get('company_id'),
                    'company_location_id' => Session::get('company_location_id'),
                    'code'                => $code,
                    'coa_type'            => 2,
                    'name'                => $validatedData['name'],
                    'parent_code'         => $validatedData['acc_id'],
                    'username'            => Auth::user()->name,
                    'date'                => now()->format("Y-m-d"),
                    'time'                => now()->format("H:i:s"),
                    'ledger_type'         => $getAccountDetail->ledger_type ?? null,
                    'operational'         => 2,
                ]);

                DB::table('chart_of_accounts')
                    ->where('id', $validatedData['category_acc_id'])
                    ->update($data1);
            }

            // --- Category Update ---
            $category = Category::findOrFail($id);

            // Assign values manually (avoids fillable issue)
            $category->name         = $validatedData['name'];
            $category->parent_id    = $validatedData['parent_id'] ?? null;
            $category->order_number = $validatedData['order_number'];

            // Handle images
            $categoryImagePath = 'category_images';
            Storage::disk('public')->makeDirectory($categoryImagePath);

            foreach (['banner_image', 'icon_image', 'cover_image'] as $field) {
                if ($request->hasFile($field)) {
                    // delete old file if exists
                    if ($category->$field && Storage::disk('public')->exists(str_replace('storage/', '', $category->$field))) {
                        Storage::disk('public')->delete(str_replace('storage/', '', $category->$field));
                    }

                    $category->$field = 'storage/' . $this->handleFileUpload($request, $field, $categoryImagePath);
                }
            }

            $category->save();

            generate_json('chart_of_accounts');
            generate_json('categories');

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }


    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = 2;
            $category->save();

            generate_json('categories');
            return response()->json(['success' => 'Category marked as inactive successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the Size.'], 500);
        }
    }
    public function status($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = 1;
            $category->save();

            generate_json('categories');

            return response()->json(['success' => 'Category marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the category.'], 500);
        }
    }
}
