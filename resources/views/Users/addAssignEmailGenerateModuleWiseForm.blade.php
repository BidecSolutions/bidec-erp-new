<?php

use App\Models\MainMenuTitle;

$accType = Auth::user()->acc_type;
$currentDate = date('Y-m-d');
$m;

?>
@extends('layouts.default')

@section('content')
    <script src="{{ URL::asset('assets/select2/select2.full.min.js') }}"></script>
    <link href="{{ URL::asset('assets/select2/select2.css') }}" rel="stylesheet">
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php echo CommonFacades::displayViewPageTitle('Add Assign Email Generate Module Wise Form');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<select class="form-control" name="mainMenuName" id="mainMenuName" onchange="loadSubMenuSectionDependMainMenuName(this.value)">
								<option value="">Select Main Menu Option</option>
								<?php foreach($mainMenuTitleList as $selectRow){?>
								<option value="<?php echo $selectRow->main_menu_id?>"><?php echo $selectRow->main_menu_id?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div id="loadSubMenuSectionDependMainMenuName" class="loadSubMenuSectionDependMainMenuName"></div>
				</div>
			</div>
		</div>
	</div>
    <script>
        $(function () {
            $("select").select2();
        });
        function loadSubMenuSectionDependMainMenuName(paramOne){
            //alert(paramOne);
            $('.loadSubMenuSectionDependMainMenuName').html('');
            var m = '<?php echo CommonFacades::getSessionCompanyId();?>';
            $.ajax({
                url: '<?php echo url('/')?>/umfal/loadSubMenuSectionDependMainMenuName',
                type: "GET",
                data: { m:m,mainMenuName:paramOne},
                success:function(data) {
                    $('.loadSubMenuSectionDependMainMenuName').append('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div></div>');
                }
            });
        }
        function addMoreRowsSectionWise(param1,param2){
            var getPriviousId = $('#'+param1+'SectionRowsEmail_'+param2+'').val();
            $.ajax({
                type: "GET",
                data: {},
                success:function(data) {
                    var newInputId = parseInt(getPriviousId) + parseInt(1);
                    $('#'+param1+'SectionRowsEmail_'+param2+'').val(newInputId);
                    $('#'+param1+'Section_'+param2+'').append('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="Email" name="'+param1+'SectionRowsEmail_'+param2+'_'+newInputId+'" id="'+param1+'SectionRowsEmail_'+param2+'_'+newInputId+'" class="form-control '+param1+'SectionRowsEmail_'+param2+'" placeholder="Type Email"></div></div>');
                }
            });
        }
        function  checkValidation(param1,param2) {
            var checkRequiredField = document.getElementsByClassName(''+param1+'SectionRowsEmail_'+param2+'');
            for (i = 0; i < checkRequiredField.length; i++){
                var rf = checkRequiredField[i].id;
                if($('#'+rf).val() == ''){
                    $('#'+rf).css('border-color', 'red');
                    $('#'+rf).focus();
                    validate = 1;
                    return false;
                }else{
                    $('#'+rf).css('border-color', '#ccc');
                    validate = 0;
                }
            }
            addDetailTesting(param1,param2);
        }
        function addDetailTesting(param1,param2){
            alert('abc');
            var _token = $("input[name='_token']").val();
            var postData = $('#'+param1+'AssingEmailGenerateModuleWise_'+param2+'').serializeArray();
            var formURL = $('#'+param1+'AssingEmailGenerateModuleWise_'+param2+'').attr("action");
            $.ajax({
                url : formURL,
                type: "POST",
                data : postData,
                success:function(data){
                    alert(data);
                }
            });
        }

        function buttonEnableDisable(param1,param2,param3){
            if(param3 == '1'){
                $('#'+param1+'SubmitButton_'+param2+'').attr('disabled','disabled');
                $('#'+param1+'AddMoreButton_'+param2+'').attr('disabled','disabled');
                $('#'+param1+'RemoveButton_'+param2+'').attr('disabled','disabled');
            }else if(param3 == '2'){
                $('#'+param1+'SubmitButton_'+param2+'').removeAttr('disabled');
                $('#'+param1+'AddMoreButton_'+param2+'').removeAttr('disabled');
                $('#'+param1+'RemoveButton_'+param2+'').removeAttr('disabled');
            }
        }

        function removeLastRowInPanel(param1,param2){
            var getPriviousId = $('#'+param1+'SectionRowsEmail_'+param2+'').val();
            var newInputId = parseInt(getPriviousId) - parseInt(1);
            if(newInputId == '0'){
                alert('Section Last Row Not Delete');
            }else{
                $('#'+param1+'SectionRowsEmail_'+param2+'').val(newInputId);
                $('#'+param1+'Section_'+param2+'').children().last().remove();
            }
        }
    </script>
@endsection