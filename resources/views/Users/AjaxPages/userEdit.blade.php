<?php

	$accType = Auth::user()->acc_type;
	$currentDate = date('Y-m-d');
	$m;
	$id = $_GET['id'];
	$userDetails = DB::Connection('mysql')->table('users')->where('id','=',$id)->first();;
	$companyIdExplode = explode("<*>",$userDetails->company_id);
	$userPasswordDetaill = explode("<*>",$userDetails->sgpe);

?>
	<div class="hidden" id="testingChecker">
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<?php echo CommonFacades::displayViewPageTitle('Edit Users Form');?>
					</div>
				</div>
				<div class="lineHeight">&nbsp;</div>
				<div class="row">
					<?php echo Form::open(array('url' => 'uad/editUsersLoginTimePeriodAndPermissionDetail?m='.$m.'','id'=>'editUsersLoginTimePeriodAndPermissionDetail'));?>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
					<input type="hidden" name="userIds" id="userIds" value="<?php echo $id ?>">
					<input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel">
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<input type="hidden" name="usersSection[]" class="form-control requiredField" id="usersSection" value="1" />
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="row">
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<label class="sf-label">Name</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="text" name="name" required="required" id="name" value="<?php echo $userDetails->name ?>" class="form-control requiredField">
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Email</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="email" required="required" name="username" id="username" value="<?php echo $userDetails->email ?>" class="form-control requiredField" onchange="checkUserLoginStatus()">
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Mobile No</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="number" name="mobile_no" 	required="required" id="mobile_no" value="<?php echo $userDetails->mobile_no ?>" class="form-control requiredField" onchange="checkMobileLength(this.value); testingFunction();">
												<p id="errorMobileNo"></p>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<label class="sf-label">CNIC No</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="text" data-inputmask="'mask': '99999-9999999-9'"  placeholder="XXXXX-XXXXXXX-X" required="required" name="cnic_no" id="cnic_no" value="<?php echo $userDetails->cnic_no ?>" class="form-control requiredField">
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Password</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="password" class="form-control requiredField" name="password" id="password" required value="<?php echo $userPasswordDetaill[1]; ?>" />
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" onchange="checkAccountType(); testingFunction();">
												<label class="sf-label">Account type</label>
												<select class="form-control" name="account_type" id="account_type" required="required">
													<option value="user" <?php if($userDetails->acc_type == 'user'){ echo 'selected';} ?> >User</option>
													<option value="superadmin" <?php if($userDetails->acc_type == 'superadmin'){ echo 'selected';} ?> >Super Admin</option>
													<option value="superuser" <?php if($userDetails->acc_type == 'superuser'){ echo 'selected';} ?> >Super User</option>
													<option value="client" style="display: none;" <?php if($userDetails->acc_type == 'client'){ echo 'selected';} ?> >Client</option>
													<option value="company" style="display: none;" <?php if($userDetails->acc_type == 'company'){ echo 'selected';} ?> >Company</option>
													<option value="master" style="display: none;" <?php if($userDetails->acc_type == 'master'){ echo 'selected';} ?>>Master</option>
												</select>
											</div>

											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Ip Address Status</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<select name="ip_address_status" id="ip_address_status" class="form-control">
													<option value="2" <?php  if($userDetails->ip_block == '2'){ echo 'selected';}?> >No</option>
													<option value="1" <?php  if($userDetails->ip_block == '1'){ echo 'selected';}?>>Yes</option>
												</select>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<label class="sf-label">Time Distrction</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<select name="time_distriction" id="time_distriction" class="form-control" onchange="timeDistrictionForLogin()">
													<option value="2" <?php  if($userDetails->time_distriction == '2'){ echo 'selected';}?> >No</option>
													<option value="1" <?php  if($userDetails->time_distriction == '1'){ echo 'selected';}?>>Yes</option>
												</select>
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Login Start Time</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="time" class="form-control requiredField" name="login_start_time" id="login_start_time" required min="00:00" max="23:59" value="<?php echo $userDetails->login_start_time ?>" />
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
												<label class="sf-label">Login End Time</label>
												<span style="font-size:17px !important; color:#F5F5F5 !important;"><strong>*</strong></span>
												<input type="time" class="form-control requiredField" name="login_end_time" id="login_end_time" required min="00:00" max="23:59" value="<?php echo $userDetails->login_end_time ?>" />
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" onchange="checkAccountType(); testingFunction();">
												<label class="sf-label">Select Company</label>
												<select id="dates-field2" class="multiselect-ui form-control" name="company_id_detail[]" multiple="multiple" required="required">
													<?php
														if($accType == 'client'){
															$companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->get();
														}else if($accType == 'superadmin'){
															$checkCompanyId = Auth::user()->company_id;
															$a = explode("<*>",$checkCompanyId);
															$companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->whereIn('id', $a)->get();
														}else if($accType == 'superuser'){
															$checkCompanyId = Auth::user()->company_id;
															$a = explode("<*>",$checkCompanyId);
															$companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->whereIn('id', $a)->get();
														}
														foreach($companiesList as $cRow1){
															$selectedValue = '';
															foreach($companyIdExplode as $row){
																if($cRow1->id == $row){
																	$selectedValue = 'selected';
																}
															}
													?>
															<option value ="<?php echo $cRow1->id;?>" <?php echo $selectedValue;?> ><?php echo $cRow1->name;?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="usersSection"></div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
							{{ Form::submit('Submit', ['class' => 'btn btn-success','value' => 'Update']) }}
							<button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
							<input type="button" style="display: none;" class="btn btn-sm btn-primary addMoreSetUsersLoginTimePeriod" value="Add More Set User's Login Time Period Section" />
						</div>
					</div>
					<?php echo Form::close();?>
				</div>
			</div>
		</div>
	</div>
    <script>

			var checkGlobalMobileNumberStatus = '';
			var checkGlobalCompanyId = '';

			function checkMobileLength(val){

			var checkLength = val.length;

			if(val[0] != 0){
				$("#errorMobileNo").html('Mobile No must Start With 0').css("color", "red");
				$('.btn-success').attr('disabled','disabled');
				checkGlobalMobileNumberStatus = 'error';

			}
			else if(checkLength > 11){
				$("#errorMobileNo").html('Mobile No must contain 10 Numbers').css("color", "red");
				$('.btn-success').attr('disabled','disabled');
				checkGlobalMobileNumberStatus = 'error';
			}
			else if(checkLength < 11){
				$("#errorMobileNo").html('Mobile No must contain 10 Numbers').css("color", "red");
				$('.btn-success').attr('disabled','disabled');
				checkGlobalMobileNumberStatus = 'error';
			}
			else if(checkLength == 11){
				$("#errorMobileNo").html('').css("color", "red");
				$('.btn-success').removeAttr('disabled');
				checkGlobalMobileNumberStatus = 'fine';
			}
			else{
				$('.btn-success').removeAttr('disabled');
				checkGlobalMobileNumberStatus = 'fine';
			}
		}


        $(":input").inputmask();
        function checkAccountType(){
            var selectedCompanies = $('#dates-field2').val();
            var accountType = $('#account_type').val();

            if(accountType == 'user'){
                if(selectedCompanies == null){
                    alert('Please asigne minimum one company for this user');
					//$('.btn-success').attr('disabled','disabled');
					checkGlobalCompanyId = 'haserror'
                }
				else if(selectedCompanies.length != 1){
                    alert('Multiple Companies not allow for user');
					//$('.btn-success').attr('disabled','disabled');
					checkGlobalCompanyId = 'haserror'
                }
				else{
					checkGlobalCompanyId = 'hasfine';
				}
			} else if(accountType == 'superadmin'){
                if(selectedCompanies == null){
                    alert('Please asigne minimum one company for this user');
					//$('.btn-success').attr('disabled','disabled');
					checkGlobalCompanyId = 'haserror'
                }
				else{
					checkGlobalCompanyId = 'hasfine';
				}

			}
			else{
				checkGlobalCompanyId = 'hasfine';
			}


        }
        $(function() {
            $('.multiselect-ui').multiselect({
                includeSelectAllOption: false
            });
        });
        function checkUserLoginStatus(){
			var userId = '<?php echo $userDetails->id?>';
            var newUsername = $('#username').val();
			var m = '<?php echo CommonFacades::getSessionCompanyId();?>';
            $.ajax({
                url: '<?php echo url('/')?>/udc/checkUserLoginStatus',
                type: "GET",
                data: { newUsername:newUsername,m:m,userId:userId},
                success:function(res) {
					var ress = $.trim(res);
                    if(ress == '1'){
                        $('#username').val('');
                        alert('Something Wrong! This email address is already used. Plz register another email address!');
                    }else{
                    }
                }
            });
        }
        $(document).ready(function() {
			idArray = [];
			var menus = ["Booking", "Finance", "Purchase", "Reports", "Sales","Users"];
			var userId = $("#userId").val();
			var m = '<?php echo $_GET["m"] ?>';

			for (i = 0; i < menus.length; i++) {


			   var id = menus[i];


			   var checkerss = $("#"+menus[i]).val();

					if(checkerss == '1'){

						var testingVariable = menus[i];
						$.ajax({
							url: '<?php echo url('/')?>/umfal/editShowModuleDetailOption',
							type: "GET",
							data: {id: menus[i], m: m,userId:userId},
							success: function (res) {
								$("#testingChecker").html(res);
								var userIdChecking = $("#testingId").val();
								$("#"+userIdChecking+'DetailOptionSection').html(res);
							}
						});

					}
				else if(checkerss === '2') {
                $('#'+menus[i]+'DetailOptionSection').html('');
				}
			}


            var p = 1;
            $('.addMoreSetUsersLoginTimePeriod').click(function (e){
                e.preventDefault();
                p++;
                var m = '<?php echo CommonFacades::getSessionCompanyId();?>';
                $.ajax({
                    url: '<?php echo url('/')?>/umfal/makeFormSetUsersLoginTimePeriod',
                    type: "GET",
                    data: { id:p,m:m},
                    success:function(data) {
                        $('.usersSection').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="bankPvs_'+p+'"><a href="#" onclick="removePvsSection('+p+')" class="btn btn-xs btn-danger">Remove</a><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div>');
                    }
                });
            });

            $(".btn-success").click(function(e){

                var pvs = new Array();
                var val;
                $("input[name='pvsSection[]']").each(function(){
                    pvs.push($(this).val());
                });
                var _token = $("input[name='_token']").val();
                for (val of pvs) {
                    jqueryValidationCustom();
                    if(validate == 0){
                        //alert(response);
                    }else{
                        return false;
                    }
                }

            });
        });



	  function showModuleDetailOption(id,value){
            var m = '<?php echo CommonFacades::getSessionCompanyId();?>';
			var userId = $("#userId").val();
            if(value === '1') {
                $.ajax({
                    url: '<?php echo url('/')?>/umfal/showModuleDetailOption',
                    type: "GET",
                    data: {id: id, m: m,userId:userId},
                    success: function (data) {
                        $('#' + id + 'DetailOptionSection').html('<div class="row">'+data+'</div>');
                        //$('.usersSection').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="bankPvs_'+p+'"><a href="#" onclick="removePvsSection('+p+')" class="btn btn-xs btn-danger">Remove</a><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div>');
                    }
                });
            }else if(value === '2') {
                $('#' + id + 'DetailOptionSection').html('');
            }

      }

        $('li :checkbox').on('click', function () {
            var $chk = $(this),
                $li = $chk.closest('li'),
                $ul, $parent;
            if ($li.has('ul')) {
                $li.find(':checkbox').not(this).prop('checked', this.checked)
            }do{
                $ul = $li.parent();
                $parent = $ul.siblings(':checkbox');
                if ($chk.is(':checked')) {
                    $parent.prop('checked', true)
                } else {
                    $parent.prop('checked', false)
                }
                $chk = $parent;
                $li = $chk.closest('li');
            } while ($ul.is(':not(.someclass)'));
        });


		function testingFunction(){

			if(checkGlobalMobileNumberStatus == 'error'){
				$('.btn-success').attr('disabled','disabled');
			}
			else if(checkGlobalCompanyId == 'haserror'){

				$('.btn-success').attr('disabled','disabled');
			}
			else if(checkGlobalCompanyId != 'haserror'){

				$('.btn-success').removeAttr('disabled');
			}
			else{
				$('.btn-success').removeAttr('disabled');
			}
		}

		function timeDistrictionForLogin(){
			var timeDistriction = $('#time_distriction').val();
			if(timeDistriction == '1'){
				$("#login_start_time").attr("required","required");
				$("#login_end_time").attr("required","required");

				$("#login_start_time").prop("disabled",false);
				$("#login_end_time").prop("disabled",false);
			}else{
				$("#login_start_time").removeAttr("required","required");
				$("#login_end_time").removeAttr("required","required");

				$("#login_start_time").prop("disabled",true);
				$("#login_end_time").prop("disabled",true);
			}
		}
		timeDistrictionForLogin();
    </script>
