<?php
	
?>
@extends('layouts.default')

@section('content')
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<?php echo CommonFacades::displayViewPageTitle('Add Ip Address');?>
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							<div class="panel">
								<div class="panel-body">
									<div class="row">
										<?php
										echo Form::open(array('url' => 'uad/addIpAddressDetail','id'=>'addIpAddressDetail'));
										?>
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<label>Ip Title Name</label>
											<input type="text" name="ip_title_name" id="ip_title_name" value="" class="form-control" />
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<label>Ip Address</label>
											<input type="text" name="ip_address" id="ip_address" value="" class="form-control" />
										</div>
										<div style="line-height: 8px;">&nbsp;</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											{{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
											<button type="reset" id="reset" class="btn btn-primary">Clear Form</button>

											<?php
											//echo Form::submit('Click Me!');
											?>
										</div>
										<?php
										echo Form::close();
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<?php echo CommonFacades::displayViewPageTitle('View Ip Address List');?>
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table class="table table-bordered sf-table-list">
											<thead>
											<th class="text-center">S.No</th>
											<th class="text-center">Title Name</th>
											<th class="text-center">Ip Address</th>
											<th class="text-center">Action</th>
											</thead>
											<tbody id="viewIpAddressList">
											</tbody>
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
    <script type="text/javascript">
        $(document).ready(function() {
            function viewIpAddressList(){
                $('#viewIpAddressList').html('<tr><td colspan="4"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><div class="loader"></div></div></div></div></td><tr>');
                $.ajax({
                    url: '<?php echo url('/')?>/udc/viewIpAddressList',
                    type: "GET",
                    success:function(data) {
                        setTimeout(function(){
                            $('#viewIpAddressList').html(data);
                        },1000);
                    }
                });
            }
            viewIpAddressList();


            $(function(){
                $('#addIpAddressDetail').on('submit',function(e){
                    $.ajaxSetup({
                        header:$('meta[name="_token"]').attr('content')
                    })
                    e.preventDefault(e);

                    $.ajax({
                        type:"POST",
                        url:'<?php echo url('/')?>/uad/addIpAddressDetail',
                        data:$(this).serialize(),
                        dataType: 'json',
                        success: function(data){
                            console.log(data);
                        },
                        error: function(data){
                        }
                    })
                    $("#reset").click();
                    viewIpAddressList();
                });
            });
        });
    </script>
@endsection