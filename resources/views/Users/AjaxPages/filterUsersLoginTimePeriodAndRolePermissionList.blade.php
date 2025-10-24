<?php
	use App\Helpers\CommonHelper;
	$counter = 1;
	$m = Session::get('company_id');
	$data ='';
	$authorityUsers = [];
	$accType = Auth::user()->acc_type;
	if($accType == 'client'){
		$authorityUsers = ['superadmin','user','superuser'];
	}else if($accType == 'superadmin'){
		$authorityUsers = ['user','superuser'];
	}else if($accType == 'superuser'){
		$authorityUsers = ['user','superuser'];
	}

	$companyNameTwo = '';//CommonHelper::getCompanyNameTwo($m);

	foreach ($userDetails as $row){
		$userPasswordDetaill = explode("<*>",$row->sgpe);
		if($row->acc_type == 'user'){
			$companyName = '';//CommonHelper::getCompanyNameTwo($row->company_id);
		}else{
			$explodeCompanyDetail = explode("<*>",$row->company_id);
			$companyName = '';
			foreach($explodeCompanyDetail as $ecdRow){
				$companyName .= '';//CommonHelper::getCompanyNameTwo($ecdRow).',';
			}
		}

		if($row->status == '2'){
			$rowBColor = 'danger';
		}else{
			$rowBColor = '';
		}

		if($accType == 'client'){
			$hiddenRow = '';
		}else{
			if (strpos($companyName, $companyNameTwo) !== false) {
				$hiddenRow = '';
			}else{
				$hiddenRow = 'hidden';
			}
		}
?>
		<tr class="<?php echo $rowBColor.' '.$hiddenRow?>">
			<td class="text-center" ><?php echo $counter++  ?> </td>
			<td><?php //echo $companyName  ?> </td>
			<td><?php //echo $row->name  ?> </td>
			<td><?php echo $row->email  ?> </td>
			<td class="text-center" >
				@foreach($row->roles as $role)
					{{ $role->name }},
				@endforeach
			</td>
			<td class="text-center hidden-print hidden">
				<div class="dropdown">
					<button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a onclick="showDetailModelTwoParamerter('udc/viewProfile','<?php echo $row->id ?>','view User Profile','')"><span class="glyphicon glyphicon-eye-open"></span> View Profile</a></li>
						<?php
							if($accType == 'client'){
								if($row->status == 1){
						?>
									<li><a onclick="showDetailModelTwoParamerter('udc/userEdit','<?php echo $row->id ?>','User Edit Detail')"><span class="glyphicon glyphicon-eye-open"></span> Edit</a></li>
									<?php /*?><li><a onclick="disableUserAccountDetail('<?php echo $row->id ?>','<?php echo $row->role_id?>')"><span class="glyphicon glyphicon-eye-open"></span> Disable Account</a></li>
									<li><a onclick="showDetailModelTwoParamerter('udc/addCompanyRoleForm','<?php echo $row->id ?>','Add Company Role')"><span class="glyphicon glyphicon-eye-open"></span> Add Company Role</a></li>
									<li><a href="{{ route('user-warehouse.permissions.create', $row->id) }}"><span class="glyphicon glyphicon-eye-open"></span> Assign Warehouse</a></li>
									<li><a href="{{ route('notification.userNotificationPermissions', $row->id) }}"><span class="glyphicon glyphicon-eye-open"></span> Assign Notification</a></li>
									<li><a onclick="showDetailModelTwoParamerter('udc/addUserAdditionalRightsForm','<?php echo $row->id ?>','Add User Additional Rights')"><span class="glyphicon glyphicon-eye-open"></span> Add User Additional Rights</a></li><?php */?>
						<?php
								}else{
						?>
									<?php /*?><li><a onclick="enableUserAccountDetail('<?php echo $row->id ?>','<?php echo $row->role_id?>')"><span class="glyphicon glyphicon-eye-open"></span> Enable Account</a></li><?php */?>
						<?php
								}
							}else{
								if($row->status == 1){
						?>
									<li><a onclick="showDetailModelTwoParamerter('udc/userEdit','<?php echo $row->id ?>','User Edit Detail')"><span class="glyphicon glyphicon-eye-open"></span> Edit</a></li>
									<?php /*?><li><a onclick="disableUserAccountDetail('<?php echo $row->id ?>','<?php echo $row->role_id?>')"><span class="glyphicon glyphicon-eye-open"></span> Disable Account</a></li>
									<li><a onclick="showDetailModelTwoParamerter('udc/addCompanyRoleForm','<?php echo $row->id ?>','Add Company Role')"><span class="glyphicon glyphicon-eye-open"></span> Add Company Role</a></li>
									<li><a href="{{ route('user-warehouse.permissions.post', $row->id) }}"><span class="glyphicon glyphicon-eye-open"></span> Assign Warehouse</a></li>
									<li><a href="{{ route('notification.userNotificationPermissions', $row->id) }}"><span class="glyphicon glyphicon-eye-open"></span> Assign Notification</a></li>
									<li><a onclick="showDetailModelTwoParamerter('udc/addUserAdditionalRightsForm','<?php echo $row->id ?>','Add User Additional Rights')"><span class="glyphicon glyphicon-eye-open"></span> Add User Additional Rights</a></li><?php */?>
						<?php
								}else{
						?>
									<?php /*?><li><a onclick="enableUserAccountDetail('<?php echo $row->id ?>','<?php echo $row->role_id?>')"><span class="glyphicon glyphicon-eye-open"></span> Enable Account</a></li><?php */?>
						<?php
								}
							}
						?>
					</ul>
				</div>
			</td>
		</tr>
<?php
	}
?>
<script>
$(document).ready(function(){
	userEnableDisableStatus();
});

 function userEnableDisableStatus(){
	 var userEnableDisableStatus = [];
	 $('input[name="checkboxes"]').each(function() {
		 var resign = $(this).val().split("<*>");

		var checkboxValues = $("#"+resign[0]).val();
		var checking2 = checkboxValues.split("<*>");
		if(checking2[1] == 1){
			 $("#"+resign[0]).prop("checked", true);
		}
		else if(checking2[1] == 2){
			 $("#"+resign[0]).prop("checked", false);
		}


		});


 }

function checkStausActive(status,userId){
	var url = '<?php echo url("/") ?>'+'/bd/checkStausActiveUser';
	$.ajax({
		type:'GET',
		url:url,
		data:{status:status,userId:userId},
		success:function(res){
			alert(res);
		}
	});

}

</script>
