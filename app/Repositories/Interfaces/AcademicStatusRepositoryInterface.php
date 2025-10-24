<?php
namespace App\Repositories\Interfaces;

Interface AcademicStatusRepositoryInterface{

    public function allAcademicStatus($data);
    public function storeAcademicStatus($data);
    public function findAcademicStatus($id);
    public function updateAcademicStatus($data, $id);
    public function changeAcademicStatusStatus($id,$status);
}
