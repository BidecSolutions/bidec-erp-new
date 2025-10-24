<?php
namespace App\Repositories\Interfaces;

Interface CompanyRepositoryInterface{

    public function allCompanies($data);
    public function storeCompany($data);
    public function findCompany($id);
    public function updateCompany($data, $id);
    public function changeCompanyStatus($id,$status);
}
