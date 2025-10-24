<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use Storage;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\CompanyLocations;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\DataTables;

use App\Mail\EmployeeCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private $employeeRepository;
    protected $page;
    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->page = 'HR.employees.';
        $this->employeeRepository = $employeeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments = Department::where('company_id', Session::get('company_id'))->get();
        if ($request->ajax()) {
            $employees =  $this->employeeRepository->allEmployees($request->all());
            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('emp_type', function ($row) {
                    if ($row->emp_type == 1) {
                        return 'Non Teaching Staff';
                    } else if ($row->emp_type == 2) {
                        return 'Teaching Staff';
                    } else if ($row->emp_type == 3) {
                        return 'Nazim';
                    } else if ($row->emp_type == 4) {
                        return 'Naib Nazim';
                    } else {
                        return 'Moavin';
                    }
                })->addColumn('status', function ($row) {
                    $toggleUrl = $row->status == 1
                        ? route('employees.destroy', $row->id)
                        : route('employees.active', $row->id);
                
                    $toggleId = $row->status == 1 ? 'inactive-record' : 'active-record';
                    $isChecked = $row->status == 1 ? 'checked' : '';
                
                    return '
                        <td class="text-center">
                            <div class="hidden-print">
                                <label class="switch">
                                    <input type="checkbox" id="' . $toggleId . '" data-url="' . $toggleUrl . '" data-id="' . $row->id . '" ' . $isChecked . '>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="d-none d-print-inline-block">'
                                . ($row->status == 1 ? 'Active' : 'Inactive') .
                            '</div>
                        </td>';
                })
                
                
                ->addColumn('action', function ($row) {
                    $data = '<td class="text-center hidden-print"><div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action<span class="caret"></span></button>
                <ul class="dropdown-menu">';
                    $data .= '<li><a onclick="showDetailModelOneParamerter(\'employees/show\', \'' . $row->id . '\', \'View Employee Detail\')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
                    if ($row->status == 1) {
                        $data .= '<li><a href="' . route('employees.edit', $row->id) . '">Edit</a></li>';
                    }
                    $data .= '</ul></div></td>';
                    return $data;
                })
                ->rawColumns(['emp_type', 'action', 'status'])
                ->make(true);
        }
        return view($this->page . 'index', compact('departments'));
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles  = Role::all();
        $cities = DB::table('cities')->get();
        $departments = Department::status(1)->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
        return view($this->page . 'create', compact('roles', 'cities', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'emp_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'emp_name' => '',
            'emp_father_name' => '',
            'date_of_birth' => '',
            'cnic_no' => '',
            'address' => '',
            'emp_email' => '',
            'phone_no' => '',
            'maritarial_status' => '',
            'no_of_childern' => '',
            'relative_name' => '',
            'relative_contact_no' => '',
            'relative_address' => '',
            'login_access' => '',
            'grace_time' => '',
            'start_time' => '',
            'end_time' => '',
            'guardian_name' => '',
            'guardian_mobile_no' => '',
            'guardian_address' => '',
            'basic_salary' => '',
            'city_id' => '',
            'department_id' => '',
            'date_of_joining' => '',
            'employment_status' => '',
            'cnic_document.*' => 'file|mimes:jpg,png,pdf|max:2048',
            'other_document.*' => 'file|mimes:jpg,png,pdf|max:2048',
        ]);

        $schoolId = Session::get('company_id');
        $schoolCampusIdsArray = [];
        $empIdsArray = [];

        // If multiple school campuses
        $schoolCampusId = Session::get('company_location_id');
        $this->processEmployee($request, $schoolCampusId, $schoolId, $schoolCampusIdsArray, $empIdsArray);
        
        // If login access is required
        if ($request->input('login_access') == 2) {
            $this->createUser($request, $empIdsArray, $schoolCampusIdsArray, $schoolId);
        }

        return redirect()->route('employees.index')->with('message', 'Employee Created Successfully');
    }

    // Function to handle employee creation and file uploads
    private function processEmployee($request, $schoolCampusId, $schoolId, &$schoolCampusIdsArray, &$empIdsArray)
    {
        $basicSalary = $request->input('basic_salary_' . $schoolCampusId) ?? $request->input('basic_salary');
        $employeeDetail = $this->employeeStore($request->all(), $schoolId, $schoolCampusId, $basicSalary);

        $registrationNo = $employeeDetail->emp_no;
        $employeeId = $employeeDetail->id;

        $schoolCampusIdsArray[] = ['company_location_id' => $schoolCampusId];
        $empIdsArray[] = ['emp_id' => $employeeId];

        // Handle image upload (Only once)
        $this->uploadImage($request, $registrationNo, $employeeId);

        // Handle documents upload (Multiple documents)
        $this->uploadDocuments($request, $registrationNo, $employeeId);
    }

    private function uploadImage($request, $registrationNo, $employeeId)
    {
        if ($image = $request->file('emp_image')) {
            //Storage::disk('public')->makeDirectory('public/EmployeeDocument/'.$registrationNo.'');
            $destinationPath = 'public/EmployeeDocument/' . $registrationNo . '';

            // Generate a unique file name using the current timestamp
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            // Check if the file already exists and avoid re-uploading it
            $image->storeAs($destinationPath, $profileImage); // Save image

            // Save image path in database
            $data['emp_image'] = $destinationPath . '/' . $profileImage;
            Employee::where('id', $employeeId)->update($data);
        }
    }

    // Function to upload CNIC and other documents
    private function uploadDocuments($request, $registrationNo, $employeeId)
    {
        $employeeDocuments = [];

        // Handle CNIC documents
        if ($request->hasFile('cnic_document')) {
            foreach ($request->file('cnic_document') as $cnicDocument) {
                $this->uploadDocument($cnicDocument, $registrationNo, $employeeId, 1, $employeeDocuments);
            }
        }

        // Handle other documents
        if ($request->hasFile('other_document')) {
            foreach ($request->file('other_document') as $otherDocument) {
                $this->uploadDocument($otherDocument, $registrationNo, $employeeId, 2, $employeeDocuments);
            }
        }

        if (!empty($employeeDocuments)) {
            DB::table('employee_documents')->insert($employeeDocuments);
        }
    }

    private function uploadDocument($document, $registrationNo, $employeeId, $documentType, &$employeeDocuments)
    {

        //Storage::disk('public')->makeDirectory('public/EmployeeDocument/'.$registrationNo.'');
        $documentPath = 'public/EmployeeDocument/' . $registrationNo . '/' . ($documentType == 1 ? 'cnic_document' : 'other_document');
        $fileName = date('YmdHis') . "." . $document->getClientOriginalExtension();

        $document->storeAs($documentPath, $fileName); // Save document

        $employeeDocuments[] = [
            'employee_id' => $employeeId,
            'document_type' => $documentType,
            'document_path' => $documentPath . '/' . $fileName,
            'status' => 1,
            'created_by' => Auth::user()->name,
            'created_date' => date('Y-m-d'),
        ];
    }

    // Function to create user for employee
    private function createUser($request, $empIdsArray, $schoolCampusIdsArray, $schoolId)
    {
        $password = Str::random(10);
        $user = User::create([
            'emp_type_multiple_campus' => $request->input('multiple_school_campus'),
            'emp_id' => $empIdsArray[0]['emp_id'],  // Assuming one employee per user creation
            'emp_ids_array' => json_encode($empIdsArray),
            'acc_type' => 'superadmin',
            'company_id' => $schoolId,
            'company_location_id' => $schoolCampusIdsArray[0]['company_location_id'],  // Assuming one campus per user
            'company_location_ids_array' => json_encode($schoolCampusIdsArray),
            'mobile_no' => $request->input('phone_no'),
            'cnic_no' => $request->input('cnic_no'),
            'name' => $request->input('emp_name'),
            'email' => $request->input('emp_email'),
            'password' => bcrypt($password),
            'username' => '-',
            'sgpe' => $password,
        ]);

        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        Mail::to($request->input('emp_email'))->send(new EmployeeCreated($request->input('emp_name'), $password));
    }

    private function employeeStore($data, $schoolId, $schoolCampusId, $basicSalary)
    {

        $registrationNo = Employee::RegistrationNo($schoolId, $schoolCampusId, $basicSalary);
        $employeeDetail = new Employee();
        $employeeDetail->emp_no = $registrationNo;


        //$employeeDetail = $this->employeeRepository->storeEmployee($data);

        $employeeDetail->company_id = $schoolId;
        $employeeDetail->company_location_id = $schoolCampusId;
        $employeeDetail->city_id = $data['city_id'];
        $employeeDetail->department_id = $data['department_id'];
        $employeeDetail->emp_name = $data['emp_name'];
        $employeeDetail->emp_image = '-';
        $employeeDetail->emp_father_name = $data['emp_father_name'];
        $employeeDetail->date_of_birth = $data['date_of_birth'];
        $employeeDetail->date_of_joining = $data['date_of_joining'];
        $employeeDetail->cnic_no = $data['cnic_no'];
        $employeeDetail->address = $data['address'];
        $employeeDetail->emp_email = $data['emp_email'];
        $employeeDetail->phone_no = $data['phone_no'];

        $employeeDetail->maritarial_status = $data['maritarial_status'];
        $employeeDetail->job_type = $data['job_type'];
        $employeeDetail->employment_status = $data['employment_status'];
        $employeeDetail->no_of_childern = $data['no_of_childern'];
        $employeeDetail->relative_name = $data['relative_name'];
        $employeeDetail->relative_contact_no = $data['relative_contact_no'];
        $employeeDetail->relative_address = $data['relative_address'];
        $employeeDetail->guardian_name = $data['guardian_name'];
        $employeeDetail->guardian_mobile_no = $data['guardian_mobile_no'];
        $employeeDetail->guardian_address = $data['guardian_address'];
        $employeeDetail->login_access = $data['login_access'];
        $employeeDetail->grace_time = $data['grace_time'];
        $employeeDetail->start_time = $data['start_time'];
        $employeeDetail->end_time = $data['end_time'];
        $employeeDetail->basic_salary = $basicSalary;

        $employeeDetail->save();



        $employeeId = $employeeDetail->id;
        // $eedData = array([
        //     'employee_id' => $employeeId,
        //     'hafiz_status' => $data['hafiz_status'] ?? 1,
        //     'memorization_location_for_hafiz' => $data['memorization_location_for_hafiz'] ?? 1,
        //     'teacher_name_for_hafiz' => $data['teacher_name_for_hafiz'] ?? 1,
        //     'taraweeh_recitation' => $data['taraweeh_recitation'] ?? 1,
        //     'tajweed_completion' => $data['tajweed_completion'] ?? 1,
        //     'schooling_completed' => $data['schooling_completed'] ?? 1,
        //     'computer_skills' => $data['computer_skills'] ?? 1,
        //     'writing_skills' => $data['writing_skills'] ?? 1,
        //     'skills_writing' => $data['skills_writing'] ?? 1,
        //     'spiritual_connection' => $data['spiritual_connection'] ?? 1,
        //     'status' => 1,
        //     'created_by' => Auth::user()->name,
        //     'created_date' => date('Y-m-d')
        // ]);
        // DB::table('employee_education_details')->insert($eedData);

        $experienceArray = $data['experienceArray'];
        foreach ($experienceArray as $eaRow) {
            $eeData = array([
                'employee_id' => $employeeId,
                'organization_name' => $data['organization_name_' . $eaRow . ''] ?? '-',
                'reason_of_resign' => $data['reason_of_resign_' . $eaRow . ''] ?? '-',
                'duration' => $data['duration_' . $eaRow . ''] ?? '-',
                'status' => 1,
                'created_by' => Auth::user()->name,
                'created_date' => date('Y-m-d')
            ]);
            DB::table('employee_experiences')->insert($eeData);
        }
        return $employeeDetail;
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
        $employee = $this->employeeRepository->findEmployee($id);
        $cities = DB::table('cities')->get();
        $departments = Department::status(1)->where('company_id', Session::get('company_id'))->get();
        $roles  = Role::all();
        $employeeEducationDetails = DB::table('employee_education_details')->where('employee_id', $id)->first();
        $employeeExperiences = DB::table('employee_experiences')->where('employee_id', $id)->get();
        $normalAllowance = DB::table('allowance_type as atype')
            ->leftJoin('employee_allowance_detail as ead', function ($join) use ($id) {
                $join->on('atype.id', '=', 'ead.allowance_id')
                    ->where('ead.employee_id', $id);
            })
            ->select('atype.*', 'ead.amount')
            ->where('atype.type', 1)
            ->where('atype.company_id', Session::get('company_id'))
            ->where('atype.company_location_id', Session::get('company_location_id'))
            ->get();
        $additionalAllowance = DB::table('allowance_type as atype')
            ->leftJoin('employee_allowance_detail as ead', function ($join) use ($id) {
                $join->on('atype.id', '=', 'ead.allowance_id')
                    ->where('ead.employee_id', $id);
            })
            ->select('atype.*', 'ead.amount')
            ->where('atype.type', 2)
            ->where('atype.company_id', Session::get('company_id'))
            ->where('atype.company_location_id', Session::get('company_location_id'))
            ->get();
        return view($this->page . 'edit', compact('employee', 'roles', 'employeeEducationDetails', 'departments', 'cities', 'employeeExperiences', 'normalAllowance', 'additionalAllowance'));
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
        // Validate request data
        $data = $request->validate([
            'emp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'emp_type' => 'required',
            'emp_name' => 'required',
            'emp_father_name' => 'required',
            'date_of_birth' => 'required',
            'cnic_no' => 'required',
            'address' => 'required',
            'emp_email' => 'required',
            'phone_no' => 'required',
            'maritarial_status' => 'required',
            'no_of_childern' => 'required',
            'relative_name' => 'required',
            'relative_contact_no' => 'required',
            'relative_address' => 'required',
            'login_access' => 'required',
            'grace_time' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'guardian_name' => 'required',
            'guardian_mobile_no' => 'required',
            'guardian_address' => 'required',
            'basic_salary' => 'required',
            'city_id' => 'required',
            'department_id' => 'required',
            'date_of_joining' => 'required',
            'employment_status' => '',
            'cnic_document.*' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'other_document.*' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Get registration number and current employee data
        $schoolId = Session::get('company_id');
        $schoolCampusId = Session::get('company_location_id');
        $registrationNo = Employee::RegistrationNo($schoolId, $schoolCampusId);
        $data['emp_no'] = $registrationNo;
        $employee = $this->employeeRepository->findEmployee($id);

        // Handle employee image upload
        if ($request->hasFile('emp_image')) {
            $this->handleImageUpload($request, $employee, $data, $registrationNo);
        }
        // Update employee data
        $this->employeeRepository->updateEmployee($data, $id);

        // Handle document uploads
        $this->handleDocumentsUpload($request, $id, $registrationNo);

        // Update education details
        $this->updateEducationDetails($request, $id);

        // Update experiences
        $this->updateExperiences($request, $id);

        // Handle user login access
        $this->handleUserAccess($request, $id);

        // Handle allowances
        $this->handleAllowances($request, $id);

        return redirect()->route('employees.index')->with('success', 'Employee Updated Successfully');
    }

    private function handleImageUpload(Request $request, $employee, &$data, $registrationNo)
    {
        $emp_image = $request->file('emp_image');
        if ($emp_image) {
            // Delete old image
            if (file_exists($employee->emp_image)) {
                unlink($employee->emp_image);
            }

            // Save new image
            $destinationPath = 'storage/app/public/EmployeeDocument/' . $registrationNo;
            $profileImageName = date('YmdHis') . "." . $emp_image->getClientOriginalExtension();
            $emp_image->move($destinationPath, $profileImageName);
            $data['emp_image'] = $destinationPath . '/' . $profileImageName;
        } else {
            $data['emp_image'] = $employee->emp_image; // Retain existing image if none uploaded
        }
    }

    private function handleDocumentsUpload(Request $request, $employeeId, $registrationNo)
    {

        if ($request->hasFile('cnic_document')) {
            $employeeDocuments = DB::table('employee_documents')->where('employee_id', $employeeId)->where('document_type', 1)->get();

            // Delete existing documents
            foreach ($employeeDocuments as $document) {
                if (file_exists($document->document_path)) {
                    unlink($document->document_path);
                }
                DB::table('employee_documents')->where('id', $document->id)->delete();
            }

            // Upload CNIC documents
            $this->uploadDocuments($request->file('cnic_document'), $employeeId, $registrationNo, 1);
        }

        if ($request->hasFile('other_document')) {
            $employeeDocuments = DB::table('employee_documents')->where('employee_id', $employeeId)->where('document_type', 1)->get();

            // Delete existing documents
            foreach ($employeeDocuments as $document) {
                if (file_exists($document->document_path)) {
                    unlink($document->document_path);
                }
                DB::table('employee_documents')->where('id', $document->id)->delete();
            }
            // Upload other documents
            $this->uploadDocuments($request->file('other_document'), $employeeId, $registrationNo, 2);
        }
    }

    // private function uploadDocuments($documents, $employeeId, $registrationNo, $documentType)
    // {
    //     if ($documents) {
    //         $directory = "EmployeeDocument/$registrationNo/" . ($documentType === 1 ? 'cnic_document' : 'other_document');
    //         Storage::disk('public')->makeDirectory($directory);

    //         $employeeDocuments = [];
    //         foreach ($documents as $document) {
    //             $imageName = date('YmdHis') . "." . $document->getClientOriginalExtension();
    //             $document->move("storage/app/public/$directory", $imageName);
    //             $employeeDocuments[] = [
    //                 'employee_id' => $employeeId,
    //                 'document_type' => $documentType,
    //                 'document_path' => "storage/app/public/$directory/$imageName",
    //                 'status' => 1,
    //                 'created_by' => Auth::user()->name,
    //                 'created_date' => now()->format('Y-m-d'),
    //             ];
    //         }
    //         DB::table('employee_documents')->insert($employeeDocuments);
    //     }
    // }

    private function updateEducationDetails(Request $request, $id)
    {
        $eedData = $request->only([
            'hafiz_status',
            'memorization_location_for_hafiz',
            'teacher_name_for_hafiz',
            'taraweeh_recitation',
            'tajweed_completion',
            'schooling_completed',
            'computer_skills',
            'writing_skills',
            'skills_writing',
            'spiritual_connection'
        ]);
        $eedData['status'] = 1;
        $eedData['created_by'] = Auth::user()->name;
        $eedData['created_date'] = now()->format('Y-m-d');

        DB::table('employee_education_details')->where('employee_id', $id)->update($eedData);
    }

    private function updateExperiences(Request $request, $id)
    {
        DB::table('employee_experiences')->where('employee_id', $id)->delete();

        $experienceArray = $request->input('experienceArray', []);
        foreach ($experienceArray as $eaRow) {
            $eeData = [
                'employee_id' => $id,
                'organization_name' => $request->input("organization_name_$eaRow", '-'),
                'reason_of_resign' => $request->input("reason_of_resign_$eaRow", '-'),
                'duration' => $request->input("duration_$eaRow", '-'),
                'status' => 1,
                'created_by' => Auth::user()->name,
                'created_date' => now()->format('Y-m-d'),
            ];
            DB::table('employee_experiences')->insert($eeData);
        }
    }

    private function handleUserAccess(Request $request, $id)
    {
        if ($request->input('login_access') == 2) {
            $user = User::where('emp_id', $id)->first();

            if ($user) {
                $user->update($this->getUserData($request, $id));
                if ($request->roles) {
                    $user->syncRoles($request->roles);
                }
            } else {
                $user = User::create($this->getUserData($request, $id));
                if ($request->roles) {
                    $user->assignRole($request->roles);
                }
            }
        }
    }

    private function getUserData(Request $request, $id)
    {
        return [
            'emp_id' => $id,
            'acc_type' => 'superadmin',
            'mobile_no' => $request->input('phone_no'),
            'cnic_no' => $request->input('cnic_no'),
            'name' => $request->input('emp_name'),
            'email' => $request->input('emp_email'),
            'password' => Hash::make('123456'),
            'username' => '-',
            'sgpe' => '-',
            'company_id' => Session::get('company_id'),
        ];
    }

    private function handleAllowances(Request $request, $id)
    {
        $normalAllowance = $request->input('normalAllowance', []);
        $additionalAllowance = $request->input('additionalAllowance', []);

        // Handle normal allowances
        $this->updateAllowances($normalAllowance, $request, $id, 1);

        // Handle additional allowances
        $this->updateAllowances($additionalAllowance, $request, $id, 2);
    }

    private function updateAllowances(array $allowances, Request $request, $id, $type)
    {
        DB::table('employee_allowance_detail')->where('type', $type)
            ->where('company_id', Session::get('company_id'))
            ->where('employee_id', $id)
            ->delete();

        foreach ($allowances as $allowanceId) {
            $naData = [
                'employee_id' => $id,
                'type' => $type,
                'allowance_id' => $allowanceId,
                'amount' => $request->input("normal_allowance_$allowanceId", 0),
                'status' => 1,
                'created_by' => Auth::user()->name,
                'created_date' => now()->format('Y-m-d'),
                'company_id' => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
            ];
            DB::table('employee_allowance_detail')->insert($naData);
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
        $this->employeeRepository->changeEmployeeStatus($id, 2);
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id)
    {
        $this->employeeRepository->changeEmployeeStatus($id, 1);
        return response()->json(['success' => 'Active Successfully!']);
    }
}
