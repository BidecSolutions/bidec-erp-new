<?php

namespace App\Repositories;

use App\Models\AcademicStatus;
use App\Repositories\Interfaces\AcademicStatusRepositoryInterface;
use App\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class AcademicStatusRepository implements AcademicStatusRepositoryInterface
{

    public function allAcademicStatus($data)
    {
        return $getData = DB::table('academic_status')
            ->where('company_id',Session::get('company_id'))
            ->where('company_location_id',Session::get('company_location_id'))
            ->get();
    }

    public function storeAcademicStatus($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return AcademicStatus::insert($data);
    }

    public function findAcademicStatus($id)
    {
        return AcademicStatus::find($id);
    }

    public function updateAcademicStatus($data, $id)
    {
        $subject = AcademicStatus::where('id', $id)->update($data);
    }

    public function changeAcademicStatusStatus($id,$status)
    {
        $subject = AcademicStatus::where('id',$id)->update(['status' => $status]);
    }
}
