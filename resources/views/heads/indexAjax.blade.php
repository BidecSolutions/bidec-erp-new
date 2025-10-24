@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($heads as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td>{{ $dRow->head_name }}</td>
    <td>@if($dRow->status == 1) Active @else In-Active @endif</td>
    <td class="text-center hidden-print">
        <div class="dropdown">
            <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
            <ul class="dropdown-menu">
                @if($dRow->status == 1)
                    <li><a href="{{ route('heads.edit', $dRow->id) }}">Edit</a></li>
                    <li><a id="inactive-record" data-url="{{ route('heads.destroy', $dRow->id) }}">Inactive</a></li>
                @else
                    <li><a id="active-record" data-url="{{ route('heads.active', $dRow->id) }}">Active</a></li>
                @endif
            </ul>
        </div>
    </td>
</tr>
@endforeach
