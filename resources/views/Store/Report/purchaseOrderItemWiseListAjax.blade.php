<?php 
    use App\Helpers\CommonFacades;
    echo CommonFacades::listPaginationFunctionality($params['startRecordNo'],$params['endRecordNo'],$countFilterPurchaseOrderVoucherList,'updateRecordLimitPurchaseOrderItemWiseList');
    //dd($params);
?>
<tr>
    <td colspan="100" class="text-right">
        <a href="{{ url('store/report/exportPurchaseOrderItemWise?toDate='. $params['toDate'] .'&&fromDate='. $params['fromDate']).'&&filterSupplierId='.$params['filterSupplierId'] }}" class="btn btn-sm btn-warning">
            <span class="glyphicon glyphicon-print"></span> Export All
        </a>
    </td>
</tr>
@forelse ($filterPurchaseOrderVoucherList as $key => $item)
    @php
        $calculateExpenseAmount = StoreFacades::calculateExpenseAmountItemWise($item->id,$item->purchase_order_no);
    @endphp
    <tr>
        <td class="text-center">{{ $key+1 }}</td>
        <td class="text-center">{{ $item->purchase_order_no }}</td>
        <td class="text-center">{{ $item->purchase_order_date }}</td>
        <td class="text-center">{{ $item->purchase_request_no == null ? 'Direct' : $item->purchase_request_no }}</td>
        <td class="text-center">{{ $item->purchase_request_date }}</td>
        <td>{{ $item->qoutation_no }}</td>
        <td>{{ $item->item_code }}</td>  
        <td>{{ $item->sub_ic }}</td>                                                            
        <td>{{ $item->unit_price }}</td>                                                            
        <td>{{ $item->pr_qty ?? 'Direct' }}</td>                                                            
        <td>{{ $item->purchase_order_qty }}</td>                                                            
        <td>{{ $item->sub_total + percentage($item->custom_tax_percent, $item->sub_total) }}</td>                                                            
        <td>{{ $calculateExpenseAmount }}</td>
        <td>{{ $calculateExpenseAmount + $item->sub_total + percentage($item->custom_tax_percent, $item->sub_total)  }}</td>
        <td>{{ $item->location_name }}</td>
        <td>{{ $item->name }}</td>
        <td>{{ $item->department_name }}</td>
        <td>{{ $item->sub_department_name }}</td>
        <td>{{ $item->project_name }}</td>                                                                    
        <td class="text-center">{{ StoreFacades::checkVoucherStatus($item->purchase_order_status,$item->status) }}</td>                                                                      
                                                                                                                                                    
    </tr>
@empty
    <tr><td colspan="100" class="text-center">No Data</td></tr>
@endforelse