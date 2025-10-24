<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ClassTimingRepositoryInterface;
use App\Models\ClassTimings;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class ClassTimingRepository implements ClassTimingRepositoryInterface
{

    public function allClassTimings($data)
    {
        return ClassTimings::where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->get();
    }

    public function storeClassTiming($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return ClassTimings::insert($data);
    }

    public function findClassTiming($id)
    {
        return ClassTimings::find($id);
    }

    public function updateClassTiming($data, $id)
    {
        $class = ClassTimings::where('id', $id)->first();
        $class->name = $data['name'];
        $class->save();
    }

    public function changeClassTimingStatus($id,$status)
    {
        $classTiming = ClassTimings::where('id',$id)->update(['status' => $status]);
    }
}
