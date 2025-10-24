@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
<style>
    .table-responsive {
        position: relative;
        overflow: auto;
    }

    .table th, .table td {
        position: relative;
        z-index: 1; /* Set z-index to ensure sticky columns are on top */
    }

    .table th.sticky, .table td.sticky {
        position: sticky;
        left: 0;
        background: #fff; /* Background color for sticky columns */
        z-index: 2; /* Higher z-index for sticky columns */
    }

    .table th.sticky:nth-child(3), .table td.sticky:nth-child(3) {
        left: 0px; /* Adjust this value based on the width of your first two sticky columns */
    }
</style>
@section('content')
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{CommonHelper::displayPageTitle('Add Payroll Detail')}}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('payroll.index') }}" class="btn btn-success btn-xs">+ View List</a>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <form id="list_data" method="get" action="{{ route('payroll.create') }}">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <label>Employee Type</label>
                        <select name="filterEmployeeType" id="filterEmployeeType" class="form-control select2">
                            <option value="1">Non Teaching Staff</option>
                            <option value="2">Teaching Staff</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 hidden">
                        <label>Job Type</label>
                        <select name="filterJobType" id="filterJobType" class="form-control select2">
                            <option value="0">All</option>
                            <option value="1">Full Time</option>
                            <option value="2">Part Time</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 hidden">
                        <label>Employment Status</label>
                        <select name="filterEmploymentStatus" id="filterEmploymentStatus" class="form-control select2">
                            <option value="0">All</option>
                            <option value="1">Permanent</option>
                            <option value="2">Contract Base</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>Month-Year</label>
                        <input type="month" name="month_year" id="month_year" class="form-control" value="{{date('Y-m')}}" />
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" style="padding: 30px;">
                        <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
                    </div>
                </div>
            </form>
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="table-responsive wrapper">
                        <form method="POST" action="{{ route('payroll.store') }}">
                            <table class="table table-responsive table-bordered" id="ExportParasList">
                                <thead>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center sticky">Name / Emp No</th>
                                    <th class="text-center">Basic Salary</th>
                                    @foreach($normalAllowance as $naRow)
                                        <th class="text-center">{{$naRow->allowance_name}}</th>
                                    @endforeach
                                    <th class="text-center">Total<br />Allowance</th>
                                    @foreach($additionalAllowance as $aaRow)
                                        <th class="text-center">{{$aaRow->allowance_name}}</th>
                                    @endforeach
                                    <th class="text-center">Total Additional<br />Allowance</th>
                                    <th class="text-center">Gross Salary</th>
                                    @foreach($deductionType as $dtRow)
                                        <th class="text-center">{{$dtRow->deduction_name}}</th>
                                    @endforeach
                                    <th class="text-center">Total<br />Deduction</th>
                                    <th class="text-center">Net Salary</th>
                                    <th class="text-center">Debit Account Id</th>
                                    <th class="text-center">Credit Account Id</th>
                                </thead>
                                <tbody id="data">
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });
        function calculateAmounts(type,className,empId){
            var sum = 0;
            $('.'+className+'_'+empId+'').each(function(){
                let value = parseFloat(this.value) || 0;
                sum += value;
            });
            if(type == 1){
                $('#emp_total_allowance_'+empId+'').val(sum);
            }else if(type == 2){
                $('#emp_total_additional_allowance_'+empId+'').val(sum);  
            }else if(type == 3){
                $('#emp_total_deduction_'+empId+'').val(sum); 
            }
            makeGrossSalary(empId);
        }

        function makeGrossSalary(empId){
            // Get values and ensure they are valid numbers or default to 0
            var empBasicSalary = parseFloat($('#emp_basic_salary_' + empId).val()) || 0;
            var empTotalAllowance = parseFloat($('#emp_total_allowance_' + empId).val()) || 0;
            var empTotalAdditionalAllowance = parseFloat($('#emp_total_additional_allowance_' + empId).val()) || 0;
            // Calculate gross salary
            var grossSalary = empBasicSalary + empTotalAllowance + empTotalAdditionalAllowance;

            // Set the gross salary value
            $('#emp_gross_salary_' + empId).val(grossSalary.toFixed(2)); // Use toFixed to format to 2 decimal places if needed
            makeNetSalary(empId);
        }

        function makeNetSalary(empId){
            // Get values and ensure they are valid numbers or default to 0
            var empGrossSalary = parseFloat($('#emp_gross_salary_' + empId).val()) || 0;
            var empTotalDeduction = parseFloat($('#emp_total_deduction_' + empId).val()) || 0;

            // Calculate net salary
            var netSalary = empGrossSalary - empTotalDeduction;

            // Set the net salary value
            $('#emp_net_salary_' + empId).val(netSalary.toFixed(2)); // Use toFixed to format to 2 decimal places if needed
        }
    </script>
@endsection