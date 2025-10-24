@php
    use App\Helpers\CommonHelper;
@endphp
@foreach ($transferNotes as $dRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{ $dRow->transfer_note_no }}</td>
        <td>{{ $dRow->transfer_note_date }}</td>
        <td>{{ $dRow->description }}</td>
        <td class="text-center">
            @if ($dRow->tn_status != 2)
                <div class="hidden-print">
                    <label class="switch">
                        @php
                            $toggleUrl =
                                $dRow->status == 1
                                    ? route('transfer-notes.destroy', $dRow->id)
                                    : route('transfer-notes.status', $dRow->id);
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
                    <li><a
                            onclick="showDetailModelOneParamerter('transfer-notes/show','<?php echo $dRow->id; ?>','View Transfer Note Detail')"><span
                                class="glyphicon glyphicon-eye-open"></span> View</a></li>
                    @if ($dRow->status == 1)
                        <li><a href="{{ route('transfer-notes.edit', $dRow->id) }}">Edit</a></li>
                    @endif
                    @if ($dRow->status == 1 && $dRow->tn_status == 2)
                        <li><a
                                onclick="showDetailModelOneParamerter('transfer-notes/viewReceiptDetail','<?php echo $dRow->id; ?>','View Transfer Note Receipt Detail')"><span
                                    class="glyphicon glyphicon-eye-open"></span> Receipt</a></li>
                        <!-- <li><a href="{{ route('transfer-notes.edit', $dRow->id) }}">Edit</a></li> -->
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
