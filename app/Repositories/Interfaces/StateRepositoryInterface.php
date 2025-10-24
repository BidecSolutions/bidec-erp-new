<?php
namespace App\Repositories\Interfaces;

Interface StateRepositoryInterface{

    public function allStates($data);
    public function storeState($data);
    public function findState($id);
    public function updateState($data, $id);
    public function changeStateStatus($id,$status);
}
