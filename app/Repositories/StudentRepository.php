<?php

namespace App\Repositories;

use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Models\Student;
use App\Models\User;
use Hash;
use App\Models\Student_document_against_registration;
use App\Models\Student_parent_and_guardian_information;
use App\Models\StudentCurrentPara;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

use App\Mail\StudentCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentRepository implements StudentRepositoryInterface
{

    public function allStudents($data)
    {
        $status = $data['filterStatus'];
        return DB::table('students as s')
            ->join('student_parent_and_guardian_informations as spagi','spagi.student_id','=','s.id')
            ->join('student_document_against_registrations as sdar','sdar.student_id','=','s.id')
            ->select('s.id','s.registration_no','s.student_name','s.date_of_admission','s.status','s.suspended',
            'spagi.father_name','spagi.parent_email','spagi.mobile_no','spagi.cnic_no','sdar.birth_certificate',
            'sdar.father_guardian_cnic','sdar.father_guardian_cnic_back','sdar.passport_size_photo','sdar.copy_of_last_report')
            ->where('s.company_id',Session::get('company_id'))
            ->where('s.company_location_id',Session::get('company_location_id'))
            ->when($status != '', function ($q) use ($status){
                return $q->where('s.status','=',$status);
            })
            ->orderBy('s.id', 'desc')
            ->get();
    }

    public function storeStudent($data)
    {
        $registrationNo = Student::RegistrationNo();
        $explodeRegistrationNo = explode('/',$registrationNo);

        $registrationCode = $explodeRegistrationNo[2];

        $student['company_id'] = Session::get('company_id');
        //$student['section_id'] = $data['section_id'];
        $student['registration_no'] = $registrationNo;
        $student['registration_code'] = $registrationCode;
        $student['date_of_admission'] = $data['date_of_admission'];
        $student['student_name'] = $data['student_name'];
        $student['date_of_birth'] = $data['date_of_birth'];
        $student['login_access'] = 1;
        $student['previous_school'] = $data['previous_school'] ?? '-';
        $student['grade_class_applied_for'] = '-';
        $student['reference'] = $data['reference'] ?? '-';
        $student['class_id'] = $data['class_id'] ?? 0;
        $student['status'] = 1;
        $student['created_by'] = Auth::user()->name;
        $student['created_date'] = date('Y-m-d');
        $student['company_location_id'] = Session::get('company_location_id');

        $studentId = Student::insertGetId($student);

        // if($data['login_access'] == 2){
        //     $password = Str::random(10);
        //     $user = User::create([
        //         'student_id' => $studentId,
        //         'acc_type' => 'user',
        //         'mobile_no' => $data['mobile_no'],
        //         'cnic_no' => '-',
        //         'name' => $data['student_name'],
        //         'email' => $registrationNo,
        //         'password' => bcrypt($password),
        //         'username' => '-',
        //         'sgpe' => $password,
        //         'company_id' => Session::get('company_id'),
        //         'company_location_id' => Session::get('company_location_id')
        //     ]);

        //     if ($data['roles']) {
        //         $user->assignRole($data['roles']);
        //     }
        // }



        $spagi['student_id'] = $studentId;
        $spagi['city_id'] = $data['city_id'];
        $spagi['father_name'] = $data['father_name'];
        $spagi['mother_name'] = $data['mother_name'];
        $spagi['father_qualification'] = $data['father_qualification'];
        $spagi['mother_qualification'] = $data['mother_qualification'] ?? '-';
        $spagi['cnic_no'] = $data['cnic_no'];
        $spagi['mobile_no'] = $data['mobile_no'];
        $spagi['parent_email'] = $data['parent_email'] ?? '-';
        $spagi['father_occupation'] = $data['father_occupation'];
        $spagi['mother_tongue'] = $data['mother_tongue'];
        $spagi['home_address'] = $data['home_address'];
        $spagi['specify_any_health_problem_medication'] = $data['specify_any_health_problem_medication'] ?? '-';
        $spagi['status'] = 1;
        $spagi['created_by'] = Auth::user()->name;
        $spagi['created_date'] = date('Y-m-d');

        Student_parent_and_guardian_information::insert($spagi);

        $sdar['student_id'] = $studentId;
        $sdar['birth_certificate'] = $data['birth_certificate'];
        $sdar['father_guardian_cnic'] = $data['father_guardian_cnic'];
        $sdar['father_guardian_cnic_back'] = $data['father_guardian_cnic_back'];
        $sdar['passport_size_photo'] = $data['passport_size_photo'];
        $sdar['copy_of_last_report'] = '-';
        $sdar['status'] = 1;
        $sdar['created_by'] = Auth::user()->name;
        $sdar['created_date'] = date('Y-m-d');

        Student_document_against_registration::insert($sdar);

        // if($data['parent_access'] == 2){
        //     $checkParentAccount = DB::table('users')->where('email',$data['cnic_no'])->where('cnic_no',$data['cnic_no'])->where('acc_type','parent')->first();
        //     if(empty($checkParentAccount)){
        //         $password = Str::random(10);
        //         $parentUser = User::create([
        //             'student_id' => null,
        //             'acc_type' => 'parent',
        //             'mobile_no' => $data['mobile_no'],
        //             'cnic_no' => $data['cnic_no'],
        //             'name' => $data['father_name'],
        //             'email' => $data['cnic_no'],
        //             'password' => bcrypt($password),
        //             'username' => '-',
        //             'sgpe' => $password,
        //             'company_id' => Session::get('company_id'),
        //             'company_location_id' => Session::get('company_location_id'),
        //         ]);

        //         $parentAccountId = $parentUser->id;
        //     }else{
        //         $parentAccountId = $checkParentAccount->id;
        //     }


        //     $studentList = DB::table('students as s')
        //         ->select('s.id','s.registration_no')
        //         ->leftJoin('student_parent_and_guardian_informations as spagi','spagi.student_id','=','s.id')
        //         ->where('spagi.cnic_no',$data['cnic_no'])
        //         ->get();

        //     $studentIdsArrayForParents = [];
        //     foreach ($studentList as $slRow) {
        //         $studentIdsArrayForParents[] = [
        //             'student_id' => $slRow->id,
        //             'registration_no' => $slRow->registration_no,
        //         ];
        //     }

        //     DB::table('users')->where('id',$parentAccountId)->update(['student_ids_array_for_parents' => json_encode($studentIdsArrayForParents)]);

        //     if ($data['parent_roles']) {
        //         $user->assignRole($data['parent_roles']);
        //     }

        // }
    }

    public function findStudent($id)
    {
        return Student::find($id);
    }

    public function getCurrentParaDetail($id){
        return DB::table('students as s')
            ->leftJoin('student_current_paras as scp','scp.student_id','=','s.id')
            ->leftJoin('paras as p','scp.para_id','=','p.id')
            ->select('s.id as studentId','s.registration_no','s.student_name','scp.para_id as para_id','p.para_name','scp.id')
            ->where('s.id',$id)
            ->first();
    }

    public function updateCurrentParaDetail($data,$id,$priviousParaId){
        if(!empty($priviousParaId)){
            DB::table('student_current_paras')->where('para_id',$priviousParaId)->where('student_id',$id)->update(['para_status' => 2]);
        }

        $data['company_id'] = Session::get('company_id');
        $data['status'] = 1;
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');

        StudentCurrentPara::insert($data);
    }

    public function updateStudent($data, $id)
    {
        $student['section_id'] = $data['section_id'];
        $student['date_of_admission'] = $data['date_of_admission'];
        $student['student_name'] = $data['student_name'];
        $student['date_of_birth'] = $data['date_of_birth'];
        $student['previous_school'] = $data['previous_school'] ?? '-';
        $student['grade_class_applied_for'] = $data['grade_class_applied_for'];
        $student['reference'] = $data['reference'] ?? '-';
        $student['status'] = 1;
        $student['created_by'] = Auth::user()->name;
        $student['created_date'] = date('Y-m-d');
        $student['company_location_id'] = Session::get('company_location_id');

        Student::where('id',$id)->update($student);

        $spagi['city_id'] = $data['city_id'];
        $spagi['father_name'] = $data['father_name'];
        $spagi['mother_name'] = $data['mother_name'];
        $spagi['father_qualification'] = $data['father_qualification'];
        $spagi['mother_qualification'] = $data['mother_qualification'] ?? '-';
        $spagi['cnic_no'] = $data['cnic_no'];
        $spagi['mobile_no'] = $data['mobile_no'];
        $spagi['parent_email'] = $data['parent_email'] ?? '-';
        $spagi['father_occupation'] = $data['father_occupation'];
        $spagi['mother_tongue'] = $data['mother_tongue'];
        $spagi['home_address'] = $data['home_address'];
        $spagi['specify_any_health_problem_medication'] = $data['specify_any_health_problem_medication'] ?? '-';
        $spagi['status'] = 1;
        $spagi['created_by'] = Auth::user()->name;
        $spagi['created_date'] = date('Y-m-d');

        Student_parent_and_guardian_information::where('student_id',$id)->update($spagi);
        //$student = Student::where('id', $id)->update($data);
    }

    public function changeStudentStatus($id,$status)
    {
        $student = Student::where('id',$id)->update(['status' => $status]);
    }
}
