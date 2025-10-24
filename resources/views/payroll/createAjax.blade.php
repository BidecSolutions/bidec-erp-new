@php
    use App\Helpers\CommonHelper;
    $counter = 1;
@endphp

@if(count($employeeList) == 0)
    <tr>
        <td colspan="100" class="text-center">
            No Record Found......
        </td>
    </tr>
@else
    <input type="hidden" name="filterEmployeeType" id="filterEmployeeType" value="{{$data['filterEmployeeType']}}" />
    <input type="hidden" name="filterJobType" id="filterJobType" value="{{$data['filterJobType']}}" />
    <input type="hidden" name="filterEmploymentStatus" id="filterEmploymentStatus" value="{{$data['filterEmploymentStatus']}}" />
    <input type="hidden" name="month_year" id="month_year" value="{{$data['month_year']}}" />
    @csrf
    @foreach($employeeList as $elRow)
        <tr>
            <input type="hidden" name="emp_array[]" id="emp_array" value="{{$elRow->id}}" />
            <td class="text-center">{{$counter++}}</td>
            <td class="sticky">{{$elRow->emp_name}} / {{$elRow->emp_no}}</td>
            <td class="text-right">
                <input type="number" onchange="makeGrossSalary('{{$elRow->id}}')" name="emp_basic_salary_{{$elRow->id}}" id="emp_basic_salary_{{$elRow->id}}" value="{{$elRow->basic_salary}}" />
            </td>
            @if($elRow->eadId != '')
                {{CommonHelper::getEmployeePayrollDetail($elRow->id,$elRow->eadId)}}
                
                @foreach($deductionType as $dtRow)
                    <td class="text-center" style="width:200px !important;">
                        <input type="hidden" name="emp_deduction_amount_{{$elRow->id}}[]" id="emp_deduction_amount_{{$elRow->id}}" value="{{$dtRow->id}}" />
                        <input type="number" onchange="calculateAmounts(3,'deductionClass','{{$elRow->id}}')" name="deduction_amount_{{$elRow->id}}_{{$dtRow->id}}" id="deduction_amount_{{$dtRow->id}}" value="0" class="deductionClass_{{$elRow->id}}" />
                    </td>
                @endforeach
                <td class="text-center" style="width:200px !important;">
                    <input type="number" readonly name="emp_total_deduction_{{$elRow->id}}" id="emp_total_deduction_{{$elRow->id}}" value="0"  />
                </td>
                <td class="text-center" style="width:200px !important;">
                    <input type="number" readonly name="emp_net_salary_{{$elRow->id}}" id="emp_net_salary_{{$elRow->id}}" value="{{$elRow->basic_salary}}"  />
                </td>
            @else
                @foreach($normalAllowance as $naRow)
                    <td class="text-center" style="width:200px !important;">
                        <input type="hidden" name="emp_normal_allowance_{{$elRow->id}}[]" id="emp_normal_allowance_{{$elRow->id}}" value="{{$naRow->id}}" />
                        <input type="number" onchange="calculateAmounts(1,'normalAllowanceClass','{{$elRow->id}}')" name="normal_allowance_{{$elRow->id}}_{{$naRow->id}}" id="normal_allowance_{{$naRow->id}}" value="0" class="normalAllowanceClass_{{$elRow->id}}" />
                    </td>
                @endforeach
                <td class="text-center" style="width:200px !important;">
                    <input type="number" readonly name="emp_total_allowance_{{$elRow->id}}" id="emp_total_allowance_{{$elRow->id}}" value="0" />
                </td>
                @foreach($additionalAllowance as $aaRow)
                    <td class="text-center" style="width:200px !important;">
                        <input type="hidden" name="emp_additional_allowance_{{$elRow->id}}[]" id="emp_additional_allowance_{{$elRow->id}}" value="{{$aaRow->id}}" />
                        <input type="number" onchange="calculateAmounts(2,'additionalAllowanceClass','{{$elRow->id}}')" name="additional_allowance_{{$elRow->id}}_{{$aaRow->id}}" id="additional_allowance_{{$aaRow->id}}" value="0" class="additionalAllowanceClass_{{$elRow->id}}" />
                    </td>
                @endforeach
                <td class="text-center" style="width:200px !important;">
                    <input type="number" readonly name="emp_total_additional_allowance_{{$elRow->id}}" id="emp_total_additional_allowance_{{$elRow->id}}" value="0" class="form-control" />
                </td>
                <td class="text-center" style="width:200px !important;">
                    <input type="number" readonly name="emp_gross_salary_{{$elRow->id}}" id="emp_gross_salary_{{$elRow->id}}" value="{{$elRow->basic_salary}}"/>
                </td>
                @foreach($deductionType as $dtRow)
                    <td class="text-center" style="width:200px !important;">
                        <input type="hidden" name="emp_deduction_amount_{{$elRow->id}}[]" id="emp_deduction_amount_{{$elRow->id}}" value="{{$dtRow->id}}" />
                        <input type="number" onchange="calculateAmounts(3,'deductionClass','{{$elRow->id}}')" name="deduction_amount_{{$elRow->id}}_{{$dtRow->id}}" id="deduction_amount_{{$dtRow->id}}" value="0" class="deductionClass_{{$elRow->id}}" />
                    </td>
                @endforeach
                <td class="text-center">
                    <input type="number" readonly name="emp_total_deduction_{{$elRow->id}}" id="emp_total_deduction_{{$elRow->id}}" value="0" />
                </td>
                <td class="text-center">
                    <input type="number" readonly name="emp_net_salary_{{$elRow->id}}" id="emp_net_salary_{{$elRow->id}}" value="{{$elRow->basic_salary}}" />
                </td>
                <td>
                    <select name="debit_acc_id_{{$elRow->id}}" id="debit_acc_id_{{$elRow->id}}" class="form-control select2">
                        @foreach ($chartOfAccountList as $row)
                            <option value="{{ $row->acc_id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="credit_acc_id_{{$elRow->id}}" id="credit_acc_id_{{$elRow->id}}" class="form-control select2">
                        @foreach ($chartOfAccountList as $row)
                            <option value="{{ $row->acc_id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </td>
            @endif
        </tr>
    @endforeach
    <tr>
        <td colspan="100" class="text-right">
            <button type="submit" class="btn btn-sm btn-success">Submit</button>
        </td>
    </tr>
@endif