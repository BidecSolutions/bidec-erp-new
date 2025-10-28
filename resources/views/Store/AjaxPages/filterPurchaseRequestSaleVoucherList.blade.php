<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use DB;
use Config;
use Session;

$counter = 1;
$m = CommonFacades::getSessionCompanyId();
$selectSubDepartment = $_GET['selectSubDepartment'];
$selectVoucherStatus = $_GET['selectVoucherStatus'];
$selectSupplier = $_GET['selectSupplier'];

if(!empty($selectSubDepartment)){
    $selectSubDepartmentTitle = $selectSubDepartment;
}else{
    $selectSubDepartmentTitle = 'All Department';
}

if(!empty($selectSupplier)){
    $selectSupplierTitle = $selectSupplier;
}else{
    $selectSupplierTitle = 'All Suppliers';
}

if($selectVoucherStatus == '0'){
    $voucherStatusTitle = 'All Vouchers';
}else if($selectVoucherStatus == '1'){
    $voucherStatusTitle = 'Pending Vouchers';
}else if($selectVoucherStatus == '2'){
    $voucherStatusTitle = 'Approve Vouchers';
}else if($selectVoucherStatus == '3'){
    $voucherStatusTitle = 'Deleted Vouchers';
}
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$data ='';
$data .='<tr><td colspan="10" class="text-center"><strong>Filter By : (Sub Department => '.$selectSubDepartmentTitle.')&nbsp;&nbsp;,&nbsp;&nbsp;(Suppliers => '.$selectSupplierTitle.')&nbsp;&nbsp;,&nbsp;&nbsp;(From Date => '.CommonFacades::changeDateFormat($fromDate).')&nbsp;&nbsp;,&nbsp;&nbsp;(To Date => '.CommonFacades::changeDateFormat($toDate).')&nbsp;&nbsp;,&nbsp;&nbsp;(Voucher Status => '.$voucherStatusTitle.')</strong></td></tr>';
foreach ($purchaseRequestSaleDetail as $row){
    $paramOne = "stdc/viewPurchaseRequestSaleVoucherDetail";
    $paramTwo = $row['purchase_request_no'];
    $paramThree = "View Purchase Request Sale Detail";
    $data.='<tr><td class="text-center">'.$counter++.'</td><td class="text-center">'.$row->purchase_request_no.'</td><td class="text-center">'.CommonFacades::changeDateFormat($row->purchase_request_date).'</td><td class="text-center">'.$row->slip_no.'</td><td class="text-center">'.CommonFacades::getMasterTableValueById($m,'sub_department','sub_department_name',$row->sub_department_id).'</td><td>'.CommonFacades::getCompanyDatabaseTableValueById($m,'supplier','name',$row->supplier_id).'</td><td class="text-center">'.StoreFacades::checkVoucherStatus($row->purchase_request_status,$row->status).'</td><td class="text-center hidden-print">';
    $data.='<a onclick="showDetailModelOneParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\')" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-eye-open"></span></a>';
    $data.='&nbsp;'.StoreFacades::displayPurchaseChallanButton($m,$row->purchase_request_status,$row->status,'1',$row->purchase_request_no).'</td></tr>';
}
?>

<?php
echo json_encode(array('data' => $data));
?>