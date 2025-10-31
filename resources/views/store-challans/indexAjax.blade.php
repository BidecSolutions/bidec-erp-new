@php
    use App\Helpers\CommonHelper;
@endphp
@foreach ($storeChallans as $dRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{ $dRow->store_challan_no }}</td>
        <td>{{ $dRow->store_challan_date }}</td>
        <td>{{ $dRow->department_name }}</td>
        <td>{{ $dRow->description }}</td>
        <td class="text-center">
            @if($dRow->store_challan_status != 2)
                <div class="hidden-print">
                    <label class="switch">
                        @php
                            $toggleUrl =
                                $dRow->status == 1
                                    ? route('store-challans.destroy', $dRow->id)
                                    : route('store-challans.status', $dRow->id);
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
                    @if ($dRow->status == 1 && $dRow->store_challan_status != 2)
                        <li><a href="{{ route('store-challans.edit', $dRow->id) }}">Edit</a></li>
                    @endif
                    <li><a onclick="showDetailModelOneParamerter('store-challans/show','<?php echo $dRow->id;?>','View Good Receipt Note Detail')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
