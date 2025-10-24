<?php 
$accType = Auth::user()->acc_type;
if($accType == 'client'){
    $m = CommonFacades::getSessionCompanyId();
}else if($accType == 'superadmin'){
    $m = CommonFacades::getSessionCompanyId();
}else{
    $m = Auth::user()->company_id;
}
$k = 1;
foreach($messageTemplateData as $row){
 if($row->status == 1){
	 $messageStatus = 'Active';
 }
 else if($row->status == 2){
	 $messageStatus = 'Deleted';
 }
 
	 
	 ?>
  
  <tr>
   <td class="text-center"><?php echo $k++ ?></td>
   <td class="text-center"><?php echo $row->message_title ?></td>
   <td class="text-center"><?php echo $row->message_description ?></td>
   <td class="text-center"><?php echo $messageStatus ?></td>
   <td class="text-center hidden-print">
	<div class="dropdown theme-btn">	
			<button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span>
			</button>
				 <ul class="dropdown-menu">
				<?php if($row->status == 1) { ?> 
				 
					<li><a onclick="showMasterTableEditModel('udc/editMessageTemplate','<?php echo $row->id ?>','Edit Message','<?php echo $m?>')"><span class="glyphicon glyphicon-edit"></span> Edit</a></li>
					<li><a onclick="checkDeleteRepost('<?php echo $row->id ?>','1')"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>
				<?php } else if($row->status == 2) { ?>	
					<li><a onclick="checkDeleteRepost('<?php echo $row->id ?>','2')"><span class="glyphicon glyphicon-trash"></span> Repost</a></li>
					
				<?php } ?>
				</ul>
		</div>
   </td>
  </tr> 
				<?php 
				}



?>
<script>
function checkDeleteRepost(id,status){
	$.ajax({
		type:'GET',
		url:'<?php echo url("/") ?>'+'/uad/checkDeleteRepost',
		data:{id:id,status:status},
		success:function(res){
			messageTemplate()
		}		
	});
	
}
</script>