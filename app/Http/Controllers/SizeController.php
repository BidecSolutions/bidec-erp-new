<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
    protected $isApi;
    protected $page;
    protected $pageTwo;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'sizes.';
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
            $filePath = storage_path('app/json_files/sizes.json');

            // Check if the JSON file exists
            if (!file_exists($filePath)) {
                // If the file doesn't exist, generate the JSON file
                generate_json('sizes');
            }

            // Read the data from the JSON file
            $sizes = json_decode(file_get_contents($filePath), true);

            // If a status is provided, filter the blog categories
            if ($status) {
                $sizes = array_filter($sizes, function ($size) use ($status) {
                    return $size['status'] == $status;
                });
            }

            $sizes = array_filter($sizes, function ($size) use ($companyId) {
                return $size['company_id'] == $companyId;
            });



            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('sizes'));
            }

            //Return JSON response
            return jsonResponse($sizes, 'Sizes Retrieved Successfully', 'success', 200);
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
                    Rule::unique('sizes')->where(function ($query) use ($companyId, $companyLocationId) {
                        return $query->where('company_id', $companyId)
                                    ->where('company_location_id', $companyLocationId);
                    })
                ],
                'pageOptionType' => 'required|string|in:normal,model'
            ]);

            // Create a new Size instance and save the data
            $size = new Size();
            $size->name = $validatedData['name']; // Use validated data to ensure data integrity
            $size->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('sizes');

            // Redirect with success message
            return jsonWithWebResponse($size, 'Sizes Created Successfully', 'success', 200, $validatedData['pageOptionType'], $this->page);
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
            $sizes = Size::findOrFail($id);

            return view('sizes.edit', compact('sizes'));
        } catch (\Exception $e) {
            return redirect()->route($this->page . 'index')->withErrors(['error' => 'The Request Was not found']);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:sizes,name,' . $id,
            ]);

            //Find Size by id or fail
            $size = Size::findOrFail($id);

            //update the database

            $size->name = $validatedData['name'];
            $size->save();

            // update the json_file
            generate_json('sizes');

            return redirect()
                ->route('sizes.index')
                ->with('success', 'Size Updated Successfully');
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
            $size = Size::findOrFail($id);
            $size->status = 2;
            $size->save();

            generate_json('sizes');

            return response()->json(['success' => 'Size marked as inactive successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the Size.'], 500);
        }
    }
    public function status($id)
    {
        try {
            $size = Size::findOrFail($id);
            $size->status = 1;
            $size->save();
            generate_json('sizes');

            return response()->json(['success' => 'Size marked as Active successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deactivating the brand.'], 500);
        }
    }
}
