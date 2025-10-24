<?php
namespace App\Repositories\Interfaces;

Interface StudentPerformanceRepositoryInterface{
    
    public function getUpdatedParaStudentList($data);
    public function storeStudentPerformance($data);
    public function allStudentPerformances($data);
    public function allStudentPerformancesReport($data);
    // public function storeStudent($data);
    // public function findStudent($id);
    // public function updateStudent($data, $id); 
    // public function destroyStudent($id);
    // public function getCurrentParaDetail($id);
    // public function updateCurrentParaDetail($data,$id);
}