<?php
namespace App\Repositories\Interfaces;

Interface TeacherRepositoryInterface{

    public function allTeachers($data);
    public function storeTeacher($data);
    public function findTeacher($id);
    public function updateTeacher($data, $id);
    public function changeTeacherStatus($id,$status);
}
