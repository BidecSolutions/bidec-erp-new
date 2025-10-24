@php
    $counter = 1;
    use App\Helpers\CommonHelper;
@endphp
@foreach($saleInvoices as $key => $row)
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$row->invoice_no}}</td>
        <td class="text-center">{{CommonHelper::changeDateformat($row->invoice_date)}}</td>
        <td class="text-center">{{$row->jv_no}}</td>
        <td class="text-center">{{$row->customer_name}}</td>
        <td class="text-center">{{number_format($row->amount)}}</td>
        <td>{{$row->description}}</td>
        <td class="text-center">
            <span class="btn {{$row->status == 1 ? 'btn-success' : 'btn-danger'}} ">{{$row->created_by}} <br /> {{$row->created_date}}</span>
        </td>
        <th class="text-center">{{CommonHelper::showFinanceVoucherStatus($row->status,$row->voucher_status)}}</th>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a onclick="showDetailModelOneParamerter('sale-invoice/show','<?php echo $row->id;?>','View Sale Invoice Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                    @if($row->status == 1 && $row->voucher_status == 1)
                        <li><a href="{{ url('sale-invoice/' . $row->id . '/edit') }}">Edit</a></li>
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
