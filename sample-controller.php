<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $isApi;
    public function __construct(Request $request)
    {
        // Check if the request is for API
        $this->isApi = $request->is('api/*');
        // Define the base view path for web views
        $this->page = 'brands.';
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

    private function validateFilters(Request $request)
    {
        return $request->validate([
            // 'type' => 'nullable|string|in:1,2', // Status filter
            // 'employee_id' => 'nullable|integer|exists:employees,id', // Employee filter
            // 'student_id' => 'nullable|integer|exists:students,id', // Student filter
            // 'from_date' => 'nullable|date', // Start date filter
            // 'to_date' => 'nullable|date|after_or_equal:from_date', // End date filter
            // 'section_id' => 'nullable|integer|exists:sections,id', // Section filter
            // 'subject_id' => 'nullable|integer|exists:subjects,id', // Subject filter
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $this->isApi) {
            try {
                // Validate input filters
                $filters = $this->validateFilters($request);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            // // Build the query with filters
            // $assignTaskQuery = $this->buildAssignTaskQuery($request, $filters);

            // // If pagination is needed, you can adjust here (e.g., paginate(10) for 10 records per page)
            // $assignTasks = $assignTaskQuery->get(); // or ->paginate(10) for paginated data
            // $totalRecords = $assignTasks->count();



            // If rendering in a web view (for non-API requests)
            if (!$this->isApi) {
                //return $this->webResponse('indexAjax', compact('assignTasks'));
            }

            // Return JSON response
            //return $this->jsonResponse($assignTasks, $totalRecords, 'Assign Tasks Retrieved Successfully', 'success', 200);
        }
        if (!$this->isApi) {
            // $empId = Auth::user()->emp_id;
            // $schoolId = Session::get('company_id');
            // $schoolCampusId = Session::get('company_location_id');


            // $query = DB::table('subject_teacher_assignments as sta')
            //     ->where('sta.company_id', $schoolId)
            //     ->where('sta.company_location_id', $schoolCampusId)
            //     ->where('sta.teacher_id', $empId)
            //     ->get();


            // // Extract unique section_ids
            // $section_ids = $query->pluck('section_id')->unique()->toArray();


            // // Fetch all sections based on unique section_ids
            // $sections = Section::with('classes')
            //     ->whereIn('id', $section_ids)
            //     ->where('company_id', $schoolId)
            //     ->where('company_location_id', $schoolCampusId)
            //     ->get();


            return view($this->page . 'index');
        }
    }

    public function edit($id){
        
    }

    public function store(Request $request){

    }

    public function update(Request $request){

    }

    public function show($id){

    }
}
