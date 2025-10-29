@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($purchaseOrders as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td>{{$dRow->po_no}}</td>
    <td>{{CommonHelper::changeDateFormat($dRow->po_date)}}</td>
   
    <td>{{$dRow->invoice_quotation_no ?? '-'}}</td>
    <td>{{CommonHelper::changeDateFormat($dRow->quotation_date)}}</td>
    <td>{{$dRow->supplier_name}}</td>
    <td class="text-center">
        @if($dRow->po_status != 2)
            <div class="hidden-print">
                <label class="switch">
                    @php
                        $toggleUrl = $dRow->status == 1 
                            ? route('purchase-orders.destroy', $dRow->id) 
                            : route('purchase-orders.status', $dRow->id);
                        $toggleId = $dRow->status == 1 ? 'inactive-record' : 'active-record';
                    @endphp
                    <input type="checkbox" id="{{ $toggleId }}" data-url="{{ $toggleUrl }}" data-id="{{ $dRow->id }}" {{ $dRow->status == 1 ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="d-none d-print-inline-block">
                @if($dRow->status == 1) Active @else In-Active @endif
            </div>
        @else
            @if($dRow->status == 1) Active @else In-Active @endif
        @endif
    </td>
    <td class="text-center hidden-print">
        <div class="dropdown">
            <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
            <ul class="dropdown-menu">
                @if($dRow->status == 1 && $dRow->po_status != 2)
                    <li><a href="{{ route('purchase-orders.edit', $dRow->id) }}">Edit</a></li>
                @endif
                <li><a onclick="showDetailModelOneParamerter('purchase-orders/show','<?php echo $dRow->id;?>','View Purchase Order Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
            </ul>
        </div>
    </td>
</tr>
@endforeach