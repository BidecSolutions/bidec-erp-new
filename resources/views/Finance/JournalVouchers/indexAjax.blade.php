@php
    $counter = 1;
    use App\Helpers\CommonHelper;
@endphp
@foreach($journalVouchers as $key => $row)
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$row->jv_no}}</td>
        <td class="text-center">{{CommonHelper::changeDateformat($row->jv_date)}}</td>
        <td class="text-center">{{$row->slip_no}}</td>
        <td>{{$row->description}}</td>
        <td>
            <div class="row">
                @foreach($row->journal_voucher_data as $jvdRow)
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{$jvdRow->account->name.' - '.number_format($jvdRow->amount,0)}}</div>
                @endforeach
            </div>
        </td>
        <td class="text-center">
            <span class="btn {{$row->status == 1 ? 'btn-success' : 'btn-danger'}} ">{{$row->username}} <br /> {{$row->date}} {{date('g:i A', strtotime($row->time))}}</span>
        </td>
        <th class="text-center">{{CommonHelper::showFinanceVoucherStatus($row->status,$row->jv_status)}}</th>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a onclick="showDetailModelOneParamerter('finance/journalvouchers/show','<?php echo $row->id;?>','View Journal Voucher Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                    @if($row->status == 1 && $row->jv_status == 1 && $row->voucher_type == 1)
                        <li><a href="{{ url('finance/journalvouchers/' . $row->id . '/edit') }}">Edit</a></li>
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
