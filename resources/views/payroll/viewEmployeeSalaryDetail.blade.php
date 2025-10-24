@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
    <style>
        .floatLeft{
            width: 50%;
            float: left;
        }
        .floatRight{
            width: 50%;
            float: right;
        }
        @media print {
            th {
                font-size: 10px;
                /* Additional styling for print, if needed */
                color: #000; /* Example: ensure text color is black for print */
            }
            td {
                font-size: 9px;
                /* Additional styling for print, if needed */
                color: #000; /* Example: ensure text color is black for print */
            }
        }
    </style>
    <div class="well_N">
        <div class="boking-wrp dp_sdw">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {{CommonHelper::displayPageTitle('View Employee Salary Detail')}}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                    <a href="{{ route('payroll.index') }}" class="btn btn-success btn-xs">+ View List</a>
                    <?php echo CommonHelper::displayPrintButtonInBlade('PrintEmployeeSalaryDetail','','1');?>
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div id="PrintEmployeeSalaryDetail">
                @foreach($viewEmployeeSalaryDetail as $vesdRow)
                    @php
                        $empId = $vesdRow->emp_id;
                        $remunerationAllowance = DB::table('allowance_type as at')
                            ->leftJoin('employee_payroll_allowance_detail as epad', function($join) {
                                $join->on('at.id', '=', 'epad.at_id')
                                    ->where('epad.status', 1);
                            })
                            ->join('employee_payroll_data_detail as epdd', function($join) use($empId) {
                                $join->on('epad.epdd_id', '=', 'epdd.id')
                                    ->where('epdd.emp_id', $empId);
                            })
                            ->select('epad.*', 'at.id as naId','at.allowance_name')
                            ->where('at.type', 1)
                            ->get();
                        $additionalAllowance = DB::table('allowance_type as at')
                            ->leftJoin('employee_payroll_allowance_detail as epad', function($join) {
                                $join->on('at.id', '=', 'epad.at_id')
                                    ->where('epad.status', 1);
                            })
                            ->join('employee_payroll_data_detail as epdd', function($join) use($empId) {
                                $join->on('epad.epdd_id', '=', 'epdd.id')
                                    ->where('epdd.emp_id', $empId);
                            })
                            ->select('epad.*', 'at.id as naId','at.allowance_name')
                            ->where('at.type', 2)
                            ->get();
                        $deductionDetail = DB::table('deduction_type as dt')
                            ->leftJoin('employee_payroll_deduction_detail as epddo', function($join) {
                                $join->on('dt.id', '=', 'epddo.dt_id')
                                    ->where('epddo.status', 1);
                            })
                            ->join('employee_payroll_data_detail as epdd', function($join) use($empId) {
                                $join->on('epddo.epdd_id', '=', 'epdd.id')
                                    ->where('epdd.emp_id', $empId);
                            })
                            ->select('epddo.*', 'dt.id as dtId', 'dt.deduction_name')
                            ->get();
                    @endphp
                    <div class="well">
                        <style>
                            .voucherCompanyClass{
                                border-top: 1px solid;
                                border-bottom: 1px solid;
                                padding: 11px;
                                font-size: 20px;
                                font-weight: bold;
                            }

                            .voucherHeadingClass{
                                border-top: 1px solid;
                                border-bottom: 1px solid;
                                padding: 11px;
                                font-size: 17px;
                                font-weight: bold;
                            }
                            .floatLeft{
                                width: 48%;
                                float: left;
                            }
                            .floatRight{
                                width: 48%;
                                float: right;
                            }
                            @media print {
                                .page-break { page-break-before:always; }
                            }
                        </style>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                <p class="voucherCompanyClass">{{Session::get('company_name')}}</p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                <p class="voucherHeadingClass">Salary Slip</p>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="floatLeft">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Date:</th>
                                                    <td>{{CommonHelper::changeDateFormat($vesdRow->created_date)}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name:</th>
                                                    <td>{{$vesdRow->emp_name}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Employee Type:</th>
                                                    <td>
                                                    @if ($vesdRow->emp_type == 1)
                                                        Non Teaching Staff
                                                    @elseif ($vesdRow->emp_type == 2)
                                                        Teaching Staff
                                                    @elseif ($vesdRow->emp_type == 3)
                                                        Nazim
                                                    @elseif ($vesdRow->emp_type == 4)
                                                        Naib Nazim
                                                    @else
                                                        Moavin
                                                    @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Job Type:</th>
                                                    <td>@if($vesdRow->job_type == 1) Full Time @else Part Time @endif</td>
                                                </tr>
                                            </thead>
                                        </table>
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="text-center">REMUNERATION</th>
                                                </tr>
                                                <tr>
                                                    <th>Basic Salary</th>
                                                    <td class="text-right">{{number_format($vesdRow->basic_salary,0)}}</td>
                                                </tr>
                                                @foreach($remunerationAllowance as $raRow)
                                                    <tr>
                                                        <td>{{$raRow->allowance_name}}</td>
                                                        <td class="text-right">{{number_format($raRow->amount,0)}}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Total Allowance</th>
                                                    <td class="text-right">{{number_format($vesdRow->total_allowance + $vesdRow->basic_salary,0)}}</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="floatRight">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Month Of:</th>
                                                    <td>{{$vesdRow->month_year}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Slip No.:</th>
                                                    <td>{{$vesdRow->slip_no}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Employment Status:</th>
                                                    <td>@if($vesdRow->employment_status == 1) Permanent @else Contract Base @endif</td>
                                                </tr>
                                            </thead>
                                        </table>
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="text-center">Additional Allowance</th>
                                                </tr>
                                                @foreach($additionalAllowance as $aaRow)
                                                    <tr>
                                                        <td>{{$aaRow->allowance_name}}</td>
                                                        <td class="text-right">{{number_format($aaRow->amount,0)}}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Total Additional Allowance</th>
                                                    <td class="text-right">{{number_format($vesdRow->total_additional_allowance,0)}}</td>
                                                </tr>
                                            </thead>
                                        </table>
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Deduction</th>
                                                </tr>
                                                @foreach($deductionDetail as $ddRow)
                                                    <tr>
                                                        <td>{{$ddRow->deduction_name}}</td>
                                                        <td class="text-right">{{number_format($ddRow->amount,0)}}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Total Deduction</th>
                                                    <td class="text-right">{{number_format($vesdRow->total_deduction,0)}}</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="floatLeft">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Gross Salary</th>
                                                    <td class="text-right">{{number_format($vesdRow->gross_salary,0)}}</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="floatRight">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Net Salary</th>
                                                    <td class="text-right">{{number_format($vesdRow->net_salary,0)}}</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="lineHeight">&nbsp;</div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <p style="border-bottom: 1px solid #000;"><strong>Received By</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&nbsp;</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <p style="border-bottom: 1px solid #000;"><strong>Prepared By</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&nbsp;</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <p style="border-bottom: 1px solid #000;"><strong>Approved By</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-break lineHeight">&nbsp;</div>
                    <div class="hidden-print">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">---------------------------------------------------------------------------------------------</div>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        <div class="lineHeight">&nbsp;</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection