@php
    $counter = 1;
@endphp
@foreach ($query as $row)
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
        <td>
            @if(empty($row->invoice_type))
                <input type="text" onchange="updatePurchaseOrderDetailForInvoiceSubmit({{$row->id}},'invoice_type')" name="invoice_type_{{$row->id}}" id="invoice_type_{{$row->id}}" value="" class="form-control" />
            @else
                {{$row->invoice_type}}
            @endif
        </td>
        <td>{{$row->supplierName}}</td>
        <td></td>
        <td>{{$row->qoutation_no}}</td>
        <td>{{$row->paymentType == 2 ? $row->invoiceAmount / $row->payment_type_rate : 0}}</td>
        <td>{{$row->paymentType == 3 ? $row->invoiceAmount / $row->payment_type_rate : 0}}</td>
        <td>{{$row->invoiceAmount}}</td>
        <td>{{$row->paidAmount ?? 0}}</td>
        <td>{{$row->qoutation_date}}</td>
        <td>
            @if($row->submited_date == '0000-00-00')
                <input type="date" onchange="updatePurchaseOrderDetailForInvoiceSubmit({{$row->id}},'submited_date')" name="submited_date_{{$row->id}}" id="submited_date_{{$row->id}}" value="" class="form-control" /></td>
            @else
                {{$row->submited_date}}
            @endif
        <td>{{$row->purchase_order_no.' - '.$row->purchase_order_date}}</td>
        <td>
            @if(empty($row->batch_no))
                <input type="text" onchange="updatePurchaseOrderDetailForInvoiceSubmit({{$row->id}},'batch_no')" name="batch_no_{{$row->id}}" id="batch_no_{{$row->id}}" class="form-control" />
            @else
                {{$row->batch_no}}
            @endif
        </td>
        <td>{{$row->purchase_request_no.' - '.$row->purchase_request_date}}</td>
        <td>{{$row->project_name}}</td>
        <td>
            <select class="form-control" name="payment_status_{{$row->id}}" id="payment_status_{{$row->id}}">
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
                <option value="Partial Paid">Partial Paid</option>
            </select>
        </td>
        <td><input type="text" name="new_pay_{{$row->id}}" id="new_pay_{{$row->id}}" value="" class="form-control" /></td>
        <td><input type="text" name="remarks_{{$row->id}}" id="remarks_{{$row->id}}" value="-" class="form-control" /></td>
        <td class="text-center">
            <input type="button" class="btn btn-xs btn-success" value="Update" onclick="updateNewPayDetail({{$row->id}})" />
        </td>
        <td>
            <select class="form-control" onchange="updatePurchaseOrderDetailForInvoiceSubmit({{$row->id}},'finance_status')" name="finance_status_{{$row->id}}" id="finance_status_{{$row->id}}">
                <option value="2" @if($row->finance_status == 2) selected @endif>Not Received in Finance</option>
                <option value="1" @if($row->finance_status == 1) selected @endif>Received in Finance</option>
                <option value="3" @if($row->finance_status == 3) selected @endif>Submitted in Finance</option>
            </select>
        </td>
    </tr>
@endforeach