<?php
namespace App\Repositories\Interfaces;

Interface CityRepositoryInterface{

    public function allCities($data);
    public function storeCity($data);
    public function findCity($id);
    public function updateCity($data, $id);
    public function changeCityStatus($id,$status);
}
