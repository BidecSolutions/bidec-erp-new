<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Models\Section;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;

class SectionRepository implements SectionRepositoryInterface
{

    public function allSections($data)
    {
        $status = $data['filterStatus'];
        return Section::where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->status($status)->get();
    }

    public function storeSection($data)
    {
        $data['company_id'] = Session::get('company_id');
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        $data['company_location_id'] = Session::get('company_location_id');
        return Section::insert($data);
    }

    public function findSection($id)
    {
        return Section::find($id);
    }

    public function updateSection($data, $id)
    {
        $section = Section::where('id', $id)->update($data);
    }

    public function changeSectionStatus($id,$status)
    {
        $section = Section::where('id',$id)->update(['status' => $status]);
    }
}
