<?php
	use App\Helpers\CommonHelper;
?>
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
									<?php echo CommonHelper::displayViewPageTitle('Add Main Menu Title');?>
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							<div class="panel">
								<div class="panel-body">
									<div class="row">
										<?php
											echo Form::open(array('url' => 'uad/addMainMenuTitleDetail','id'=>'addMainMenuTitleForm'));
										?>
											<input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<label for="">Menu Type</label>
												<select name="menu_type" id="menu_type" required class="form-select select2 required form-control" id="">
													@foreach($menuType as $key => $mtRow)
														<option value="{{$key}}">{{$mtRow}}</option>
													@endforeach
												</select>
											</div>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<label for="">Menu Icon</label>
                                        		<input type="text" id="menu_icon" name="menu_icon" required placeholder="Menu Icon" class="form-control required">
											</div>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<label>Sub Navigation Title Name</label>
												<input type="text" name="title_name" id="title_name" value="" class="form-control" />
											</div>
											<div>&nbsp;</div>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												{{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
												<button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
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
									<?php echo CommonHelper::displayViewPageTitle('View Main Menu Title');?>
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
												<th class="text-center">Sub Navigation Title</th> 
												<th class="text-center">Action</th>
											</thead>
											<tbody id="viewMainMenuTitleList">
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
@endsection


