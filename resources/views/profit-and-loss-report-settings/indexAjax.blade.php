@php
    use App\Helpers\CommonHelper;
@endphp
@foreach ($profitAndLossReportSettingsList as $dRow)
    <tr>
        <td class="text-center">{{ $loop->index + 1 }}</td>
        <td>{{ $dRow->name }}</td>
        <td>
            {{ match($dRow->acc_type) {
                0 => 'None',
                1 => 'Revenue Section',
                2 => 'Expense Section',
                3 => 'COGS Section',
                4 => 'Sales Section',
                default => 'Unknown'
            } }}
        </td>
    </tr>
@endforeach
