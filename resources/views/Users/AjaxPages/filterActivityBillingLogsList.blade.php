<?php
use BookingFacades;


$counter = 1;

$m = CommonFacades::getSessionCompanyId();
$currentDate = date('Y-m-d');
$getUserName = $_GET['getUserName'];
$getUserNameText = $_GET['getUserNameText']; 
$activity       = $_GET['activity'];
 

$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$data = '';
$data .='<tr><td colspan="25" class="text-center"><strong>Filter By : '.$activity.' (From Date => '.CommonFacades::changeDateFormat($fromDate).')&nbsp;&nbsp;,&nbsp;&nbsp;(To Date => '.CommonFacades::changeDateFormat($toDate).')&nbsp;&nbsp;</strong></td></tr>';

foreach ($filterActivityBillingLogsList as $row){ 
		
    $data.='<tr><td class="text-center">'.$counter++.' <a class="hidden specialDeleteButton btn btn-xs btn-danger" ><span class="glyphicon glyphicon-trash"></span></a></td><td class="text-center>'.$row->voucher_no.'</td><td class="text-center">'.$row->voucher_no.'</td><td class="text-center">'.$row->option_name.'</td><td class="text-center">'.$row->user_name.'</td><td class="text-center">'.$row->activity.'</td><td class="text-center">'.$row->date.'</td><td class="text-center">'.$row->time.'</td><td class="text-center">'.$row->description.'</td>';
      $data.='</ul></div></td></tr>';
}
echo json_encode(array('data' => $data));


?>