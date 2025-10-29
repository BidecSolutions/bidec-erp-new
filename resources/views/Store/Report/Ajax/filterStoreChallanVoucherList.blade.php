<?php
$counter = 1;
$currentDateTime = date('Y-m-d H:i:s');
$startRecordNo = Input::get('startRecordNo');
$endRecordNo = Input::get('endRecordNo');
$m;
$data ='';
$data .= '<tr><td colspan="100" class="text-right">
	<a href="'.url('store/report/ExportStoreChallanItemWiseReport?toDate='. $params['toDate'] .'&&fromDate='. $params['fromDate']).'&&selectVoucherStatus='.$params['selectVoucherStatus'].'" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-print"></span> Export All</a></td></tr>';
$data .= CommonFacades::listPaginationFunctionality($startRecordNo,$endRecordNo,$countStoreChallanVoucherList,'updateRecordLimitStoreChallanList');
foreach ($filterStoreChallanVoucherList as $row){
    $paramOne = "stdc/viewStoreChallanVoucherDetail";
    $paramTwo = $row->store_challan_no;
    $paramThree = "View Store Challan Voucher Detail";
    $paramFour = "store/editStoreChallanVoucherForm";
    
    $materialRequestCreatedDetail = '<span class="btn btn-xs btn-success">'.$row->username.'<br />'.CommonFacades::changeDateFormat($row->date).'<br />'.$row->time.' - ';
	$createdDateTime = $row->date.' '.$row->time;
    $materialRequestCreatedDetail .= CommonFacades::displayDateTimeDifference($createdDateTime,$currentDateTime,'d').'</span>';
    
    if($row->store_challan_status == 2 && $row->status == 1){
        if($row->approve_date == '0000-00-00'){
			$materialRequestApprovedDetail = '<span class="btn btn-xs btn-success">'.$row->username.'<br />'.CommonFacades::changeDateFormat($row->date).'<br />'.$row->time.' - ';
			$approvedDateTime = $row->date.' '.$row->time;
			$materialRequestApprovedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
		}else{
			$materialRequestApprovedDetail = '<span class="btn btn-xs btn-success">'.$row->approve_username.'<br />'.CommonFacades::changeDateFormat($row->approve_date).'<br />'.$row->approve_time.' - ';
			$approvedDateTime = $row->approve_date.' '.$row->approve_time;
			$materialRequestApprovedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
		}
    }else{
        $materialRequestApprovedDetail = '<span class="btn btn-xs btn-danger">-</span>';
    }
    
    $data.='<tr><td class="text-center">'.$counter++.'</td>';
    $data.='<td class="text-center">'.$row->store_challan_no.'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheLocation','location',$row->location_id,'location_name').'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheDepartment','department',$row->department_id,'department_name').' / '.CommonFacades::displayValueByIdUsingCache('cacheSubDepartment','sub_department',$row->sub_department_id,'sub_department_name').'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheProject','project',$row->project_id,'project_name').'</td>';
    $data.='<td class="text-center">'.$row->sub_ic.'</td>';
    $data.='<td class="text-center">'.$row->issue_qty.'</td>';
    $data.='<td class="text-center">'.$row->receive_qty.'</td>';
    $data.='<td class="text-center">'.$row->material_request_no.'</td>';
    $data.='<td class="text-center">'.CommonFacades::changeDateFormat($row->material_request_date).'</td>';
    $data.='<td class="text-center">'.$row->store_challan_no.'</td>';
    $data.='<td class="text-center">'.CommonFacades::changeDateFormat($row->store_challan_date).'</td>';
    $data.='<td class="text-center">'.PurchaseFacades::checkVoucherStatus($row->store_challan_status,$row->status).'</td>'; 
    
        //$data.=StoreFacades::displayMaterialRequestVoucherListButton($m,$row->material_request_status,$row->status,$row->material_request_no,'material_request_no','material_request_status','status','material_request','material_request_data',$paramFour,'Material Request Voucher Edit Detail Form');
    $data.='</tr>';
}
?>

<?php
echo json_encode(array('data' => $data));
?>