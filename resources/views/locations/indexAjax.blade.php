@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($locations as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td class="text-center">{{ $dRow->company_name }}</td>
    <td>{{ $dRow->name }}</td>
    <td class="text-center">{{ $dRow->phone_no }}</td>
    <td>{{ $dRow->email }}</td>
    <td class="text-center">{{ $dRow->address }}</td>
    <td class="text-center">
        <div class="hidden-print">
            <label class="switch">
                @php
                    $toggleUrl =
                        $dRow->status == 1
                            ? route('locations.status', $dRow->id)
                            : route('locations.active', $dRow->id);
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
    </td>
    <td class="text-center hidden-print">
        <div class="dropdown">
            <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
            <ul class="dropdown-menu">
                @if($dRow->status == 1)
                    <li><a href="{{ route('locations.edit', $dRow->id) }}">Edit</a></li>
                @endif
            </ul>
        </div>
    </td>
</tr>
@endforeach
