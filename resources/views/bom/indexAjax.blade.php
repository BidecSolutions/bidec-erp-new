@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($boms as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td>{{$dRow->bom_no}}</td>
    <td>{{CommonHelper::changeDateFormat($dRow->bom_date)}}</td>
    <td>{{$dRow->finish_product}}</td>
    <td class="text-center">
        @if($dRow->bom_status != 2)
            <div class="hidden-print">
                <label class="switch">
                    @php
                        $toggleUrl = $dRow->status == 1 
                            ? route('bom.destroy', $dRow->id) 
                            : route('bom.status', $dRow->id);
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
                @if($dRow->status == 1 && $dRow->bom_status != 2)
                    <li><a href="{{ route('bom.edit', $dRow->id) }}">Edit</a></li>
                @endif
                <li><a onclick="showDetailModelOneParamerter('bom/show','<?php echo $dRow->id;?>','View BOM Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
            </ul>
        </div>
    </td>
</tr>
@endforeach