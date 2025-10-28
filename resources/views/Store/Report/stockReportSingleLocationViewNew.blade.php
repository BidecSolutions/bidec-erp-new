<?php
    use App\Helpers\CommonHelper;
    use App\Helpers\ReuseableCode;
    $export = true;
    $m = getSessionCompanyId();
    $currentMonthEndDate =  isset($_GET['toDate']) ? $_GET['toDate'] :date('Y-m-t');
    use App\Helpers\PurchaseHelper; 
?>
@extends('layouts.default')

@section('content')
    @include('select2')

    <div class="container-fluid">
        <div class="well_N">
            <div class="dp_sdw">
                <div id="stockReportLocationWiseViewDiv">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <?php echo CommonHelper::headerPrintSectionInPrintView($m);?>
                                    <form method="get" id="filterForm">
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label>To Date</label>
                                                <input type="date" name="toDate" id="toDate" value="{{ $currentMonthEndDate }}" class="form-control">
                                            </div>
                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                                <input type="submit" value="View Filter Data" class="btn btn-sm btn-danger" style="margin-top: 32px;" />
                                            </div>
                                        </div>
                                    </form>
                                    <div id="filterSubItemList">
                                        @include('Store.Report.stockReportSingleLocationViewPartial', ['subItems' => $subItems])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#filterForm').submit(function(e) {
                e.preventDefault();
                viewSubItemList();
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var toDate = $('#toDate').val();
                fetch_data(url, toDate);
            });

            function fetch_data(url, toDate) {
                $('#filterSubItemList').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                $.ajax({
                    url: url,
                    data: { toDate: toDate },
                    success: function(data) {
                        $('#filterSubItemList').html(data);
                    }
                });
            }

            function viewSubItemList() {
                $('#filterSubItemList').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
                var form = $('#filterForm');
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    success: function(data) {
                        $('#filterSubItemList').html(data);
                    }
                });
            }
        });
    </script>
@endsection