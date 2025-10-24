@php
    $counter = 1;
    use App\Helpers\CommonHelper;
@endphp
@foreach($saleReceipts as $key => $row)
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$row->rv_no}}</td>
        <td class="text-center">{{CommonHelper::changeDateFormat($row->rv_date)}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? 'Cash' : 'Bank'}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? '-' : $row->cheque_no}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? '-' : CommonHelper::changeDateFormat($row->cheque_date)}}</td>
        <td>{{$row->description}}</td>
        <td>
            <div class="row">
                @foreach($row->receipt_data as $pdRow)
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{$pdRow->account->name.' - '.number_format($pdRow->amount,0)}}</div>
                @endforeach
            </div>
        </td>
        <td class="text-center">
            <span class="btn {{$row->status == 1 ? 'btn-success' : 'btn-danger'}} ">{{$row->username}} <br /> {{$row->date}} {{date('g:i A', strtotime($row->time))}}</span>
        </td>
        <th class="text-center">{{CommonHelper::showFinanceVoucherStatus($row->status,$row->rv_status)}}</th>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a onclick="showDetailModelOneParamerter('finance/sale-receipts/show','<?php echo $row->id;?>','View Sale Receipt Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
