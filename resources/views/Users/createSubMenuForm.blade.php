@extends('layouts.layouts')

@section('content')
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<?php //echo CommonFacades::displayViewPageTitle('Add Sub Menu');?>
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							
							<div class="row">
								<?php
								echo Form::open(array('url' => 'uad/addSubMenuDetail','id'=>'addSubMenuForm'));
								?>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label>Main Navigation Name</label>
									<select class="form-control" name="menu_id" id="menu_id">
										<option value="">Select Menu</option>
										@foreach($menus as $row)
											<option value="{{$row->id}}">{{$row->menu_name}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label for="">Sub Menu Type</label>
									<select name="sub_menu_type" id="sub_menu_type" required class="form-control form-select select2 required">
										<option value="">Select Sub Menu Type</option>
										<option value="1">Front Nav</option>
										<option value="2">Inside Page</option>
									</select>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<label for="">Sub Menu Icon</label>
                                        <input type="text" id="sub_menu_icon" name="sub_menu_icon" required placeholder="Menu Icon" class="form-control required">
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label for="">Sub Menu Name</label>
                                    <input type="text" id="sub_menu_name" name="sub_menu_name" required placeholder="Sub Menu Name" class="form-control required">
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<label for="">URL</label>
                                    <input type="text" id="url" name="url" required placeholder="URL" class="form-control required">
								</div>
								<div>&nbsp;</div>
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
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<?php //echo CommonFacades::displayViewPageTitle('View Sub Menu');?>
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table class="table table-bordered sf-table-list">
											<thead>
											<th class="text-center">S.No</th>
											<th class="text-center">Main Navigation</th>
											<th class="text-center">Sub Navigation</th>
											<th class="text-center">Action</th>
											</thead>
											<tbody id="viewSubMenuList">
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
            function viewSubMenuList(){
                $('#viewSubMenuList').html('<tr><td colspan="4"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><div class="loader"></div></div></div></div></td><tr>');
                $.ajax({
                    url: '<?php echo url('/')?>/udc/viewSubMenuList',
                    type: "GET",
                    success:function(data) {
                        setTimeout(function(){
                            $('#viewSubMenuList').html(data);
                        },1000);
                    }
                });
            }
            //viewSubMenuList();


            $(function(){
                $('#addSubMenuForm').on('submit',function(e){
                    $.ajaxSetup({
                        header:$('meta[name="_token"]').attr('content')
                    })
                    e.preventDefault(e);

                    $.ajax({
                        type:"POST",
                        url:'<?php echo url('/')?>/uad/addSubMenuDetail',
                        data:$(this).serialize(),
                        dataType: 'json',
                        success: function(data){
                            alert(data);
                            console.log(data);
                        },
                        error: function(data){
                        }
                    })
                    $("#reset").click();
                    //viewSubMenuList();
                });
            });
        });
	</script>
@endsection