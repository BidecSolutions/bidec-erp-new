@php
    $counter = 1;
    use App\Helpers\CommonHelper;
@endphp
@foreach($payments as $key => $row)
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$row->pv_no}}</td>
        <td class="text-center">{{CommonHelper::changeDateformat($row->pv_date)}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? 'Cash' : 'Bank'}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? '-' : $row->cheque_no}}</td>
        <td class="text-center">{{$row->voucher_type == 1 ? '-' : CommonHelper::changeDateformat($row->cheque_date)}}</td>
        <td>{{$row->description}}</td>
        <td>
            <div class="row">
                @foreach($row->payment_data as $pdRow)
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{$pdRow->account->name.' - '.number_format($pdRow->amount,0)}}</div>
                @endforeach
            </div>
        </td>
        <td class="text-center">
            <span class="btn {{$row->status == 1 ? 'btn-success' : 'btn-danger'}} ">{{$row->username}} <br /> {{$row->date}} {{date('g:i A', strtotime($row->time))}}</span>
        </td>
        <th class="text-center">{{CommonHelper::showFinanceVoucherStatus($row->status,$row->pv_status)}}</th>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a onclick="showDetailModelOneParamerter('finance/payments/show','<?php echo $row->id;?>','View Payment Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                    @if($row->status == 1 && $row->pv_status == 1)
                        <li><a href="{{ url('finance/payments/' . $row->id . '/edit') }}">Edit</a></li>
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
