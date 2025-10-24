<?php
namespace App\Repositories\Interfaces;

Interface CountryRepositoryInterface{

    public function allCountries($data);
    public function storeCountry($data);
    public function findCountry($id);
    public function updateCountry($data, $id);
    public function changeCountryStatus($id,$status);
}
