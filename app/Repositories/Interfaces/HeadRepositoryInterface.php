<?php
namespace App\Repositories\Interfaces;

Interface HeadRepositoryInterface{

    public function allHeads($data);
    public function storeHead($data);
    public function findHead($id);
    public function updateHead($data, $id);
    public function changeHeadStatus($id,$status);
}
