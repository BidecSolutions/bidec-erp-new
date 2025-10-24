@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($directSaleInvoices as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td>{{$dRow->dsi_no}}</td>
    <td>{{CommonHelper::changeDateformat($dRow->dsi_date)}}</td>
    <td>{{$dRow->customer_name}}</td>
    <td class="text-center">
        <span class="btn {{$dRow->status == 1 ? 'btn-success' : 'btn-danger'}} ">{{$dRow->created_by}} <br /> {{$dRow->created_date}}</span>
    </td>
    <th class="text-center">{{CommonHelper::showFinanceVoucherStatus($dRow->status,$dRow->dsi_status)}}</th>
    <td class="text-center">
        @if($dRow->dsi_status != 2)
            <div class="hidden-print">
                <label class="switch">
                    @php
                        $toggleUrl =
                            $dRow->status == 1
                                ? route('direct-sale-invoices.destroy', $dRow->id)
                                : route('direct-sale-invoices.status', $dRow->id);
                        $toggleId = $dRow->status == 1 ? 'inactive-record' : 'active-record';
                    @endphp
                    <input type="checkbox" id="{{ $toggleId }}" data-url="{{ $toggleUrl }}"
                        data-id="{{ $dRow->id }}" {{ $dRow->status == 1 ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="d-none d-print-inline-block">
                @if ($dRow->status == 1)
                    Active
                @else
                    In-Active
                @endif
            </div>
        @else
            @if ($dRow->status == 1)
                Active
            @else
                In-Active
            @endif
        @endif
    </td>
    <td class="text-center hidden-print">
        <div class="dropdown">
            <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action <span
                    class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a onclick="showDetailModelOneParamerter('direct-sale-invoices/show','<?php echo $dRow->id;?>','View Direct Sale Invoice Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
               <!--  @if ($dRow->status == 1 && $dRow->dsi_status != 2)
                    <li><a href="{{ route('direct-sale-invoices.edit', $dRow->id) }}">Edit</a></li>
                @endif -->
            </ul>
        </div>
    </td>
</tr>
@endforeach
