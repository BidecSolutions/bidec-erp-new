@php
    use App\Helpers\CommonHelper;
    use Illuminate\Support\Facades\Session;

    $schoolId = Session::get('company_id');
    $schoolCampusId = Session::get('company_location_id');
    $companyLocations = CommonHelper::getCompanyLocations($schoolId);

    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $toDate = date('Y-m-d', strtotime('+30 days'));
@endphp

@extends('layouts.layouts')

@section('content')
<div class="row mb-3">
    <div class="col-lg-12 text-right">
        {!! CommonHelper::displayPrintButtonInBlade('PrintDashboardDetail', '', '1') !!}
    </div>
</div>

<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row" id="PrintDashboardDetail">
            
            <!-- 🔹 Filters -->
            <div class="col-lg-12 text-center mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-3 mb-2">
                        <label for="company_location"><strong>Select Location</strong></label>
                        <select name="company_location" id="company_location" class="form-control">
                            @foreach ($companyLocations as $id => $name)
                                <option value="{{ $id }}" {{ $id == $schoolCampusId ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="from_date"><strong>From Date</strong></label>
                        <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="to_date"><strong>To Date</strong></label>
                        <input type="date" id="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="loadDashboardSummaryDetail()">Filter</button>
                    </div>
                </div>
            </div>

            <!-- 🔹 Data -->
            <div class="col-lg-12" id="loadData">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function loadDashboardSummaryDetail() {
        let fromDate = $('#from_date').val();
        let toDate = $('#to_date').val();
        let companyLocation = $('#company_location').val();

        $("#loadData").html('<div class="text-center"><div class="loader"></div></div>');

        $.ajax({
            url: '{{ route('dashboard') }}',
            method: 'GET',
            data: { fromDate, toDate, companyLocation },
            success: function(response) {
                $('#loadData').html(response);
            },
            error: function() {
                alert('Error loading dashboard data.');
            }
        });
    }

    $(document).ready(function() {
        loadDashboardSummaryDetail();
    });
</script>
@endsection
