<?php
$accType = Auth::user()->acc_type;
$m;
$parentCode = $_GET['parentCode'];
$k=1;
$messageStatus = '0'; 
?>
 
@extends('layouts.default')
@section('content')
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
			<?php echo CommonFacades::displayPrintButtonInBlade('PrintLawnList','','1');?>
			<?php echo CommonFacades::displayExportButton('LawnList','','1')?>
		</div>
	</div>
	<div class="lineHeight">&nbsp;</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('View Message Template List');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div id="PrintLawnList">
						<div id="editPageResponse">
						</div>
						<div class="row" id="mainViewListPage">
							<div class="col-lg-12 col-md-12 col-sm-12col-xs-12">
								<div class="table-responsive wrapper">
									<table class="table table-bordered customTableTwo" id="LawnList">
										<thead>
										<th class="text-center col-sm-1">S.No</th>
										<th class="text-center">Message Title</th>
										<th class="text-center">Message Description</th>
										<th class="text-center">Status</th>
										<th class="text-center col-sm-2 hidden-print">Action</th>
										</thead>
										<tbody id="showMessageTemplate">
											
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
	<script>
$(document).ready(function() {	
	messageTemplate()

});

function messageTemplate(){
	var m = '<?php echo $m ?>';
	$.ajax({
			type:'GET',
			url:'<?php echo url("/") ?>'+'/udc/filterMessageTemplate',
			data:{m:m},
			success:function(res){
				$("#showMessageTemplate").html(res);
			}
		});
}

</script>
@endsection


