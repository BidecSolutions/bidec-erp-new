<?php
namespace App\Repositories\Interfaces;

Interface ParaRepositoryInterface{

    public function allParas($data);
    public function storePara($data);
    public function findPara($id);
    public function updatePara($data, $id);
    public function changeParaStatus($id,$status);
    public function storeAddOtherParaDetail($data);
    public function allParasOtherDetails();
}
