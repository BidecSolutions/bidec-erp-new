<?php
    use App\Helpers\CommonFacades;
    $view = true;
    
    echo CommonFacades::listPaginationFunctionality($params['startRecordNo'],$params['endRecordNo'],$countGetDetail,'updateRecordLimitStockTransferListList');
    $Counter = 1;
    $paramOne = "store/viewStockTransferDetail?m=".$params['m'];
    foreach($getDetail as $row):
        $edit_url = url('/store/editStockTransferForm/'.$row->id.'/'.$row->tr_no.'?m='.$params['m']);
        $voucherType = 'Stock Transfer';
        if($row->type == 2){
            $voucherType = 'Unit Transfer';
        }
?>
            <tr id="RemoveTr<?php echo $row->id?>">
                <td class="text-center"><?php echo $Counter++;?></td>
                <td class="text-center"><?php echo strtoupper($row->tr_no);?></td>
                <td class="text-center"><?php echo CommonFacades::changeDateFormat($row->tr_date);?></td>
                <td class="text-center">{{$voucherType}}</td>
                <td><?php echo strtoupper($row->description);?></td>
                <td class="text-center">
                    @if($view==true)
                        <button onclick="showDetailModelOneParamerter('<?php echo $paramOne?>','<?php echo $row->tr_no;?>','View Stock Transfer Detail')"   type="button" class="btn btn-success btn-xs">View</button>
                    @endif
                </td>
            </tr>
<?php 
        endforeach;
?>