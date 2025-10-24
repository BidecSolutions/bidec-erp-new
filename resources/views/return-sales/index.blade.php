@php
    use App\Helpers\CommonHelper;
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $toDate = date('Y-m-d', strtotime('+30 days'));
@endphp

@extends('layouts.layouts')
@section('content')
    <div class="well_N">
        <div class="row">
            <div class="col-lg-12 text-right">
                {!! CommonHelper::displayPrintButtonInBlade('PrintReturnSaleList', '', '1') !!}
                <button id="csv" onclick="generateCSVFile('ExportReturnSaleList','View Return Sale List')"
                    class="btn btn-sm btn-warning">TO CSV</button>
                <button id="pdf" onclick="generatePDFFile('ExportReturnSaleList','View Return Sale List')"
                    class="btn btn-sm btn-success">TO PDF</button>
            </div>
        </div>

        <form id="list_data" method="get" action="{{ route('sales-return.index') }}">
            <div class="row">
                <div class="col-lg-3">
                    <label>Status</label>
                    <select name="filterStatus" id="filterStatus" class="form-control select2">
                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control" />
                </div>
                <div class="col-lg-1 text-center">
                    <label>&nbsp;</label>
                    <input type="text" class="form-control text-center" readonly value="Between" />
                </div>
                <div class="col-lg-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control" />
                </div>
                <div class="col-lg-1" style="padding-top: 30px;">
                    <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
                </div>
            </div>
        </form>

        <div class="lineHeight">&nbsp;</div>
        <div class="boking-wrp dp_sdw" id="PrintReturnSaleList">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 hidden-print">
                            {!! CommonHelper::displayPageTitle('View Return Sales List') !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right hidden-print">
                            <a href="{{ route('sales-return.create') }}" class="btn btn-success btn-xs">+
                                Create New</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive wrapper">
                        <table class="table table-bordered" id="ExportReturnSaleList">
                            {!! CommonHelper::displayPDFTableHeader('1000', 'View Return Sale List') !!}
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Return Sale No</th>
                                    <th class="text-center">Order No</th>
                                    <th class="text-center">Return Date</th>
                                    <th class="text-center">Customer</th>
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
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            get_ajax_data();
        });

        function get_ajax_data() {
            $.ajax({
                url: "{{ route('sales-return.index') }}",
                data: $('#list_data').serialize(),
                type: 'GET',
                success: function (response) {
                    $('#data').html(response.html);
                },
                error: function () {
                    alert('Error loading data');
                }
            });
        }
    </script>
@endsection