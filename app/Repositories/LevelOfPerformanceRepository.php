<?php

namespace App\Repositories;

use App\Repositories\Interfaces\LevelOfPerformanceRepositoryInterface;
use App\Models\LevelOfPerformance;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;

class LevelOfPerformanceRepository implements LevelOfPerformanceRepositoryInterface
{

    public function allLevelOfPerformances($data)
    {
        $status = $data['filterStatus'];
        return LevelOfPerformance::status($status)->get();
    }

    public function storeLevelOfPerformance($data)
    {
        return LevelOfPerformance::create($data);
    }

    public function findLevelOfPerformance($id)
    {
        return LevelOfPerformance::find($id);
    }

    public function updateLevelOfPerformance($data, $id)
    {
        $levelOfPerformance = LevelOfPerformance::where('id', $id)->update($data);
    }

    public function changeLevelOfPerformanceStatus($id,$status)
    {
        $levelOfPerformance = LevelOfPerformance::where('id',$id)->update(['status' => $status]);
    }
}
