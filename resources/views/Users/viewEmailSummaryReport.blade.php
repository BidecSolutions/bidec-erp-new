<?php

$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$headerFooterSetting = Cache::rememberForever('cacheHeaderFooterSetting_'.$m.'',function() use ($m){
	return DB::table('header_footer_setting')->where('company_id','=',$m)->first();
});
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate = date('Y-m-t');
?>

@extends('layouts.default')

@section('content')

    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div style="float:right;">
				<button class="btn btn-sm btn-info" onclick="checkPrintStatus('printViewEmailSummaryReportList','1')">
					<span class="glyphicon glyphicon-print"></span> Print
				</button>
				
				<button class="btn btn-sm btn-warning" onclick="checkCsvStatus()" >
					<span class="glyphicon glyphicon-print"></span> Export to CSV
				</button>
			</div>
		</div>
		<div class="lineHeight">&nbsp;</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('View Email Summary Report');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<input type="hidden" name="functionName" id="functionName" value="udc/filterViewEmailSummaryReportList" readonly="readonly" class="form-control" />
					<input type="hidden" name="tbodyId" id="tbodyId" value="filterViewEmailSummaryReportList" readonly="readonly" class="form-control" />
					<input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
					<input type="hidden" name="filterType" id="filterType" value="filterViewEmailSummaryReportList" readonly="readonly" class="form-control" />
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<label>Email's</label>
							<select  name="filterEmail" id="filterEmail" class="form-control">
								<option value="">Select Email</option>
								<?php foreach($getEmailList as $gelRow){?>
									<option value="<?php echo $gelRow->email?>"><?php echo $gelRow->email?></option>
								<?php }?>
							</select>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<label>Filter Type</label>
							<select name="filter_type" id="filter_type" class="form-control">
								<option value="">Select Filter Type</option>
								<option value="1">Pending</option>
								<option value="2">Send</option>
								<option value="3">Error</option>
							</select>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<label>From Date</label>
							<input type="text" name="fromDate" id="fromDate" readonly value="<?php echo date("d-m-Y", strtotime($currentMonthStartDate)) ?>"  class="form-control bookingListFromDate" />
						</div>
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-center"><label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<input type="text" readonly class="form-control text-center" value="Between" /></div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<label>To Date</label>
							<input type="text" name="toDate" id="toDate" readonly value="<?php echo date("d-m-Y", strtotime($currentMonthEndDate)) ?>"  class="form-control bookingListToDate"  />
						</div>
						
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">
							<input type="button" value="View Filter Data" class="btn btn-sm btn-danger" onclick="viewRangeWiseDataFilter();" style="margin-top: 32px;" />
						</div>
					</div>
					
					<div class="lineHeight">&nbsp;</div>
					<div id="printViewEmailSummaryReportList">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								
								<div class="row" style="overflow-y: scroll;">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="table-responsive wrapper">
											<table class="table table-bordered customTableTwo" id="viewEmailSummaryReportList">
												<thead>
													<tr>
														<th class="text-center">S.No</th>
														<th class="text-center">Email</th>
														<th class="text-center">Status</th>
														<th class="text-center">Date</th>
														<th class="text-center">Time</th>
														<th class="text-center">Email Detail</th>
														<th class="text-center">Response Detail</th>
													</tr>
												</thead>
												<tbody id="filterViewEmailSummaryReportList"></tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <script src="{{ URL::asset('assets/custom/js/customBookingFunction.js') }}"></script>
	<script src="{{ URL::asset('assets/custom/js/customMainFunction.js') }}"></script>
    <script>
		$(function () {
			$("select").select2();
		});
		$(".bookingListFromDate").datepicker({
			dateFormat: "dd-mm-yy",
			orientation:'bottom'
		});

		$(".bookingListToDate").datepicker({
			dateFormat: "dd-mm-yy",
			orientation:'bottom'
		});
		function checkPrintStatus(paramOne,paramTwo){
			printView(paramOne,'',1);			
		}
			
		function checkCsvStatus(){
			exportView('viewEmailSummaryReportList','','1');
		}
	</script>
@endsection