<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmployeeAttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;
use App\Models\Attendance;
use App\Models\Employee;
use Session;

class AttendanceController extends Controller
{
    public function __construct(Request $request)
    {
        $this->page = 'HR.attendances.';
        
    }

    public function index(Request $request){
        $startDate = $request->input('filter_from_date');
        $endDate = $request->input('filter_to_date');
        if($request->ajax()){
            $attendanceList = Attendance::select('attendances.date', 'attendances.clock_in', 'attendances.clock_out', 'e.id', 'e.emp_no', 'e.emp_name', 'e.grace_time', 'e.start_time', 'e.end_time')
                ->join('employees as e', 'attendances.emp_id', '=', 'e.id')
                ->where('attendances.company_id', Session::get('company_id'))
                ->where('attendances.company_location_id', Session::get('company_location_id'))
                ->where('attendances.type', 1)
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->get();
            return view($this->page.'indexAjax',compact('attendanceList'));
        }
        return view($this->page.'index');
    }

    public function monthlyAttendanceReport(Request $request){
        if($request->ajax()){
            return view($this->page.'monthlyAttendanceReportAjax',compact());
        }
        return view($this->page.'monthlyAttendanceReport');
    }

    public function import(){
        return view($this->page.'import');
    }

    public function store(Request $request){
        // $request->validate([
        //     'sample_file' => 'required|file|mimes:xlsx,xls', // Adjust validation rules as per your requirements
        // ]);
        if($request->hasFile('sample_file')){
            $path = $request->file('sample_file')->getRealPath();
            $data = Excel::import(new EmployeeAttendanceImport(), $path, null, ExcelType::XLSX);
        }
        return view($this->page.'index');
    }

    public function manualAttendanceStore(Request $request){
        
    }
}
