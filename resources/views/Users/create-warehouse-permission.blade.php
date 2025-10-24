<?php
    use App\Helpers\CommonFacades;
	$accType = Auth::user()->acc_type;
	$m;
?>
@extends('layouts.default')

@section('content')
	
	<div class="well_N">
		<div class="boking-wrp dp_sdw">
			
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					@if (count($errors) > 0)
						<div class = "alert alert-danger">
							<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
							</ul>
						</div>
					@endif
				</div>
				<div class="lineHeight">&nbsp;</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="well">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<span class="subHeadingLabelClass">Assign Warehouse</span>
							</div>
						</div>
						<div class="lineHeight">&nbsp;</div>
						{{ Form::open(array('route' => 'user-warehouse.permissions.post','id'=>'permissionForm')) }}
							
							<div class="panel">
								<div class="panel-body">
									
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>{{ getUserDetail($user_id)->name }}</label>
                                                    <input type="hidden" name="user_id" value="{{ $user_id }}">                            
                                                </div>                                                                                            
                                                <div class="col-md-3">
                                                    <label>Select Warehouse:</label>
                                                    <span class="rflabelsteric"><strong>*</strong></span>
                                                    
                                                    <select class="select2 form-control requiredField" multiple="multiple" required name="warehouse_id[]" id="warehouse_id" >
                                                        {!! SelectListFacades::getAllLocation($m) !!}
                                                    </select>
                                                </div>                                                
                                            </div>        
										</div>
									</div>									
								</div>
							</div>
							<div class="lineHeight">&nbsp;</div>
							<div class="locationSection"></div>
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
									{{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
									<button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
									<input type="hidden" class="btn btn-sm btn-primary addMoreLocationSection" value="Add More Location's Section" />
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
@section('custom-js-end')
	@include('select2')
	<script>
		$(document).ready(function() {
			$('.select2').select2();
		});
	</script>
@endsection
