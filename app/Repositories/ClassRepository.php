<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ClassRepositoryInterface;
use App\Models\Classes;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class ClassRepository implements ClassRepositoryInterface
{

    public function allClasses($data)
    {
        return DB::table('classes as c')
        ->select('c.*')
        ->where('c.company_id',Session::get('company_id'))
        ->where('c.company_location_id',Session::get('company_location_id'))
        ->get();
    }

    public function storeClass($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return Classes::insert($data);
    }

    public function findClass($id)
    {
        return Classes::find($id);
    }

    public function updateClass($data, $id)
    {
        $class = Classes::where('id', $id)->update($data);
    }

    public function changeClassStatus($id,$status)
    {
        $class = Classes::where('id',$id)->update(['status' => $status]);
    }
}
