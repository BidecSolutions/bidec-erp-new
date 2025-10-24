<?php
    $accType = Auth::user()->acc_type;
    $currentDate = date('Y-m-d');
    $m;
?>
@extends('layouts.default')

@section('content')
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('Create Period Lock Form');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php echo Form::open(array('url' => 'uad/addPeriodLockDetail?m='.$m.'','id'=>'addUsersLoginTimePeriodAndPermissionDetail'));?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                            <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Month-Year</label>
                                        <input type="month" value="<?php echo date('Y-m')?>" name="month_year" class="form-control" id="month_year" />
                                    </div>
                                </div>
                                <div class="lineHeight">&nbsp;</div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                        {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                                        <input type="button" style="display: none;" class="btn btn-sm btn-primary addMoreSetUsersLoginTimePeriod" value="Add More Set User's Login Time Period Section" />
                                    </div>
                                </div>
                            <?php echo Form::close();?>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 hidden">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection