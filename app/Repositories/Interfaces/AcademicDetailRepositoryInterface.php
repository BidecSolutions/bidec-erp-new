<?php
namespace App\Repositories\Interfaces;

Interface AcademicDetailRepositoryInterface{

    public function allAcademicDetail($data);
    public function storeAcademicDetail($data);
    public function findAcademicDetail($id);
    public function updateAcademicDetail($data, $id);
    public function changeAcademicDetailStatus($id,$status);
}
