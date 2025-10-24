<?php

namespace App\Repositories;

use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class DepartmentRepository implements DepartmentRepositoryInterface
{

    public function allDepartments($data)
    {
        $status = $data['filterStatus'];
        return Department::select(
            'departments.*'
        )
        ->when($status != '', function ($q) use ($status){
            return $q->where('departments.status','=',$status);
        })
        ->where('departments.company_id', Session::get('company_id'))
        ->get();
    }

    public function storeDepartment($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['status'] = 1;
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return Department::insert($data);
    }

    public function findDepartment($id)
    {
        return Department::find($id);
    }

    public function updateDepartment($data, $id)
    {
        $department = Department::where('id', $id)->update($data);
    }

    public function changeDepartmentStatus($id,$status)
    {
        $department = Department::where('id',$id)->update(['status' => $status]);
    }
}
