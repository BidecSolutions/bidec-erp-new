<?php

$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$users = DB::Connection('mysql')->table('users')
	->join('roles', 'users.id','=','roles.user_id')
	->select('users.acc_type','users.sgpe','users.id','users.name','users.username','users.email','users.userEnableDisableStatus','roles.login_start_time','roles.login_end_time','roles.company_id')
	->where('roles.company_id','=',$m)->get();
	CommonFacades::companyDatabaseConnection($m);
$bookingLogs = DB::table('booking_logs')->select('booking_logs.activity')->distinct('activity')->get();
 CommonFacades::reconnectMasterDatabase();

$headerFooterSetting = Cache::rememberForever('cacheHeaderFooterSetting_'.$m.'',function() use ($m){
	return DB::table('header_footer_setting')->where('company_id','=',$m)->first();
});
$banquetRecordSettingDetail = DB::table('banquet_record_setting')->where('user_id','=',Auth::user()->id)->where('banquet_id','=',$m)->where('option_name','=','Booking List')->first();
if(empty($banquetRecordSettingDetail)){
	$bookingListRights = '0';
	$startDays = '0';
	$endDays = '0';
	$currentMonthStartDate = date('Y-m-01');
	$currentMonthEndDate   = date('Y-m-t');
}else{
	$bookingListRights = $banquetRecordSettingDetail->booking_record_setting_status;
	$startDays = $banquetRecordSettingDetail->display_start_day;
	$endDays = $banquetRecordSettingDetail->display_end_day;
	$dateOne = date('Y-m-d', strtotime($current_date. ' - '.$startDays.' days'));
	$dateTwo = date('Y-m-d', strtotime($current_date. ' + '.$endDays.' days'));
	
	$datetimeSD1 = date_create($dateOne);
	$datetimeSD2 = date_create($current_date);
	$intervalSD = date_diff($datetimeSD1, $datetimeSD2);
	$daysDifferenceOne = $intervalSD->format('%R%a');
	if($daysDifferenceOne <= '31'){
		$currentMonthStartDate = date('Y-m-d', strtotime($current_date. ' - '.$startDays.' days'));
	}else{
		$currentMonthStartDate = date('Y-m-01');
	}

	$datetimeED1 = date_create($dateTwo);
	$datetimeED2 = date_create($current_date);
	$intervalED = date_diff($datetimeED2, $datetimeED1);
	$daysDifferenceTwo = $intervalED->format('%R%a');
	if($daysDifferenceTwo <= '31'){
		$currentMonthEndDate = date('Y-m-d', strtotime($current_date. ' + '.$endDays.' days'));
	}else{
		$currentMonthEndDate = date('Y-m-t');
	}
}
?>

@extends('layouts.default')

@section('content')

    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div style="float: left;"><a onclick="removeHiddenGatePass()">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>
			<div style="float:right;">
				<button class="btn btn-sm btn-info" onclick="checkPrintStatus('printBookingList','1')">
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
							<?php echo CommonFacades::displayViewPageTitle('View Users  Booking Activity Logs Detail List');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<input type="hidden" name="functionName" id="functionName" value="udc/filterActivityBookingLogsList" readonly="readonly" class="form-control" />
					<input type="hidden" name="tbodyId" id="tbodyId" value="filterActivityBookingLogsList" readonly="readonly" class="form-control" />
					<input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
					<input type="hidden" name="filterType" id="filterType" value="filterActivityBookingLogsList" readonly="readonly" class="form-control" />
					<input type="hidden" name="bookingListRights" id="bookingListRights" value="<?php echo $bookingListRights?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="startDays" id="startDays" value="<?php echo '-'.$startDays?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="endDays" id="endDays" value="<?php echo $endDays?>" readonly="readonly" class="form-control" />
					<div class="row">
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<label>Select Users</label>
							<select name="selectUser" id="selectUser" class="form-control">
									<option value="">Select User</option>
								<?php
								foreach($users as $value){
									?>
									<option value="<?php echo $value->id;?>"><?php echo $value->name;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<label>Filter Type Activity</label>
							<select name="activity" id="activity" class="form-control">
								
								<option value="">Select Activity</option>
								<?php
									foreach($bookingLogs as $row){
										?>
										<option value="<?php echo $row->activity;?>"><?php echo $row->activity;?></option>
										<?php
									}
								?>
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
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 hidden">
							<label>Select Voucher Status</label>
							<select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
								<option value="3">Up Comming + Completed</option>
								<option value="1">Up Comming</option>
								<option value="2">Completed</option>
								
							</select>
						</div>

						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">
							<input type="button" value="View Filter Data" class="btn btn-sm btn-danger" onclick="viewRangeWiseDataFilter();" style="margin-top: 32px;" />
						</div>
					</div>
					
					<div class="lineHeight">&nbsp;</div>
					<div id="printBookingList">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								
								<div class="row" style="overflow-y: scroll;">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="table-responsive wrapper">
											<table class="table table-bordered customTableTwo" id="bookingList">
												<thead>
													<tr>
														<th class="text-center">S.No</th>
														<th class="text-center">Order No</th>
														<th class="text-center">Option Name</th>
														<th class="text-center">User Name</th>
														<th class="text-center">Activity Type</th>
														<th class="text-center">Date</th>
														<th class="text-center">Time</th>
														<th class="text-center">Description</th>
													</tr>
												</thead>
												<tbody id="filterActivityBookingLogsList"></tbody>
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
    <script>
        function removeHiddenGatePass() {
            $(".userHiddenGatePass").removeClass("hidden");
        }
    </script>
    <script src="{{ URL::asset('assets/custom/js/customBookingFunction.js') }}"></script>
	<script src="{{ URL::asset('assets/custom/js/customMainFunction.js') }}"></script>
    <script>
	//$(document).ready(function(){
		$("#AllDate").hide();
		
		
	
	
		var bookingListRights = $('#bookingListRights').val();
		var startDays = $('#startDays').val();
		var endDays = $('#endDays').val();
		if(bookingListRights == '0'){
			$(".bookingListFromDate").datepicker({
				dateFormat: "dd-mm-yy",
				orientation:'bottom'
			});

			$(".bookingListToDate").datepicker({
				dateFormat: "dd-mm-yy",
				orientation:'bottom'
			});
		}else{
			$(".bookingListFromDate").datepicker({
				dateFormat: "dd-mm-yy",
				orientation:'bottom',
				minDate: startDays,
				maxDate: endDays
			});

			$(".bookingListToDate").datepicker({
				dateFormat: "dd-mm-yy",
				orientation:'bottom',
				minDate: startDays,
				maxDate: endDays
			});
		}
		
		function updateThirdFormRows(paramOne,paramTwo,paramThree,paramFour,paramFive){
			//alert(paramOne+' -- '+paramTwo+' -- '+paramThree);
			if(paramTwo == '1'){
				$('#loadThirdFormSelectedTypeSection').append('<tr id="'+paramThree+'"><td><input type="text" name="service_'+paramFive+'" id="service_'+paramFive+'" class="form-control requiredField" required value="'+paramFour+'" readonly /></td><td><input type="number" name="lumpsum_'+paramFive+'" id="lumpsum_'+paramFive+'" class="form-control requiredField" required onchange="updateRateAndSubTotal(this.id,this.value,\''+paramFive+'\')"/></td><td><input type="number" name="rate_'+paramFive+'" id="rate_'+paramFive+'" class="form-control requiredField" required onchange="updateLumpsumAndSubTotal(this.id,this.value,\''+paramFive+'\')" /></td><td><input type="number" name="booked_qty_'+paramFive+'" id="booked_qty_'+paramFive+'" class="form-control requiredField updateGuestThirdForm" required /></td><td><input type="number" name="discount_'+paramFive+'" id="discount_'+paramFive+'" class="form-control requiredField" required value="0" onchange="updateSubTotal(this.id,this.value,\''+paramFive+'\')" /></td><td><input type="number" name="sub_total_'+paramFive+'" id="sub_total_'+paramFive+'" class="form-control requiredField" readonly required /></td></tr>');
			}else if(paramTwo == '0'){
				var elem = document.getElementById(paramThree);
				elem.remove();
			}else if(paramTwo == ''){
				var elem = document.getElementById(paramThree);
				elem.remove();
			}
		}

		function updateGuestThirdForm(paramOne){
			$('.updateGuestThirdForm').val(paramOne);
		}
		function updateRateAndSubTotal(paramOne,paramTwo,paramThree){
			//alert(paramOne+' -- '+paramTwo+' -- '+paramThree);
			if(paramTwo == null && paramTwo == '0'){
				var lumpsumAmount = '0';
			}else{
				var lumpsumAmount = paramTwo;
			}
			var bookedQuantity = $('#booked_qty_'+paramThree+'').val();

			var makeRate = parseInt(lumpsumAmount) / parseInt(bookedQuantity);
			$('#rate_'+paramThree+'').val(makeRate);
			$('#sub_total_'+paramThree+'').val(lumpsumAmount);

		}

		function updateLumpsumAndSubTotal(paramOne,paramTwo,paramThree){
			//alert(paramOne+' -- '+paramTwo+' -- '+paramThree);
			if(paramTwo == null && paramTwo == '0'){
				var rate = '0';
			}else{
				var rate = paramTwo;
			}

			var bookedQuantity = $('#booked_qty_'+paramThree+'').val();

			var makeLumpsum = parseInt(rate) * parseInt(bookedQuantity);
			$('#lumpsum_'+paramThree+'').val(makeLumpsum);
			$('#sub_total_'+paramThree+'').val(makeLumpsum);

		}

		function updateSubTotal(paramOne,paramTwo,paramThree){
			var subTotalAmount = $('#sub_total_'+paramThree+'').val();
			var newSubTotalAmount = parseInt(subTotalAmount) - parseInt(paramTwo);
			$('#sub_total_'+paramThree+'').val(newSubTotalAmount);
		}

		function deleteBookingWithAllPayment(paramOne,paramTwo,paramThree){
			var mainTitle = 'BookingSetup';
			var pageType = '<?php echo $_GET["pageType"] ?>';
			var parentCode = '<?php echo $_GET["parentCode"] ?>';
			var cancelBookingReason = '';
			var cancel_form_no = '';
			var data = { bookingNo:paramOne,m:paramThree,jv_no:paramTwo,pageType:pageType,parentCode:parentCode,mainTitle:mainTitle,cancelBookingReason:cancelBookingReason,cancel_form_no:cancel_form_no};
			$.ajax({
				url: '<?php echo url('/')?>'+'/bd/deleteBookingWithAllPayment',
				type: "GET",
				data: data,
				success: function (data) {
					if(data == 'duplicate'){
						checkUserPermissionForSingleOptionModel('/bdc/dontPermissionForModal','Delete Details');
					}else{
						viewRangeWiseDataFilter();
					}
				}		
			});
		}
		
		function deleteBookingWithAllPaymentComplete(paramOne,paramTwo,paramThree){
			//alert('Testing - '+paramOne+' - '+paramTwo+' - '+paramThree);
			//return false;
			var mainTitle = 'BookingSetup';
			var pageType = '<?php echo $_GET["pageType"] ?>';
			var parentCode = '<?php echo $_GET["parentCode"] ?>';
			var data = { bookingNo:paramOne,m:paramThree,jv_no:paramTwo,pageType:pageType,parentCode:parentCode,mainTitle:mainTitle};
			$.ajax({
				url: '<?php echo url('/')?>'+'/bd/deleteBookingWithAllPaymentComplete',
				type: "GET",
				data: data,
				success: function (data) {
					if(data == 'duplicate'){
						checkUserPermissionForSingleOptionModel('/bdc/dontPermissionForModal','Delete Details');
					}else{
						viewRangeWiseDataFilter();
					}
				}		
			});
		}
		
		

		function deleteHalfBooking(paramOne,paramTwo,paramThree){
			var data = { bookingNo:paramOne,m:paramThree,jv_no:paramTwo};
			$.ajax({
				url: '<?php echo url('/')?>/bd/deleteHalfBooking',
				type: "GET",
				data: data,
				success: function (data) {
					viewRangeWiseDataFilter();
					//$('.loadEmployeeLeaveApplicationDetailSection').html(data);
				}
			});
		}

		function repostBookingWithAllPayment(paramOne,paramTwo,paramThree,paramFour,paramFive,paramSix){
			var mainTitle = 'BookingSetup';
			var data = { bookingNo:paramOne,m:paramThree,jv_no:paramTwo,lawnId:paramFour,functionDate:paramFive,bookingTiming:paramSix,mainTitle:mainTitle};
			$.ajax({
				url: '<?php echo url('/')?>/bd/repostBookingWithAllPayment',
				type: "GET",
				data: data,
				success: function (data) {
					if(data == 'duplicate'){
						 checkUserPermissionForSingleOptionModel('/bdc/dontPermissionForModal','Repost Details');
					 }
					else{
						viewRangeWiseDataFilter();
					}	
				}
			});
		}

            
			
		function checkPrintStatus(paramOne,paramTwo){
			if(paramTwo == '1'){
				var checkPrintStatus = '<?php echo CommonFacades::checkUserPermissionForMenu("right_printlist","BookingSetup",Auth::user()->id,$m); ?>';
			}else if(paramTwo == '2'){
				var checkPrintStatus = '<?php echo CommonFacades::checkUserPermissionForMenu("right_printsingle","BookingSetup",Auth::user()->id,$m); ?>';
			}
			if(checkPrintStatus == 0){
				checkUserPermissionForSingleOptionModel('/bdc/dontPermissionForModal','PrintList Details');	
				return;
			}
			 printView(paramOne,'',1);			
		}
			
		function checkCsvStatus(){
			var checkCsvStatuss = '<?php echo CommonFacades::checkUserPermissionForMenu("right_export","BookingSetup",Auth::user()->id,$m); ?>';
			if(checkCsvStatuss == 0){
				checkUserPermissionForSingleOptionModel('/bdc/dontPermissionForModal','Csv Details');
				 return;	
			}
			   exportView('printBookingList','','1');
		}
       
    </script>
@endsection