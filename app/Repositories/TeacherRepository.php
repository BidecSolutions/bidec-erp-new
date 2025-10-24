<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Models\Teacher;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class TeacherRepository implements TeacherRepositoryInterface
{

    public function allTeachers($data)
    {
        $departmentId = $data['filterDepartmentId'];
        $sectionId = $data['filterSectionId'];
        $status = $data['filterStatus'];
        return DB::table('teachers as t')
            ->select('t.*','s.section_name','d.department_name')
            ->join('sections as s','t.section_id','=','s.id')
            ->join('departments as d','t.department_id','=','d.id')
            ->when($departmentId != '', function ($q) use ($departmentId) {
                return $q->where('t.department_id','=',$departmentId);
            })
            ->when($sectionId != '', function ($q) use ($sectionId) {
                return $q->where('t.section_id','=',$sectionId);
            })
            ->when($status != '', function ($q) use ($status){
                return $q->where('t.status','=',$status);
            })
            ->where('t.company_id',Session::get('company_id'))
            ->where('t.company_location_id',Session::get('company_location_id'))
            ->orderBy('t.id', 'ASC')
            ->get();
    }

    public function storeTeacher($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        $teacher = Teacher::insert($data);

        $password = "teacher@glee_gather"; // Set default password
        $user = User::create([
            'name' => $teacher->teacher_name,
            'email' => $teacher->teacher_email,
            'password' => Hash::make($password),
            'role' => 'teacher'
        ]);



    }

    public function findTeacher($id)
    {
        return Teacher::find($id);
    }

    public function updateTeacher($data, $id)
    {
        $teacher = Teacher::where('id', $id)->update($data);
    }

    public function changeTeacherStatus($id,$status)
    {
        $teacher = Teacher::where('id',$id)->update(['status' => $status]);
    }
}
