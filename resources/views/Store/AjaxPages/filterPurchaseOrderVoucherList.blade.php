<?php


$counter = 1;
$currentDateTime = date('Y-m-d H:i:s');
$startRecordNo = Input::get('startRecordNo');
$endRecordNo = Input::get('endRecordNo');
$m;
$data ='';
$data .= CommonFacades::listPaginationFunctionality($startRecordNo,$endRecordNo,$countPurchaseOrderVoucherList,'updateRecordLimitPurchaseOrderList');
foreach ($filterPurchaseOrderVoucherList as $row){
    $paramOne = "stdc/viewPurchaseOrderVoucherDetail";
    $editPO = "store/editPurchaseOrderForm/".$row->purchase_order_no;
    $InvoicePO = "store/InvoicePO/".$row->purchase_order_no;
    $paramTwo = $row->purchase_order_no;
    $paramThree = "View Purchase Order Detail";
	$paramFour = "Edit Purchase Order Detail";
	$createdDateTime = $row->date.' '.$row->time;
    $createdDetail = '<span class="btn btn-xs btn-success">'.$row->username.'<br />'.$row->date.'<br />'.$row->time.' - '.CommonFacades::displayDateTimeDifference($createdDateTime,$currentDateTime,'d').'</span>';
	if($row->purchase_order_status == 2){
		if($row->approve_date == '0000-00-00'){
			$approvedDateTime = $row->date.' '.$row->time;
			$approvedDetail = $row->approve_username.'<br />'.$row->date.'<br />'.$row->time.' - '.CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
		}else{
			$approvedDateTime = $row->approve_date.' '.$row->approve_time;
			$approvedDetail = '<span class="btn btn-xs btn-success">'.$row->approve_username.'<br />'.$row->approve_date.'<br />'.$row->approve_time.' - '.CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
		}
	}else{
		$approvedDetail = '<span class="btn btn-xs btn-danger">-</span>';
	}

	if($row->totalRemainingItemNotGeneratedGRN == 0){
        $allRemainingPOStatus = '<span class="btn btn-xs btn-success">Done</span>';
    }else{
        $allRemainingPOStatus = '<span class="btn btn-xs btn-danger">'. $row->totalRemainingItemNotGeneratedGRN. ' of ' . $row->totalItemsPurchaseOrderData .' Pending</span>';
	}
	
	$data.='<tr><td class="text-center">'.$counter++.'</td>';
	$data.='<td class="text-center">'.$row->purchase_order_no.' / '.CommonFacades::changeDateFormat($row->purchase_order_date).'</td>';
	if ($row->po_type == 'direct') {                                 
		$data.='<td class="text-center">DIRECT</td>';
	}else {
		$data.='<td class="text-center">'.$row->purchase_request_no.' / '.CommonFacades::changeDateFormat($row->purchase_request_date).'</td>';                                    
	}
	$data.='<td>'.$row->location_name.'</td>';
	$data.='<td >'.$row->name.'</td>';
	$data.='<td>'.$row->department_name.'</td>';
	$data.='<td>'.$row->sub_department_name.'</td>';
	$data.='<td>'.$row->project_name.'</td>';
	$data.='<td>'.$row->description.'</td>';
	$data.='<td class="text-center">'.$createdDetail.'</td>';
	$data.='<td class="text-center">'.StoreFacades::checkVoucherStatus($row->purchase_order_status,$row->status).'</td>';
	$data.='<td>'.$row->voucher_remarks.'</td>';
	$data.='<td class="text-center">'.$approvedDetail.'</td>';
	$data.='<td class="text-center">'.$allRemainingPOStatus.'</td>';
	$data.='<td class="text-center hidden-print"><div class="dropdown"><button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button><ul class="dropdown-menu">';
		if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_viewsingle', Auth::user()->acc_type)){
			$data.='<li><a onclick="showDetailModelOneParamerter(\''.$paramOne.'\',\''.$paramTwo.'\',\''.$paramThree.'\')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
		}
		if ($row->purchase_order_status == 1) {
			if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)){
				$data.='<li><a onclick="showDetailModelOneParamerter(\''.$editPO.'\',\''.$paramTwo.'\',\''.$paramFour.'\')"><span class="glyphicon glyphicon-eye-open"></span> Edit</a></li>';
			}
		}
		if ($row->purchase_order_status == 2) {
			if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_edit', Auth::user()->acc_type)){
				$data.='<li><a onclick="showDetailModelOneParamerter(\''.$InvoicePO.'\',\''.$paramTwo.'\',\''.$paramThree.'\')"><span class="glyphicon glyphicon-eye-open"></span>Edit Invoice Number </a></li>';
			}
		}
		$data.='<li><a onclick="showDetailModelOneParamerter(\'stdc/viewPurchaseOrderVoucherLogDetail\',\''.$paramTwo.'\',\'View Purchase Order Log Detail\')"><span class="glyphicon glyphicon-eye-open"></span> View P.O.Log Detail</a></li>';
    //$data.=''.StoreHelper::displayPurchaseOrderButton($m,$row->purchase_order_status,$row->status,'1',$row->purchase_order_no).'</ul></div></td></tr>';
}
echo json_encode(array('data' => $data));
?>