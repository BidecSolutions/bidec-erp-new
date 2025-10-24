<?php
namespace App\Repositories\Interfaces;

Interface StudentRepositoryInterface{

    public function allStudents($data);
    public function storeStudent($data);
    public function findStudent($id);
    public function updateStudent($data, $id);
    public function changeStudentStatus($id,$status);
    public function getCurrentParaDetail($id);
    public function updateCurrentParaDetail($data,$id,$priviousParaId);
}
