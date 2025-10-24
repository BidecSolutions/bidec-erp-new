<?php
namespace App\Repositories\Interfaces;

Interface ChartOfAccountRepositoryInterface{

    public function allChartOfAccounts($data);
    public function storeChartOfAccount($data);
    public function findChartOfAccount($id);
    public function updateChartOfAccount($data, $id);
    public function changeChartOfAccountStatus($id,$status);
}
