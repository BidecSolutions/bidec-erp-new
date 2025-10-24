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
							<?php echo CommonFacades::displayViewPageTitle('View Period Lock List');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">S.No</th>
                                            <th class="text-center">Year-Month</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $counter = 1;
                                        @endphp
                                        @foreach($getData as $key => $row)
                                            @php
                                                if($row->status == 1):
                                                    $status = 'Disable';
                                                    $eSelected = '';
                                                    $dSelected = 'selected';
                                                else:
                                                    $status = 'Enable';
                                                    $eSelected = 'selected';
                                                    $dSelected = '';
                                                endif;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td class="text-center">{{ $row->year_and_month }}</td>
                                                <td>{{$status}}</td>
                                                <td>
                                                    <select class="form-control" id="change_period_lock_status_<?php echo $row->id?>" name="change_period_lock_status_<?php echo $row->id?>" onchange="changePeriodLockStatus('<?php echo $row->id?>')">
                                                        <option value="1" <?php echo $dSelected;?>>Disable</option>
                                                        <option value="2" <?php echo $eSelected;?>>Enable</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changePeriodLockStatus(param){
            var periodLockStatus = $('#change_period_lock_status_'+param+'').val();
            var baseUrl = $("#baseUrl").val();
            $.ajax({
                url: "<?php echo url('/udc/changePeriodLockStatus')?>",
                type: "GET",
                data: {id:param,periodLockStatus:periodLockStatus},
                success:function(data) {
                    location.reload();
                    //alert(data);
                }
            });
        }
    </script>
@endsection