<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use DB;
use Config;
use Session;
use Input;

$counter = 1;
$fromDate = date("Y-m-d", strtotime(Input::get('fromDate')));
$toDate = date("Y-m-d", strtotime(Input::get('toDate')));
$m = CommonFacades::getSessionCompanyId();

$startRecordNo = Input::get('startRecordNo');
$endRecordNo = Input::get('endRecordNo');
$filterPojectId = Input::get('filterProjectId');

$voucherStatusTitle = 'All Vouchers';


$data ='';
//$data .='<tr><td colspan="10" class="text-center"><strong>Filter By : (Sub Department => '.$selectSubDepartmentTitle.')&nbsp;&nbsp;,&nbsp;&nbsp;(From Date => '.CommonFacades::changeDateFormat($fromDate).')&nbsp;&nbsp;,&nbsp;&nbsp;(To Date => '.CommonFacades::changeDateFormat($toDate).')&nbsp;&nbsp;,&nbsp;&nbsp;(Voucher Status => '.$voucherStatusTitle.')</strong></td></tr>';
foreach ($storeChallanReturnDetail as $row){
    $paramOne = "stdc/viewStoreChallanReturnDetail";
    $paramTwo = $row->store_challan_return_no;
    $paramThree = "View Store Challan Return Detail";
    $data.='<tr><td class="text-center">'.$counter++.'</td><td class="text-center">'.$row->store_challan_return_no.'</td><td class="text-center">'.CommonFacades::changeDateFormat($row->store_challan_return_date).'</td><td class="text-center">'.$row->slip_no.'</td><td class="text-center">'.CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$row->sub_department_id).'</td><td class="text-center">'.StoreFacades::checkVoucherStatus($row->store_challan_return_status,$row->status).'</td><td class="text-center hidden-print">';
    $data.='<a onclick="showDetailModelOneParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\')" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-eye-open"></span></a>';
    // $data.='&nbsp;'.StoreFacades::displayStoreChallanReturnButton($m,$row->store_challan_return_status,$row->status,$row->store_challan_return_no,'store_challan_return_no','store/editStoreChallanReturnForm','Store Challan Return Edit Detail Form','store_challan_return','store_challan_return_data','fara','status','scr_no','scr_date').'</td></tr>';
}
echo json_encode(array('data' => $data));
?>