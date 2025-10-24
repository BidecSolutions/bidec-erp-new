<?php

namespace App\Repositories;

use App\Models\AcademicDetail;
use App\Repositories\Interfaces\AcademicDetailRepositoryInterface;
use App\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class AcademicDetailRepository implements AcademicDetailRepositoryInterface
{

    public function allAcademicDetail($data)
    {
        $getData = AcademicDetail::where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))
            ->with('academicStatus')
            ->get();

        return $getData;

    }

    public function storeAcademicDetail($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return AcademicDetail::insert($data);
    }

    public function findAcademicDetail($id)
    {
        return AcademicDetail::find($id);
    }

    public function updateAcademicDetail($data, $id)
    {
        $subject = AcademicDetail::where('id', $id)->update($data);
    }

    public function changeAcademicDetailStatus($id, $status)
    {
        $subject = AcademicDetail::where('id', $id)->update(['status' => $status]);
    }
}
