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
							<?php echo CommonFacades::displayViewPageTitle('Add Header Set in Vouchers Form');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<select class="form-control" name="banquetName" id="banquetName" onchange="loadBanquetHeaderSetInVouchersBanquetWise(this.value)">
								<option value="">Select Banquet Name</option>
								<?php foreach($banquetList as $row){?>
									<option value="<?php echo $row->id?>"><?php echo $row->name?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div id="loadBanquetHeaderSetInVouchersBanquetWise" class="loadBanquetHeaderSetInVouchersBanquetWise"></div>
				</div>
			</div>
		</div>
	</div>
    <script>
        $(function () {
            $("select").select2();
        });
		function loadBanquetHeaderSetInVouchersBanquetWise(paramOne){
        	$('.loadBanquetHeaderSetInVouchersBanquetWise').html('');
            $('.loadBanquetHeaderSetInVouchersBanquetWise').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
            var m = '<?php echo $m;?>';
            $.ajax({
                url: '<?php echo url('/')?>/umfal/loadBanquetHeaderSetInVouchersBanquetWise',
                type: "GET",
                data: { m:m,banquetId:paramOne},
                success:function(data) {
                    $('.loadBanquetHeaderSetInVouchersBanquetWise').html('');
                    $('.loadBanquetHeaderSetInVouchersBanquetWise').append('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div></div>');
                }
            });
        }

        function updateBanquetWiseHeaderSetInVouchersDetail(paramOne){
			var banquetId = $('#banquetName').val();
        	var postData = $('#updateBanquetWiseHeaderSetInVouchersDetail').serializeArray();
            var formURL = $('#updateBanquetWiseHeaderSetInVouchersDetail').attr("action");
            $.ajax({
            	headers: {
      				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                url : formURL,
                type: "POST",
                data : postData,
                success:function(data){
                    loadBanquetHeaderSetInVouchersBanquetWise(banquetId);
                }
            });
        }
    </script>
@endsection