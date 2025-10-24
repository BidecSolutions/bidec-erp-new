<?php
namespace App\Repositories\Interfaces;

Interface EmployeeRepositoryInterface{

    public function allEmployees($data);
    public function storeEmployee($data);
    public function findEmployee($id);
    public function updateEmployee($data, $id);
    public function changeEmployeeStatus($id,$status);
}
