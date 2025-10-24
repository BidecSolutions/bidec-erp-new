<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;

use App\Helpers\CommonHelper;
use App\Models\LevelOfPerformance;
use App\Models\ParaOtherDetail;
use App\Models\StudentDayWisePerformance;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ParentController extends Controller
{
    public function dashboard(){
       return view('parent_module.dashboard');
    }

    public function comletedParasList(Request $request){
        if($request->ajax()){
            $studentId = $request->input('filter_student_id');
            $completedParasList = DB::table('student_performance_para_wise_view')
                ->when($studentId != '', function ($q) use ($studentId) {
                    return $q->where('student_id','=',$studentId);
                })
                ->get();
            return view('parent_module.comletedParasListAjax',compact('completedParasList'));
        }

        return view('parent_module.comletedParasList');
    }

    public function attendance_list(Request $request){
        if($request->ajax()){
             $studentId = $request->input('filter_student_id');
             $monthYear = $request->input('filter_month_year');
             if(empty($monthYear)){
                 $year = '';
                 $month = '';
             }else{
                 $explodeMonthYear = explode('-',$monthYear);
                 $year = $explodeMonthYear[0];
                 $month = $explodeMonthYear[1];
             }
             
             $attenlanceList = DB::table('student_day_wise_performances as sdwp')
                 ->join('students as s', 'sdwp.student_id', '=', 's.id')
                 ->join('student_parent_and_guardian_informations as spagi', 's.id', '=', 'spagi.student_id')
                 ->select(
                     's.id',
                     's.registration_no',
                     's.student_name',
                     'spagi.father_name',
                     'spagi.mobile_no',
                     DB::raw('COUNT(sdwp.id) as total_days'),
                     DB::raw('SUM(CASE WHEN sdwp.performance_activity_type = 1 THEN 1 ELSE 0 END) as present_days'),
                     DB::raw('SUM(CASE WHEN sdwp.performance_activity_type = 2 THEN 1 ELSE 0 END) as leave_days'),
                     DB::raw('SUM(CASE WHEN sdwp.performance_activity_type = 3 THEN 1 ELSE 0 END) as holidays_days')
                 )
                 ->when($year, function ($query) use ($year) {
                     return $query->whereYear('sdwp.performance_date', $year);
                 })
                 ->when($month, function ($query) use ($month) {
                     return $query->whereMonth('sdwp.performance_date', $month);
                 })
                 ->when($studentId, function ($query) use ($studentId) {
                     return $query->where('sdwp.student_id', $studentId);
                 })
                 ->groupBy('s.id', 's.registration_no', 's.student_name', 'spagi.father_name', 'spagi.mobile_no')
                 ->get();
             
             return view('parent_module.attendance_list_ajax',compact('attenlanceList','monthYear'));
         }
 
         return view('parent_module.attendance_list');
     }

     public function viewStudentPerformanceReport (Request $request){
        $getAllParas = CommonHelper::get_all_paras();
        $getAllStudents = CommonHelper::get_all_students();
        if($request->ajax()){
            $studentId = $request->input('studentId');
            $paraId = $request->input('paraId');
            
            $query = DB::table('student_current_paras as scp')
                ->select(
                    's.student_name', 
                    's.registration_no', 
                    'p.para_name', 
                    'scp.student_id', 
                    'scp.company_id',
                    'scp.company_location_id', 
                    'scp.para_id',
                    DB::raw('(SELECT COUNT(sdwp.id)
                            FROM student_day_wise_performances sdwp
                            INNER JOIN students s ON sdwp.student_id = s.id
                            WHERE scp.student_id = sdwp.student_id
                                AND scp.para_id = sdwp.para_id) as no_of_days'),
                    DB::raw('(SELECT SUM(sdwp.no_of_lines)
                            FROM student_day_wise_performances sdwp
                            INNER JOIN students s ON sdwp.student_id = s.id
                            WHERE scp.student_id = sdwp.student_id
                                AND scp.para_id = sdwp.para_id) as no_of_lines'),
                    DB::raw('(SELECT pod.total_lines_in_para
                            FROM para_other_details pod
                            WHERE pod.para_id = scp.para_id
                                AND pod.company_id = scp.company_id) as total_lines_in_para')
                )
                ->when($studentId != '', function ($q) use ($studentId) {
                    return $q->where('scp.student_id','=',$studentId);
                })
                ->when($paraId != '', function ($q) use ($paraId) {
                    return $q->where('scp.para_id','=',$paraId);
                })
                ->join('students as s', 'scp.student_id', '=', 's.id')
                ->join('paras as p', 'scp.para_id', '=', 'p.id')
                ->where('scp.para_status', 1);
                $query->havingRaw('no_of_days != 0');
                $query->havingRaw('no_of_lines != 0');
                $allStudentPerformancesReport = $query->get();
            return view('parent_module.studentPerformanceReportAjax',compact('allStudentPerformancesReport'));
        }
        return view('parent_module.studentPerformanceReport',compact('getAllParas','getAllStudents'));
    }

    public function viewMonthlyPerformanceReport(Request $request){
        if($request->ajax()){
            $company_id = Session::get('company_id');
            $studentId = $request->input('filter_student_id');
            $monthYear = $request->input('filter_month_year');
            $explodeMonthYear = explode("-",$monthYear);
            $month = $explodeMonthYear[1];
            $year = $explodeMonthYear[0];
            $startDate = date('Y-m-01', strtotime($monthYear));
            $endDate = date('Y-m-t', strtotime($monthYear));

            $date = new DateTime($monthYear);
            $date->modify('first day of previous month');
            $previousMonth = $date->format('Y-m');
            $pStartDate = $previousMonth.'-01';
            $pEndDate = date($previousMonth.'-t');
            $studentDetail = Student::select(
                'students.id',
                'students.registration_no',
                'students.date_of_admission',
                'students.date_of_birth',
                'students.student_name',
                'students.concession_fees',
                'student_parent_and_guardian_informations.father_name',
                'student_parent_and_guardian_informations.mobile_no',
                'student_parent_and_guardian_informations.parent_email',
                'student_parent_and_guardian_informations.father_occupation',
                'student_parent_and_guardian_informations.home_address',
                'departments.department_name',
                'departments.department_fees',
                'employees.emp_name',
                'classes.class_name',
                'emp.emp_name as nazim_name'
                
            )
            ->join('student_parent_and_guardian_informations', 'students.id', '=', 'student_parent_and_guardian_informations.student_id')
            ->leftJoin('classes', 'students.teacher_id', '=', 'classes.teacher_id')
            ->leftJoin('companies', 'students.company_id', '=', 'companies.id')
            ->leftJoin('employees as emp', 'companies.nazim_id', '=', 'emp.id')
            ->where('students.id',$studentId)
            ->first();
            $getSabqiPerformanceDetail = DB::table('sabqi_performances as sp')
                ->join('paras as p', 'sp.para_id', '=', 'p.id')
                ->join('level_of_performances as lop', 'sp.level_of_performance_id', '=', 'lop.id')
                ->whereRaw('sp.student_id = ? AND MONTH(sp.month_year) = ? AND YEAR(sp.month_year) = ?', [$studentId, $month, $year])
                ->select('p.para_no','p.para_name', 'lop.performance_name')
                ->get();
            $getManzilPerformanceDetail = DB::table('manzil_performance_datas as mpd')
                ->join('manzil_performances as mp', 'mpd.manzil_performance_id', '=', 'mp.id')
                ->join('paras as p', 'mpd.para_id', '=', 'p.id')
                ->join('level_of_performances as lop', 'mpd.level_of_performance_id', '=', 'lop.id')
                ->whereRaw('mp.student_id = ? AND MONTH(mp.month_year) = ? AND YEAR(mp.month_year) = ?', [$studentId, $month, $year])
                ->select('p.para_no', 'p.para_name', 'lop.performance_name')
                ->get();
            $getAdditionalActivityDetail = DB::table('additional_activity_datas as aad')
                ->join('additional_activities as aa', 'aad.additional_activity_id', '=', 'aa.id')
                ->join('heads as h', 'aad.head_id', '=', 'h.id')
                ->join('level_of_performances as lop', 'aad.level_of_performance_id', '=', 'lop.id')
                ->whereRaw('aa.student_id = ? AND MONTH(aa.month_year) = ? AND YEAR(aa.month_year) = ?', [$studentId, $month, $year])
                ->select('h.head_name', 'lop.performance_name','lop.marks')
                ->get();
            $sabaqSummaryInLines = DB::table('student_current_paras as scp')
                ->select('p.para_name','p.id', 'pod.total_lines_in_para', DB::raw('SUM(sdwp.no_of_lines) AS total_lines'))
                ->join('paras as p', 'scp.para_id', '=', 'p.id')
                ->join('para_other_details as pod', function ($join) {
                    $join->on('pod.para_id', '=', 'scp.para_id')
                        ->where('pod.company_id', '=', DB::raw('scp.company_id'));
                })
                ->leftJoin('student_day_wise_performances as sdwp', function ($join) {
                    $join->on('sdwp.para_id', '=', 'scp.para_id')
                        ->on('sdwp.student_id', '=', 'scp.student_id');
                })
                ->where('scp.para_status', 1)
                ->where('scp.student_id', $studentId)
                ->groupBy('p.para_name', 'pod.total_lines_in_para','p.id')
                ->first();
            $currentParaPriviousLines = DB::table('student_day_wise_performances')
                ->where('performance_date', '>=', $pStartDate)
                ->where('performance_date', '<=', $pEndDate)
                ->where('student_id', $studentId)
                ->where('para_id', $sabaqSummaryInLines->id)
                ->sum('no_of_lines');
            $levelOfPerformances = LevelOfPerformance::get();
            return view('parent_module.viewMonthlyPerformanceReportAjax',compact('studentDetail','month','year','getSabqiPerformanceDetail','getManzilPerformanceDetail','getAdditionalActivityDetail','sabaqSummaryInLines','monthYear','currentParaPriviousLines','levelOfPerformances'));
        }
        return view('parent_module.viewMonthlyPerformanceReport');
    }

    public function viewStudentperformancesShow(Request $request){
        $paramDetail = $request->get('id');
        $explodeParamDetail = explode('<*>',$paramDetail);
        $studentId = $explodeParamDetail[0];
        $paraId = $explodeParamDetail[1];
        $studentDetail = Student::select(
            'students.id',
            'students.registration_no',
            'students.date_of_admission',
            'students.date_of_birth',
            'students.student_name',
            'students.concession_fees',
            'student_parent_and_guardian_informations.father_name',
            'student_parent_and_guardian_informations.mobile_no',
            'student_parent_and_guardian_informations.parent_email',
            'student_parent_and_guardian_informations.father_occupation',
            'student_parent_and_guardian_informations.home_address',
            'departments.department_name',
            'departments.department_fees',
            'employees.emp_name',
            'classes.class_name',
            'emp.emp_name as nazim_name'
            
        )
        ->join('student_parent_and_guardian_informations', 'students.id', '=', 'student_parent_and_guardian_informations.student_id')
        ->leftJoin('classes', 'students.teacher_id', '=', 'classes.teacher_id')
        ->leftJoin('companies', 'students.company_id', '=', 'companies.id')
        ->leftJoin('employees as emp', 'companies.nazim_id', '=', 'emp.id')
        ->where('students.id',$studentId)
        ->first();
        $paraDetails = ParaOtherDetail::select(
            'paras.id',
            'paras.para_name',
            'para_other_details.total_lines_in_para',
            'para_other_details.estimated_completion_days',
            'para_other_details.excelent',
            'para_other_details.good',
            'para_other_details.average'
        )
        ->join('paras', 'para_other_details.para_id', '=', 'paras.id')
        ->where('para_other_details.para_id', $paraId)
        ->first();
        $studentPerformances = StudentDayWisePerformance::where('para_id', $paraId)
            ->where('student_id', $studentId)
            ->get();
        return view('parent_module.viewStudentPerformanceParaWiseDetail', compact('studentDetail','paraId'));
    }
}
