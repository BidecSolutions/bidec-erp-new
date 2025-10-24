<?php
namespace App\Repositories\Interfaces;

Interface ClassRepositoryInterface{

    public function allClasses($data);
    public function storeClass($data);
    public function findClass($id);
    public function updateClass($data, $id);
    public function changeClassStatus($id,$status);
}
