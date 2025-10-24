<?php
namespace App\Repositories\Interfaces;

Interface DepartmentRepositoryInterface{

    public function allDepartments($data);
    public function storeDepartment($data);
    public function findDepartment($id);
    public function updateDepartment($data, $id);
    public function changeDepartmentStatus($id,$status);
}
