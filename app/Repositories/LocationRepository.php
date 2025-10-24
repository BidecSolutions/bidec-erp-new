<?php

namespace App\Repositories;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Models\CompanyLocations;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LocationRepository implements LocationRepositoryInterface
{

    public function allLocations($data)
    {
        $status = $data['filterStatus'];
        return CompanyLocations::status($status)
            ->join('companies as c', 'company_locations.company_id', '=', 'c.id')
            ->where('company_locations.company_id', Session::get('company_id'))
            ->select('company_locations.*', 'c.name as company_name')
            ->get();
    }

    public function findLocation($id)
    {
        return CompanyLocations::find($id);
    }

    public function changeLocationStatus($id, $status)
    {
        $updated = CompanyLocations::where('id', $id)->update(['status' => $status]);
          Log::info("Change status for ID: $id to $status, Rows updated: $updated");
        return $updated;
    }
}
