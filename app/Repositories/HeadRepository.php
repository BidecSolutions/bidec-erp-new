<?php

namespace App\Repositories;

use App\Repositories\Interfaces\HeadRepositoryInterface;
use App\Models\Head;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;

class HeadRepository implements HeadRepositoryInterface
{

    public function allHeads($data)
    {
        $status = $data['filterStatus'];
        return Head::status($status)->get();
    }

    public function storeHead($data)
    {
        return Head::create($data);
    }

    public function findHead($id)
    {
        return Head::find($id);
    }

    public function updateHead($data, $id)
    {
        $head = Head::where('id', $id)->update($data);
    }

    public function changeHeadStatus($id,$status)
    {
        $head = Head::where('id',$id)->update(['status' => $status]);
    }
}
