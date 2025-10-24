<?php
namespace App\Repositories\Interfaces;

Interface SectionRepositoryInterface{

    public function allSections($data);
    public function storeSection($data);
    public function findSection($id);
    public function updateSection($data, $id);
    public function changeSectionStatus($id,$status);
}
