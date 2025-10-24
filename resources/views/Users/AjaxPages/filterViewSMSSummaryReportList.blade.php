<?php

	$counter = 1;
	$m = CommonFacades::getSessionCompanyId();
	$currentDate = date('Y-m-d');
	$fromDate = $_GET['fromDate'];
	$toDate = $_GET['toDate'];
	$data = '';
	if(count($filterViewSMSSummaryReportList) == 0){
		$data = '<tr><td colspan="9" class="text-center"><strong>No Record Found....</strong></td></tr>';
	}else{
		foreach ($filterViewSMSSummaryReportList as $row){
		
			if($row->status == 2){
				$rowColor = 'bg-success';
				$smsStatus = 'Send';
			}else if($row->status == 3){
				$rowColor = 'bg-danger';
				$smsStatus = 'Error';
			}else{
				$rowColor = 'bg-warning';
				$smsStatus = 'Pending';
			}
			
			$data.='<tr class="'.$rowColor.'"><td class="text-center">'.$counter++.'</td><td class="text-center">'.$row->mobile_no.'</td><td class="text-center">'.$row->sms_service_provider.'</td><td class="text-center">'.$row->masking_name.'</td><td class="text-center">'.$smsStatus.'</td><td class="text-center">'.CommonFacades::changeDateFormat($row->date).'</td><td class="text-center">'.CommonFacades::changeTimeFormat($row->time).'</td><td>'.$row->message_detail.'</td><td>'.$row->response.'</td></tr>';
		}
	}
	echo json_encode(array('data' => $data));
?>