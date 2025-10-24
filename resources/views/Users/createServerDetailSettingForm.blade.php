@extends('layouts.app')
@section('content')
	<br />
	<br />
	<br />
	<br />
	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table class="table table-bordered sf-table-list">
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Banquet Name</th>
							<th class="text-center">Server Status</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$counter = 1;
							foreach($banquetList as $blRow){
						?>
							<tr>
								<td class="text-center"><?php echo $counter++;?></td>
								<td><?php echo $blRow->name;?></td>
								<td>
									<?php
										if($blRow->server_on_off == 1){
											echo '<button class="btn btn-sm btn-primary" onclick="trougleServerStatus(\''.$blRow->id.'\',\'2\',)">Enable</button>';
										}else{
											echo '<button class="btn btn-sm btn-danger" onclick="trougleServerStatus(\''.$blRow->id.'\',\'1\',)">Disable</button>';
										}
									?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				<table>
			</div>
		</div>
	</div>
	<script>
		function trougleServerStatus(paramOne,paramTwo){
			var m = '<?php echo CommonFacades::getSessionCompanyId()?>';
			var data = { id:paramOne,serverStatus:paramTwo,m:m};
			$.ajax({
				url: '<?php echo url('/')?>'+'/users/trougleServerStatus',
				type: "GET",
				data: data,
				success: function (data) {
					window.location.href = "<?php echo url('/logout')?>";
				}		
			});
		}
	</script>
@endsection