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
                            <?php echo CommonFacades::displayViewPageTitle('Add LogOut Timing Banquet Setting Form');?>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control" name="banquetName" id="banquetName" onchange="loadLogOutTimingBanquetSettingBanquetWise(this.value)">
                                <option value="">Select Banquet Name</option>
                                <?php foreach($banquetList as $row){?>
                                    <option value="<?php echo $row->id?>"><?php echo $row->name?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="lineHeight">&nbsp;</div>
                    <div id="loadLogOutTimingBanquetSettingBanquetWise" class="loadLogOutTimingBanquetSettingBanquetWise"></div>
                </div>
			</div>
		</div>
	</div>
    <script>
        $(function () {
            $("select").select2();
        }); 
		function dateFieldEnableDisable(paramOne,paramTwo){
            var selectedValue = $('#option_rights_'+paramOne+'_'+paramTwo+'').val();
            if(selectedValue == '1'){
                $('#option_open_number_'+paramOne+'_'+paramTwo+'').prop("disabled", false);
            }else if(selectedValue == '2'){
                $('#option_open_number_'+paramOne+'_'+paramTwo+'').prop("disabled", true);
            }else{
                $('#option_open_number_'+paramOne+'_'+paramTwo+'').prop("disabled", true);
            }
        }
        function loadLogOutTimingBanquetSettingBanquetWise(paramOne){
        	$('.loadLogOutTimingBanquetSettingBanquetWise').html('');
            $('.loadLogOutTimingBanquetSettingBanquetWise').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/umfal/loadLogOutTimingBanquetSettingBanquetWise',
                type: "GET",
                data: { m:m,banquetId:paramOne},
                success:function(data) {
                    $('.loadLogOutTimingBanquetSettingBanquetWise').html('');
                    $('.loadLogOutTimingBanquetSettingBanquetWise').append(data);
                }
            });
        }
		function calculateMinutes(paramOne){
			var minutes = $('#minutes_'+paramOne+'').val();
			var minutesToSeconds = minutes*60;
			var seconds = $('#seconds_'+paramOne+'').val(minutesToSeconds);
			
		}
        
        function optionDisableAndEnableAutomaticLogout(paramOne){
            var automaticLogout = $('#automaticLogout_'+paramOne+'').val();
            if(automaticLogout == 1){
                $('#minutes_'+paramOne+'').prop("disabled", true);
            }else{
                $('#minutes_'+paramOne+'').prop("disabled", false);
            }
        }

        function updateUsersWiseBanquetSetting(paramOne){

        	var postData = $('#updateUsersWiseLogOutTimingBanquetSettingFormDetail').serializeArray();
            var formURL = $('#updateUsersWiseLogOutTimingBanquetSettingFormDetail').attr("action");
            $.ajax({
            	headers: {
      				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                url : formURL,
                type: "POST",
                data : postData,
                success:function(data){
                   location.reload();
                }
            });
        }
    </script>
@endsection