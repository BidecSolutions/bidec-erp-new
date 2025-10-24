<?php

$accType = Auth::user()->acc_type;
$m;
$d = DB::selectOne('select `dbName` from `companies` where `id` = '.$m.'')->dbName;
?>
@extends('layouts.default')

@section('content')
    <?php
    $currentDate = date('Y-m-d');
    ?>
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('Create Message Template');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<?php echo Form::open(array('url' => 'uad/addMessageTemplate?m='.$m.'&&d='.$d.'','id'=>'messageTemplate'));?>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
					<input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
					<div class="panel">
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<input type="hidden" name="messgaeTemplate[]" class="form-control" id="messgaeTemplate" value="1" />
								</div>
							</div>


							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label>Message Title</label>
									<span class="rflabelsteric"><strong>*</strong></span>
									<input type="text" name="MessageTitle1" id="MessageTitle1" value="" class="form-control requiredField" />
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label>Message Description:</label>
									<span class="rflabelsteric"><strong>*</strong></span>
								<textarea rows="4" cols="50" name="MessageDescription1" id="MessageDescription1" class="form-control requiredField"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="moreMessageTemplate">&nbsp;</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
							{{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
							<button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
							<input type="button" class="btn btn-sm btn-primary AddMoreMessage" value="Add More Message" />
						</div>
					</div>
					<?php echo Form::close();?>
				</div>
			</div>
		</div>
	</div>
    <script>
        $(document).ready(function() {
            var message = 1;
            $('.AddMoreMessage').click(function (e){
                e.preventDefault();
                message++;

                $.ajax({
                    url: '<?php echo url('/')?>/umfal/AddMoreMessageTemplate',
                    type: "GET",
                    data: { id:message},
                    success:function(data) {

                        $('.moreMessageTemplate').append('<div id="sectionLawn_'+message+'"><a href="#" onclick="removeLawnSection('+message+')" class="btn btn-xs btn-danger">Remove</a><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div>');
                    }
                });
            });

            // Wait for the DOM to be ready
            $(".btn-success").click(function(e){
                var message = new Array();
                var val;
                $("input[name='messgaeTemplate[]']").each(function(){
                    message.push($(this).val());
                });
                var _token = $("input[name='_token']").val();
                for (val of message) {

                    jqueryValidationCustom();
                    if(validate == 0){
                        //alert(response);
                    }else{
                        return false;
                    }
                }

            });

        });

        function removeLawnSection(id){
            var elem = document.getElementById('sectionLawn_'+id+'');
            elem.parentNode.removeChild(elem);
        }
    </script>
@endsection
