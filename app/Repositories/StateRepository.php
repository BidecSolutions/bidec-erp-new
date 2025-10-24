<?php

namespace App\Repositories;

use App\Repositories\Interfaces\StateRepositoryInterface;
use App\Models\States;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class StateRepository implements StateRepositoryInterface
{

    public function allStates($data)
    {
        $status = $data['filterStatus'];
        return States::with(['country' => function ($query) {
            $query->get(['country_name']);
        },])->status($status)->where('company_id',Session::get('company_id'))->get();
    }

    public function storeState($data)
    {
        return States::create($data);
    }

    public function findState($id)
    {
        return States::find($id);
    }

    public function updateState($data, $id)
    {
        $state = States::where('id', $id)->update($data);
    }

    public function changeStateStatus($id,$status)
    {
        $state = States::where('id',$id)->update(['status' => $status]);
    }
}
