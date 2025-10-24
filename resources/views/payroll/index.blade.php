@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
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
            <form id="list_data" method="get" action="{{ route('payroll.index') }}">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <label>Employee Type</label>
                        <select name="filterEmployeeType" id="filterEmployeeType" class="form-control select2">
                            <option value="">All</option>
                            <option value="1">Non Teaching Staff</option>
                            <option value="2">Teaching Staff</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>From Date</label>
                        <input type="month" name="filter_from_month_year" id="filter_from_month_year" value="{{date('Y-m')}}" class="form-control" />
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>&nbsp;</label>
                        <input type="text" class="form-control" readonly value="Between" />
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <label>To Date</label>
                        <input type="month" name="filter_to_month_year" id="filter_to_month_year" value="{{date('Y-m')}}" class="form-control" />
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
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" style="padding: 30px;">
                        <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
                    </div>
                </div>
            </form>
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive wrapper">
                        <table class="table table-responsive table-bordered" id="ExportParasList">
                            <thead>
                                <th class="text-center">S.No</th>
                                <th class="text-center">Month-Year</th>
                                <th class="text-center">Employee Type</th>
                                <th class="text-center">Job Type</th>
                                <th class="text-center">Employment Status</th>
                                <th class="text-center">Overall Basic Salary</th>
                                <th class="text-center">Overall Allowances</th>
                                <th class="text-center">Overall Additional Allowances</th>
                                <th class="text-center">Overall Gross Salary</th>
                                <th class="text-center">Overall Deductions</th>
                                <th class="text-center">Overall Net Salary</th>
                                <th class="text-center">Action</th>
                            </thead>
                            <tbody id="data">
                            </tbody>
                        </table>
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
    </script>
@endsection