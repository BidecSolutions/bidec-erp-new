@foreach ($returnGRNs as $key => $rRow)
    <tr>
        <td class="text-center">{{ $key + 1 }}</td>
        <td>{{ $rRow->return_grn_no }}</td>
        <td>{{ $rRow->grn_no }}</td>
        <td>{{ $rRow->return_date }}</td>
        <td>{{ $rRow->supplier_name }}</td>
        <td>{{ $rRow->reason }}</td>
        <td class="text-center">
            @if($rRow->return_grn_status != 2)
                <div class="hidden-print">
                    <label class="switch">
                        @php
                            $toggleUrl =
                                $rRow->status == 1
                                ? route('return-good-receipt-notes.destroy', $rRow->id)
                                : route('return-good-receipt-notes.status', $rRow->id);
                            $toggleId = $rRow->status == 1 ? 'inactive-record' : 'active-record';
                        @endphp
                        <input type="checkbox" id="{{ $toggleId }}" data-url="{{ $toggleUrl }}" data-id="{{ $rRow->id }}" {{ $rRow->status == 1 ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="d-none d-print-inline-block">
                    @if ($rRow->status == 1)
                        Active
                    @else
                        In-Active
                    @endif
                </div>
            @else
                @if ($rRow->status == 1)
                    Active
                @else
                    In-Active
                @endif
            @endif
        </td>
        <td class="text-center hidden-print">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">
                    Action <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @if ($rRow->status == 1 && $rRow->return_grn_status != 2)
                        <li><a href="{{ route('return-good-receipt-notes.edit', $rRow->id) }}">Edit</a></li>
                    @endif
                    <li>
                        <a
                            onclick="showDetailModelOneParamerter('return-good-receipt-notes/show','{{ $rRow->id }}','View Return GRN Detail')">
                            <span class="glyphicon glyphicon-eye-open"></span> View
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach