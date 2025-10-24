<?php
namespace App\Repositories\Interfaces;

Interface ClassTimingRepositoryInterface{

    public function allClassTimings($data);
    public function storeClassTiming($data);
    public function findClassTiming($id);
    public function updateClassTiming($data, $id);
    public function changeClassTimingStatus($id,$status);
}
