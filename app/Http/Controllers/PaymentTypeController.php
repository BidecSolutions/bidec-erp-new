<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PaymentType;
use Exception;
use Session;    

class PaymentTypeController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'payment-types.';
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

        return view($this->page . 'create');
    }

    public function index(Request $request)
    {
        $companyId = Session::get('company_id');
        if ($request->ajax() || $this->isApi) {
            $status = $request->input('filterStatus');
            $filePath = storage_path('app/json_files/payment_types.json');

            // Check if the JSON file exists
            if (!file_exists($filePath)) {
                // If the file doesn't exist, generate the JSON file
                generate_json('payment_types');
            }

            // Read the data from the JSON file
            $paymentTypes = json_decode(file_get_contents($filePath), true);

            // Filter by company_id
            $paymentTypes = array_filter($paymentTypes, function ($paymentType) use ($companyId) {
                return isset($paymentType['company_id']) && $paymentType['company_id'] == $companyId;
            });

            // If a status is provided, filter the blog categories
            if ($status) {
                $paymentTypes = array_filter($paymentTypes, function ($paymentType) use ($status) {
                    return $paymentType['status'] == $status;
                });
            }



            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                return webResponse($this->page, 'indexAjax', compact('paymentTypes'));
            }

            //Return JSON response
            return jsonResponse($paymentTypes, 'Payment Types Retrieved Successfully', 'success', 200);
        }
        if (!$this->isApi) {
            return view($this->page . 'index');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:payment_types,name', // Ensure 'name' is unique in the 'payment_types' table
                'conversion_rate' => 'nullable|numeric', // Assuming convention_rate should be a number or nullable
                'rate_type' => 'required|in:1,2' // Ensures rate_type is either 1 or 2
            ]);

            // Create a new Payment Type instance and save the data
            $paymentType = new PaymentType();
            $paymentType->name = $validatedData['name']; // Use validated data to ensure data integrity
            $paymentType->conversion_rate = $validatedData['conversion_rate'];
            $paymentType->rate_type = $validatedData['rate_type'];
            $paymentType->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('payment_types');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Payment Type Created Successfully');
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
            $paymentType = PaymentType::findOrFail($id);

            return view($this->page . 'edit', compact('paymentType'));
        } catch (\Exception $e) {
            return redirect()->route($this->page . 'index')->withErrors(['error' => 'The Request Was not found']);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:payment_types,name,' . $id, // Ensure 'name' is unique except for the current record
                'conversion_rate' => 'nullable|numeric', // 'conversion_rate' can be null or a number
                'rate_type' => 'required|in:1,2', // Ensures 'rate_type' is either 1 (Fixed) or 2 (Changeable)
            ]);

            // Find the existing PaymentType record by ID
            $paymentType = PaymentType::findOrFail($id);

            // Update the record with validated data
            $paymentType->name = $validatedData['name'];
            $paymentType->conversion_rate = $validatedData['conversion_rate'];
            $paymentType->rate_type = $validatedData['rate_type'];
            $paymentType->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('payment_types');

            // Redirect with success message
            return redirect()
                ->route($this->page . 'index')
                ->with('success', 'Payment Type Updated Successfully');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }


    public function status($id)
    {
        try {
            // Find the PaymentType record by ID
            $paymentType = PaymentType::findOrFail($id);

            // Toggle the status of the PaymentType record
            $paymentType->status = 1;
            $paymentType->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('payment_types');

            // Redirect with success message
            return response()->json(['success' => 'payment Type marked as Active successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }
    public function destroy($id)
    {
        try {
            // Find the PaymentType record by ID
            $paymentType = PaymentType::findOrFail($id);
            // Toggle the status of the PaymentType record
            $paymentType->status = 2;
            $paymentType->save();

            // Call a helper function to regenerate JSON if applicable
            generate_json('payment_types');

            // Redirect with success message
            return response()->json(['success' => 'payment type marked as inactive successfully!']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }
}
