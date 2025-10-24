<?php 
	url('/');
	url()->current();
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="bhoechie-tab-container">
			<div class="bhoechie-tab-menu">
				<div class="list-group">
					<?php $MainMenuTitles = DB::Connection('mysql')->table('menus')->select(['title','id','title_id'])->where('main_menu_id','=','Users')->get();?>
					@foreach($MainMenuTitles as $MainMenuTitle)
						<div data-toggle="collapse" data-target="#{{ $MainMenuTitle->title_id }}" class="collapsed">
							<a href="#" class="list-group-item list-group-item-collaps">{{ $MainMenuTitle->title }}</a>
						</div>
						<div class="sub-menu collapse" id="{{ $MainMenuTitle->title_id }}">
							<?php 
								$subMenu = DB::Connection('mysql')->table('menu')->select(['m_type','name','m_controller_name','m_main_title'])->where('m_parent_code','=',$MainMenuTitle->id)->get();
								foreach($subMenu as $row1){
									$makeUrl = url(''.$row1->m_controller_name.'');
									if(url()->current() == $makeUrl){
							?>
									<script>$('#<?php echo $row1->m_main_title?>').addClass("in");</script>
									<?php
										}
									?>
									<a href="<?php echo url(''.$row1->m_controller_name.'')?>" class="list-group-item <?php if(url()->current() == $makeUrl){echo 'triangle-isosceles right';}?>">&nbsp;<?php echo $row1->name;?></a>
							<?php 
								}
							?>
                  		</div>
						<div class="lineHeight">&nbsp;</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>