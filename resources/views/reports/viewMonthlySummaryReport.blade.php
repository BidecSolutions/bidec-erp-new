@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintDashboardDetail','','1');?>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row" id="PrintDashboardDetail">
            <style>
                .cardBody {
                    border: 1px solid #ccc;
                    margin-left: 16px;
                    border-radius: 6px;
                    box-shadow: 0 0 8px rgb(0 0 0 / 25%);
                }
                .cardHeading {
                    font-size: 16px;
                    text-align: center;
                    font-weight: bold;
                    color: #000;
                    border-bottom: 1px #ccc solid;
                    padding: 12px;
                }
                .cardContent {
                    padding: 13px;
                    text-align: center;
                    font-size: 18px;
                }
                .leftCardBorder {
                    border-left: 7px #0e276f solid;
                    border-radius: 6px;
                }
                @media print {
                    .cardBody {
                        border: 1px solid #ccc;
                        margin-left: 10px;
                        border-radius: 6px;
                        box-shadow: 0 0 8px rgb(0 0 0 / 25%);
                    }
                    .cardHeading {
                        font-size: 12px;
                        text-align: center;
                        font-weight: bold;
                        color: #000;
                        border-bottom: 1px #ccc solid;
                        padding: 12px;
                    }
                    .cardContent {
                        padding: 13px;
                        text-align: center;
                        font-size: 14px;
                    }
                    .leftCardBorder {
                        border-left: 7px #0e276f solid;
                        border-radius: 6px;
                    }
                }
            </style>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="text-center">
                    <img style="width:15%;" src="{{CommonHelper::displaySchoolLogo()}}">
                </div>
                <div class="text-center">
                    <input type="month" name="month_year" id="month_year" value="{{date('Y-m')}}" onchange="loadMonthlySummaryReportDetail()" class="form-control" />
                </div>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div id="loadData"></div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        function loadMonthlySummaryReportDetail(){
            var monthYear = $('#month_year').val();
            $("#loadData").html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div>');
            $.ajax({
                url: '{{ route('reports.viewMonthlySummaryReport') }}',
                method: 'GET',
                data: {
                    monthYear: monthYear
                },
                error: function() {
                    alert('error');
                },
                success: function(response) {
                    $('#loadData').html(response);
                }
            });
        }
        $(document).ready(function() {
            loadMonthlySummaryReportDetail();
        });
    </script>
@endsection
