<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CityRepositoryInterface;
use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class CityRepository implements CityRepositoryInterface
{

    public function allCities($data)
    {
        $status = $data['filterStatus'];
        return City::with(['state' => function ($query) {
            $query->get(['state_name']);
        },])->status($status)->where('company_id',Session::get('company_id'))->get();
    }

    public function storeCity($data)
    {
        date_default_timezone_set("Asia/Karachi");
        return City::create($data);
    }

    public function findCity($id)
    {
        return City::find($id);
    }

    public function updateCity($data, $id)
    {
        $city = City::where('id', $id)->update($data);
    }

    public function changeCityStatus($id,$status)
    {
        $city = City::where('id',$id)->update(['status' => $status]);
    }
}
