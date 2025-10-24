@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('View Chart of Account List')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('chartofaccounts.create') }}" class="btn btn-success btn-xs"><span></span> Create New</a>
            </div>
        </div>
        <form id="list_data" method="get" action="{{ route('chartofaccounts.index') }}">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Status</label>
                    <select name="filterStatus" id="filterStatus" class="form-control">
                        <option value="1">Active</option>
                        <option value="2">InActive</option>
                        <option value="">All Status</option>
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
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive wrapper">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Account Name</th>
                                        <th class="text-center">Parent Account</th>
                                        <th class="text-center">Account Type</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="data"></tbody>
                            </table>
                        </div>
                    </div>
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