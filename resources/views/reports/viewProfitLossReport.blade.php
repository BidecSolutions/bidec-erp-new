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
            <?php echo CommonHelper::displayPrintButtonInBlade('PrintProfitLossReport', '', '1'); ?>
            <button id="pdf" onclick="generatePDFFile('PrintProfitLossReport','Profit and Loss Report')" class="btn btn-sm btn-success">TO PDF</button>
        </div>
    </div>
    <div class="lineHeight">&nbsp;</div>
    <form id="list_data" method="get" action="{{ route('reports.viewProfitLossReport') }}">
        <div class="row">
            <div class="col-lg-3">
                <input type="hidden" value="{{ Session::get('company_id') }}" name="company_id" id="company_id" />
                <input type="hidden" value="{{ Session::get('company_location_id') }}" name="company_location_id" id="company_location_id" />
            </div>
            <div class="col-lg-2">
                <label>From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="form-control" />
            </div>
            <div class="col-lg-1 text-center">
                <label>&nbsp;</label>
                <input type="text" readonly class="form-control" value="To" />
            </div>
            <div class="col-lg-2">
                <label>To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="form-control" />
            </div>
            <div class="col-lg-1" style="padding-top: 30px;">
                <input type="button" value="Filter" onclick="get_ajax_data_for_profit_loss()" class="btn btn-xs btn-success" />
            </div>
        </div>
    </form>
    <div class="lineHeight">&nbsp;</div>
    <div class="boking-wrp dp_sdw">
        <div id="PrintProfitLossReport">
            <div id="data"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        get_ajax_data_for_profit_loss();
    });

    function get_ajax_data_for_profit_loss() {
        $.ajax({
            url: "{{ route('reports.viewProfitLossReport') }}",
            data: $("#list_data").serialize(),
            type: 'GET',
            success: function(data) {
                $("#data").html(data);
            },
            error: function() {
                alert('Failed to fetch Profit and Loss data.');
            }
        });
    }
</script>
@endsection
