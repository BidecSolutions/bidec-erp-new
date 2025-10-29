<?php
    $counter = 1;
    $currentDateTime = date('Y-m-d H:i:s');
    $startRecordNo = Input::get('startRecordNo');
    $endRecordNo = Input::get('endRecordNo');
    $m;
    $data ='';
    $data .= '<tr><td colspan="100" class="text-right"><a href="'.url('store/report/ExportStockTransferItemAndTypeWiseReport?toDate='. $params['toDate'] .'&&fromDate='. $params['fromDate']).'&&voucherType='.$params['voucherType'].'&&recordType='.$params['recordType'].'" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-print"></span> Export All</a></td></tr>';
    $data .= CommonFacades::listPaginationFunctionality($startRecordNo,$endRecordNo,$countStockTransferVoucherList,'updateRecordLimitStoreChallanList');
    foreach ($filterStockTransferVoucherList as $row){
        
        $data.='<tr><td class="text-center">'.$counter++.'</td>';
        $data.='<td class="text-center">'.$row->tr_no.'</td>';
        $data.='<td class="text-center">'.CommonFacades::changeDateFormat($row->tr_date).'</td>';
        $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheLocation','location',$row->warehouse_from,'location_name').'</td>';
        $data.='<td>'.CommonFacades::displayValueByIdUsingCache('cacheLocation','location',$row->warehouse_to,'location_name').'</td>';
        $data.='<td>'.$row->sub_ic.'</td>';
        $data.='<td>'.$row->iot.'</td>';
        if($params['recordType'] == 1){
            $data.='<td>'.$row->description.'</td>';
        }else{
            $data.='<td>'.$row->desc.'</td>';
        }
        $data.='<td class="text-center hideColumn">'.$row->qty.'</td>';
        $data.='</tr>';
    }
    if($params['recordType'] == 1){
        $data.='<script>$(".hideColumn").addClass("hidden");</script>';
    }
    echo json_encode(array('data' => $data));
?>
