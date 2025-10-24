<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Storage;
use Spatie\Permission\Models\Role;
use Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;
use Yajra\DataTables\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->page = 'HR.loans.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Loan::with('employee:id,emp_name')
                ->where('loans.company_id', Session::get('company_id'))
                ->where('loans.company_location_id', Session::get('company_location_id'));

            $loans = $query->get();

            return DataTables::of($loans)
                ->addColumn('emp_name', function ($row) {
                    return $row->employee->emp_name;
                })
                ->addColumn('status', function ($row) {
                    $toggleUrl = $row->status == 1
                        ? route('loan.destroy', $row->id)
                        : route('loan.active', $row->id);
                    $toggleId = $row->status == 1 ? 'inactive-record' : 'active-record';

                    return '<td class="text-center">
                                <div class="hidden-print">
                                    <label class="switch">
                                        <input type="checkbox" id="' . $toggleId . '" data-url="' . $toggleUrl . '" 
                                            data-id="' . $row->id . '" ' . ($row->status == 1 ? 'checked' : '') . '>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="d-none d-print-inline-block">
                                    ' . ($row->status == 1 ? 'Active' : 'Inactive') . '
                                </div>
                            </td>';
                })
                ->addColumn('action', function ($row) {
                    return '<td class="text-center hidden-print">
                                <div class="dropdown">
                                    <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action<span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a onclick="showDetailModelOneParamerter(\'loan/show\', \'' . $row->id . '\', \'View Loan Detail\')">
                                            <span class="glyphicon glyphicon-eye-open"></span> View
                                        </a></li>
                                        <li><a href="' . route('loan.edit', $row->id) . '">Edit</a></li>
                                    </ul>
                                </div>
                            </td>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view($this->page . 'index');
    }


    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employeeList = DB::table('employees as e')
            ->leftJoin('employee_allowance_detail as ead', 'e.id', '=', 'ead.employee_id')
            ->select('e.id', 'e.emp_no', 'e.emp_name', 'e.basic_salary', DB::raw('MAX(ead.id) AS eadId'))
            ->where('e.company_id', Session::get('company_id'))
            ->where('e.company_location_id', Session::get('company_location_id'))
            ->groupBy('e.id', 'e.emp_no', 'e.emp_name', 'e.basic_salary')
            ->get();
        return view($this->page . 'create', compact('employeeList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id', // Ensure the employee exists
            'apply_loan_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'per_month_deduction' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            // Create a new loan instance
            $loan = new Loan();
            $loan->employee_id = $data['employee_id'];
            $loan->apply_loan_date = $data['apply_loan_date'];
            $loan->amount = $data['amount'];
            $loan->per_month_deduction = $data['per_month_deduction'];
            $loan->description = $data['description'];

            // Save the loan entry to the database
            $loan->save();

            // Redirect to the loan index with a success message
            return redirect()->route('loan.index')->with('message', 'Loan Created Successfully');
        } catch (\Exception $e) {
            // Handle the exception, log it, or notify the user
            return redirect()->back()->withErrors(['error' => 'Failed to create loan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $employeeDetail = DB::table('employees as e')
            ->leftJoin('cities as c', 'e.city_id', '=', 'c.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->leftJoin('employee_education_details as eed', 'e.id', '=', 'eed.employee_id')
            ->select('e.*', 'eed.*', 'c.city_name', 'd.department_name')
            ->where('e.id', $id)
            ->first();
        $employeeDocuments = DB::table('employee_documents')->where('employee_id', $id)->get();
        $employeeAllowanceDetail = DB::table('employee_allowance_detail')->where('employee_id', $id)->get();
        $employeeExperiences = DB::table('employee_experiences')->where('employee_id', $id)->get();

        return view($this->page . 'viewEmployeeDetail', compact('employeeDetail', 'employeeDocuments', 'employeeAllowanceDetail', 'employeeExperiences'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeList = DB::table('employees as e')
            ->leftJoin('employee_allowance_detail as ead', 'e.id', '=', 'ead.employee_id')
            ->select('e.id', 'e.emp_no', 'e.emp_name', 'e.basic_salary', DB::raw('MAX(ead.id) AS eadId'))
            ->where('e.company_id', Session::get('company_id'))
            ->where('e.company_location_id', Session::get('company_location_id'))
            ->groupBy('e.id', 'e.emp_no', 'e.emp_name', 'e.basic_salary')
            ->get();
        $loan = Loan::with('employee:id,emp_name')->where('id', $id)->first();
        return view($this->page . 'edit', compact('employeeList', 'loan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id', // Ensure the employee exists
            'apply_loan_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'per_month_deduction' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            // Create a new loan instance
            $loan = Loan::findOrFail($id);
            $loan->employee_id = $data['employee_id'];
            $loan->apply_loan_date = $data['apply_loan_date'];
            $loan->amount = $data['amount'];
            $loan->per_month_deduction = $data['per_month_deduction'];
            $loan->description = $data['description'];

            // Save the loan entry to the database
            $loan->save();

            // Redirect to the loan index with a success message
            return redirect()->route('loan.index')->with('message', 'Loan Updated Successfully');
        } catch (\Exception $e) {
            // Handle the exception, log it, or notify the user
            return redirect()->back()->withErrors(['error' => 'Failed to update loan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Loan::where('id', $id)->update(['status' => 2]);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function status($id)
    {
        Loan::where('id', $id)->update(['status' => 1]);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
