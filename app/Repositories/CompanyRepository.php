<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;

class CompanyRepository implements CompanyRepositoryInterface
{

    public function allCompanies($data)
    {
        $status = $data['filterStatus'];
        return Company::status($status)
        ->with(['nazim' => function ($query) {
            $query->get(['emp_name']);
        },])
        ->with(['naibnazim' => function ($query) {
            $query->get(['emp_name']);
        },])
        ->with(['moavin' => function ($query) {
            $query->get(['emp_name']);
        },])
        ->get();
    }

    public function storeCompany($data)
    {
        $data['accounting_year'] = 0;
        $data['dbName'] = '-';
        $data['db_username'] = '-';
        $data['db_password'] = '-';
        $data['msg_footer'] = '-';
        $data['sms_service_provider'] = '-';
        $data['masking_url'] = '-';
        $data['masking_name'] = '-';
        $data['masking_id'] = '-';
        $data['masking_password'] = '-';
        $data['masking_key'] = '-';
        $data['nazim_e_talimat'] = '-';
        $data['nazim_id'] = '-';
        $data['naib_nazim_id'] = '-';
        $data['moavin_id'] = '-';
        $data['longitude'] = 0;
        $data['latitude'] = 0;
        $data['status'] = 1;
        $data['username'] = Auth::user()->name;
        $data['date'] = date('Y-m-d');
        $data['time'] = date('H:i:s');
        return Company::insert($data);
    }

    public function findCompany($id)
    {
        return Company::find($id);
    }

    public function updateCompany($data, $id)
    {
        $company = Company::where('id', $id)->update($data);
    }

    public function changeCompanyStatus($id,$status)
    {
        $company = Company::where('id',$id)->update(['status' => $status]);
    }
}
