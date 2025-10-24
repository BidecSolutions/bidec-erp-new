<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SubjectRepositoryInterface;
use App\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class SubjectRepository implements SubjectRepositoryInterface
{

    public function allSubjects($data)
    {
        return $getData = DB::table('subjects as s')
            ->join('sections as sec','s.section_id','=','sec.id')
            ->join('classes as c','sec.class_id','=','c.id')
            ->where('s.company_id',Session::get('company_id'))
            ->where('s.company_location_id',Session::get('company_location_id'))
            ->select('s.*','sec.section_name','c.class_no','c.class_name')
            ->get();
    }

    public function storeSubject($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return Subject::insert($data);
    }

    public function findSubject($id)
    {
        return Subject::find($id);
    }

    public function updateSubject($data, $id)
    {
        $subject = Subject::where('id', $id)->update($data);
    }

    public function changeSubjectStatus($id,$status)
    {
        $subject = Subject::where('id',$id)->update(['status' => $status]);
    }
}
