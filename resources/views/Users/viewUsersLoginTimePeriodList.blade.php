<?php
use App\Helpers\CommonHelper;
$accType = Auth::user()->acc_type;
$m = Session::get('company_id');
//$accountYearDates = DB::Connection('mysql')->table('accountyear')->select('AccountYearStartDate','AccountYearEndDate')->where('status','=','1')->where('AccountYearId','=',Session::get('accountYear'))->first();
$current_date = date('Y-m-d');
$currentMonthStartDate = date('Y-m-01');
$currentMonthEndDate   = date('Y-m-t');
?>

@extends('layouts.layouts')
@section('content')
<!DOCTYPE html>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<div class="well_N">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
			<?php echo CommonHelper::displayPrintButtonInBlade('PrintUsersLoginTimePeriodAndRolePermissionList','','1');?>
			<button id="csv" onclick="generateCSVFile('usersLoginTimePeriodAndRolePermission','View Users List')" class="btn btn-sm btn-warning">TO CSV</button>
            <button id="pdf" onclick="generatePDFFile('usersLoginTimePeriodAndRolePermission','View Users List')" class="btn btn-sm btn-success">TO PDF</button>
		</div>
	</div>
	<div class="lineHeight">&nbsp;</div>
	<div class="boking-wrp dp_sdw">
		<div class="row" id="PrintUsersLoginTimePeriodAndRolePermissionList">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print">
                                {{CommonHelper::displayPageTitle('View Users List')}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
						<div class="table-responsive wrapper">
							<table class="table table-bordered customTableTwo" id="usersLoginTimePeriodAndRolePermission">
								{{CommonHelper::displayPDFTableHeader('1000','View Users List')}}
								<thead>
									<tr>
										<th class="text-center">S.No</th>
										<th class="text-center">Assign Company Name</th>
										<th class="text-center">Name</th>
										<th class="text-center">Email</th>
										<th class="text-center">Roles</th>
										<th class="text-center hidden-print hidden">Action</th>
									</tr>
								</thead>
								<tbody id="filterUsersLoginTimePeriodAndRolePermissionList">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
    
    <script src="{{ URL::asset('assets/custom/js/customHrFunction.js') }}"></script>
    <script>
	
		$(document).ready(function(){
			UsersLoginTimePeriodAndRolePermissionList()
		});
	
		function UsersLoginTimePeriodAndRolePermissionList(){
			
			var m = '<?php echo $m ?>';
			var url = '<?php echo url('/') ?>'+'/udc/filterUsersLoginTimePeriodAndRolePermissionList';	
				
			$('#filterUsersLoginTimePeriodAndRolePermissionList').html('<tr><td colspan="50"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div></td><tr>');
			$.ajax({
				type:'GET',
				url:url,
				data:{m:m},
				success:function(res){
					$("#filterUsersLoginTimePeriodAndRolePermissionList").html(res);
				}
			});
		}
	
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
		
		var switchStatus = false;
		
		function testing(){
			var selectedSwitchCase = [];
			var unSelectedSwitchCase = [];
			var checkedValues='';
			var uncheckedValues='';
		$('input[name="checkboxes"]').each(function() {
				
				
			var res = $(this).val().split("<*>");
			 
			var ischecked= $(this).is(':checked');
			 if(ischecked){
				selectedSwitchCase.push(res[0]); 
			 } 
			else if(!ischecked){
				unSelectedSwitchCase.push(res[0]);
			}
			
			});
			
				var another = [];
				
				
				if(selectedSwitchCase.length === 0){
					
					another = ['empty',unSelectedSwitchCase];
				}
				else if(unSelectedSwitchCase.length === 0){
					
					another = [selectedSwitchCase,'empty'];
				}
				else if(selectedSwitchCase.length !== 0 || unSelectedSwitchCase.length !== 0 ){
					another = [selectedSwitchCase,unSelectedSwitchCase];
				}
	
				
				
			$.ajax({
				url: '<?php echo url('/')?>/udc/checkUsersEnableOrDisable',
				type: "GET", 
				data: {another:another},
				success:function(data) {
					
				}
			});
		
		
		}
		
	function enableUserAccountDetail(userId,roleId){
		var m = '<?php echo $m?>';
		var url = '<?php echo url("/") ?>'+'/bd/enableUserAccountDetail';
		$.ajax({
			type:'GET',
			url:url,
			data:{userId:userId,roleId:roleId,m:m},
			success:function(res){
				location.reload();
			}
		});
	}

	function disableUserAccountDetail(userId,roleId){
		var m = '<?php echo $m?>';
		var url = '<?php echo url("/") ?>'+'/bd/disableUserAccountDetail';
		$.ajax({
			type:'GET',
			url:url,
			data:{userId:userId,roleId:roleId,m:m},
			success:function(res){
				location.reload();
			}
		});
	}

    </script>
@endsection