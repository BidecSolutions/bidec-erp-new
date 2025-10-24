@php
    use App\Helpers\CommonHelper;
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $toDate = date('Y-m-d', strtotime('+30 days'));
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
	<div class="boking-wrp dp_sdw">
	    <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{CommonHelper::displayPageTitle('View Purchase Payment Voucher List')}}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('purchase-payments.create') }}" class="btn btn-success btn-xs"><span></span> Create New</a>
            </div>
        </div>
        <form id="list_data" method="get" action="{{ route('purchase-payments.index') }}">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Voucher Type</label>
                    <select name="filterVoucherType" id="filterVoucherType" class="form-control">
                        <option value="">All Vouchers</option>
                        <option value="1">Cash</option>
                        <option value="2">Bank</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Status</label>
                    <select name="filterStatus" id="filterStatus" class="form-control">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="2">InActive</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <label>From Date</label>
                    <input type="date" name="from_date" id="from_date" value="{{$fromDate}}" class="form-control" />
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <label>&nbsp;</label>
                    <input type="text" class="form-control text-center" readonly value="Between" />
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <label>To Date</label>
                    <input type="date" name="to_date" id="to_date" value="{{$toDate}}" class="form-control" />
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" style="padding: 30px;">
                    <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
                </div>
            </div>
        </form>
        <div class="lineHeight">&nbsp;</div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-center">P.V.No.</th>
                                <th class="text-center">P.V.Date</th>
                                <th class="text-center">Voucher Type</th>
                                <th class="text-center">Cheque No</th>
                                <th class="text-center">Cheque Date</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Account Detail</th>
                                <th class="text-center">Created Detail</th>
                                <th class="text-center">Voucher Status</th>
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
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });
    </script>
@endsection