@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($cartOrders as $coRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{$coRow->order_no}}</td>
        <td>{{CommonHelper::changeDateFormat($coRow->order_date)}}</td>
        <td>{{$coRow->customer_name}}</td>
        <td class="text-right">{{ number_format($coRow->total_amount, 0) }}</td>
        <td class="text-right">{{ number_format($coRow->payment_amount, 0) }}</td>
        <td class="text-right">{{ number_format($coRow->change_amount, 0) }}</td>
        <td>@if($coRow->status == 1) Active @else In-Active @endif</td>
        <td class="text-center hidden-print">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action <span
                        class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a
                            onclick="showDetailModelOneParamerter('pos/show','<?php    echo $coRow->id;?>','View POS Sale Detail')"><span
                                class="glyphicon glyphicon-eye-open"></span> View</a></li>

                    @if($coRow->status == 1)
                        <li><a href="{{ route('pos.edit', $coRow->id) }}">Edit</a></li>
                        <li><a id="inactive-record" data-url="{{ route('pos.destroy', $coRow->id) }}">Inactive</a></li>
                    @else
                        <li><a id="active-record" data-url="{{ route('pos.active', $coRow->id) }}">Active</a></li>

                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach