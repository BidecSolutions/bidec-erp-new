<?php
	
?>
@extends('layouts.default')

@section('content')
	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="well_N">
				<div class="boking-wrp dp_sdw">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php echo CommonFacades::displayViewPageTitle('Add Role and Permission Detail');?>
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<?php
						echo Form::open(array('url' => 'uad/addRoleDetail','id'=>'addRoleDetail'));
					?>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<label>Role Name:</label>
							<input type="text" name="role_name" id="role_name" class="form-control" />
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<label>Role Description:</label>
							<textarea name="role_description" id="role_description" style="resize:none;" class="form-control"></textarea>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							
						</div>
					</div>
					<div class="lineHeight">&nbsp;</div>
					<div class="row">
						<?php
							$MainMenuTitles = DB::Connection('mysql')->table('menus')->select(['main_menu_id'])->groupBy('main_menu_id')->get();
							$counter = 1;
							foreach($MainMenuTitles as $row){
						?>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-primary">
								<div class="panel-heading">
								<h3 class="panel-title"><?php echo $row->main_menu_id;?></h3>
								<span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
								</div>
								<div class="panel-body">
									<?php
										$MainMenuTitlesSub = DB::Connection('mysql')->table('menus')->select(['main_menu_id','title','title_id','id'])->where('main_menu_id','=',$row->main_menu_id)->get();
										foreach($MainMenuTitlesSub as $row1){
									?>
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<label><?php echo $row1->title; ?> :</label>
												<input type="hidden" name="<?php echo $row1->title_id; ?>_checkbox_id" id="<?php echo $row1->title_id; ?>_checkbox_id" value="<?php echo $row1->id; ?>" />
											</div>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												
												<ul>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[2]" value="Add" /> <strong>Add</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[3]" value="ViewList" /> <strong>View List</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[4]" value="ViewSingle" /> <strong>View Single</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[5]" value="Edit" /> <strong>Edit</strong></li>
												</ul>
												<ul>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[6]" value="Delete" /> <strong>Delete</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[7]" value="PrintList" /> <strong>Print List</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[8]" value="PrintSingle" /> <strong>Print Single</strong></li>
													<li class="checkbox-inline"><input type="checkbox" checked="checked" name="<?php echo $row1->title_id; ?>_checkbox[9]" value="Export" /> <strong>Export</strong></li>
												</ul>
											</div>
										</div>
										<div class="lineHeight">&nbsp;</div>
									<?php }?>
								</div>
							</div>
						</div>
						<?php }?>
					</div>
					<?php
						echo Form::submit('Click Me!');
						echo Form::close();
					?>
				</div>
			</div>
		</div>
	</div>
	<script>
		<?php /*?>$(document).on('click', '.panel-heading span.clickable', function(e){
    		var $this = $(this);
			if(!$this.hasClass('panel-collapsed')) {
				$this.parents('.panel').find('.panel-body').slideUp();
				$this.addClass('panel-collapsed');
				$this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
			} else {
				$this.parents('.panel').find('.panel-body').slideDown();
				$this.removeClass('panel-collapsed');
				$this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			}
		})<?php */?>
		$('li :checkbox').on('click', function () {
    		var $chk = $(this),
        		$li = $chk.closest('li'),
        		$ul, $parent;
    			if ($li.has('ul')) {
        			$li.find(':checkbox').not(this).prop('checked', this.checked)
    			}do{
        			$ul = $li.parent();
        			$parent = $ul.siblings(':checkbox');
        		if ($chk.is(':checked')) {
					$parent.prop('checked', true)
        		} else {
            		$parent.prop('checked', false)
        		}
        		$chk = $parent;
        		$li = $chk.closest('li');
    		} while ($ul.is(':not(.someclass)'));
		});
	</script>
@endsection