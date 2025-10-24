<?php
namespace App\Repositories\Interfaces;

Interface LevelOfPerformanceRepositoryInterface{

    public function allLevelOfPerformances($data);
    public function storeLevelOfPerformance($data);
    public function findLevelOfPerformance($id);
    public function updateLevelOfPerformance($data, $id);
    public function changeLevelOfPerformanceStatus($id,$status);
}
