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
            <?php echo CommonHelper::displayPrintButtonInBlade('PrintLedgerDetail', '', '1');?>
            <button id="pdf" onclick="generatePDFFile('PrintLedgerDetail','View Ledger Report')"
                class="btn btn-sm btn-success">TO PDF</button>
        </div>
    </div>
    <div class="lineHeight">&nbsp;</div>
    <form id="list_data" method="get" action="{{ route('reports.viewLedgerReport') }}">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <input type="hidden" value="{{Session::get('company_id')}}" id="company_id" name="company_id" />
                <input type="hidden" value="{{Session::get('company_location_id')}}" id="company_location_id"
                    name="company_location_id" />
                <label>Account Name</label>
                <input type="hidden" name="account_name" id="account_name" value="" />
                <select name="acc_id" id="acc_id" class="form-control select2" onchange="showAccountName()">
                    @foreach(CommonHelper::get_all_chart_of_account() as $key => $row)
                        <option value="{{$row->id}}">{{$row->code}} ---- {{$row->name}}</option>
                    @endforeach
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
    <div class="boking-wrp dp_sdw">
        <div id="PrintLedgerDetail">
            <div id="data"></div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        function showAccountName(){
            var accountText = $('#acc_id option:selected').text();
            $('#account_name').val(accountText);
        }
        showAccountName();
        $(document).ready(function () {
            get_ajax_data();
            showAccountName();
        });
    </script>
@endsection