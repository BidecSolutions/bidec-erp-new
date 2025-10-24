<?php
$accType = Auth::user()->acc_type;
$m;
Session::get('accountYear');
$accountYearDates = DB::Connection('mysql')->table('accountyear')->select('AccountYearStartDate','AccountYearEndDate')->where('status','=','1')->where('AccountYearId','=',Session::get('accountYear'))->first();
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');
?>

@extends('layouts.default')
@section('content')
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
			<?php echo CommonFacades::displayPrintButtonInBlade('PrintUsersOptionPermissionList','','1');?>
			<?php echo CommonFacades::displayExportButton('usersOptionPermissionList','','1')?>
		</div>
		<div class="lineHeight">&nbsp;</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('View Users Option Permission List');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<input type="hidden" name="functionName" id="functionName" value="udc/filterUsersOptionPermissionList" readonly="readonly" class="form-control" />
					<input type="hidden" name="tbodyId" id="tbodyId" value="filterUsersOptionPermissionList" readonly="readonly" class="form-control" />
					<input type="hidden" name="m" id="m" value="<?php echo $m?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/')?>" readonly="readonly" class="form-control" />
					<input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly" class="form-control" />
					<input type="hidden" name="filterType" id="filterType" value="UsersOptionPermissionList" readonly="readonly" class="form-control" />
					<div id="PrintUsersOptionPermissionList">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								
								<?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="table-responsive">
											<table class="table table-bordered sf-table-list" id="usersOptionPermission">
												<thead>
												<th class="text-center">S.No</th>
												<th class="text-center">Assign Company Name</th>
												<th class="text-center">Name</th>
												<th class="text-center">Email</th>
												<th class="text-center">Add Booking</th>
												<th class="text-center">Completed Booking Edit</th>
												<th class="text-center">Booking Payment Edit</th>
												<th class="text-center">Payment Delete</th>
												<th class="text-center hidden-print">Action</th>
												</thead>
												<tbody id="filterUsersOptionPermissionList"></tbody>
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
    <script src="{{ URL::asset('assets/custom/js/customHrFunction.js') }}"></script>
    <script>
        function deleteUserWithRoles(param1,param2,param3,param4){
            $.ajax({
            url: ''+baseUrl+'/cdOne/deleteUserWithRoles',
            type: "GET",
            data: {m:param1,employeeId:param2,userId:param3,roleId:param4},
            success:function(data) {
                filterVoucherList();
            }
        });
        }
    </script>
@endsection