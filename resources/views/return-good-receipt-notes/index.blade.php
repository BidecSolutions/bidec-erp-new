@php
    use App\Helpers\CommonHelper;
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $toDate = date('Y-m-d', strtotime('+30 days'));
@endphp
@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
            {!! CommonHelper::displayPrintButtonInBlade('PrintReturnGRNList','','1') !!}
            <button id="csv" onclick="generateCSVFile('ExportReturnGRNList','View Return GRN List')" class="btn btn-sm btn-warning">TO CSV</button>
            <button id="pdf" onclick="generatePDFFile('ExportReturnGRNList','View Return GRN List')" class="btn btn-sm btn-success">TO PDF</button>
        </div>
    </div>
    <div class="lineHeight">&nbsp;</div>
    <form id="list_data" method="get" action="{{ route('return-good-receipt-notes.index') }}">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label>Status</label>
                <select name="filterStatus" id="filterStatus" class="form-control select2">
                    <option value="">All Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Rejected</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="form-control" />
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                <label>&nbsp;</label>
                <input type="text" class="form-control text-center" readonly value="Between" />
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="form-control" />
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" style="padding: 30px;">
                <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
            </div>
        </div>
    </form>
    <div class="lineHeight">&nbsp;</div>
    <div class="boking-wrp dp_sdw" id="PrintReturnGRNList">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 hidden-print">
                                {!! CommonHelper::displayPageTitle('View Return GRN List') !!}
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right hidden-print">
                                <a href="{{ route('return-good-receipt-notes.create') }}" class="btn btn-success btn-xs">+ Create New</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive wrapper">
                            <table class="table table-responsive table-bordered" id="ExportReturnGRNList">
                                {!! CommonHelper::displayPDFTableHeader('1000','View Return GRN List') !!}
                                <thead>
                                    <tr>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Return GRN No</th>
                                        <th class="text-center">GRN No</th>
                                        <th class="text-center">Return Date</th>
                                        <th class="text-center">Supplier Name</th>
                                        <th class="text-center">Reason</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center hidden-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="data">
                                    <!-- AJAX content will be loaded here -->
                                </tbody>
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
    // Function to call the AJAX endpoint for filtering
    function get_ajax_data() {
        $.ajax({
            url: "{{ route('return-good-receipt-notes.index') }}",
            data: $('#list_data').serialize(),
            type: 'GET',
            success: function(response) {
                // Assuming the response returns HTML for the table rows
                $('#data').html(response.html);
            },
            error: function(xhr) {
                alert('Error loading data');
            }
        });
    }
</script>
@endsection
