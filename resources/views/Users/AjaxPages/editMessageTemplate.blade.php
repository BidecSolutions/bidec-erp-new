<?php
$accType = Auth::user()->acc_type;
$m;
$d = DB::selectOne('select `dbName` from `companies` where `id` = '.$m.'')->dbName;
$id = $_GET['id'];
$editUserDetail = DB::table('message_template')->where('id','=',$id)->first();
?>
    <div class="well">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php echo CommonFacades::displayViewPageTitle('Create Message Template');?>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                           <form>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <input type="hidden" name="pageType" value="<?php echo $_GET['pageType']?>">
                            <input type="hidden" name="parentCode" value="<?php echo $_GET['parentCode']?>">
							 <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']?>">
                            <div class="panel">
                                <div class="panel-body">
									<div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <input type="hidden" name="messgaeTemplate[]" class="form-control" id="messgaeTemplate" value="1" />
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label>Message Title</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                            <input type="text" name="MessageTitle1" id="MessageTitle1" value="<?php echo $editUserDetail->message_title ?>" class="form-control requiredField" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label>Message Description:</label>
                                            <span class="rflabelsteric"><strong>*</strong></span>
                                           <textarea rows="4" cols="50" name="MessageDescription1" id="MessageDescription1" class="form-control requiredField"><?php echo $editUserDetail->message_description ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
							<div class="moreMessageTemplate">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                <input type="submit" class="btn btn-success" />
                                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>

                                </div>
                            </div>
                           </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php



?>
<script>
      $(".btn-success").click(function(e){
			e.preventDefault();
			var id = $("#id").val();
			var messageTitle = $("#MessageTitle1").val();
			var messageDescription = $("#MessageDescription1").val();
			var m = '<?php echo $m ?>';

			if(messageTitle == ''){
				alert('Please insert Message Tittle');
			}
			else if(messageDescription == ''){
				alert('Please insert Message Description');
			}
			else{
				$.ajax({
					type:'GET',
					url:'<?php echo url("/") ?>'+'/uad/updateMessageTemplate',
					data:{messageTitle:messageTitle,messageDescription:messageDescription,id:id,m:m},
					success:function(res){
						if(res == 'duplicate'){
							alert('Message Title is Duplicate');
						}
						else{
						messageTemplate()
						$("#showMasterTableEditModel").modal('hide');
						}
					}
				});
			}
       });

	   $("#reset").click(function(e){
		  e.preventDefault();
		  var messageTitle = $("#MessageTitle1").val('');
		  var messageDescription = $("#MessageDescription1").val('');

	   });


</script>
