<?php
namespace App\Repositories\Interfaces;

Interface LocationRepositoryInterface{

    public function allLocations($data);
    public function findLocation($id);
    //public function updateLocation($data, $id);
    public function changeLocationStatus($id,$status);
}
