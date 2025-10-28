

@php
    $counter = 1;
    
@endphp
@foreach ($query as $row)
@if($params['payment_status'] != '')
    @if($params['payment_status'] != $row->payment_status)
        @php continue; @endphp
    @endif
@endif
    @php
        $paymentStatus = 'Unpaid';
        if($row->invoiceAmount == $row->paidAmount){
            $paymentStatus = 'Paid';
        }else if($row->paidAmount != 0){
            $paymentStatus = 'Partial Paid';
        }
    @endphp
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td >
           {{$row->invoice_type}}
           
        </td>
        <td>{{$row->supplierName}}</td>
        <td><?php $subitems=DB::table('purchase_order_data')->where('purchase_order_no',$row->purchase_order_no)->where('status',1)->get();foreach($subitems as $val){echo "<p>".DB::table('subitem')->select('item_code')->where('id',$val->sub_item_id)->value('item_code')." - ".DB::table('subitem')->select('sub_ic')->where('id',$val->sub_item_id)->value('sub_ic')."</p>";}?></td>
        <td>{{$row->qoutation_no}}</td>
        <td>{{$row->paymentType == 2 ? $row->invoiceAmount / $row->payment_type_rate : 0}}</td>
        <td>{{$row->paymentType == 3 ? $row->invoiceAmount / $row->payment_type_rate : 0}}</td>
        <td>{{$row->invoiceAmount}}</td>
        <td>{{$row->qoutation_date}}</td>
        <td>
            
            {{$row->submited_date}}
            
        <td>{{$row->purchase_order_no.' - '.$row->purchase_order_date}}</td>
        <td>
           
                {{$row->batch_no}}
           
        </td>
        <td>{{$row->purchase_request_no.' - '.$row->purchase_request_date}}</td>
        <td>{{$row->project_name}}</td>
        <td>{{ $row->payment_status }}</td>
        
        <td>{{ $row->remarks }}</td>

        <td>
        @if($row->finance_status == 2) 
            Not Received in Finance
        @elseif($row->finance_status == 1)
            Received in Finance
        @elseif($row->finance_status == 3)
            Submitted in Finance
        @endif
            
        </td>
    </tr>
@endforeach