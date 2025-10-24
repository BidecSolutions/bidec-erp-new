<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function allEmployees($data)
    {
        $status = $data['filterStatus'];
        $empType = $data['filterEmpType'];
        $departments = $data['filterDepartments'];
        return DB::table('employees as e')
            ->select('e.*')
            ->when($status != '', function ($q) use ($status){
                return $q->where('e.status','=',$status);
            })
            ->when($empType != '', function ($q) use ($empType){
                return $q->where('e.emp_type','=',$empType);
            })
            ->when($departments != '', function ($q) use ($departments){
                return $q->where('e.department_id','=',$departments);
            })
            ->where('e.company_id',Session::get('company_id'))
            ->where('e.company_location_id',Session::get('company_location_id'))
            ->orderBy('e.id', 'ASC')
            ->get();
    }

    public function storeEmployee($data)
    {
        return Employee::create($data);
        
    }

    public function findEmployee($id)
    {
        return Employee::find($id);
    }

    public function updateEmployee($data, $id)
    {
        $employee = Employee::where('id', $id)->update($data);
    }

    public function changeEmployeeStatus($id,$status)
    {
        $employee = Employee::where('id',$id)->update(['status' => $status]);
    }
}
