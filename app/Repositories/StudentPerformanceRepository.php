<?php

namespace App\Repositories;

use App\Repositories\Interfaces\StudentPerformanceRepositoryInterface;
use App\Models\StudentDayWisePerformance;
use App\Models\StudentCurrentPara;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class StudentPerformanceRepository implements StudentPerformanceRepositoryInterface
{

    public function getUpdatedParaStudentList($data){
        $teacherId = $data['teacherId'];
        $studentId = $data['studentId'];
        $schoolId = Session::get('company_id');
        $schoolCampusId = Session::get('company_location_id');
        return DB::table('student_current_paras as scp')
        ->select(
            's.registration_no',
            'scp.student_id',
            'scp.id',
            'scp.para_id',
            's.student_name',
            'p.para_name',
            'pod.total_lines_in_para',
            DB::raw('(select SUM(no_of_lines) FROM student_day_wise_performances as sdwp WHERE sdwp.student_id = scp.student_id and sdwp.para_id = scp.para_id) as total_lines_learned') 
        )
        ->join('students as s', 'scp.student_id', '=', 's.id')
        ->join('paras as p', 'scp.para_id', '=', 'p.id')
        ->join('para_other_details as pod', 'pod.para_id', '=', 'p.id')
        ->when($studentId != '', function ($q) use ($studentId) {
            return $q->where('scp.student_id','=',$studentId);
        })
        ->when($teacherId != '', function ($q) use ($teacherId) {
            return $q->where('s.teacher_id','=',$teacherId);
        })
        ->where('scp.company_id', $schoolId)
        ->where('scp.company_location_id', $schoolCampusId)
        ->where('pod.company_id', $schoolId)
        ->where('pod.company_location_id', $schoolCampusId)
        ->where('scp.status', 1)
        ->where('scp.para_status', 1)
        ->get();
    }

    public function storeStudentPerformance($data){
        $recordArray = $data['recordArray'];
        foreach($recordArray as $row){
            
            if (isset($data['spRecordArray_'.$row.''])) {
                $spRecordArray = $data['spRecordArray_'.$row.''];
                $remainingLines = $data['remainingLines_'.$row.''];
                $scp_id = $data['scp_id_'.$row.''];
                $totalLines = 0;
                foreach($spRecordArray as $row2){
                    $noOfLines = $data['no_of_lines_'.$row.'_'.$row2.''];
                    if($noOfLines == 'Holiday'){
                        $data2['performance_activity_type'] = 3;
                        $data2['no_of_lines'] = 0;
                    }else if($noOfLines == 'Leave'){
                        $data2['performance_activity_type'] = 2;
                        $data2['no_of_lines'] = 0;
                    }else{
                        $totalLines += $noOfLines;
                        $data2['performance_activity_type'] = 1;
                        $data2['no_of_lines'] = $noOfLines;
                    }
                    
                    $data2['company_id'] = Session::get('company_id');
                    $data2['student_id'] = $data['student_id_'.$row.''];
                    $data2['para_id'] = $data['para_id_'.$row.''];
                    $data2['performance_date'] = $data['performance_date_'.$row.'_'.$row2.''];
                    $data2['status'] = 1;
                    $data2['created_by'] = Auth::user()->name;
                    $data2['date'] = date('Y-m-d');
                    $data2['company_location_id'] = Session::get('company_location_id');
                    StudentDayWisePerformance::insert($data2);
                }
                if($remainingLines == $totalLines){
                    StudentCurrentPara::where('id',$scp_id)->update(['para_status' => 2]);
                }
            }else{
                echo 'Empty';
            }
        }
    }

    public function allStudentPerformances($data){
        $studentId = $data['studentId'];
        $paraId = $data['paraId'];
        $schoolId = Session::get('company_id');
        $fromDate = $data['fromDate'];
        $toDate = $data['toDate'];
        return DB::table('student_day_wise_performances as sdwp')
            ->select('sdwp.*','s.registration_no','s.student_name','p.para_name')
            ->join('students as s','sdwp.student_id','=','s.id')
            ->join('paras as p','sdwp.para_id','=','p.id')
            ->when($studentId != '', function ($q) use ($studentId) {
                return $q->where('sdwp.student_id','=',$studentId);
            })
            ->when($paraId != '', function ($q) use ($paraId) {
                return $q->where('sdwp.para_id','=',$paraId);
            })
            ->whereBetween('sdwp.performance_date',[$fromDate,$toDate])
            ->where('sdwp.company_id',Session::get('company_id'))
            ->where('sdwp.company_location_id',Session::get('company_location_id'))
            ->get();
    }
    public function allStudentPerformancesReport($data){
        $studentId = $data['studentId'];
        $paraId = $data['paraId'];
        $schoolId = Session::get('company_id');

        $query = DB::table('student_current_paras as scp')
            ->select(
                's.student_name', 
                's.registration_no', 
                'p.para_name', 
                'scp.student_id', 
                'scp.company_id', 
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
            ->where('scp.company_id',Session::get('company_id'))
            ->where('scp.company_location_id',Session::get('company_location_id'))
            ->join('students as s', 'scp.student_id', '=', 's.id')
            ->join('paras as p', 'scp.para_id', '=', 'p.id')
            ->where('scp.para_status', 1);
            $query->havingRaw('no_of_days != 0');
            $query->havingRaw('no_of_lines != 0');
            return $results = $query->get();

        // return DB::table('student_day_wise_performances as sdwp')
        //     ->select('s.registration_no','s.student_name','p.para_name','sdwp.student_id','sdwp.para_id')
        //     ->join('students as s','sdwp.student_id','=','s.id')
        //     ->join('paras as p','sdwp.para_id','=','p.id')
        //     ->when($studentId != '', function ($q) use ($studentId) {
        //         return $q->where('sdwp.student_id','=',$studentId);
        //     })
        //     ->when($paraId != '', function ($q) use ($paraId) {
        //         return $q->where('sdwp.para_id','=',$paraId);
        //     })
        //     ->where('sdwp.company_id',Session::get('company_id'))
        //     ->groupBy(['sdwp.student_id','sdwp.para_id','s.registration_no','s.student_name','p.para_name'])
        //     ->paginate(25);
    }
}
