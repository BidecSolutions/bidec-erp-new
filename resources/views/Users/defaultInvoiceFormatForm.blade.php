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
							<?php echo CommonFacades::displayViewPageTitle('Add Banquet Default Invoice Format Form');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<select class="form-control" name="banquetName" id="banquetName" onchange="loadBanquetDefaultInvoiceFormatBanquetWise(this.value)">
								<option value="">Select Banquet Name</option>
								<?php foreach($banquetList as $row){?>
									<option value="<?php echo $row->id?>"><?php echo $row->name?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="lineHeight">&nbsp;</div>
					<div id="loadBanquetDefaultInvoiceFormatBanquetWise" class="loadBanquetDefaultInvoiceFormatBanquetWise"></div>
				</div>
			</div>
		</div>
	</div>
    <script>
        $(function () {
            $("select").select2();
        });
		function loadBanquetDefaultInvoiceFormatBanquetWise(paramOne){
        	$('.loadBanquetDefaultInvoiceFormatBanquetWise').html('');
            var m = '<?php echo $m;?>';
			if(paramOne == ''){
				alert('Something Wrong! Please select Banquet...');
				return false;
			}else{
				$('.loadBanquetDefaultInvoiceFormatBanquetWise').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');
				$.ajax({
					url: '<?php echo url('/')?>/umfal/loadBanquetDefaultInvoiceFormatBanquetWise',
					type: "GET",
					data: { m:m,banquetId:paramOne},
					success:function(data) {
						$('.loadBanquetDefaultInvoiceFormatBanquetWise').html('');
						$('.loadBanquetDefaultInvoiceFormatBanquetWise').append('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="lineHeight">&nbsp;</div><div class="panel"><div class="panel-body">'+data+'</div></div></div></div>');
					}
				});
			}
        }

        function updateBanquetWiseDefaultInvoiceFormatDetail(paramOne){
			var banquetId = $('#banquetName').val();
        	var postData = $('#updateBanquetWiseDefaultInvoiceFormatDetail').serializeArray();
            var formURL = $('#updateBanquetWiseDefaultInvoiceFormatDetail').attr("action");
            $.ajax({
            	headers: {
      				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                url : formURL,
                type: "POST",
                data : postData,
                success:function(data){
                    loadBanquetDefaultInvoiceFormatBanquetWise(banquetId);
                }
            });
        }
    </script>
@endsection