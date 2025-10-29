<?php
$counter = 1;
$currentDateTime = date('Y-m-d H:i:s');
$startRecordNo = Input::get('startRecordNo');
$endRecordNo = Input::get('endRecordNo');
$m;
$data ='';
$data .= CommonFacades::listPaginationFunctionality($startRecordNo,$endRecordNo,$countMaterialRequestList,'updateRecordLimitMaterialRequestList');

foreach ($filterMaterialRequestList as $row){
    $paramOne = "stdc/viewMaterialRequestVoucherDetail";
    $paramTwo = $row->material_request_no;
    $paramThree = "View Material Request Voucher Detail";
    $paramFour = "store/editMaterialRequestVoucherForm";

    
    $materialRequestCreatedDetail = '<span class="btn btn-xs btn-success">'.$row->username.'<br />'.CommonFacades::changeDateFormat($row->date).'<br />'.$row->time.' - ';
	$createdDateTime = $row->date.' '.$row->time;
    $materialRequestCreatedDetail .= CommonFacades::displayDateTimeDifference($createdDateTime,$currentDateTime,'d').'</span>';
    
    if($row->material_request_status == 2 && $row->status == 1){
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

    if($row->totalRemainingItemNotGeneratedSC == 0){
        $allRemainingMRStatus = '<span class="btn btn-xs btn-success">Done</span>';
    }else{
        $allRemainingMRStatus = '<span class="btn btn-xs btn-danger">'.$row->totalRemainingItemNotGeneratedSC.' out of '.$row->totalRequestItems.' Pending</span>';
    }
    
    $data.='<tr><td class="text-center">'.$counter++.'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheLocation','location',$row->location_id,'location_name').'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheDepartment','department',$row->department_id,'department_name').' / '.CommonFacades::displayValueByIdUsingCache('cacheSubDepartment','sub_department',$row->sub_department_id,'sub_department_name').'</td>';
    $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheProject','project',$row->project_id,'project_name').'</td>';
    $data.='<td class="text-center">'.$row->material_request_no.'</td>';
    $data.='<td class="text-center">'.CommonFacades::changeDateFormat($row->material_request_date).'</td>';
    $data.='<td>'.$row->description.'</td>';
    $data.='<td class="text-center">'.$materialRequestCreatedDetail.'</td>';
    $data.='<td class="text-center">'.PurchaseFacades::checkVoucherStatus($row->material_request_status,$row->status).'</td>';
    $data.='<td>'.$row->additional_remarks.'</td>';
    $data.='<td class="text-center">'.$materialRequestApprovedDetail.'</td>';
    $data.='<td class="text-center">'.$allRemainingMRStatus.'</td>';
    $data.='<td class="text-center hidden-print"><div class="dropdown"><button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button><ul class="dropdown-menu">';
        // dd($row->store_challan_status);
        $data.='<li><a onclick="showDetailModelOneParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
        $data.=StoreFacades::displayMaterialRequestVoucherListButton($m,$row->material_request_status,$row->status,$row->material_request_no,'material_request_no','material_request_status','status','material_request','material_request_data',$paramFour,'Material Request Voucher Edit Detail Form',$paramTwo, $row->totalRemainingItemNotGeneratedSC, $row->totalRequestItems);
    $data.='</ul></div></td></tr>';
}
?>

<?php
echo json_encode(array('data' => $data));
?>