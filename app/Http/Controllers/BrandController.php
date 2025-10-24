<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    protected $isApi;
    protected $page;
    protected $pageTwo;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'brands.';
        $this->pageTwo = 'layouts.';
    }

    /**
     * Store a new task in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $request)
    {
        // Return error response if accessed via API
        if ($this->isApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid endpoint for API.',
            ], 400);
        }
        if ($request->input('type') == 'model') {
            $columnId = $request->input('columnId');
            return view($this->page . 'create', ['pageOptionType' => 'model', 'columnId' => $columnId]);
        } else {
            // Ensure you're passing the correct data to the view
            return view($this->pageTwo . 'create', ['page' => $this->page, 'pageOptionType' => 'normal', 'columnId' => '']);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $companyId = Session::get('company_id');
            $companyLocationId = Session::get('company_location_id');
            $filePath = storage_path('app/json_files/brands.json');

            // Check if the JSON file exists
            if (!file_exists($filePath)) {
                // If the file doesn't exist, generate the JSON file
                generate_json('brands');
            }

            // Read the data from the JSON file
            $brands = json_decode(file_get_contents($filePath), true);

            // If a status is provided, filter the blog categories
            if ($status) {
                $brands = array_filter($brands, function ($brand) use ($status) {
                    return $brand['status'] == $status;
                });
            }

            $brands = array_filter($brands, function ($brand) use ($companyId, $companyLocationId) {
                return $brand['company_id'] == $companyId;
            });



            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('brands'));
            }

            //Return JSON response
            return jsonResponse($brands, 'Brands Retrieved Successfully', 'success', 200);
        }
        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function store(Request $request)
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('brands')->where(function ($query) use ($companyId, $companyLocationId) {
                        return $query->where('company_id', $companyId)
                                    ->where('company_location_id', $companyLocationId);
                    })
                ],
                'pageOptionType' => 'required|string|in:normal,model'
            ]);

            // Create a new Brand instance and save the data
            $brand = new Brand();
            $brand->name = $validatedData['name']; // Use validated data to ensure data integrity
            $brand->save();
            generate_json('brands');
            return jsonWithWebResponse($brand, 'Brand Created Successfully', 'success', 200, $validatedData['pageOptionType'], $this->page);
        } catch (\Exception $e) {
            // Handle unexpected errors
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

            $brands = Brand::findOrFail($id);

            return view('brands.edit', compact('brands'));
        } catch (\Exception $e) {

            return redirect()->route($this . page . 'index')->withErrors(['error' => 'The Request Was not found']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:brands,name,' . $id,
            ]);
            // Find the brand by ID or fail
            $brand = Brand::findOrFail($id);

            // Update the database
            $brand->name = $validatedData['name'];
            $brand->save();

            // Optional custom function
            generate_json('brands');

            return redirect()
                ->route('brands.index')
                ->with('success', 'Brand Updated Successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }


    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->status = 2;
            $brand->save();

            generate_json('brands');

            return response()->json(['success' => 'Brand marked as inactive successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the brand.'], 500);
        }
    }

    public function status($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->status = 1;
            $brand->save();
            generate_json('brands');

            return response()->json(['success' => 'Brand marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the brand.'], 500);
        }
    }
}
