@php
    $counter = 1;
@endphp
@foreach($getPayrollList as $gplRow)
    <tr>
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$gplRow->month_year}}</td>
        <td class="text-center">
            @if ($gplRow->employee_type_id == 1)
                Non Teaching Staff
            @elseif ($gplRow->employee_type_id == 2)
                Teaching Staff
            @elseif ($gplRow->employee_type_id == 3)
                Nazim
            @elseif ($gplRow->employee_type_id == 4)
                Naib Nazim
            @else
                Moavin
            @endif
        </td>
        <td>All</td>
        <td>All</td>
        <td class="text-right">{{number_format($gplRow->total_basic_salary)}}</td>
        <td class="text-right">{{number_format($gplRow->total_allowance)}}</td>
        <td class="text-right">{{number_format($gplRow->total_additional_allowance)}}</td>
        <td class="text-right">{{number_format($gplRow->total_gross_salary)}}</td>
        <td class="text-right">{{number_format($gplRow->total_deduction)}}</td>
        <td class="text-right">{{number_format($gplRow->total_net_salary)}}</td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('payroll.show', ['id' => $gplRow->id]) }}"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach