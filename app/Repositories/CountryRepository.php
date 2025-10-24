<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CountryRepositoryInterface;
use App\Models\Country;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;

class CountryRepository implements CountryRepositoryInterface
{

    public function allCountries($data)
    {
        $status = $data['filterStatus'] ?? 1;
        return Country::status($status)->where('company_id', Session::get('company_id'))->get();
    }

    public function storeCountry($data)
    {
        return Country::create($data);
    }

    public function findCountry($id)
    {
        return Country::find($id);
    }

    public function updateCountry($data, $id)
    {
        $country = Country::where('id', $id)->update($data);
    }

    public function changeCountryStatus($id,$status)
    {
        $country = Country::where('id',$id)->update(['status' => $status]);
    }
}
