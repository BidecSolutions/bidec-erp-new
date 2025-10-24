<?php 
	
	$id = $_GET['id'];
	$m = CommonFacades::getSessionCompanyId();
	$userDetail = DB::Connection('mysql')->table('users')->where('id','=',$id)->first();
	$accType = Auth::user()->acc_type;
	$accTypeTwo = $userDetail->acc_type;
	$companyNameTwo = CommonFacades::getCompanyNameTwo($m);
	$userPasswordDetaill = explode("<*>",$userDetail->sgpe);
	
	if($accTypeTwo == 'user'){
		$companyName = CommonFacades::getCompanyNameTwo($userDetail->company_id);
	}else{
		
		$explodeCompanyDetail = explode("<*>",$userDetail->company_id);
		$companyName = '';
		foreach($explodeCompanyDetail as $ecdRow){
			$companyName .= CommonFacades::getCompanyNameTwo($ecdRow).',';
		}
	}
	
	if($userDetail->ip_block == '1'){
		$ipBlock = 'Yes';
	}else{
		$ipBlock = 'No';
	}
	
	if($userDetail->time_distriction == '1'){
		$timeDistriction = 'Yes';
		$loginStartTime = $userDetail->login_start_time;
		$loginEndTime = $userDetail->login_end_time;
	}else{
		$timeDistriction = 'No';
		$loginStartTime = '-';
		$loginEndTime = '-';
	}
?>
<script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
<link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
<div class="well">
	<?php echo Form::open(array('url' => 'uad/updateUserAdditionalRightsDetail?m='.$m.'','id'=>'updateUserAdditionalRightsDetail'));?>
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
		<input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
		<input type="text" name="userId" id="userId" value="<?php echo $id?>" />
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				 <div class="table-responsive">
					<table class="table table-bordered sf-table-list">
						<thead>
							<tr>
								<th class="text-center">Assign Company Name</th>
								<th class="text-center">Name</th>
								<th class="text-center">Email</th>
								<th class="text-center"><span class="hidden">Password</span></th>
								<th class="text-center">User Type</th>
								<th class="text-center">Ip Block</th>
								<th class="text-center">Time Distriction</th>
								<th class="text-center">Start Login Time</th>
								<th class="text-center">End Login Time</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $companyName  ?> </td>
								<td><?php echo $userDetail->name  ?> </td>
								<td><?php echo $userDetail->email  ?> </td>
								<td class="text-center" ><?php echo $userPasswordDetaill[1]  ?> </td>
								<td class="text-center" ><?php echo $userDetail->acc_type  ?> </td>
								<td class="text-center"><?php echo $ipBlock;?></td>
								<td class="text-center"><?php echo $timeDistriction;?></td>
								<td class="text-center" ><?php echo $loginStartTime  ?> </td>
								<td class="text-center" ><?php echo $loginEndTime  ?> </td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<select class="form-control" name="banquet_id" id="banquet_id" onchange="loadUserAdditionalRgihtsDetail()">
					<option value="">Select Company</option>
					<?php
						if($userDetail->acc_type == 'user'){
					?>
							<option value="<?php echo $userDetail->company_id?>"><?php echo $companyName?></option>
					<?php
						}else{
							if($accType == 'client'){
								foreach($explodeCompanyDetail as $ecdRow2){
					?>
									<option value="<?php echo $ecdRow2?>"><?php echo CommonFacades::getCompanyNameTwo($ecdRow2)?></option>
					<?php
								}
							}else{
								foreach($explodeCompanyDetail as $ecdRow2){
									$companyNameThree = CommonFacades::getCompanyNameTwo($ecdRow2);
					?>
									<option value="<?php echo $ecdRow2?>" <?php if($companyNameTwo == $companyNameThree){}else{echo 'style="display:none"';}?>><?php echo $companyNameThree?></option>
					<?php
								}
							}
						}
					?>
				</select>
			</div>
		</div>
		<div class="lineHeight">&nbsp;</div>
		<div id="loadUserAdditionalRgihtsSection"></div>
	<?php echo Form::close();?>
</div>
<script>
	$("select").select2();
	function loadUserAdditionalRgihtsDetail(){
		$('#loadUserAdditionalRgihtsSection').html('');
		var banquetId = $('#banquet_id').val();
		var userId = '<?php echo $id?>';
		var m = '<?php echo $m?>';
		if(banquetId == ''){
			alert('Something Wrong! Please Select Company......');
			return false;
		}else{
			var url = '<?php echo url("/") ?>'+'/udc/loadUserAdditionalRgihtsDetail';
			$.ajax({
				type:'GET',
				url:url,
				data:{banquetId:banquetId,userId:userId,m:m},
				success:function(res){
					$('#loadUserAdditionalRgihtsSection').html(res);
				}
			});
		}
	}

    function loadCreatedUser(paramOne){
        var optionRight = $('#option_right_'+paramOne+'').val();
		var m = '<?php echo $m?>';
		var banquetId = $('#banquet_id').val();
        if(optionRight == 1){
            $('#additionalOptionRight_'+paramOne+'').html('');
        }else{
			var url = '<?php echo url("/") ?>'+'/udc/loadUserDetailList';
			$.ajax({
				type:'GET',
				url:url,
				data:{banquetId:banquetId,m:m},
				success:function(res){
					$('#additionalOptionRight_'+paramOne+'').html('<select name="additionOptionalValue_'+paramOne+'[]" id="additionOptionalValue_'+paramOne+'" class="form-control" multiple="multiple">'+res+'</select>');
					$("select").select2();
				}
			});
        }
        
    }

    function loadAccountingYear(paramOne){
        var optionRight = $('#option_right_'+paramOne+'').val();
		var m = '<?php echo $m?>';
		var banquetId = $('#banquet_id').val();
        if(optionRight == 1){
            $('#additionalOptionRight_'+paramOne+'').html('');
        }else{
            var url = '<?php echo url("/") ?>'+'/masdc/loadAccountingYearDetailList';
			$.ajax({
				type:'GET',
				url:url,
				data:{banquetId:banquetId,m:m},
				success:function(res){
					$('#additionalOptionRight_'+paramOne+'').html('<select name="additionOptionalValue_'+paramOne+'[]" id="additionOptionalValue_'+paramOne+'" class="form-control" multiple="multiple">'+res+'</select>');
					$("select").select2();
				}
			});
        }
    }
	
	
</script>