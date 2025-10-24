<?php
namespace App\Repositories\Interfaces;

Interface SubjectRepositoryInterface{

    public function allSubjects($data);
    public function storeSubject($data);
    public function findSubject($id);
    public function updateSubject($data, $id);
    public function changeSubjectStatus($id,$status);
}
