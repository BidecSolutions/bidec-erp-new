<?php


$counter = 1;
$m = CommonFacades::getSessionCompanyId();
$data ='';
foreach ($usersList as $row){
    $banquetSetting = DB::Connection('mysql')->table('company_setting')
        ->select('option_rights','open_right_date')
        ->where('user_id','=',$row->id)->get();
    $userPasswordDetaill = explode("<*>", $row->sgpe);
	$companyName = CommonFacades::getCompanyNameTwo($row->company_id);
    
    $countBanquetSetting = count($banquetSetting);
	

    $data.='<tr><td class="text-center">'.$counter++.'</td><td>'.$companyName.'</td><td>'.$row->name.'</td><td>'.$row->email.'</td>';
    if($countBanquetSetting == '0'){
    	$data.='<td class="text-center">-</td>';
    	$data.='<td class="text-center">-</td>';
    	$data.='<td class="text-center">-</td>';
    	$data.='<td class="text-center">-</td>';
    }else{
    	foreach ($banquetSetting as $row) {
    		if($row->option_rights == '1'){
    			$optionRights = 'Yes';
    		}else if($row->option_rights == '2'){
    			$optionRights = 'No';
    		}
    		$data.='<td class="text-center">'.$optionRights.'<br />'.CommonFacades::changeDateFormat($row->open_right_date).'</td>';
    	}
    }
    $data.'<td class="text-center"></td></tr>';
}
?>

<?php
echo json_encode(array('data' => $data));
?>


<?php /*?><td class="text-center"><a class="delete-modal btn btn-xs btn-danger btn-xs" data-dismiss="modal" aria-hidden="true" onclick="deleteUserWithRoles(\''.$row->company_id.'\',\''.$employeeDetaill->id.'\',\''.$userDetaill->id.'\',\''.$row->id.'\')"><span class="glyphicon glyphicon-trash"></span></a></td><?php */?>