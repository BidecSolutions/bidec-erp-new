@php
    use App\Helpers\CommonHelper;
@endphp
@foreach ($balanceSheetReportSettingsList as $dRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{ $dRow->name }}</td>
        <td>
            {{ match($dRow->acc_type) {
                0 => 'None',
                1 => 'Assets Section',
                2 => 'Liability Section',
                3 => 'Equity Section',
                default => 'Unknown'
            } }}
        </td>
    </tr>
@endforeach
