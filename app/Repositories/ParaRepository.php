<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ParaRepositoryInterface;
use App\Models\Paras;
use App\Models\ParaOtherDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Auth;
use DB;

class ParaRepository implements ParaRepositoryInterface
{

    public function allParas($data)
    {
        $status = $data['filterStatus'];
        return Paras::status($status)->get();
    }

    public function storePara($data)
    {
        $data['created_by'] = Auth::user()->name;
        $data['created_date'] = date('Y-m-d');
        return Paras::insert($data);
    }

    public function findPara($id)
    {
        return Paras::find($id);
    }

    public function updatePara($data, $id)
    {
        $para = Paras::where('id', $id)->update($data);
    }

    public function changeParaStatus($id,$status)
    {
        $para = Paras::where('id',$id)->update(['status' => $status]);
    }

    public function storeAddOtherParaDetail($data){

        $paraArray = $data['paraArrays'];
        foreach($paraArray as $paRow){
            $podId = $data['podId_'.$paRow.''];
            $changeValues = $data['change_value_'.$paRow.''];
            if($changeValues == 2){
                if(!empty($podId)){
                    $data2['company_id'] = Session::get('company_id');
                    $data2['company_location_id'] = Session::get('company_location_id');
                    $data2['para_id'] = $paRow;
                    $data2['total_lines_in_para'] = $data['total_lines_in_para_'.$paRow.''];
                    $data2['estimated_completion_days'] = $data['estimated_completion_days_'.$paRow.''];
                    $data2['excelent'] = $data['excelent_'.$paRow.''];
                    $data2['good'] = $data['good_'.$paRow.''];
                    $data2['average'] = $data['average_'.$paRow.''];
                    $data2['created_by'] = Auth::user()->name;
                    $data2['created_date'] = date('Y-m-d');
                    ParaOtherDetail::where('id', $podId)->update($data2);
                }else{
                    $data2['company_id'] = Session::get('company_id');
                    $data2['company_location_id'] = Session::get('company_location_id');
                    $data2['para_id'] = $paRow;
                    $data2['total_lines_in_para'] = $data['total_lines_in_para_'.$paRow.''];
                    $data2['estimated_completion_days'] = $data['estimated_completion_days_'.$paRow.''];
                    $data2['excelent'] = $data['excelent_'.$paRow.''];
                    $data2['good'] = $data['good_'.$paRow.''];
                    $data2['average'] = $data['average_'.$paRow.''];
                    $data2['created_by'] = Auth::user()->name;
                    $data2['created_date'] = date('Y-m-d');
                    ParaOtherDetail::insert($data2);
                }
            }
        }
    }

    public function allParasOtherDetails(){
        return DB::table('para_other_details as pod')->join('paras as p','pod.para_id','=','p.id')->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->paginate(25);
    }

}
