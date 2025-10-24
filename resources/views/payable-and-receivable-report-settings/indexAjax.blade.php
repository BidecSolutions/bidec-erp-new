@php
    use App\Helpers\CommonHelper;
@endphp
@foreach ($payableAndReceivableReportSettingsList as $dRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{ $dRow->name }}</td>
        <td>
            {{ match($dRow->option_id) {
                0 => 'None',
                1 => 'Payable',
                2 => 'Receiable',
                default => 'Unknown'
            } }}
        </td>
    </tr>
@endforeach
